"""Keyword detection untuk instruksi modifikasi dan fitur special"""
from config.settings import MODIFY_KEYWORDS, SHUFFLE_KEYWORDS, SCORE_KEYWORDS


def detect_modify_keywords(prompt: str) -> bool:
    """
    Detect jika user ingin modify existing groups
    
    Args:
        prompt: user instruction/prompt
        
    Returns:
        True jika ada indikasi modify, False jika fresh grouping
    """
    prompt_lower = prompt.lower()
    return any(keyword in prompt_lower for keyword in MODIFY_KEYWORDS)


def detect_shuffle_intent(prompt: str) -> bool:
    """Detect jika user minta shuffle/acak"""
    prompt_lower = prompt.lower()
    return any(keyword in prompt_lower for keyword in SHUFFLE_KEYWORDS)


def detect_score_based_intent(prompt: str) -> bool:
    """Detect jika user minta grouping berdasarkan nilai/skor"""
    prompt_lower = prompt.lower()
    return any(keyword in prompt_lower for keyword in SCORE_KEYWORDS)
