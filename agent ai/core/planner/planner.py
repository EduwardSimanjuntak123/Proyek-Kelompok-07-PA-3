"""Main planner yang mendekomposisi natural language user ke structured plan"""
from openai import OpenAI
from config.settings import OPENAI_API_KEY, LLM_MODEL, LLM_TEMPERATURE
from memory.memory import get_memory_by_user, get_last_result_by_user
from typing import List, Dict, Any, Optional

from .keyword_detector import detect_modify_keywords
from .schema_builder import build_existing_groups_context, build_prior_memory_context, build_system_prompt

client = OpenAI(api_key=OPENAI_API_KEY)


def clean_json(text: str) -> str:
    """
    Bersihkan output AI dari markdown formatting
    
    Args:
        text: Response dari LLM yang mungkin wrapped dengan ```json
        
    Returns:
        Clean JSON string
    """
    if "```" in text:
        text = text.replace("```json", "").replace("```", "")
    return text.strip()


def plan(
    prompt: str,
    dosen_context: List[Dict[str, Any]],
    user_id: Optional[int] = None,
    existing_groups: Optional[List[Dict[str, Any]]] = None
) -> str:
    """
    Konversi natural language prompt ke structured execution plan
    
    Args:
        prompt: user instruction dalam bahasa natural
        dosen_context: context tentang dosen/koordinator
        user_id: for memory retrieval
        existing_groups: hasil grouping sebelumnya jika ada
        
    Returns:
        JSON string representation of execution plan
    """
    
    # Build context strings
    prior_memory_text = ""
    if user_id is not None:
        memory_list = get_memory_by_user(user_id)
        if memory_list:
            prior_memory_text = build_prior_memory_context(memory_list)
    
    existing_groups_context = build_existing_groups_context(existing_groups)
    
    # Build system prompt
    system_prompt = build_system_prompt(
        dosen_context=dosen_context,
        existing_groups_context=existing_groups_context,
        prior_memory_text=prior_memory_text
    )
    
    try:
        response = client.chat.completions.create(
            model=LLM_MODEL,
            messages=[
                {"role": "system", "content": system_prompt},
                {"role": "user", "content": prompt}
            ],
            temperature=LLM_TEMPERATURE
        )

        result = response.choices[0].message.content
        return clean_json(result)

    except Exception as e:
        return f'{{"error": "{str(e)}"}}'
