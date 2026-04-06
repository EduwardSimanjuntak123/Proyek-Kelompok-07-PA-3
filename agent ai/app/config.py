import os
from dotenv import load_dotenv

load_dotenv()

OPENAI_API_KEY = os.getenv("OPENAI_API_KEY")
DATABASE_URL = os.getenv("DATABASE_URL")

# LLM Configuration
LLM_MODEL = os.getenv("LLM_MODEL", "gpt-4o-mini")
LLM_TEMPERATURE_QA = float(os.getenv("LLM_TEMPERATURE_QA", "0.7"))
LLM_MAX_TOKENS_QA = int(os.getenv("LLM_MAX_TOKENS_QA", "1000"))