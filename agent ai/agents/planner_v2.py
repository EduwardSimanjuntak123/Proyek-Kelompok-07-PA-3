#!/usr/bin/env python3
"""
Planner - Smart routing to query generator or quick answer module
"""

from agents.dynamic_query_generator import process_instruction
from agents.quick_answer import generate_quick_answer
from app.config import OPENAI_API_KEY, LLM_MODEL, LLM_TEMPERATURE_QA, LLM_MAX_TOKENS_QA
from openai import OpenAI
import re


def _is_question(prompt: str) -> bool:
    """Simple question detection"""
    
    markers = [
        r'^\\s*(apa|berapa|bagaimana|siapa|apakah|ada)',
        r'\\s+\\?\\s*$'
    ]
    
    return any(re.search(m, prompt.lower()) for m in markers)


def _is_greeting(prompt: str) -> bool:
    """Detect greeting prompts"""
    
    greetings = [
        'halo', 'haloo', 'haii', 'hai', 'hello', 'hi',
        'assalamualaikum', 'assalamu', 'pagi', 'siang', 'sore', 'malam',
        'siapa kamu', 'siapakah kamu', 'kamu siapa', 'apa itu', 'apa tujuanmu',
        'apa fungsi', 'help', 'bantuan'
    ]
    
    prompt_lower = prompt.lower().strip()
    
    # Check if prompt is exactly or starts with greeting
    for greeting in greetings:
        if prompt_lower == greeting or prompt_lower.startswith(greeting):
            return True
    
    return False


def _generate_greeting_response(user_id: int = None, context_data: dict = None) -> str:
    """Generate AI agent introduction response"""
    
    prodi = ""
    if context_data and context_data.get('prodi'):
        prodi = f" ({context_data['prodi']})"
    
    response = (
        f"Halo! 👋 Saya adalah AI Agent yang siap membantu Anda dalam manajemen Proyek Akhir{prodi}.\n\n"
        "Saya dapat membantu dengan:\n"
        "• Membuat kelompok proyek akhir secara otomatis\n"
        "• Menampilkan data dosen dan mahasiswa\n"
        "• Menghitung jumlah mahasiswa\n"
        "• Memodifikasi kelompok yang sudah ada\n"
        "• Menjawab pertanyaan terkait manajemen PA\n\n"
        "Silakan berikan instruksi atau pertanyaan Anda! 😊"
    )
    
    return response


def plan_v2(prompt: str, user_id: int = None, context_data: dict = None) -> dict:
    """Main planning function"""
    
    # Check for greeting first
    if _is_greeting(prompt):
        return {
            "type": "greeting_response",
            "answer": _generate_greeting_response(user_id=user_id, context_data=context_data),
            "is_direct": True
        }
    
    if _is_question(prompt):
        # Try quick answer first
        quick_answer = generate_quick_answer(prompt, user_id=user_id)
        
        if quick_answer:
            return {
                "type": "question_response",
                "answer": quick_answer,
                "is_direct": True
            }
        
        # LLM fallback
        try:
            client = OpenAI(api_key=OPENAI_API_KEY)
            response = client.chat.completions.create(
                model=LLM_MODEL,
                messages=[
                    {"role": "system", "content": "Jawab singkat dan langsung."},
                    {"role": "user", "content": prompt}
                ],
                temperature=LLM_TEMPERATURE_QA,
                max_tokens=LLM_MAX_TOKENS_QA
            )
            return {
                "type": "question_response",
                "answer": response.choices[0].message.content,
                "is_direct": False
            }
        except:
            return {
                "type": "question_response",
                "answer": "Maaf, tidak bisa memproses pertanyaan.",
                "is_direct": False
            }
    
    # Process as command
    else:
        instruction = prompt
        if user_id:
            instruction += f" [User: {user_id}]"
        if context_data and context_data.get('prodi_id'):
            instruction += f" [Prodi: {context_data['prodi_id']}]"
        
        try:
            # Pass context_data to query generator
            result = process_instruction(instruction, context_data=context_data)
            return {
                "type": "command_response",
                "data": result,
                "is_query_result": True
            }
        except:
            return {
                "type": "command_response",
                "data": "Maaf, ada error memproses perintah.",
                "is_query_result": False
            }




