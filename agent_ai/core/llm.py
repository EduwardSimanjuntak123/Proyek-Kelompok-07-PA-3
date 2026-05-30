from functools import lru_cache
import os

from dotenv import load_dotenv
from openai import OpenAI

load_dotenv()


@lru_cache(maxsize=1)
def get_client():
    api_key = os.getenv("OPENAI_API_KEY")
    if not api_key:
        raise RuntimeError(
            "OPENAI_API_KEY is not set. Set it before calling LLM-backed features."
        )
    return OpenAI(api_key=api_key)

def call_llm(messages, system_prompt=None, context=None):
    if isinstance(messages, str):
        messages = [{"role": "user", "content": messages}]

    final_messages = []

    if system_prompt:
        final_messages.append({
            "role": "system",
            "content": system_prompt
        })

    final_messages.extend(messages)

    response = get_client().chat.completions.create(
        model="gpt-5-mini",
        messages=final_messages
    )

    return response.choices[0].message.content