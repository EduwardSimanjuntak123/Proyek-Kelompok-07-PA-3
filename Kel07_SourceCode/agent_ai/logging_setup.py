import logging
import sys
from typing import Iterable


class Cp1252SafeFilter(logging.Filter):
    @staticmethod
    def _safe_text(value):
        if not isinstance(value, str):
            return value
        try:
            return value.encode("cp1252", errors="replace").decode("cp1252")
        except Exception:
            return value

    def filter(self, record):
        record.msg = self._safe_text(record.msg)
        if isinstance(record.args, tuple):
            record.args = tuple(self._safe_text(arg) for arg in record.args)
        elif isinstance(record.args, dict):
            record.args = {k: self._safe_text(v) for k, v in record.args.items()}
        return True


def configure_concise_logging(level: int = logging.INFO, noisy_loggers: Iterable[str] = None) -> None:
    """Configure concise logging for console and file.

    - Sets root logger to `level` (default INFO)
    - Adds a human-friendly formatter and file logger
    - Lowers verbosity for known noisy libraries (pymongo, asyncio, etc.)
    """
    if noisy_loggers is None:
        noisy_loggers = [
            "pymongo",
            "pymongo.topology",
            "pymongo.serverSelection",
            "pymongo.connection",
            "asyncio",
            "urllib3",
            "botocore",
            "aiobotocore",
            "uvicorn",
            "uvicorn.error",
            "uvicorn.access",
            "fastapi",
            "asyncio",
            "aiosqlite",
        ]

    root = logging.getLogger()
    root.setLevel(level)

    # Console handler
    ch = logging.StreamHandler(sys.stdout)
    ch.setLevel(level)
    formatter = logging.Formatter("%(asctime)s [%(name)s] %(levelname)s: %(message)s")
    ch.setFormatter(formatter)
    ch.addFilter(Cp1252SafeFilter())

    # File handler (rotating could be added later)
    fh = logging.FileHandler("agent_api.log")
    fh.setLevel(logging.DEBUG)
    fh.setFormatter(formatter)
    fh.addFilter(Cp1252SafeFilter())

    # Remove existing handlers to avoid duplicate logs
    for h in list(root.handlers):
        root.removeHandler(h)

    root.addHandler(ch)
    root.addHandler(fh)

    # Lower verbosity for noisy libraries
    for name in noisy_loggers:
        try:
            logging.getLogger(name).setLevel(logging.WARNING)
        except Exception:
            pass

    # Keep application modules visible
    logging.getLogger("agent_ai").setLevel(level)
