"""
Advanced Query Handler Nodes

Nodes untuk:
1. Student information queries
2. Dosen information queries
3. Kelompok status queries
4. Student score queries
5. Matkul queries
6. Student/mahasiswa manipulations
7. Pembimbing manipulations
"""

from core.state import AgentState
from agents.advanced_parser import (
    parse_student_query,
    parse_student_group_query,
    parse_student_score_query,
    parse_unscheduled_student_query,
    parse_dosen_detail_query,
    parse_group_pembimbing_status_query,
    parse_student_matkul_query,
    parse_student_manipulation,
    parse_pembimbing_manipulation,
    parse_multiple_instructions
)


def node_student_query(state: AgentState) -> AgentState:
    """Handle student information queries (by nama atau nim)"""
    
    print("[STUDENT QUERY NODE] Processing student information request...")
    
    # Use enriched prompt if available (with memory context), fallback to original
    prompt_to_parse = state.get("enriched_prompt", "") or state.get("prompt", "")
    parsed = parse_student_query(prompt_to_parse)
    
    response = {
        "type": "student_query_result",
        "message": f"Mencari mahasiswa berdasarkan {parsed.get('search_type')}: {parsed.get('query_value')}",
        "action": "query_student",
        "strategy": parsed.get("search_type", "unknown"),
        "search_query": parsed.get("query_value"),
        "steps": [
            {
                "action": "search_student",
                "params": {
                    "search_type": parsed.get("search_type"),
                    "query_value": parsed.get("query_value")
                }
            }
        ],
        "metadata": {
            "description": f"Cari mahasiswa berdasarkan {parsed.get('search_type')}"
        }
    }
    
    state["response"] = response
    print(f"[STUDENT QUERY NODE] ✓ Student query ready")
    return state


def node_student_group_query(state: AgentState) -> AgentState:
    """Handle: mahasiswa X kelompok berapa"""
    
    print("[STUDENT GROUP QUERY NODE] Processing student group query...")
    
    # Use enriched prompt if available (with memory context), fallback to original
    prompt_to_parse = state.get("enriched_prompt", "") or state.get("prompt", "")
    parsed = parse_student_group_query(prompt_to_parse)
    
    response = {
        "type": "student_group_query_result",
        "message": f"Mencari kelompok untuk mahasiswa {parsed.get('student_value')}",
        "action": "query_student_group",
        "student_identifier": parsed.get("student_value"),
        "student_type": parsed.get("student_type"),
        "steps": [
            {
                "action": "find_student_group",
                "params": {
                    "student_type": parsed.get("student_type"),
                    "student_value": parsed.get("student_value")
                }
            }
        ],
        "metadata": {
            "description": f"Temukan kelompok untuk mahasiswa {parsed.get('student_value')}"
        }
    }
    
    state["response"] = response
    print(f"[STUDENT GROUP QUERY NODE] ✓ Ready to find student's group")
    return state


def node_unscheduled_student_query(state: AgentState) -> AgentState:
    """Handle: mahasiswa belum punya kelompok siapa"""
    
    print("[UNSCHEDULED STUDENT NODE] Processing unscheduled student query...")
    
    response = {
        "type": "unscheduled_student_query_result",
        "message": "Mencari mahasiswa yang belum punya kelompok",
        "action": "query_unscheduled_students",
        "steps": [
            {
                "action": "get_unscheduled_students",
                "params": {}
            }
        ],
        "metadata": {
            "description": "Cari mahasiswa yang belum punya kelompok"
        }
    }
    
    state["response"] = response
    print(f"[UNSCHEDULED STUDENT NODE] ✓ Ready to find unscheduled students")
    return state


def node_student_score_query(state: AgentState) -> AgentState:
    """Handle: nilai mahasiswa queries"""
    
    print("[STUDENT SCORE NODE] Processing student score query...")
    
    # Use enriched prompt if available (with memory context), fallback to original
    prompt_to_parse = state.get("enriched_prompt", "") or state.get("prompt", "")
    parsed = parse_student_score_query(prompt_to_parse)
    
    response = {
        "type": "student_score_query_result",
        "message": "Mengambil nilai mahasiswa",
        "action": "query_student_scores",
        "steps": [
            {
                "action": "get_student_scores",
                "params": {
                    "prompt": state.get("prompt")
                }
            }
        ],
        "metadata": {
            "description": "Query nilai mahasiswa"
        }
    }
    
    state["response"] = response
    print(f"[STUDENT SCORE NODE] ✓ Score query ready")
    return state


