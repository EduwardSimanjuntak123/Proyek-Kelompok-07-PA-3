"""
Nodes untuk pembimbing (lecturer assignment) dan dosen queries.

Menangani request terkait informasi dosen dan assignment pembimbing ke kelompok.
"""

import json
from core.state import AgentState
from agents.parser import parse_pembimbing_command, parse_dosen_query
from tools.dosen_tool import get_dosen_list_by_prodi
from openai import OpenAI
from app.config import OPENAI_API_KEY

client = OpenAI(api_key=OPENAI_API_KEY)


def _is_specific_dosen_question(prompt: str) -> bool:
    """Detect jika user bertanya tentang dosen spesifik, bukan request list semua
    
    Examples of specific questions:
    - "apa jabatan dosen oppir?"
    - "siapa yang mengajar kelas A?"
    - "berapa nip dosen Ardiles?"
    - "dosen mana yang berpendidikan doktor?"
    """
    prompt_lower = prompt.lower()
    
    # Keywords untuk specific questions
    specific_keywords = [
        "apa jabatan", "jabatan dosen", "siapa yang", "berapa nip",
        "berapa nidn", "email", "pendidikan", "kualifikasi", "gelar",
        "nip berapa", "nidn berapa", "siapa dosen"
    ]
    
    return any(keyword in prompt_lower for keyword in specific_keywords)


def _answer_specific_dosen_question(prompt: str, dosen_data: list, prodi_name: str) -> str:
    """Use LLM to answer specific questions about dosen dengan context"""
    
    if not dosen_data:
        return "Tidak ada data dosen untuk menjawab pertanyaan ini"
    
    # Format data untuk LLM
    dosen_context = _format_dosen_for_llm(dosen_data)
    
    system_prompt = f"""Anda adalah asisten untuk menjawab pertanyaan tentang dosen di program studi {prodi_name}.

Berikut adalah data lengkap dosen:

{dosen_context}

Jawab pertanyaan user dengan singkat dan akurat berdasarkan data di atas."""
    
    try:
        response = client.chat.completions.create(
            model="gpt-4o-mini",
            messages=[
                {"role": "system", "content": system_prompt},
                {"role": "user", "content": prompt}
            ],
            temperature=0.5,
            max_tokens=500
        )
        
        answer = response.choices[0].message.content.strip()
        print(f"[DOSEN NODE] LLM answered: {answer[:100]}...")
        return answer
        
    except Exception as e:
        print(f"[DOSEN NODE] LLM error: {str(e)}")
        return f"Gagal menjawab pertanyaan: {str(e)}"


def _format_dosen_for_llm(dosen_data: list) -> str:
    """Format dosen data untuk LLM context agar bisa answer specific questions"""
    if not dosen_data:
        return "Tidak ada data dosen"
    
    lines = []
    for idx, dosen in enumerate(dosen_data, 1):
        nama = dosen.get("nama", "-")
        jabatan = dosen.get("jabatan_akademik_desc", "-")
        nip = dosen.get("nip", "-")
        nidn = dosen.get("nidn", "-")
        email = dosen.get("email", "-")
        pendidikan = dosen.get("jenjang_pendidikan", "-")
        
        lines.append(f"{idx}. {nama}")
        lines.append(f"   - Jabatan: {jabatan}")
        lines.append(f"   - NIP: {nip}")
        lines.append(f"   - NIDN: {nidn}")
        if email and email != "-" and email != "None":
            lines.append(f"   - Email: {email}")
        if pendidikan and pendidikan != "-" and pendidikan != "None":
            lines.append(f"   - Pendidikan: {pendidikan}")
    
    return "\n".join(lines)