def plan(prompt, dosen_context, user_id=None, existing_groups=None):
    """Backward compatibility wrapper - route to correct handler"""
    
    # Extract user_id
    if not user_id and isinstance(dosen_context, (list, tuple)) and len(dosen_context) > 0:
        ctx = dosen_context[0]
        user_id = ctx.user_id if hasattr(ctx, 'user_id') else ctx.get('user_id')
    
    # Extract context data
    context_data = None
    if isinstance(dosen_context, (list, tuple)) and len(dosen_context) > 0:
        ctx = dosen_context[0]
        context_data = ctx.dict() if hasattr(ctx, 'dict') else ctx
    
    # Check if this is a CREATE/MODIFY grouping request (old handler)
    create_keywords = ['buat kelompok', 'generate kelompok', 'acak', 'random', 'ubah kelompok', 'karir', 'pembimbing']
    is_create_grouping = any(kw in prompt.lower() for kw in create_keywords)
    
    # IMPROVEMENT: Check for COMBINED requests (grouping + other intents)
    from agents.combined_request_builder import detect_combined_request, build_combined_steps
    
    combined_detection = detect_combined_request(prompt)
    
    if combined_detection['type'] in ['combined', 'single'] and combined_detection['intents']:
        # Handle combined or detected intentional requests
        ctx_for_steps = dosen_context
        if isinstance(dosen_context, list) and len(dosen_context) > 0:
            ctx_for_steps = dosen_context[0]
        
        combined_result = build_combined_steps(combined_detection, ctx_for_steps)
        steps_list = combined_result.get("steps", [])
        
        if steps_list:
            return {
                "type": "combined" if combined_detection['type'] == 'combined' else "dynamic_grouping",
                "steps": steps_list,
                "intents": combined_detection.get("intents", []),
                "metadata": {
                    "combined": combined_detection['type'] == 'combined'
                }
            }
    
    # Original grouping parsing (for compatibility)
    if is_create_grouping:
        try:
            from agents.parser import parse_grouping_request  
            from agents.step_builder import build_grouping_steps
            
            parsed = parse_grouping_request(prompt)
            if parsed.get("action"):
                # Convert dosen_context to dict if needed for step_builder
                ctx_for_steps = dosen_context
                if isinstance(dosen_context, list) and len(dosen_context) > 0:
                    ctx_for_steps = dosen_context[0]
                
                steps_dict = build_grouping_steps(parsed, ctx_for_steps)
                # Extract actual steps list from the dict returned by build_grouping_steps
                steps_list = steps_dict.get("steps", [])
                metadata = steps_dict.get("metadata", {})
                
                return {
                    "type": "dynamic_grouping",
                    "parsed": parsed,
                    "steps": steps_list,  # Use the actual steps list
                    "metadata": metadata
                }
        except Exception as e:
            print(f"[PLANNER] Error parsing grouping request: {e}")
            import traceback
            traceback.print_exc()
            pass
    
    # For VIEW/LIST queries (daftar, lihat, ambil kelompok) - use dynamic query
    view_keywords = ['daftar', 'lihat', 'ambil', 'tampilkan', 'view', 'list']
    query_keywords = ['kelompok', 'mahasiswa', 'dosen', 'nilai', 'prodi']
    is_view_query = any(vk in prompt.lower() for vk in view_keywords) and any(qk in prompt.lower() for qk in query_keywords)
    
    if is_view_query:
        result = plan_v2(prompt, user_id=user_id, context_data=context_data)
        return {
            "type": result.get('type', 'command_response'),
            "message": result.get('data') or result.get('answer'),
            "is_query_result": result.get('is_query_result', True)
        }
    
    # Default: use new dynamic flow
    result = plan_v2(prompt, user_id=user_id, context_data=context_data)
    
    import json
    if result['type'] == 'question_response':
        return {
            "type": "natural_response",
            "message": result['answer']
        }
    else:
        return {
            "type": "command_response",
            "message": result['data'],
            "is_query_result": result.get('is_query_result', True)
        }
