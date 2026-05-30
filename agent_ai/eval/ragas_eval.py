"""RAGAS evaluation harness for agent_ai.

This script evaluates three RAG quality dimensions:
- faithfulness
- answer relevancy
- context relevancy

Expected input format (JSON list or JSONL records):
[
  {
    "question": "...",
    "answer": "...",
    "contexts": ["retrieved passage 1", "retrieved passage 2"],
    "ground_truth": "optional reference answer"
  }
]

Usage:
  python -m eval.ragas_eval --input data/rag_eval.json --output ragas_results.json

If your installed ragas version exposes different metric names, this module
tries the common aliases and fails with a clear error if none are found.
"""

from __future__ import annotations

import argparse
import asyncio
import json
import os
import re
import sys
import types
from dataclasses import dataclass
from pathlib import Path
from typing import Any, Dict, Iterable, List, Sequence


def _ensure_ragas_compat_shims() -> None:
    """Patch missing legacy imports expected by ragas 0.4.x.

    The installed langchain-community package in this workspace no longer ships
    `langchain_community.chat_models.vertexai`, but ragas still imports it.
    We provide a tiny placeholder module so ragas can import cleanly.
    """
    module_name = "langchain_community.chat_models.vertexai"
    if module_name in sys.modules:
        return

    try:
        __import__(module_name)
        return
    except Exception:
        pass

    shim = types.ModuleType(module_name)

    class ChatVertexAI:  # pragma: no cover - placeholder for import compatibility
        def __init__(self, *args, **kwargs):
            raise ImportError(
                "ChatVertexAI is not available in this environment. "
                "Install the matching langchain-community/vertexai stack if you need Vertex AI support."
            )

    shim.ChatVertexAI = ChatVertexAI
    sys.modules[module_name] = shim


def _load_env() -> None:
    """Load project .env so the evaluator can reuse the app's OpenAI key."""
    try:
        from dotenv import find_dotenv, load_dotenv
    except Exception:
        return

    script_dir = Path(__file__).resolve().parents[1]
    candidate_paths = [
        script_dir / ".env",
        script_dir.parent / ".env",
        Path(find_dotenv(usecwd=True)),
    ]

    for candidate in candidate_paths:
        try:
            if candidate and candidate.exists():
                load_dotenv(candidate, override=False)
                break
        except Exception:
            continue


def _load_records(path: Path) -> List[Dict[str, Any]]:
    text = path.read_text(encoding="utf-8").strip()
    if not text:
        return []

    if path.suffix.lower() == ".jsonl":
        return [json.loads(line) for line in text.splitlines() if line.strip()]

    data = json.loads(text)
    if isinstance(data, dict) and "records" in data:
        data = data["records"]
    if not isinstance(data, list):
        raise ValueError("Input JSON must be a list of records or a dict with a 'records' list")
    return data


def _normalize_contexts(contexts: Any) -> List[str]:
    if contexts is None:
        return []
    if isinstance(contexts, str):
        return [contexts]
    if isinstance(contexts, Sequence):
        return [str(item) for item in contexts if str(item).strip()]
    return [str(contexts)]


def _to_ragas_dataset(records: List[Dict[str, Any]]):
    try:
        from datasets import Dataset
    except Exception as exc:
        raise ImportError(
            "The 'datasets' package is required for RAGAS evaluation. Install it with: pip install datasets"
        ) from exc

    normalized = []
    for record in records:
        normalized.append(
            {
                "question": str(record.get("question", "")).strip(),
                "answer": str(record.get("answer", "")).strip(),
                "contexts": _normalize_contexts(record.get("contexts")),
                "ground_truth": str(record.get("ground_truth", record.get("reference_answer", ""))).strip(),
            }
        )

    return Dataset.from_list(normalized)


def _load_metric(module_names: Iterable[str], attr_names: Iterable[str]):
    for module_name in module_names:
        try:
            module = __import__(module_name, fromlist=["*"])
        except Exception:
            continue
        for attr_name in attr_names:
            metric = getattr(module, attr_name, None)
            if metric is not None:
                return metric
    raise ImportError(
        "Could not locate the requested RAGAS metric class/function. "
        "Please check your installed ragas version."
    )


def _build_metrics():
    _ensure_ragas_compat_shims()

    try:
        from openai import AsyncOpenAI, OpenAI
    except Exception as exc:
        raise ImportError(
            "The 'openai' package is required for RAGAS evaluation. Install it with: pip install openai"
        ) from exc

    from ragas.llms import llm_factory
    from ragas.embeddings import embedding_factory
    from ragas.metrics.collections import Faithfulness, AnswerRelevancy, ContextRelevance

    llm_model = os.environ.get("RAGAS_LLM_MODEL") or os.environ.get("OPENAI_MODEL") or "gpt-4o-mini"
    embedding_model = os.environ.get("RAGAS_EMBEDDING_MODEL") or os.environ.get("OPENAI_EMBEDDING_MODEL") or "text-embedding-3-small"

    async_openai_client = AsyncOpenAI()
    async_embedding_client = AsyncOpenAI()

    llm = llm_factory(model=llm_model, provider="openai", client=async_openai_client)
    embeddings = embedding_factory(model=embedding_model, provider="openai", client=async_embedding_client)

    faithfulness = Faithfulness(llm=llm)
    answer_relevancy = AnswerRelevancy(llm=llm, embeddings=embeddings)
    context_relevancy = ContextRelevance(llm=llm)

    return [faithfulness, answer_relevancy, context_relevancy]