def node_dosen_detail_query(state: AgentState) -> AgentState:
    """Handle: dosen detail queries (jabatan, mengajar, dll)"""
    
    print("[DOSEN DETAIL NODE] Processing dosen detail query...")
    
    # Use enriched prompt if available (with memory context), fallback to original
    prompt_to_parse = state.get("enriched_prompt", "") or state.get("prompt", "")
    parsed = parse_dosen_detail_query(prompt_to_parse)
    
    response = {
        "type": "dosen_detail_query_result",
        "message": f"Mencari informasi dosen {parsed.get('dosen_name')}",
        "action": "query_dosen_detail",
        "query_type": parsed.get("query_type"),
        "dosen_name": parsed.get("dosen_name"),
        "steps": [
            {
                "action": "get_dosen_info",
                "params": {
                    "dosen_name": parsed.get("dosen_name"),
                    "query_type": parsed.get("query_type")
                }
            }
        ],
        "metadata": {
            "description": f"Cari informasi dosen {parsed.get('dosen_name')}"
        }
    }
    
    state["response"] = response
    print(f"[DOSEN DETAIL NODE] ✓ Dosen detail query ready")
    return state


def node_group_pembimbing_status_query(state: AgentState) -> AgentState:
    """Handle: kelompok pembimbing status queries"""
    
    print("[GROUP PEMBIMBING STATUS NODE] Processing group pembimbing status...")
    
    # Use enriched prompt if available (with memory context), fallback to original
    prompt_to_parse = state.get("enriched_prompt", "") or state.get("prompt", "")
    parsed = parse_group_pembimbing_status_query(prompt_to_parse)
    
    response = {
        "type": "group_pembimbing_status_result",
        "message": f"Query status pembimbing kelompok {parsed.get('group_number')}",
        "action": "query_group_pembimbing",
        "group_number": parsed.get("group_number"),
        "query_type": parsed.get("query_type"),
        "steps": [
            {
                "action": "get_group_pembimbing_status",
                "params": {
                    "group_number": parsed.get("group_number"),
                    "query_type": parsed.get("query_type")
                }
            }
        ],
        "metadata": {
            "description": f"Query status pembimbing kelompok {parsed.get('group_number')}"
        }
    }
    
    state["response"] = response
    print(f"[GROUP PEMBIMBING STATUS NODE] ✓ Pembimbing status query ready")
    return state


def node_student_matkul_query(state: AgentState) -> AgentState:
    """Handle: matkul queries - both specific student and general course queries"""
    
    print("[STUDENT MATKUL NODE] Processing student matkul query...")
    
    # Use enriched prompt if available (with memory context), fallback to original
    prompt_to_parse = state.get("enriched_prompt", "") or state.get("prompt", "")
    
    # Check if this is a dynamic (multi-semester) course query
    from agents.parser import detect_dynamic_course_query, parse_dynamic_course_query
    
    if detect_dynamic_course_query(prompt_to_parse):
        # General course query for multiple semesters
        parsed = parse_dynamic_course_query(prompt_to_parse)
        semesters = parsed.get("semesters", [])
        
        response = {
            "type": "student_matkul_query_result",
            "message": f"Daftar matakuliah semester {', '.join(map(str, semesters))}",
            "action": "query_student_matkul",
            "semesters": semesters,
            "is_general_query": True,
            "steps": [
                {
                    "action": "get_student_matkul",
                    "params": {
                        "semesters": semesters
                    }
                }
            ],
            "metadata": {
                "description": f"Daftar matakuliah untuk semester {', '.join(map(str, semesters))}"
            }
        }
    else:
        # Specific student matkul query
        parsed = parse_student_matkul_query(prompt_to_parse)
        
        response = {
            "type": "student_matkul_query_result",
            "message": f"Query matkul mahasiswa {parsed.get('student_name')} semester {parsed.get('semester')}",
            "action": "query_student_matkul",
            "student_name": parsed.get("student_name"),
            "semester": parsed.get("semester"),
            "is_general_query": False,
            "steps": [
                {
                    "action": "get_student_matkul",
                    "params": {
                        "student_name": parsed.get("student_name"),
                        "semester": parsed.get("semester")
                    }
                }
            ],
            "metadata": {
                "description": f"Query matkul mahasiswa {parsed.get('student_name')} semester {parsed.get('semester')}"
            }
        }
    
    state["response"] = response
    print(f"[STUDENT MATKUL NODE] ✓ Matkul query ready")
    return state


