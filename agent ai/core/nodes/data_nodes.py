"""
Nodes untuk data retrieval (view groups, mahasiswa, scores).

Menangani request yang membutuhkan fetch data dari database/tools.
"""

import json
from core.state import AgentState


def node_view_groups(state: AgentState) -> AgentState:
    """
    Node View Groups - Menampilkan kelompok yang sudah ada.
    
    Bisa menampilkan:
    - Semua kelompok (general view)
    - Anggota kelompok spesifik (berdasarkan nomor)
    
    Args:
        state: Current agent state
        
    Returns:
        state: Updated state dengan view groups response
    """
    
    print(f"[VIEW GROUPS NODE] Memproses view groups request...")
    
    prompt = state.get("prompt", "")
    dosen_context = state.get("dosen_context")
    group_number = state.get("group_number")
    user_id = state.get("user_id")
    
    # Extract context
    context = None
    if dosen_context and len(dosen_context) > 0:
        context = dosen_context[0]
    
    if group_number is not None:
        # User wants specific group
        print(f"[VIEW GROUPS NODE] Menampilkan kelompok #{group_number}")
        
        kategori_pa = None
        prodi_id = None
        angkatan = None
        
        if context:
            if isinstance(context, dict):
                kategori_pa = context.get("kategori_pa")
                prodi_id = context.get("prodi_id")
                angkatan = context.get("angkatan")
            else:
                kategori_pa = getattr(context, "kategori_pa", None)
                prodi_id = getattr(context, "prodi_id", None)
                angkatan = getattr(context, "angkatan", None)
        
        state["response"] = {
            "type": "view_group_by_number",
            "group_number": group_number,
            "message": f"Menampilkan anggota kelompok nomor {group_number}",
            "action": "get_group_by_number",
            "context": {
                "kategori_pa": kategori_pa,
                "prodi_id": prodi_id,
                "angkatan": angkatan,
            }
        }
    else:
        # User wants all groups - fetch data from tools
        print(f"[VIEW GROUPS NODE] Menampilkan semua kelompok")
        
        try:
            from tools.kelompok_status_tool import list_all_groups
            
            # Get prodi_id from context
            prodi_id = None
            if context:
                if isinstance(context, dict):
                    prodi_id = context.get("prodi_id")
                else:
                    prodi_id = getattr(context, "prodi_id", None)
            
            # Fetch all groups
            groups_data = list_all_groups(prodi_id=prodi_id, context=context)
            
            if groups_data and len(groups_data) > 0:
                message = f"📋 **Ditemukan {len(groups_data)} Kelompok**\n\n"
                message += "```\n"
                message += f"{'No':<5} {'Kelompok':<20} {'Anggota':<10} {'Program Studi':<25}\n"
                message += "-" * 70 + "\n"
                
                for idx, g in enumerate(groups_data, 1):
                    nomor = g.get('nomor_kelompok', '?')
                    member_count = g.get('member_count', 0)
                    nama_prodi = g.get('nama_prodi', 'N/A')
                    message += f"{idx:<5} {nomor:<20} {member_count:<10} {nama_prodi:<25}\n"
                
                message += "```\n"
                
                state["response"] = {
                    "type": "view_existing_groups",
                    "message": message,
                    "data": groups_data,
                    "count": len(groups_data),
                    "action": "view_groups"
                }
            else:
                state["response"] = {
                    "type": "view_existing_groups",
                    "message": "❌ Belum ada kelompok yang dibuat",
                    "data": [],
                    "count": 0,
                    "action": "view_groups"
                }
        except Exception as e:
            print(f"[VIEW GROUPS NODE] Error fetching groups: {e}")
            state["response"] = {
                "type": "view_existing_groups",
                "message": f"⚠️ Error: {str(e)}",
                "error": str(e),
                "action": "view_groups"
            }
    
    print(f"[VIEW GROUPS NODE] ✓ View groups ready")
    
    return state