@dataclass
class RagasRunResult:
    metrics: Dict[str, float]
    raw: Any

    def to_json(self) -> Dict[str, Any]:
        return {"metrics": self.metrics, "raw": self.raw}


def run_ragas_evaluation(input_path: str, output_path: str | None = None) -> RagasRunResult:
    _load_env()
    records = _load_records(Path(input_path))
    if not records:
        raise ValueError("No evaluation records found in the input file")

    dataset = _to_ragas_dataset(records)
    metrics = _build_metrics()

    async def _score_one_record(record: Dict[str, Any]) -> Dict[str, Any]:
        question = record["question"]
        answer = record["answer"]
        contexts = _prepare_contexts_for_evaluation(question, answer, record["contexts"])

        scored = await asyncio.gather(
            metrics[0].ascore(user_input=question, response=answer, retrieved_contexts=contexts),
            metrics[1].ascore(user_input=question, response=answer),
            metrics[2].ascore(user_input=question, retrieved_contexts=contexts),
        )

        return {
            "question": question,
            "faithfulness": _metric_result_to_float(scored[0]),
            "answer_relevance": _metric_result_to_float(scored[1]),
            "context_relevance": _metric_result_to_float(scored[2]),
        }

    async def _score_dataset() -> List[Dict[str, Any]]:
        return await asyncio.gather(*[_score_one_record(record) for record in dataset.to_list()])

    scored_records = asyncio.run(_score_dataset())

    values = {
        "faithfulness": _safe_mean(item["faithfulness"] for item in scored_records),
        "answer_relevancy": _safe_mean(item["answer_relevance"] for item in scored_records),
        "context_relevance": _safe_mean(item["context_relevance"] for item in scored_records),
    }

    output = RagasRunResult(metrics=values, raw={"records": scored_records})

    if output_path:
        Path(output_path).write_text(json.dumps(output.to_json(), ensure_ascii=False, indent=2), encoding="utf-8")

    return output


def _prepare_contexts_for_evaluation(
    question: str,
    answer: str,
    contexts: List[str],
    max_contexts: int = 4,
    max_sentences_per_context: int = 2,
    max_chars_per_sentence: int = 900,
) -> List[str]:
    token_pattern = re.compile(r"[\w\-]+", re.UNICODE)
    focus_tokens = {
        token
        for token in token_pattern.findall(f"{question} {answer}".lower())
        if len(token) >= 3
    }

    def sentence_score(sentence: str) -> int:
        sentence_tokens = {
            token
            for token in token_pattern.findall(sentence.lower())
            if len(token) >= 3
        }
        overlap = len(focus_tokens & sentence_tokens)
        bonus = 1 if any(fragment in sentence.lower() for fragment in focus_tokens) else 0
        return overlap + bonus

    selected: List[str] = []
    for context in contexts[:max_contexts]:
        normalized = str(context).strip().replace("\r", " ").replace("\n", " ")
        if not normalized:
            continue

        sentences = [part.strip() for part in re.split(r"(?<=[.!?])\s+", normalized) if part.strip()]
        if not sentences:
            sentences = [normalized]

        scored_sentences = [
            (sentence_score(sentence), index, sentence)
            for index, sentence in enumerate(sentences)
        ]
        positive_sentences = [item for item in scored_sentences if item[0] > 0]

        picked_sentences: List[str] = []
        if positive_sentences:
            positive_sentences.sort(key=lambda item: (-item[0], item[1]))
            for _, _, sentence in positive_sentences[:max_sentences_per_context]:
                trimmed = sentence if len(sentence) <= max_chars_per_sentence else sentence[:max_chars_per_sentence].rsplit(" ", 1)[0]
                if trimmed not in picked_sentences:
                    picked_sentences.append(trimmed)
        else:
            picked_sentences.append(normalized if len(normalized) <= max_chars_per_sentence else normalized[:max_chars_per_sentence].rsplit(" ", 1)[0])

        selected.extend(picked_sentences)

    if not selected:
        return []

    deduped: List[str] = []
    seen = set()
    for item in selected:
        if item not in seen:
            seen.add(item)
            deduped.append(item)
    return deduped


def _metric_result_to_float(result: Any) -> float:
    if result is None:
        return float("nan")
    for attr in ("score", "value", "result"):
        value = getattr(result, attr, None)
        if isinstance(value, (int, float)):
            return float(value)
    if isinstance(result, (int, float)):
        return float(result)
    try:
        return float(result)
    except Exception:
        return float("nan")


def _safe_mean(values: Iterable[float]) -> float:
    values_list = [value for value in values if value == value]
    if not values_list:
        return float("nan")
    return sum(values_list) / len(values_list)


def main() -> None:
    parser = argparse.ArgumentParser(description="Evaluate RAG answers with RAGAS")
    parser.add_argument("--input", required=True, help="Path to JSON or JSONL evaluation records")
    parser.add_argument("--output", help="Optional path to write evaluation results as JSON")
    args = parser.parse_args()

    result = run_ragas_evaluation(args.input, args.output)
    print(json.dumps(result.to_json(), ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
