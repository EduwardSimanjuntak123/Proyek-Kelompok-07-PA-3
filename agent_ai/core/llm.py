from openai import OpenAI
import os
from dotenv import load_dotenv

load_dotenv()

client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))

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

    response = client.chat.completions.create(
        model="gpt-5-mini",
        messages=final_messages
    )

    return response.choices[0].message.content