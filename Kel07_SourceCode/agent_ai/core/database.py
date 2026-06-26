"""
Database configuration dan connection setup
"""
import os
from urllib.parse import quote_plus
from sqlalchemy.exc import OperationalError, SQLAlchemyError
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker, Session
from sqlalchemy.ext.declarative import declarative_base
from dotenv import load_dotenv

load_dotenv()

def _first_env(*keys: str, default: str = "") -> str:
    for key in keys:
        value = os.getenv(key)
        if value not in (None, ""):
            return value
    return default


def _build_database_url() -> str:
    database_url = os.getenv("DATABASE_URL")
    if database_url:
        return database_url

    db_connection = _first_env("DB_CONNECTION", default="mysql").lower()
    host = _first_env("DB_HOST", default="127.0.0.1")
    port = _first_env("DB_PORT", default="3306")
    database = _first_env("DB_DATABASE", "DB_NAME", default="vokasitera_BDv2")
    username = _first_env("DB_USERNAME", "DB_USER", default="root")
    password = _first_env("DB_PASSWORD", default="")

    if db_connection == "sqlite":
        return _first_env("DATABASE_PATH", default="sqlite:///./test.db")

    if password:
        auth_part = f"{quote_plus(username)}:{quote_plus(password)}"
    else:
        auth_part = quote_plus(username)

    if db_connection in {"mysql", "mariadb"}:
        return f"mysql+pymysql://{auth_part}@{host}:{port}/{database}?charset=utf8mb4"

    return f"mysql+pymysql://{auth_part}@{host}:{port}/{database}?charset=utf8mb4"


# Get database URL from .env or Laravel-compatible DB_* settings
DATABASE_URL = _build_database_url()


def is_database_connection_error(error: object) -> bool:
    """Detect koneksi database putus atau credential/host error."""
    text = str(error).lower()
    keywords = [
        "connection refused",
        "can't connect to mysql server",
        "couldn't connect to server",
        "lost connection to mysql server",
        "server has gone away",
        "operationalerror",
        "database is locked",
        "unable to open database file",
        "access denied for user",
        "unknown database",
        "no route to host",
        "connection timed out",
    ]
    return any(keyword in text for keyword in keywords)

# Create engine
engine = create_engine(
    DATABASE_URL,
    pool_pre_ping=True,  # Verify connection before using
    pool_recycle=1800,
    connect_args={"check_same_thread": False} if "sqlite" in DATABASE_URL else {}
)

# Create session factory
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

# Base class untuk models
Base = declarative_base()


def get_db() -> Session:
    """Dependency untuk get database session"""
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()


def test_connection():
    """Test database connection"""
    try:
        with engine.connect() as conn:
            return True
    except (OperationalError, SQLAlchemyError) as e:
        print(f"Database connection error: {e}")
        return False
    except Exception as e:
        print(f"Database connection error: {e}")
        return False