def node_student_manipulation(state: AgentState) -> AgentState:
    """Handle: manipulate mahasiswa (roker, tukar, ubah, hapus)"""
    
    print("[STUDENT MANIPULATION NODE] Processing student manipulation...")
    
    # Use enriched prompt if available (with memory context), fallback to original
    prompt_to_parse = state.get("enriched_prompt", "") or state.get("prompt", "")
    parsed = parse_student_manipulation(prompt_to_parse)
    
    response = {
        "type": "student_manipulation_result",
        "message": f"{parsed.get('operation')} mahasiswa {', '.join(parsed.get('students', []))}",
        "action": f"manipulate_students_{parsed.get('operation')}",
        "operation": parsed.get("operation"),
        "students": parsed.get("students"),
        "groups": parsed.get("groups"),
        "steps": [
            {
                "action": f"execute_{parsed.get('operation')}",
                "params": {
                    "operation": parsed.get("operation"),
                    "students": parsed.get("students"),
                    "groups": parsed.get("groups")
                }
            }
        ],
        "metadata": {
            "description": f"{parsed.get('operation')} mahasiswa {', '.join(parsed.get('students', []))} kelompok {', '.join(parsed.get('groups', []))}"
        }
    }
    
    state["response"] = response
    print(f"[STUDENT MANIPULATION NODE] ✓ Student manipulation ready")
    return state


def node_pembimbing_manipulation(state: AgentState) -> AgentState:
    """Handle: manipulate pembimbing (roker, hapus, update)"""
    
    print("[PEMBIMBING MANIPULATION NODE] Processing pembimbing manipulation...")
    
    # Use enriched prompt if available (with memory context), fallback to original
    prompt_to_parse = state.get("enriched_prompt", "") or state.get("prompt", "")
    parsed = parse_pembimbing_manipulation(prompt_to_parse)
    
    response = {
        "type": "pembimbing_manipulation_result",
        "message": f"{parsed.get('operation')} pembimbing kelompok {parsed.get('group_number')}",
        "action": f"manipulate_pembimbing_{parsed.get('operation')}",
        "operation": parsed.get("operation"),
        "group_number": parsed.get("group_number"),
        "new_dosen": parsed.get("new_dosen"),
        "steps": [
            {
                "action": f"execute_{parsed.get('operation')}",
                "params": {
                    "operation": parsed.get("operation"),
                    "group_number": parsed.get("group_number"),
                    "new_dosen": parsed.get("new_dosen")
                }
            }
        ],
        "metadata": {
            "description": f"{parsed.get('operation')} pembimbing kelompok {parsed.get('group_number')}"
        }
    }
    
    state["response"] = response
    print(f"[PEMBIMBING MANIPULATION NODE] ✓ Pembimbing manipulation ready")
    return state


def node_multiple_instructions(state: AgentState) -> AgentState:
    """Handle: multiple instructions in one request"""
    
    print("[MULTIPLE INSTRUCTIONS NODE] Processing multiple instructions...")
    
    # Use enriched prompt if available (with memory context), fallback to original
    prompt_to_parse = state.get("enriched_prompt", "") or state.get("prompt", "")
    parsed = parse_multiple_instructions(prompt_to_parse)
    
    response = {
        "type": "multiple_instructions_result",
        "action": "execute_multiple_instructions",
        "instruction_count": parsed.get("count"),
        "instructions": parsed.get("instructions"),
        "steps": [
            {
                "action": "parse_and_execute_sequentially",
                "params": {
                    "instructions": parsed.get("instructions")
                }
            }
        ],
        "metadata": {
            "description": f"Eksekusi {parsed.get('count')} instruksi berurutan"
        }
    }
    
    state["response"] = response
    print(f"[MULTIPLE INSTRUCTIONS NODE] ✓ Multiple instructions ready")
    return state
