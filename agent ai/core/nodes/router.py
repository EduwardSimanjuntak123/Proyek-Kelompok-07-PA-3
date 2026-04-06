"""
Router Node - Mendeteksi dan merutekan request user ke node yang sesuai.

Node ini menganalisis prompt user dan menentukan jenis request:
- greeting: User hanya menyapa
- capability: User bertanya tentang kemampuan agent
- pembimbing: Terkait pembimbing/mentor kelompok
- dosen: Terkait informasi dosen
- view_groups: User ingin melihat kelompok
- list_mahasiswa: User ingin melihat daftar mahasiswa
- count_mahasiswa: User ingin menghitung mahasiswa
- view_scores: User ingin melihat nilai mahasiswa
- grouping: User ingin membuat/modify kelompok
- save_groups: User ingin menyimpan kelompok
- delete_groups: User ingin menghapus kelompok
- question: User bertanya (tidak terkait langsung dengan PA)
"""

from core.state import AgentState
from agents.parser import (
    detect_pembimbing_command,
    detect_dosen_query,
    detect_greeting_keywords,
    detect_view_existing_groups,
    detect_list_mahasiswa_request,
    detect_count_mahasiswa_request,
    detect_view_scores_request,
    detect_score_based_grouping,
    detect_save_groups_request,
    detect_delete_groups_request,
    detect_question_vs_command,
    extract_group_number
)
from agents.capability_handler import detect_capability_query
from agents.combined_request_builder import detect_combined_request
from agents.advanced_parser import (
    detect_student_query,
    detect_student_group_query,
    detect_student_score_query,
    detect_unscheduled_student_query,
    detect_dosen_detail_query,
    detect_group_pembimbing_status_query,
    detect_student_matkul_query,
    detect_student_manipulation,
    detect_pembimbing_manipulation,
    detect_multiple_instructions
)


