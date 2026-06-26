"""Lightweight retriever: index documents (PDFs) into a simple JSON
and perform keyword-based retrieval as a fallback when embeddings are
not available.
"""
import os
import json
from typing import List
from collections import Counter

from . import loader

INDEX_FILE = os.path.join(os.path.dirname(__file__), "index.json")


def _save_index(index: List[dict]):
    with open(INDEX_FILE, "w", encoding="utf-8") as f:
        json.dump(index, f, ensure_ascii=False, indent=2)


def _load_index() -> List[dict]:
    if not os.path.exists(INDEX_FILE):
        return []
    with open(INDEX_FILE, "r", encoding="utf-8") as f:
        return json.load(f)


def index_documents(rag_dir: str = None):
    """Scan agent_ai/RAG folder for PDFs, chunk, and save index."""
    if rag_dir is None:
        rag_dir = os.path.dirname(__file__)
    index: List[dict] = []
    for fname in os.listdir(rag_dir):
        path = os.path.join(rag_dir, fname)
        if os.path.isfile(path) and fname.lower().endswith(".pdf"):
            try:
                chunks = loader.extract_chunks_from_pdf(path)
            except ImportError as e:
                # re-raise to let caller know dependencies missing
                raise
            except Exception:
                # skip problematic files but continue
                continue
            index.extend(chunks)
    _save_index(index)
    return index


def _score_chunk_by_query(chunk_text: str, query: str) -> int:
    # Simple token overlap scoring (case-insensitive)
    q_tokens = [t for t in query.lower().split() if t]
    if not q_tokens:
        return 0
    ct = Counter(chunk_text.lower().split())
    score = sum(ct.get(tok, 0) for tok in q_tokens)
    return score


def query(query_text: str, top_k: int = 3) -> List[dict]:
    """Return top_k matching chunks ordered by simple keyword score.

    Each hit is a dict with keys: id, source, chunk, score
    """
    index = _load_index()
    if not index:
        # Try to auto-index available documents
        try:
            index = index_documents()
        except ImportError:
            return [{"id": None, "source": None, "chunk": "RAG unavailable: install PyPDF2 to enable PDF ingestion.", "score": 0}]

    scored = []
    for entry in index:
        try:
            score = _score_chunk_by_query(entry.get("chunk", ""), query_text)
        except Exception:
            score = 0
        if score > 0:
            scored.append({**entry, "score": score})

    # If no scored chunks found, also fallback to substring search
    if not scored:
        for entry in index:
            if query_text.lower() in entry.get("chunk", "").lower():
                scored.append({**entry, "score": 1})

    scored.sort(key=lambda x: x.get("score", 0), reverse=True)
    return scored[:top_k]
