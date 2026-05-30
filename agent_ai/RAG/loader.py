import os
import json
from typing import List


def _read_pdf_text(pdf_path: str) -> str:
    """Attempt to extract text from a PDF using PyPDF2 if available.
    If PyPDF2 is not installed, raise an informative ImportError.
    """
    try:
        from PyPDF2 import PdfReader
    except Exception as e:
        raise ImportError(
            "PyPDF2 is required to extract PDF text. Install with: pip install PyPDF2"
        ) from e

    text_parts: List[str] = []
    reader = PdfReader(pdf_path)
    for page in reader.pages:
        try:
            text_parts.append(page.extract_text() or "")
        except Exception:
            # best-effort, continue
            continue
    return "\n".join(text_parts)


def chunk_text(text: str, chunk_size: int = 1000, overlap: int = 100) -> List[str]:
    """Split text into overlapping chunks of approx chunk_size characters."""
    if not text:
        return []
    chunks: List[str] = []
    start = 0
    length = len(text)
    while start < length:
        end = min(start + chunk_size, length)
        chunks.append(text[start:end])
        start = end - overlap if end < length else end
    return chunks


def extract_chunks_from_pdf(pdf_path: str, chunk_size: int = 1000, overlap: int = 100) -> List[dict]:
    """Return list of dicts: {"source": filename, "chunk": text, "id": idx}"""
    text = _read_pdf_text(pdf_path)
    chunks = chunk_text(text, chunk_size=chunk_size, overlap=overlap)
    results = []
    for i, c in enumerate(chunks):
        results.append({"id": f"{os.path.basename(pdf_path)}::{i}", "source": os.path.basename(pdf_path), "chunk": c})
    return results