def node_router(state: AgentState) -> AgentState:
    """
    Node Router - Mendeteksi jenis request dan merutekan ke handler yang tepat.
    
    Urutan prioritas detection:
    1. Capability query (questions about features)
    2. Pembimbing commands (lecturer assignment - most specific)
    3. Dosen queries (lecturer info)
    4. Greeting (simple hello)
    5. View groups (show existing groups)
    6. Mahasiswa requests (list/count students)
    7. Scores requests (view student scores)
    8. Save/Delete groups (confirm/cancel grouping)
    9. Grouping commands (create/modify groups)
    10. Question vs Command detection
    
    Args:
        state: Current agent state
        
    Returns:
        state: Updated state dengan detected_type dan next_action
    """
    
    # Use enriched prompt if available (with memory context), fallback to original
    prompt = (state.get("enriched_prompt", "") or state.get("prompt", "")).lower().strip()
    detected_type = None
    next_action = None
    
    print(f"[ROUTER] Menganalisis prompt: {prompt[:50]}...")
    
    # === PRIORITY 1: Capability Query ===
    if detect_capability_query(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: CAPABILITY QUERY")
        detected_type = "capability"
        next_action = "node_capability"
        
    # === PRIORITY 2: Pembimbing Command (MOST SPECIFIC) ===
    elif detect_pembimbing_command(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: PEMBIMBING COMMAND")
        detected_type = "pembimbing"
        next_action = "node_pembimbing"
        
    # === PRIORITY 3: Dosen Query ===
    elif detect_dosen_query(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: DOSEN QUERY")
        detected_type = "dosen"
        next_action = "node_dosen"
        
    # === PRIORITY 4: Greeting ===
    elif detect_greeting_keywords(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: GREETING")
        detected_type = "greeting"
        next_action = "node_greeting"
        
    # === PRIORITY 5: View Existing Groups ===
    elif detect_view_existing_groups(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: VIEW GROUPS")
        detected_type = "view_groups"
        next_action = "node_view_groups"
        
        # Ekstrak group number jika ada (untuk view specific group)
        group_num = extract_group_number(state.get("prompt", ""))
        if group_num:
            state["group_number"] = group_num
        
    # === PRIORITY 6: Count Mahasiswa (CHECK BEFORE LIST - more specific) ===
    elif detect_count_mahasiswa_request(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: COUNT MAHASISWA")
        detected_type = "count_mahasiswa"
        next_action = "node_mahasiswa"
        
    # === PRIORITY 7: List Mahasiswa ===
    elif detect_list_mahasiswa_request(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: LIST MAHASISWA")
        detected_type = "list_mahasiswa"
        next_action = "node_mahasiswa"
        
    # === PRIORITY 8: View Scores ===
    elif detect_view_scores_request(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: VIEW SCORES")
        detected_type = "view_scores"
        next_action = "node_scores"
        
    # === PRIORITY 8.5: Combined Requests (COMPOUND ACTIONS) ===
    # Check BEFORE individual save/delete to catch "buat kelompok dan simpan" patterns
    elif detect_combined_request(state.get("prompt", "")).get("type") == "combined":
        combined = detect_combined_request(state.get("prompt", ""))
        actions = combined.get("actions", [])
        
        # If grouping is one of the actions, make that the primary action
        if "create_group" in actions:
            other_actions = [a for a in actions if a != "create_group"]
            print(f"[ROUTER] ✓ Terdeteksi: COMBINED REQUEST - Grouping + {other_actions}")
            detected_type = "grouping"
            next_action = "node_grouping"
            # Store combined request for grouping node to use smart resolver
            state["combined_request"] = combined
        else:
            # Combined request without grouping - handle normally
            pass
    
    # === PRIORITY 9: Save Groups ===
    elif detect_save_groups_request(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: SAVE GROUPS")
        detected_type = "save_groups"
        next_action = "node_save_delete"
        
    # === PRIORITY 10: Delete Groups ===
    elif detect_delete_groups_request(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: DELETE GROUPS")
        detected_type = "delete_groups"
        next_action = "node_save_delete"
    
    # === PRIORITY 11-20: ADVANCED QUERIES (NEW) ===
    # Check specific manipulations BEFORE multiple instructions (more specific patterns first)
    
    # Student manipulation (roker/tukar/ubah/hapus)
    elif detect_student_manipulation(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: STUDENT MANIPULATION")
        detected_type = "student_manipulation"
        next_action = "node_student_manipulation"
    
    # Pembimbing manipulation (roker/update/hapus)
    elif detect_pembimbing_manipulation(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: PEMBIMBING MANIPULATION")
        detected_type = "pembimbing_manipulation"
        next_action = "node_pembimbing_manipulation"
    
    # Check for multiple instructions (after specific manipulations)
    elif detect_multiple_instructions(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: MULTIPLE INSTRUCTIONS")
        detected_type = "multiple_instructions"
        next_action = "node_multiple_instructions"
    
    # Student queries
    elif detect_student_group_query(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: STUDENT GROUP QUERY")
        detected_type = "student_group_query"
        next_action = "node_student_group_query"
    
    elif detect_unscheduled_student_query(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: UNSCHEDULED STUDENT QUERY")
        detected_type = "unscheduled_student_query"
        next_action = "node_unscheduled_student_query"
    
    elif detect_student_query(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: STUDENT QUERY")
        detected_type = "student_query"
        next_action = "node_student_query"
    
    # Score queries
    elif detect_student_score_query(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: STUDENT SCORE QUERY")
        detected_type = "student_score_query"
        next_action = "node_student_score_query"
    
    # Matkul queries
    elif detect_student_matkul_query(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: STUDENT MATKUL QUERY")
        detected_type = "student_matkul_query"
        next_action = "node_student_matkul_query"
    
    # Dosen queries
    elif detect_dosen_detail_query(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: DOSEN DETAIL QUERY")
        detected_type = "dosen_detail_query"
        next_action = "node_dosen_detail_query"
    
    # Kelompok pembimbing status
    elif detect_group_pembimbing_status_query(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: GROUP PEMBIMBING STATUS")
        detected_type = "group_pembimbing_status_query"
        next_action = "node_group_pembimbing_status_query"
    
    # === PRIORITY 10.5: Score-Based Grouping (BEFORE generic grouping) ===
    elif detect_score_based_grouping(state.get("prompt", "")):
        print(f"[ROUTER] ✓ Terdeteksi: SCORE-BASED GROUPING")
        detected_type = "score_based_grouping"
        next_action = "node_score_based_grouping"
    
    # === DEFAULT: Fallback ke Question vs Command ===
    else:
        print(f"[ROUTER] Menggunakan LLM parser untuk deteksi...")
        query_type = detect_question_vs_command(state.get("prompt", ""))
        
        if query_type == "question":
            print(f"[ROUTER] ✓ Terdeteksi: QUESTION (general)")
            detected_type = "question"
            next_action = "node_question"
        else:
            print(f"[ROUTER] ✓ Terdeteksi: GROUPING COMMAND")
            detected_type = "grouping"
            next_action = "node_grouping"
    
    # Update state dengan hasil detection
    state["detected_type"] = detected_type
    state["next_action"] = next_action
    
    print(f"[ROUTER] Routing ke: {next_action}")
    
    return state
