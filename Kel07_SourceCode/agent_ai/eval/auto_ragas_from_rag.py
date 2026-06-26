"""Build a RAGAS-ready dataset from the current RAG retriever output.

This runner takes an input file with questions and reference answers, queries the
local retriever for top-k contexts, then writes a RAGAS evaluation dataset.

Example input:
[
  {"question": "Apa tujuan proyek akhir?", "ground_truth": "...", "answer": "...optional manual answer..."}
]

Usage:
  python -m eval.auto_ragas_from_rag --input data/rag_eval.json --output data/rag_eval_prepared.json
  python -m eval.ragas_eval --input data/rag_eval_prepared.json --output ragas_results.json
"""

from __future__ import annotations

import argparse
import json
import re
import sys
from pathlib import Path
from typing import Any, Dict, List


def _load_records(path: Path) -> List[Dict[str, Any]]:
    text = path.read_text(encoding="utf-8").strip()
    if not text:
        return []
    data = json.loads(text)
    if isinstance(data, dict) and "records" in data:
        data = data["records"]
    if not isinstance(data, list):
        raise ValueError("Input must be a JSON list or an object containing a 'records' list")
    return data


def _chunk_context_text(text: str, max_chars: int = 400) -> List[str]:
    normalized = str(text).strip().replace("\r", " ").replace("\n", " ")
    if not normalized:
        return []

    sentences = [part.strip() for part in re.split(r"(?<=[.!?])\s+", normalized) if part.strip()]
    if not sentences:
        sentences = [normalized]

    chunks: List[str] = []
    current = ""
    for sentence in sentences:
        candidate = f"{current} {sentence}".strip() if current else sentence
        if len(candidate) <= max_chars:
            current = candidate
            continue

        if current:
            chunks.append(current)
            current = ""

        if len(sentence) <= max_chars:
            current = sentence
        else:
            while len(sentence) > max_chars:
                split_at = sentence[:max_chars].rsplit(" ", 1)[0]
                if not split_at:
                    split_at = sentence[:max_chars]
                chunks.append(split_at)
                sentence = sentence[len(split_at):].strip()
            current = sentence

    if current:
        chunks.append(current)

    deduped: List[str] = []
    seen = set()
    for chunk in chunks:
        if chunk and chunk not in seen:
            seen.add(chunk)
            deduped.append(chunk)
    return deduped


def _merge_context_sources(source_contexts: List[str], retriever_contexts: List[str]) -> List[str]:
    merged: List[str] = []
    seen = set()

    for context in source_contexts + retriever_contexts:
        for chunk in _chunk_context_text(context):
            if chunk not in seen:
                seen.add(chunk)
                merged.append(chunk)

    return merged


def _get_retriever():
    # Make sure the agent_ai package root is importable when running this file directly.
    script_dir = Path(__file__).resolve().parents[1]
    script_dir_str = str(script_dir)
    if script_dir_str not in sys.path:
        sys.path.insert(0, script_dir_str)

    from RAG import retriever

    return retriever


def build_prepared_dataset(input_path: str, output_path: str, top_k: int = 3) -> List[Dict[str, Any]]:
    retriever = _get_retriever()
    source_records = _load_records(Path(input_path))
    prepared: List[Dict[str, Any]] = []

    for record in source_records:
        question = str(record.get("question", "")).strip()
        if not question:
            continue

        hits = retriever.query(question, top_k=top_k)
        source_contexts = record.get("contexts") or []
        retrieved_contexts = [hit.get("chunk", "") for hit in hits if hit.get("chunk")]
        contexts = _merge_context_sources(source_contexts, retrieved_contexts)

        prepared.append(
            {
                "question": question,
                "answer": str(record.get("answer", "")).strip(),
                "contexts": contexts,
                "ground_truth": str(record.get("ground_truth", record.get("reference_answer", ""))).strip(),
                "source_question": record.get("question"),
            }
        )

    Path(output_path).write_text(json.dumps(prepared, ensure_ascii=False, indent=2), encoding="utf-8")
    return prepared


def main() -> None:
    parser = argparse.ArgumentParser(description="Prepare a RAGAS dataset from the current RAG retriever")
    parser.add_argument("--input", required=True, help="Input JSON with question/answer/ground_truth records")
    parser.add_argument("--output", required=True, help="Output JSON path for RAGAS-ready records")
    parser.add_argument("--top-k", type=int, default=3, help="How many contexts to retrieve per question")
    args = parser.parse_args()

    prepared = build_prepared_dataset(args.input, args.output, top_k=args.top_k)
    print(json.dumps({"records": len(prepared), "output": args.output}, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
