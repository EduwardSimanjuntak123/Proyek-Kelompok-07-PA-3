"""Simple RAG package for agent_ai.

Provides a lightweight document ingestion + keyword retriever as a safe
default for environments without heavy vector/embedding dependencies.

Usage:
  from RAG import retriever
  retriever.index_documents()  # scan agent_ai/RAG for PDFs and build index
  hits = retriever.query("apa isi pedoman tentang ...")
"""

from . import retriever

__all__ = ["retriever"]
