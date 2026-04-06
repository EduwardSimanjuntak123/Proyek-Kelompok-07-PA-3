import os
from dotenv import load_dotenv

load_dotenv()

# API Configuration
OPENAI_API_KEY = os.getenv("OPENAI_API_KEY")

# Database Configuration
DATABASE_URL = os.getenv("DATABASE_URL")

# LLM Model Configuration
LLM_MODEL = "gpt-4.1-mini"
LLM_TEMPERATURE = 0

# Grouping Configuration
DEFAULT_GROUP_SIZE = 6
DEFAULT_ALLOW_DEVIATION = 0.5

# Memory Configuration
MEMORY_RETENTION_DAYS = 90
SHORT_TERM_MEMORY_SIZE = 20

# Keywords
MODIFY_KEYWORDS = ["acak", "ubah", "jangan", "harus", "kembali", "ulangi", "ganti", "tukar"]
SHUFFLE_KEYWORDS = ["acak", "random", "shuffle", "kocok"]
SCORE_KEYWORDS = ["nilai", "skor", "prestasi", "semester", "IPK", "nilai matkul"]