def node_pembimbing(state: AgentState) -> AgentState:
    """
    Node Pembimbing - Menangani pembimbing/mentor assignment untuk kelompok.
    
    Memproses command seperti:
    - Lihat pembimbing kelompok
    - Assign pembimbing ke kelompok (AUTO ASSIGN)
    - Ubah pembimbing
    
    Args:
        state: Current agent state
        
    Returns:
        state: Updated state dengan pembimbing command result
    """
    
    print(f"[PEMBIMBING NODE] Memproses pembimbing command...")
    
    prompt = state.get("prompt", "")
    dosen_context = state.get("dosen_context")
    
    # Extract context
    context = None
    if dosen_context and len(dosen_context) > 0:
        context = dosen_context[0]
    
    # Parse pembimbing command
    parsed_pembimbing = parse_pembimbing_command(prompt, context)
    
    print(f"[PEMBIMBING NODE] Parsed action: {parsed_pembimbing.get('action', 'unknown')}")
    
    # Extract konteks tambahan
    kpa_id = None
    prodi_id = None
    angkatan = None
    
    if context:
        if isinstance(context, dict):
            kpa_id = parsed_pembimbing.get("kpa_id") or context.get("kategori_pa")
            prodi_id = parsed_pembimbing.get("prodi_id") or context.get("prodi_id")
            angkatan = parsed_pembimbing.get("tm_id") or context.get("angkatan")
        else:
            kpa_id = parsed_pembimbing.get("kpa_id") or getattr(context, "kategori_pa", None)
            prodi_id = parsed_pembimbing.get("prodi_id") or getattr(context, "prodi_id", None)
            angkatan = parsed_pembimbing.get("tm_id") or getattr(context, "angkatan", None)
    else:
        kpa_id = parsed_pembimbing.get("kpa_id")
        prodi_id = parsed_pembimbing.get("prodi_id")
        angkatan = parsed_pembimbing.get("tm_id")
    
    # Build detailed message based on action
    action = parsed_pembimbing.get("action", "view_all_pembimbing")
    
    try:
        if action == "auto_assign":
            print(f"[PEMBIMBING NODE] Executing auto-assignment: prodi_id={prodi_id}, kpa_id={kpa_id}, tm_id={angkatan}")
            
            # Import assignment tool
            from tools.pembimbing_assignment_tool import (
                assign_pembimbing_automatically,
                format_assignment_result
            )
            
            # Execute auto assignment
            assignment_result = assign_pembimbing_automatically(
                prodi_id=prodi_id,
                kpa_id=kpa_id,
                tm_id=angkatan,
                jabatan_filter=None
            )
            
            if assignment_result.get("success"):
                # Format the result for display
                formatted_result = format_assignment_result(assignment_result)
                
                message = (
                    "✅ **Penugasan Pembimbing Berhasil**\n\n"
                    f"📊 Ringkasan:\n"
                    f"- Pembimbing ditetapkan: {assignment_result.get('created_assignments', 0)}\n"
                    f"- Kelompok dengan 1 pembimbing: {assignment_result.get('groups_with_1_lecturer', 0)}\n"
                    f"- Kelompok dengan 2 pembimbing: {assignment_result.get('groups_with_2_lecturers', 0)}\n"
                    f"- Kelompok tanpa pembimbing: {assignment_result.get('groups_with_0_lecturers', 0)}\n\n"
                    f"📋 Hasil Penugasan:\n"
                    f"{formatted_result}\n\n"
                    f"💾 Data telah disimpan ke database"
                )
                
                response_data = {
                    "type": "pembimbing_command",
                    "message": message,
                    "action": action,
                    "status": "success",
                    "assignment_result": assignment_result,
                    "context": {
                        "kategori_pa": kpa_id,
                        "prodi_id": prodi_id,
                        "tahun_masuk": angkatan,
                    }
                }
            else:
                message = f"❌ **Gagal Menugaskan Pembimbing**\n\n{assignment_result.get('message', 'Kesalahan tidak diketahui')}"
                
                response_data = {
                    "type": "pembimbing_command",
                    "message": message,
                    "action": action,
                    "status": "failed",
                    "error": assignment_result.get("message", "Unknown error"),
                    "context": {
                        "kategori_pa": kpa_id,
                        "prodi_id": prodi_id,
                        "tahun_masuk": angkatan,
                    }
                }
        
        elif action == "view_group_pembimbing":
            print(f"[PEMBIMBING NODE] Viewing pembimbing for group")
            
            from tools.pembimbing_assignment_tool import get_group_with_pembimbing_details
            
            kelompok_id = parsed_pembimbing.get("kelompok_id")
            kelompok_number = parsed_pembimbing.get("kelompok_number")
            
            if kelompok_id:
                pembimbing_data = get_group_with_pembimbing_details(kelompok_id)
                
                if pembimbing_data:
                    message = f"👥 **Pembimbing Kelompok {kelompok_number or kelompok_id}**\n\n"
                    for pb in pembimbing_data:
                        message += f"- {pb.get('nama_dosen', 'N/A')} ({pb.get('nip', 'N/A')})\n"
                else:
                    message = f"⚠️ Kelompok {kelompok_number or kelompok_id} belum memiliki pembimbing"
            else:
                message = "⚠️ Kelompok tidak ditemukan"
            
            response_data = {
                "type": "pembimbing_command",
                "message": message,
                "action": action,
                "kelompok_id": kelompok_id,
                "kelompok_number": kelompok_number,
                "context": {
                    "kategori_pa": kpa_id,
                    "prodi_id": prodi_id,
                    "tahun_masuk": angkatan,
                }
            }
        
        else:  # view_all_pembimbing (default) or other
            print(f"[PEMBIMBING NODE] Viewing all pembimbing assignments")
            
            from tools.pembimbing_assignment_tool import (
                get_groups_with_pembimbing_by_context,
                format_groups_with_pembimbing
            )
            
            # Get all groups with their pembimbing
            groups_with_pembimbing = get_groups_with_pembimbing_by_context(
                prodi_id=prodi_id,
                kpa_id=kpa_id,
                tm_id=angkatan
            )
            
            if groups_with_pembimbing:
                formatted_data = format_groups_with_pembimbing(groups_with_pembimbing)
                message = (
                    "📋 **Daftar Semua Pembimbing Kelompok**\n\n"
                    f"{formatted_data}"
                )
            else:
                message = "⚠️ Tidak ada kelompok atau pembimbing yang ditemukan"
            
            response_data = {
                "type": "pembimbing_command",
                "message": message,
                "action": action,
                "status": "success",
                "context": {
                    "kategori_pa": kpa_id,
                    "prodi_id": prodi_id,
                    "tahun_masuk": angkatan,
                }
            }
    
    except Exception as e:
        print(f"[PEMBIMBING NODE] Error: {e}")
        import traceback
        traceback.print_exc()
        
        message = f"❌ **Kesalahan saat memproses pembimbing**\n\nError: {str(e)}"
        
        response_data = {
            "type": "pembimbing_command",
            "message": message,
            "action": action,
            "status": "error",
            "error": str(e),
            "context": {
                "kategori_pa": kpa_id,
                "prodi_id": prodi_id,
                "tahun_masuk": angkatan,
            }
        }
    
    # Build response
    state["response"] = response_data
    
    # Store parsed request untuk execution
    state["parsed_request"] = parsed_pembimbing
    
    print(f"[PEMBIMBING NODE] ✓ Response ready untuk action: {action}")
    
    return state


