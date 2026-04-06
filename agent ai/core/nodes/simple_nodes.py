"""
Simple Nodes untuk greeting dan capability queries.

Nodes ini menangani request yang sederhana dan tidak butuh data external.
"""

import json
from core.state import AgentState
from agents.capability_handler import generate_capability_response
from memory.memory import save_conversation_memory
from tools.db_tool import get_dosen_by_user_id


def node_greeting(state: AgentState) -> AgentState:
    """
    Node Greeting - Merespons greeting user dengan sapaan yang personal.
    
    Mengambil informasi dosen dari database dan merespon dengan ramah.
    
    Args:
        state: Current agent state
        
    Returns:
        state: Updated state dengan response greeting
    """
    
    print(f"[GREETING NODE] Memproses greeting...")
    
    # Ambil informasi dasar
    dosen_context = state.get("dosen_context")
    coordinator_name = "Koordinator PA"
    kategori_pa = 1
    
    # Extract context jika ada
    if dosen_context and len(dosen_context) > 0:
        ctx = dosen_context[0]
        
        # Ambil kategori_pa
        if isinstance(ctx, dict):
            kategori_pa = ctx.get("kategori_pa", 1)
        else:
            kategori_pa = getattr(ctx, "kategori_pa", 1)
        
        # Coba ambil info dosen dari database
        user_id = None
        if isinstance(ctx, dict):
            user_id = ctx.get("user_id")
        else:
            user_id = getattr(ctx, "user_id", None)
        
        if user_id:
            try:
                dosen_info = get_dosen_by_user_id(user_id)
                if dosen_info:
                    coordinator_name = dosen_info.get("nama", "Koordinator PA")
            except:
                pass  # Gunakan default jika error
    
    # Buat response greeting
    greeting_message = f"Halo juga {coordinator_name} sebagai Koordinator PA {kategori_pa}, saya adalah agent Anda. Apakah ada yang ingin saya kerjakan hari ini?"
    
    state["response"] = {
        "type": "greeting",
        "message": greeting_message
    }
    
    print(f"[GREETING NODE] ✓ Respons: {greeting_message[:50]}...")
    
    return state


def node_capability(state: AgentState) -> AgentState:
    """
    Node Capability - Menjawab pertanyaan tentang kemampuan agent.
    
    Memberikan informasi lengkap tentang fitur dan kemampuan yang tersedia.
    
    Args:
        state: Current agent state
        
    Returns:
        state: Updated state dengan response capability
    """
    
    print(f"[CAPABILITY NODE] Memproses capability query...")
    
    prompt = state.get("prompt", "")
    user_id = state.get("user_id")
    
    # Generate capability response menggunakan LLM
    capability_response = generate_capability_response(prompt)
    
    state["response"] = {
        "type": "capability_query",
        "response": capability_response.get("response", ""),
        "capabilities": capability_response.get("capabilities_shown", [])
    }
    
    # Simpan ke memory jika ada user_id
    if user_id:
        try:
            save_conversation_memory(
                user_id=user_id,
                prompt=prompt,
                response=capability_response.get("response", ""),
                query_type="capability_query",
                status="success"
            )
        except:
            pass  # Silent fail jika memory save gagal
    
    print(f"[CAPABILITY NODE] ✓ Menjawab capability query")
    
    return state


def node_question(state: AgentState) -> AgentState:
    """
    Node Question - Menjawab pertanyaan umum yang tidak spesifik PA.
    
    Menggunakan LLM untuk generate natural language response.
    
    Args:
        state: Current agent state
        
    Returns:
        state: Updated state dengan response
    """
    
    print(f"[QUESTION NODE] Memproses general question...")
    
    from agents.responder import respond_to_unrecognized_query
    
    prompt = state.get("prompt", "")
    
    # Generate natural language response
    llm_response = respond_to_unrecognized_query(prompt)
    
    state["response"] = {
        "type": "natural_response",
        "message": llm_response
    }
    
    print(f"[QUESTION NODE] ✓ Menjawab question: {llm_response[:50]}...")
    
    return state
