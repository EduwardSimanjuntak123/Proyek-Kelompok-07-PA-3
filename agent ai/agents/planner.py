"""
PLANNER - LangGraph Version 2.0

Interface utama untuk PA Agent yang sekarang menggunakan LangGraph.

Perubahan:
- Refactored dari 500+ line giant function menjadi modular node-based architecture
- Menggunakan LangGraph untuk orchestration dan routing  
- Lebih maintainable, testable, dan scalable
- Struktur yang jelas dan mudah dipahami

Dokumentasi:
- core/graph.py: Main graph orchestration
- core/state.py: State definitions
- core/nodes/: Individual node handlers
"""

import json
from typing import Optional, List, Dict, Any

from core.graph import get_agent_graph
from core.state import AgentState as LGAgentState
from memory.memory import (
    get_memory_by_user,
    save_conversation_memory,
    get_last_result_by_user,
    save_grouping_draft,
    get_latest_grouping_draft,
    save_execution_result,
    get_user_memory_context,
    build_memory_aware_prompt
)
from tools.db_tool import get_dosen_by_user_id
from openai import OpenAI
from app.config import OPENAI_API_KEY

# Legacy imports untuk backward compatibility
from agents.parser import (
    parse_grouping_request, extract_detailed_requirements,
    detect_question_vs_command, detect_dosen_query, parse_dosen_query,
    detect_pembimbing_command, parse_pembimbing_command,
    # NEW: Request 1-4 detection functions
    detect_check_groups_exist, detect_pembimbing_status_query, parse_pembimbing_status_query,
    detect_dynamic_course_query, parse_dynamic_course_query
)
from agents.step_builder import build_grouping_steps
from agents.capability_handler import (
    detect_capability_query, generate_capability_response, get_capability_summary
)
from agents.course_query_parser import detect_course_query, parse_course_query
# NEW: Agent identity for consistent personality
from agents.agent_identity import is_identity_question, get_identity_answer
# NEW: Database tools for queries
from tools.kelompok_status_tool import (
    check_any_groups_exist, count_groups, get_groups_summary
)
from tools.pembimbing_tool import (
    check_any_pembimbing_exist, get_groups_without_pembimbing,
    get_pembimbing_for_kelompok_number, get_groups_by_pembimbing_count,
    get_all_pembimbing_assignments, get_pembimbing_groups_summary
)
from tools.course_tool import get_courses_by_semesters

client = OpenAI(api_key=OPENAI_API_KEY)


