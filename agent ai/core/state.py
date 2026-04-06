"""
State definition untuk LangGraph workflow.
Mengelola semua state yang diperlukan selama eksekusi agent.
"""

from typing import TypedDict, Optional, List, Dict, Any
from dataclasses import field


class AgentState(TypedDict, total=False):
    """
    State dictionary untuk LangGraph di seluruh workflow.
    
    Attributes:
        prompt: Input user (pertanyaan/command)
        dosen_context: Context tentang dosen/koordinator PA
        user_id: ID dari user yang request
        existing_groups: Kelompok yang sudah ada (jika ada)
        
        # Hasil detection/routing
        detected_type: Jenis request yang terdeteksi (greeting, capability, pembimbing, dosen, grouping, etc)
        
        # Data intermediate
        parsed_request: Hasil parsing dari prompt
        mahasiswa_data: Data mahasiswa yang diambil
        scores_data: Data nilai/score mahasiswa
        dosen_info: Informasi dosen
        
        # Execution results
        response: Respons akhir untuk user
        error: Error message jika ada
        
        # Metadata
        memory_list: Memory user sebelumnya
        prior_context: Context dari request sebelumnya
    """
    
    # ========== INPUT SECTION ==========
    prompt: str
    dosen_context: Optional[List[Dict[str, Any]]]
    user_id: Optional[str]
    existing_groups: Optional[List[Dict[str, Any]]]
    
    # ========== DETECTION/ROUTING SECTION ==========
    detected_type: Optional[str]  # "greeting", "capability", "pembimbing", "dosen", "view_groups", "grouping", etc
    
    # ========== DATA SECTION ==========
    parsed_request: Optional[Dict[str, Any]]
    mahasiswa_data: Optional[List[Dict[str, Any]]]
    scores_data: Optional[List[Dict[str, Any]]]
    dosen_info: Optional[Dict[str, Any]]
    group_number: Optional[int]  # Untuk view specific group
    
    # ========== EXECUTION SECTION ==========
    response: Optional[Dict[str, Any]]
    error: Optional[str]
    steps: Optional[List[Dict[str, Any]]]  # Step-by-step actions
    
    # ========== METADATA SECTION ==========
    memory_list: Optional[List[Dict[str, Any]]]
    prior_context: Optional[str]
    memory_context: Optional[Dict[str, str]]  # Full memory context with last_instruction, last_result, context
    enriched_prompt: Optional[str]  # Prompt enriched with memory context
    
    # ========== ROUTING DECISION ==========
    next_action: Optional[str]  # Menunjuk ke node mana selanjutnya


# Type hints untuk convenience
QueryType = str  # greeting, capability, pembimbing, dosen, grouping, etc
RequestAction = str  # create_group, modify_group, view_group, get_mahasiswa, etc