def node_dosen(state: AgentState) -> AgentState:
    """
    Node Dosen - Menangani queries tentang informasi dosen.
    
    Memproses query seperti:
    - Daftar dosen yang tersedia
    - Dosen di prodi tertentu
    - Pertanyaan spesifik tentang dosen (apa jabatan, siapa, dll)
    
    Args:
        state: Current agent state
        
    Returns:
        state: Updated state dengan dosen info
    """
    
    print(f"[DOSEN NODE] Memproses dosen query...")
    
    prompt = state.get("prompt", "")
    dosen_context = state.get("dosen_context")
    
    # Extract context
    context = None
    if dosen_context and len(dosen_context) > 0:
        context = dosen_context[0]
    
    # Parse dosen query
    parsed_dosen = parse_dosen_query(prompt, context)
    
    print(f"[DOSEN NODE] Parsed action: {parsed_dosen.get('action', 'unknown')}")
    
    # Extract konteks tambahan
    kategori_pa = None
    prodi_id = None
    prodi_name = None
    
    if context:
        if isinstance(context, dict):
            kategori_pa = context.get("kategori_pa")
            prodi_id = context.get("prodi_id")
            prodi_name = context.get("prodi")
        else:
            kategori_pa = getattr(context, "kategori_pa", None)
            prodi_id = getattr(context, "prodi_id", None)
            prodi_name = getattr(context, "prodi", None)
    
    # Fetch dosen data
    dosen_data = []
    try:
        if prodi_id:
            dosen_result = get_dosen_list_by_prodi(prodi_id)
            # get_dosen_list_by_prodi returns dict with "data" field
            if isinstance(dosen_result, dict) and dosen_result.get("success"):
                dosen_data = dosen_result.get("data", [])
            else:
                dosen_data = dosen_result if isinstance(dosen_result, list) else []
            print(f"[DOSEN NODE] Fetched {len(dosen_data)} dosen for prodi_id={prodi_id}")
        else:
            print(f"[DOSEN NODE] No prodi_id, cannot fetch dosen data")
    except Exception as e:
        print(f"[DOSEN NODE] Error fetching dosen data: {str(e)}")
        dosen_data = []
    
    # Check if user is asking a specific question about dosen
    is_specific_question = _is_specific_dosen_question(prompt) 
    
    if is_specific_question and dosen_data:
        print(f"[DOSEN NODE] Detected specific dosen question, using LLM to answer...")
        specific_answer = _answer_specific_dosen_question(prompt, dosen_data, prodi_name)
        
        # Build response dengan specific answer
        state["response"] = {
            "type": "dosen_query",
            "message": specific_answer,
            "action": "specific_dosen_question",
            "prodi_id": prodi_id,
            "prodi_name": prodi_name,
            "dosen_data": {
                "data": dosen_data,
                "count": len(dosen_data),
            },
            "context": {
                "kategori_pa": kategori_pa,
                "prodi_id": prodi_id,
            }
        }
    else:
        # Default: show full list in table format
        # Format dosen data untuk response (simple format untuk table di chat)
        dosen_formatted = ""
        if dosen_data:
            dosen_formatted = f"<p><strong>Total: {len(dosen_data)} dosen</strong></p>"
            dosen_formatted += '<table class="agent-data-table"><thead><tr><th>No</th><th>Nama Dosen</th><th>Jabatan</th><th>NIP</th></tr></thead><tbody>'
            for idx, dosen in enumerate(dosen_data, 1):
                nama = dosen.get("nama", "-")
                jabatan = dosen.get("jabatan_akademik_desc", "-")
                nip = dosen.get("nip", "-")
                dosen_formatted += f"<tr><td>{idx}</td><td>{nama}</td><td>{jabatan}</td><td>{nip}</td></tr>"
            dosen_formatted += "</tbody></table>"
        
        # Build response dengan actual dosen data
        state["response"] = {
            "type": "dosen_query",
            "message": f"Menampilkan daftar dosen {prodi_name or ''}".strip(),
            "action": parsed_dosen.get("action", "list_dosen_current"),
            "prodi_id": prodi_id,
            "prodi_name": prodi_name,
            "dosen_data": {
                "data": dosen_data,
                "count": len(dosen_data),
                "formatted": dosen_formatted
            },
            "context": {
                "kategori_pa": kategori_pa,
                "prodi_id": prodi_id,
            },
            # Add LLM context dengan dosen data untuk answer specific questions
            "llm_context": f"""Berikut adalah daftar lengkap dosen untuk program studi {prodi_name or 'ini'}:

{_format_dosen_for_llm(dosen_data)}

Gunakan informasi di atas untuk menjawab pertanyaan spesifik tentang dosen."""
        }
    
    # Store parsed request untuk execution
    state["parsed_request"] = parsed_dosen
    
    print(f"[DOSEN NODE] ✓ Response ready dengan {len(dosen_data)} dosen" + (" (specific question)" if is_specific_question else ""))
    
    return state
