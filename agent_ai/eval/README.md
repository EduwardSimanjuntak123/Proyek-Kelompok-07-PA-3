# RAGAS Evaluation

This folder contains a small RAGAS evaluation harness for measuring:

- faithfulness
- answer relevancy
- context relevancy

## Install

From `agent_ai/`:

```powershell
pip install ragas datasets langchain-community
```

## Run

Prepare a JSON file with records like:

```json
[
  {
    "question": "Apa tujuan proyek akhir?",
    "answer": "Tujuan proyek akhir adalah ...",
    "contexts": ["...retrieved passage 1...", "...retrieved passage 2..."],
    "ground_truth": "...optional reference answer..."
  }
]
```

Then run:

```powershell
python -m eval.ragas_eval --input data/rag_eval.json --output ragas_results.json
```

Notes:
- The harness includes a small compatibility shim for `ragas 0.4.3` and the installed `langchain-community` package in this workspace.
- If you want real scores, the active environment still needs a working LLM/embeddings backend accepted by RAGAS for your chosen metrics.