def plan(
    prompt: str,
    dosen_context: Optional[List[Dict[str, Any]]] = None,
    user_id: Optional[str] = None,
    existing_groups: Optional[List[Dict[str, Any]]] = None
) -> str:
    """
    Main planning function - Entry point untuk PA Agent.
    
    Fungsi ini menerima request user dan menentukan action yang harus dilakukan.
    
    Argument:
        prompt: User input (pertanyaan atau command)
        dosen_context: Context tentang dosen/koordinator yang login
        user_id: ID dari user yang membuat request
        existing_groups: Kelompok yang sudah ada (untuk modify scenarios)
        
    Return:
        str: JSON response dengan hasil planning
        
    Contoh:
        response = plan("buat 5 kelompok", dosen_context=[...], user_id="123")
        result = json.loads(response)
        print(result["type"])  # "dynamic_grouping" atau "greeting" dsb
    """
    
    print(f"\n{'='*60}")
    print(f"[PLANNER v2.0] Mengolah request dari user...")
    print(f"[PLANNER v2.0] Prompt: {prompt[:50]}...")
    print(f"{'='*60}\n")
    
    try:
        # ===== 1. PREPARE INITIAL STATE =====
        # Kumpulkan memory sebelumnya jika ada user_id
        memory_list = None
        prior_context = None
        memory_context = {'last_instruction': '', 'last_result': '', 'context': ''}
        enriched_prompt = prompt
        
        if user_id:
            try:
                memory_list = get_memory_by_user(user_id)
                memory_context = get_user_memory_context(user_id, limit=3)
                
                # Build enriched prompt with memory context
                enriched_prompt = build_memory_aware_prompt(
                    prompt, 
                    user_id=user_id, 
                    system_context="Gunakan konteks percakapan sebelumnya jika relevan untuk memperbaiki pemahaman request."
                )
                print(f"[PLANNER] Memory context loaded for user {user_id}")
                
                if memory_list:
                    prior_context = "\n".join([
                        f"[history] prompt={m.get('prompt')} | feedback={m.get('feedback')}"
                        for m in memory_list[-5:]
                    ])
            except Exception as e:
                print(f"[PLANNER] Warning: Memory loading failed: {e}")
                pass  # Silent fail jika memory fetch gagal
        
        # Build initial state untuk LangGraph
        initial_state: LGAgentState = {
            "prompt": prompt,
            "dosen_context": dosen_context,
            "user_id": user_id,
            "existing_groups": existing_groups,
            "memory_list": memory_list,
            "prior_context": prior_context,
            "memory_context": memory_context,
            "enriched_prompt": enriched_prompt,
        }
        
        print(f"[PLANNER v2.0] Initial state prepared")
        
        # ===== 2a. CHECK FOR COMBINED REQUESTS =====
        # Try to detect if this is a multi-intent request
        try:
            from agents.combined_request_builder import detect_combined_request, build_combined_steps
            
            combined_detection = detect_combined_request(prompt)
            if combined_detection.get('type') == 'combined':
                print(f"[PLANNER v2.0] ✓ Detected COMBINED REQUEST")
                print(f"[PLANNER v2.0]   Intents: {[i.get('action') for i in combined_detection.get('intents', [])]}")
                
                # Build combined steps
                ctx = dosen_context[0] if dosen_context else {}
                combined_result = build_combined_steps(combined_detection, ctx)
                
                # Return combined plan directly
                response = {
                    "type": "combined",
                    "steps": combined_result.get('steps', []),
                    "intents": combined_detection.get('intents', []),
                    "combined_request": True
                }
                
                # Add memory context
                if enriched_prompt and enriched_prompt != prompt:
                    response["_memory_enriched"] = True
                    response["_enriched_prompt"] = enriched_prompt
                if memory_context:
                    response["_memory_context"] = {
                        "last_instruction": memory_context.get("last_instruction", ""),
                        "last_result": memory_context.get("last_result", ""),
                        "has_prior_context": bool(memory_context.get("context"))
                    }
                
                result = json.dumps(response, ensure_ascii=False, indent=2)
                print(f"[PLANNER v2.0] ✓ Combined plan ready dengan {len(response.get('steps', []))} steps")
                print(f"{'='*60}\n")
                return result
        except Exception as e:
            print(f"[PLANNER v2.0] Combined detection warning: {str(e)}")
            pass  # Continue with regular routing if combined detection fails
        
        # ===== 2b. CHECK DYNAMIC COURSE QUERIES (REQUEST #3) - PRIORITIZE MULTI-SEMESTER =====
        # Handle multi-semester course queries (must check before generic course queries)
        try:
            if detect_dynamic_course_query(prompt):
                print(f"[PLANNER v2.0] ✓ Detected DYNAMIC COURSE QUERY")
                
                parsed = parse_dynamic_course_query(prompt, dosen_context[0] if dosen_context else None)
                semesters = parsed.get("semesters", [])
                
                if semesters:
                    print(f"[PLANNER v2.0]   Requested semesters: {semesters}")
                
                ctx = dosen_context[0] if dosen_context else {}
                
                try:
                    courses = get_courses_by_semesters(semesters, context=ctx)
                    
                    # Format response message
                    message = ""
                    for sem, course_list in sorted(courses.items()):
                        message += f"\n📚 **Semester {sem}** ({len(course_list)} matakuliah):\n"
                        for course in course_list:
                            message += f"  - {course.get('kode_mk', 'N/A')}: {course.get('nama_matkul', 'N/A')} ({course.get('sks', 0)} SKS)\n"
                    
                    response = {
                        "type": "dynamic_course_query",
                        "action": "get_courses",
                        "data": {"semesters": semesters, "courses": courses},
                        "response": message if message else "Tidak ada matakuliah ditemukan."
                    }
                except Exception as db_error:
                    print(f"[PLANNER v2.0] Database error in course query: {str(db_error)}")
                    response = {
                        "type": "dynamic_course_query",
                        "action": "get_courses",
                        "error": str(db_error),
                        "response": "Gagal mengambil data matakuliah dari database."
                    }
                
                if enriched_prompt and enriched_prompt != prompt:
                    response["_memory_enriched"] = True
                    response["_enriched_prompt"] = enriched_prompt
                if memory_context:
                    response["_memory_context"] = {
                        "last_instruction": memory_context.get("last_instruction", ""),
                        "last_result": memory_context.get("last_result", ""),
                        "has_prior_context": bool(memory_context.get("context"))
                    }
                
                result = json.dumps(response, ensure_ascii=False, indent=2)
                print(f"[PLANNER v2.0] ✓ Dynamic course query ready")
                print(f"{'='*60}\n")
                return result
        except Exception as e:
            print(f"[PLANNER v2.0] Dynamic course detection warning: {str(e)}")
            pass  # Continue with regular routing if detection fails
        
        # ===== 2c. CHECK FOR COURSE QUERIES (GENERIC/SINGLE-SEMESTER) =====
        # Check if user is asking to display courses/matakuliah (fallback for single semester)
        try:
            if detect_course_query(prompt):
                print(f"[PLANNER v2.0] ✓ Detected COURSE QUERY")
                
                # Build course query plan
                ctx = dosen_context[0] if dosen_context else {}
                course_query = parse_course_query(prompt, ctx)
                
                # Extract semester filter if present
                semester_filter = course_query.get("semester_filter")
                show_rata_rata = course_query.get("show_rata_rata", False)
                
                if semester_filter:
                    print(f"[PLANNER v2.0]   Semester filter: {semester_filter}")
                if show_rata_rata:
                    print(f"[PLANNER v2.0]   Show rata-rata: Yes")
                
                # Return course plan with single step
                response = {
                    "type": "course_query",
                    "steps": [{
                        "action": "get_courses",
                        "params": {
                            "semester_filter": semester_filter,
                            "show_rata_rata": show_rata_rata
                        }
                    }],
                    "query": course_query
                }
                
                # Add memory context
                if enriched_prompt and enriched_prompt != prompt:
                    response["_memory_enriched"] = True
                    response["_enriched_prompt"] = enriched_prompt
                if memory_context:
                    response["_memory_context"] = {
                        "last_instruction": memory_context.get("last_instruction", ""),
                        "last_result": memory_context.get("last_result", ""),
                        "has_prior_context": bool(memory_context.get("context"))
                    }
                
                result = json.dumps(response, ensure_ascii=False, indent=2)
                print(f"[PLANNER v2.0] ✓ Course query plan ready")
                if response.get("_memory_enriched"):
                    print(f"[PLANNER v2.0] Memory context: enriched with history")
                print(f"{'='*60}\n")
                return result
        except Exception as e:
            print(f"[PLANNER v2.0] Course detection warning: {str(e)}")
            pass  # Continue with regular routing if course detection fails
        
        # ===== 2c. CHECK FOR IDENTITY QUESTIONS (REQUEST #2) =====
        # Check if user is asking who I am (consistent personality)
        try:
            if is_identity_question(prompt):
                print(f"[PLANNER v2.0] ✓ Detected IDENTITY QUESTION")
                
                response = {
                    "type": "identity_answer",
                    "action": "respond_identity",
                    "response": get_identity_answer()
                }
                
                if enriched_prompt and enriched_prompt != prompt:
                    response["_memory_enriched"] = True
                    response["_enriched_prompt"] = enriched_prompt
                if memory_context:
                    response["_memory_context"] = {
                        "last_instruction": memory_context.get("last_instruction", ""),
                        "last_result": memory_context.get("last_result", ""),
                        "has_prior_context": bool(memory_context.get("context"))
                    }
                
                result = json.dumps(response, ensure_ascii=False, indent=2)
                print(f"[PLANNER v2.0] ✓ Identity answer ready")
                print(f"{'='*60}\n")
                return result
        except Exception as e:
            print(f"[PLANNER v2.0] Identity detection warning: {str(e)}")
            pass  # Continue with regular routing if identity detection fails
        
        # ===== 2d. CHECK IF GROUPS EXIST (REQUEST #1) =====
        # Check if user is asking about group existence
        try:
            if detect_check_groups_exist(prompt):
                print(f"[PLANNER v2.0] ✓ Detected GROUP EXISTENCE CHECK")
                
                ctx = dosen_context[0] if dosen_context else {}
                
                try:
                    any_exist = check_any_groups_exist(context=ctx)
                    count = count_groups(context=ctx)
                    summary = get_groups_summary(context=ctx)
                    
                    if any_exist and count:
                        message = f"Ya, sudah ada {count} kelompok yang dibuat."
                        if summary:
                            message += f"\nTotal anggota: {summary.get('total_members', 0)} orang\n"
                            message += f"Rata-rata per kelompok: {summary.get('avg_members_per_group', 0):.1f} orang"
                    else:
                        message = "Belum ada kelompok yang dibuat."
                        count = 0
                    
                    response = {
                        "type": "check_groups_exist",
                        "action": "query",
                        "data": {
                            "any_exist": any_exist,
                            "count": count,
                            "summary": summary
                        },
                        "response": message
                    }
                except Exception as db_error:
                    print(f"[PLANNER v2.0] Database error in group check: {str(db_error)}")
                    response = {
                        "type": "check_groups_exist",
                        "action": "query",
                        "error": str(db_error),
                        "response": "Gagal mengecek data kelompok dari database."
                    }
                
                if enriched_prompt and enriched_prompt != prompt:
                    response["_memory_enriched"] = True
                    response["_enriched_prompt"] = enriched_prompt
                if memory_context:
                    response["_memory_context"] = {
                        "last_instruction": memory_context.get("last_instruction", ""),
                        "last_result": memory_context.get("last_result", ""),
                        "has_prior_context": bool(memory_context.get("context"))
                    }
                
                result = json.dumps(response, ensure_ascii=False, indent=2)
                print(f"[PLANNER v2.0] ✓ Group existence check ready")
                print(f"{'='*60}\n")
                return result
        except Exception as e:
            print(f"[PLANNER v2.0] Group check detection warning: {str(e)}")
            pass  # Continue with regular routing if detection fails
        
        # ===== 2e. DYNAMIC COURSE QUERIES - MOVED TO 2b (PRIORITIZED) =====
        # This section is now handled earlier in section 2b to prevent
        # generic course queries from intercepting multi-semester queries
        
        # ===== 2f. CHECK PEMBIMBING STATUS QUERIES (REQUEST #4) =====
        # Handle pembimbing lecturer assignment queries
        try:
            if detect_pembimbing_status_query(prompt):
                print(f"[PLANNER v2.0] ✓ Detected PEMBIMBING STATUS QUERY")
                
                parsed = parse_pembimbing_status_query(prompt, dosen_context[0] if dosen_context else None)
                query_type = parsed.get("query_type", "unknown")
                
                print(f"[PLANNER v2.0]   Query type: {query_type}")
                
                ctx = dosen_context[0] if dosen_context else {}
                message = ""
                data = {}
                
                try:
                    if query_type == "check_any_exist":
                        exists = check_any_pembimbing_exist(context=ctx)
                        message = f"{'Ada' if exists else 'Belum ada'} pembimbing yang ditugaskan."
                        data = {"exists": exists}
                    
                    elif query_type == "groups_without":
                        groups = get_groups_without_pembimbing(context=ctx)
                        if groups:
                            message = f"Ada {len(groups)} kelompok yang belum memiliki pembimbing:\n"
                            for grp in groups:
                                message += f"  - {grp.get('nomor_kelompok', 'N/A')}\n"
                        else:
                            message = "✓ Semua kelompok sudah memiliki pembimbing!"
                        data = {"groups": groups, "count": len(groups)}
                    
                    elif query_type == "get_for_group":
                        group_num = parsed.get("group_number")
                        if group_num:
                            result_data = get_pembimbing_for_kelompok_number(int(group_num), ctx)
                            if result_data and result_data.get("has_pembimbing"):
                                names = (result_data.get("pembimbing_names", "") or "").split(",")
                                message = f"Kelompok {result_data.get('kelompok_nomor')} memiliki {result_data.get('pembimbing_count')} pembimbing:\n"
                                for name in names:
                                    if name.strip():
                                        message += f"  - {name.strip()}\n"
                                data = result_data
                            else:
                                message = f"Kelompok {group_num} belum memiliki pembimbing atau tidak ditemukan."
                                data = result_data or {"has_pembimbing": False}
                    
                    elif query_type == "groups_with_one":
                        groups = get_groups_by_pembimbing_count(1, context=ctx)
                        if groups:
                            message = f"Kelompok yang memiliki 1 pembimbing ({len(groups)} kelompok):\n"
                            for grp in groups:
                                message += f"  - {grp.get('nomor_kelompok', 'N/A')}\n"
                        else:
                            message = "Tidak ada kelompok dengan 1 pembimbing."
                        data = {"groups": groups, "count": len(groups)}
                    
                    elif query_type == "groups_with_two":
                        groups = get_groups_by_pembimbing_count(2, context=ctx)
                        if groups:
                            message = f"Kelompok yang memiliki 2 pembimbing ({len(groups)} kelompok):\n"
                            for grp in groups:
                                message += f"  - {grp.get('nomor_kelompok', 'N/A')}\n"
                        else:
                            message = "Tidak ada kelompok dengan 2 pembimbing."
                        data = {"groups": groups, "count": len(groups)}
                    
                    elif query_type == "list_all":
                        assignments = get_all_pembimbing_assignments(context=ctx)
                        if assignments:
                            message = f"Daftar semua pembimbing ({len(assignments)} assignments):\n"
                            for asn in assignments:
                                message += f"  - {asn.get('kelompok_nomor', 'N/A')}: {asn.get('dosen_nama', 'N/A')} ({asn.get('jabatan_akademik_desc', 'N/A')})\n"
                        else:
                            message = "Belum ada pembimbing yang ditugaskan."
                        data = {"assignments": assignments, "count": len(assignments)}
                    
                    else:  # summary
                        summary = get_pembimbing_groups_summary(context=ctx)
                        if summary:
                            message = (
                                f"📊 **Ringkasan Pembimbing**:\n"
                                f"  - Total kelompok: {summary.get('total_groups', 0)}\n"
                                f"  - Kelompok dengan pembimbing: {summary.get('groups_with_pembimbing', 0)}\n"
                                f"  - Kelompok tanpa pembimbing: {summary.get('groups_without_pembimbing', 0)}\n"
                                f"  - Coverage: {summary.get('coverage_percentage', 0)}%"
                            )
                        else:
                            message = "Tidak ada data pembimbing."
                        data = summary or {}
                    
                    response = {
                        "type": "pembimbing_status_query",
                        "action": "query_pembimbing",
                        "query_type": query_type,
                        "data": data,
                        "response": message
                    }
                except Exception as db_error:
                    print(f"[PLANNER v2.0] Database error in pembimbing query: {str(db_error)}")
                    response = {
                        "type": "pembimbing_status_query",
                        "action": "query_pembimbing",
                        "query_type": query_type,
                        "error": str(db_error),
                        "response": "Gagal mengambil data pembimbing dari database."
                    }
                
                if enriched_prompt and enriched_prompt != prompt:
                    response["_memory_enriched"] = True
                    response["_enriched_prompt"] = enriched_prompt
                if memory_context:
                    response["_memory_context"] = {
                        "last_instruction": memory_context.get("last_instruction", ""),
                        "last_result": memory_context.get("last_result", ""),
                        "has_prior_context": bool(memory_context.get("context"))
                    }
                
                result = json.dumps(response, ensure_ascii=False, indent=2)
                print(f"[PLANNER v2.0] ✓ Pembimbing status query ready")
                print(f"{'='*60}\n")
                return result
        except Exception as e:
            print(f"[PLANNER v2.0] Pembimbing status detection warning: {str(e)}")
            pass  # Continue with regular routing if detection fails
        
        # ===== 2. GET COMPILED GRAPH =====
        # Ambil compiled LangGraph (singleton pattern - efficient)
        graph = get_agent_graph()
        
        print(f"[PLANNER v2.0] Graph loaded, invoking workflow...")
        
        # ===== 3. INVOKE GRAPH WORKFLOW =====
        # Jalankan workflow LangGraph dari initial_state
        # Graph akan:
        # 1. Router detects jenis request
        # 2. Route ke handler node yang sesuai
        # 3. Handler process dan return response
        # 4. State dengan response dikembalikan
        
        final_state = graph.invoke(initial_state)
        
        print(f"[PLANNER v2.0] Workflow completed")
        
        # ===== 4. EXTRACT & RETURN RESPONSE =====
        response = final_state.get("response", {})
        
        # Ensure response is proper dict
        if not isinstance(response, dict):
            response = {"type": "error", "message": "Invalid response format"}
        
        # ===== 5. ADD MEMORY CONTEXT TO RESPONSE =====
        # Include enriched prompt and memory context if available (for debugging/tracking)
        if enriched_prompt and enriched_prompt != prompt:
            response["_memory_enriched"] = True
            response["_enriched_prompt"] = enriched_prompt
        
        if memory_context:
            response["_memory_context"] = {
                "last_instruction": memory_context.get("last_instruction", ""),
                "last_result": memory_context.get("last_result", ""),
                "has_prior_context": bool(memory_context.get("context"))
            }
        
        # Convert to JSON string
        result = json.dumps(response, ensure_ascii=False, indent=2)
        
        print(f"[PLANNER v2.0] ✓ Success!")
        print(f"[PLANNER v2.0] Response type: {response.get('type', 'unknown')}")
        if response.get("_memory_enriched"):
            print(f"[PLANNER v2.0] Memory context: enriched with {len(memory_context.get('context', ''))} chars of history")
        print(f"{'='*60}\n")
        
        return result
        
    except Exception as e:
        # Error handling - return error response
        print(f"[PLANNER v2.0] ✗ ERROR: {str(e)}")
        import traceback
        traceback.print_exc()
        print(f"{'='*60}\n")
        
        error_response = {
            "type": "error",
            "message": str(e),
            "error_type": type(e).__name__
        }
        
        return json.dumps(error_response, ensure_ascii=False)


