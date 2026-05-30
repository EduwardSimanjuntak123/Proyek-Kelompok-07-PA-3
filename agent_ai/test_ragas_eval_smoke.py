#!/usr/bin/env python3
"""Offline smoke test for the RAGAS preparation and evaluation flow.

This test avoids external API calls by stubbing the retriever and metrics.
Run from the `agent_ai/` directory:

  python test_ragas_eval_smoke.py
"""

from __future__ import annotations

import json
import tempfile
from dataclasses import dataclass
from pathlib import Path

from eval import auto_ragas_from_rag, ragas_eval


@dataclass
class _FakeScore:
    score: float


class _FakeMetric:
    def __init__(self, score: float):
        self._score = score

    async def ascore(self, **kwargs):
        return _FakeScore(self._score)


class _FakeRetriever:
    def query(self, question: str, top_k: int = 3):
        return [
            {"chunk": f"{question} :: konteks 1"},
            {"chunk": f"{question} :: konteks 2"},
        ][:top_k]


def test_build_prepared_dataset() -> None:
    with tempfile.TemporaryDirectory() as temp_dir:
        temp_dir_path = Path(temp_dir)
        input_path = temp_dir_path / "rag_eval.json"
        output_path = temp_dir_path / "rag_eval_prepared.json"

        input_path.write_text(
            json.dumps(
                [
                    {
                        "question": "Apa bentuk dokumen akhir kelengkapan seminar PA?",
                        "answer": "Dokumen akhir berupa laporan dan berkas seminar.",
                        "ground_truth": "Dokumen akhir berupa laporan dan lampiran seminar.",
                    }
                ],
                ensure_ascii=False,
            ),
            encoding="utf-8",
        )

        original_get_retriever = auto_ragas_from_rag._get_retriever
        try:
            auto_ragas_from_rag._get_retriever = lambda: _FakeRetriever()
            prepared = auto_ragas_from_rag.build_prepared_dataset(str(input_path), str(output_path), top_k=2)
        finally:
            auto_ragas_from_rag._get_retriever = original_get_retriever

        assert len(prepared) == 1
        assert prepared[0]["question"] == "Apa bentuk dokumen akhir kelengkapan seminar PA?"
        assert prepared[0]["source_question"] == "Apa bentuk dokumen akhir kelengkapan seminar PA?"
        assert prepared[0]["contexts"] == [
            "Apa bentuk dokumen akhir kelengkapan seminar PA? :: konteks 1",
            "Apa bentuk dokumen akhir kelengkapan seminar PA? :: konteks 2",
        ]
        assert output_path.exists()


def test_run_ragas_evaluation_with_stub_metrics() -> None:
    with tempfile.TemporaryDirectory() as temp_dir:
        temp_dir_path = Path(temp_dir)
        input_path = temp_dir_path / "rag_eval_prepared.json"
        output_path = temp_dir_path / "ragas_results.json"

        input_path.write_text(
            json.dumps(
                [
                    {
                        "question": "Apa bentuk dokumen akhir kelengkapan seminar PA?",
                        "answer": "Dokumen akhir berupa laporan dan berkas seminar.",
                        "contexts": [
                            "Dokumen akhir seminar berisi laporan akhir",
                            "Berkas yang wajib disiapkan untuk seminar",
                        ],
                        "ground_truth": "Dokumen akhir berupa laporan dan lampiran seminar.",
                    }
                ],
                ensure_ascii=False,
            ),
            encoding="utf-8",
        )

        original_load_env = ragas_eval._load_env
        original_build_metrics = ragas_eval._build_metrics
        try:
            ragas_eval._load_env = lambda: None
            ragas_eval._build_metrics = lambda: [_FakeMetric(0.8), _FakeMetric(0.6), _FakeMetric(1.0)]
            result = ragas_eval.run_ragas_evaluation(str(input_path), str(output_path))
        finally:
            ragas_eval._load_env = original_load_env
            ragas_eval._build_metrics = original_build_metrics

        assert output_path.exists()
        assert abs(result.metrics["faithfulness"] - 0.8) < 1e-9
        assert abs(result.metrics["answer_relevancy"] - 0.6) < 1e-9
        assert abs(result.metrics["context_relevance"] - 1.0) < 1e-9
        assert result.raw["records"][0]["question"] == "Apa bentuk dokumen akhir kelengkapan seminar PA?"


def test_context_selection_prefers_relevant_sentences() -> None:
    contexts = [
        "Kalimat pembuka yang tidak relevan. Dokumen akhir kelengkapan seminar PA mencakup ToR, PRS, PP, PD, PI, PT, dan PR yang telah direvisi. Kalimat penutup lain.",
        "Informasi umum timeline proyek. Seminar PA menuntut dokumen pengembangan produk yang direvisi sebelum pengumpulan final.",
    ]

    selected = ragas_eval._prepare_contexts_for_evaluation(
        "Apa bentuk dokumen akhir kelengkapan seminar PA?",
        "Dokumen akhir kelengkapan seminar PA mencakup ToR, PRS, PP, PD, PI, PT, dan PR yang telah direvisi.",
        contexts,
    )

    assert any("ToR, PRS, PP, PD, PI, PT, dan PR" in item for item in selected)


if __name__ == "__main__":
    test_build_prepared_dataset()
    test_run_ragas_evaluation_with_stub_metrics()
    test_context_selection_prefers_relevant_sentences()
    print("OK: RAGAS smoke tests passed")