def node_score_based_grouping(state: AgentState) -> AgentState:
    """
    Node Score-Based Grouping - Membuat kelompok berdasarkan nilai.
    
    Menampilkan tabel dengan:
    - Kelompok nomor
    - Anggota (nama, NIM)
    - Semester yang dinilai
    - Rata-rata nilai per mahasiswa
    - Rata-rata nilai per kelompok
    
    Args:
        state: Current agent state
        
    Returns:
        state: Updated state dengan grouping result
    """
    
    print(f"[SCORE GROUPING NODE] Membuat kelompok berdasarkan nilai...")
    
    dosen_context = state.get("dosen_context")
    user_id = state.get("user_id")
    
    # Extract context
    context = None
    if dosen_context and len(dosen_context) > 0:
        context = dosen_context[0]
    
    try:
        # Import tools
        from tools.score_tool import create_groups_by_score_with_details
        from tools.db_tool import get_mahasiswa_by_context
        
        # Get context info
        kategori_pa = None
        
        if context:
            if isinstance(context, dict):
                kategori_pa = context.get("kategori_pa")
            else:
                kategori_pa = getattr(context, "kategori_pa", None)
        
        print(f"[SCORE GROUPING NODE] Context: kategori_pa={kategori_pa}, context={context}")
        
        # Get students - pass context directly
        students = get_mahasiswa_by_context(
            context=context,
            fields=['nama', 'nim']
        )
        
        if not students or len(students) == 0:
            print("[SCORE GROUPING NODE] No students found")
            state["response"] = {
                "type": "score_based_grouping",
                "message": "❌ Tidak ada mahasiswa yang ditemukan",
                "data": [],
                "action": "score_grouping"
            }
            return state
        
        print(f"[SCORE GROUPING NODE] Found {len(students)} students, calling create_groups_by_score_with_details...")
        
        # Create groups by score
        grouping_result = create_groups_by_score_with_details(
            students=students,
            kategori_pa=kategori_pa,
            group_size=6
        )
        
        # Build response
        state["response"] = {
            "type": "score_based_grouping",
            "message": grouping_result.get("summary_table", ""),
            "data": grouping_result.get("groups", []),
            "class_stats": grouping_result.get("class_stats", {}),
            "total_groups": grouping_result.get("total_groups", 0),
            "total_students": grouping_result.get("total_students", 0),
            "action": "score_grouping"
        }
        
        print(f"[SCORE GROUPING NODE] ✓ Score-based grouping complete: {grouping_result.get('total_groups', 0)} groups")
        
    except Exception as e:
        print(f"[SCORE GROUPING NODE] Error: {e}")
        import traceback
        traceback.print_exc()
        
        state["response"] = {
            "type": "score_based_grouping",
            "message": f"⚠️ Error saat membuat kelompok berdasarkan nilai: {str(e)}",
            "error": str(e),
            "action": "score_grouping"
        }
    
    return state


def node_mahasiswa(state: AgentState) -> AgentState:
    """
    Node Mahasiswa - Menampilkan daftar mahasiswa atau jumlah mahasiswa.
    
    Bisa:
    - List semua mahasiswa
    - Count total mahasiswa
    
    Args:
        state: Current agent state
        
    Returns:
        state: Updated state dengan mahasiswa response
    """
    
    print(f"[MAHASISWA NODE] Memproses mahasiswa request...")
    
    detected_type = state.get("detected_type")
    
    if detected_type == "count_mahasiswa":
        print(f"[MAHASISWA NODE] Action: COUNT mahasiswa")
        
        state["response"] = {
            "type": "count_mahasiswa",
            "message": "Menghitung total mahasiswa",
            "action": "count_mahasiswa",
            "strategy": "count",
            "steps": [
                {
                    "action": "count_mahasiswa",
                    "params": {
                        "prodi_filter": None
                    }
                }
            ],
            "metadata": {
                "type": "count"
            }
        }
    else:
        # Default: list mahasiswa
        print(f"[MAHASISWA NODE] Action: LIST mahasiswa")
        
        state["response"] = {
            "type": "list_mahasiswa",
            "message": "Menampilkan daftar mahasiswa",
            "action": "get_mahasiswa",
            "strategy": "list",
            "steps": [
                {
                    "action": "get_mahasiswa",
                    "params": {
                        "fields": ["nama", "nim"]
                    }
                }
            ],
            "metadata": {
                "show_scores": False,
                "shuffle": False,
            }
        }
    
    print(f"[MAHASISWA NODE] ✓ Mahasiswa request ready")
    
    return state


def node_scores(state: AgentState) -> AgentState:
    """
    Node Scores - Menampilkan nilai/score mahasiswa.
    
    Mengambil:
    - Data mahasiswa
    - Data score berdasarkan kategori_pa
    
    Args:
        state: Current agent state
        
    Returns:
        state: Updated state dengan scores response
    """
    
    print(f"[SCORES NODE] Memproses view scores request...")
    
    dosen_context = state.get("dosen_context")
    
    # Extract kategori_pa dari context
    kategori_pa = 3  # Default
    
    if dosen_context and len(dosen_context) > 0:
        ctx = dosen_context[0]
        if isinstance(ctx, dict):
            kategori_pa = ctx.get("kategori_pa", 3)
        else:
            kategori_pa = getattr(ctx, "kategori_pa", 3)
    
    print(f"[SCORES NODE] Mengambil scores untuk kategori_pa: {kategori_pa}")
    
    state["response"] = {
        "type": "view_scores",
        "message": f"Menampilkan nilai mahasiswa untuk kategori PA {kategori_pa}",
        "action": "view_scores",
        "strategy": "score-view",
        "steps": [
            {
                "action": "get_mahasiswa",
                "params": {
                    "fields": ["nama", "nim"]
                }
            },
            {
                "action": "get_student_scores_by_category",
                "params": {
                    "kategori_pa": kategori_pa
                }
            }
        ],
        "metadata": {
            "type": "scores",
            "show_scores": True
        }
    }
    
    print(f"[SCORES NODE] ✓ Scores response ready")
    
    return state