def clean_json(text: str) -> str:
    """
    Bersihkan output dari markdown code blocks.
    
    Helper untuk parsing response yang mungkin dibungkus dalam ```json ...```
    """
    
    if "```" in text:
        text = text.replace("```json", "").replace("```", "")
    
    return text.strip()


def plan_with_debug(
    prompt: str,
    dosen_context: Optional[List[Dict[str, Any]]] = None,
    user_id: Optional[str] = None,
    existing_groups: Optional[List[Dict[str, Any]]] = None,
    verbose: bool = True
) -> Dict[str, Any]:
    """
    Versi debugging dari plan() dengan output lebih detail.
    
    Return dict jika verbose=True, JSON string jika verbose=False.
    Berguna untuk development dan troubleshooting.
    """
    
    result_str = plan(prompt, dosen_context, user_id, existing_groups)
    result_dict = json.loads(result_str)
    
    if verbose:
        print(f"\n[DEBUG] Response structure:")
        print(f"  - Type: {result_dict.get('type')}")
        print(f"  - Keys: {list(result_dict.keys())}")
        
        if "error" in result_dict:
            print(f"  - Error: {result_dict['error']}")
        elif "message" in result_dict:
            print(f"  - Message: {result_dict['message'][:100]}...")
    
    return result_dict


if __name__ == "__main__":
    """
    Test script untuk development.
    
    Jalankan:
        python agents/planner.py
    """
    
    print("[TEST] Testing LangGraph-based Planner v2.0...\n")
    
    # Test case 1: Greeting
    print("Test 1: Greeting")
    result = plan("halo", dosen_context=[])
    data = json.loads(result)
    print(f"  Response type: {data.get('type')}\n")
    
    # Test case 2: Capability query  
    print("Test 2: Capability Query")
    result = plan("apa kemampuan anda?", dosen_context=[])
    data = json.loads(result)
    print(f"  Response type: {data.get('type')}\n")
    
    # Test case 3: List mahasiswa
    print("Test 3: List Mahasiswa")
    result = plan("tampilkan daftar mahasiswa", dosen_context=[])
    data = json.loads(result)
    print(f"  Response type: {data.get('type')}\n")
    
    print("All tests completed!")
