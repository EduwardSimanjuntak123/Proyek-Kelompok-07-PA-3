from openai import OpenAI
from app.config import OPENAI_API_KEY
import json
import random
import re
from tools.db_tool import get_existing_groups_by_user, check_existing_groups_by_context
from memory.advanced_operations import get_user_episodes, save_knowledge, save_episode
from memory.memory import get_user_conversation_history

client = OpenAI(api_key=OPENAI_API_KEY)


# ============================================================
# MEMORY RECALL - Ambil dari conversation_memory
# ============================================================
def _get_conversation_history(user_id, limit=5):
    """Get recent conversation history dari session memory"""
    try:
        history = get_user_conversation_history(user_id, limit=limit)
        return [
            {
                'id': h.get('id'),
                'prompt': h.get('prompt'),
                'created_at': h.get('created_at')
            }
            for h in history
        ]
    except:
        return []


def _recall_previous_question(user_id, question_index=1):
    """Recall pertanyaan sebelumnya (1-based: 1=most recent)"""
    try:
        history = _get_conversation_history(user_id, limit=10)
        if len(history) > question_index:
            return history[question_index]  # Index 0 = current, 1 = previous, etc
        return None
    except:
        return None


def _answer_previous_question(user_id):
    """Direct answer: Apa pertanyaan saya sebelumnya?"""
    try:
        prev = _recall_previous_question(user_id, question_index=1)
        if prev:
            prompt = prev.get('prompt', 'N/A')
            return f"Pertanyaan Anda sebelumnya: \"{prompt}\""
        return "Tidak ada pertanyaan sebelumnya dalam history."
    except:
        return None


def _answer_conversation_history(user_id):
    """Direct answer: Tunjukkan history pertanyaan"""
    try:
        history = _get_conversation_history(user_id, limit=5)
        if not history:
            return "Belum ada riwayat percakapan."
        
        summary = "Riwayat percakapan Anda:\n"
        for i, h in enumerate(history, 1):
            prompt = h.get('prompt', 'N/A')[:50]
            summary += f"{i}. {prompt}...\n" if len(h.get('prompt', '')) > 50 else f"{i}. {prompt}\n"
        return summary
    except:
        return None


# ============================================================
# DIRECT ANSWER PATTERNS - Jawab langsung tanpa LLM
# ============================================================
def _detect_direct_answer_pattern(prompt, context_data=None, group_context=None, user_id=None):
    """Deteksi pertanyaan yang bisa dijawab langsung dari data"""
    
    prompt_lower = prompt.lower().strip()
    
    # Remove extra spaces and normalize
    prompt_normalized = re.sub(r'\s+', ' ', prompt_lower)
    
    debug = f"[RESPONDER] Pattern matching: '{prompt_normalized}'"
    
    # Pattern 7: "tampilkan riwayat" / "lihat history lengkap" - Show full history
    # PRIORITY: Check this before previous_question (more specific)
    if re.search(r'tampilkan.*riwayat|lihat.*riwayat|semua\s+pertanyaan|percakapan\s+kita|history\s+lengkap', prompt_normalized):
        if user_id:
            print(f"{debug} → Matched HISTORY pattern")
            return _answer_conversation_history(user_id)
    
    # Pattern 6: "apa pertanyaan saya sebelumnya?" - Recall JUST previous question
    # ENHANCED: Handle typos like "pertanayaan", different word orders, "sebelumnya", etc.
    # Matches: "apa" + (pertan|tanya|soal) + "sebelum*", or reverse word orders
    # "pertan" catches both "pertanyaan" and "pertanayaan" (typo)
    pattern_6_regex = r'apa.*(pertan|tanya|soal).*sebelum|sebelum.*(pertan|tanya|soal).*apa|tanya.*sebelum|soal.*sebelum|tanya.*apa'
    if re.search(pattern_6_regex, prompt_normalized):
        if user_id:
            print(f"{debug} → Matched PREVIOUS_QUESTION pattern")
            return _answer_previous_question(user_id)
        else:
            print(f"{debug} → Matched PREVIOUS_QUESTION pattern but no user_id, returning None")
            return None
    
    # Pattern 1: "apakah sudah ada kelompok?" - Check if groups exist
    if re.search(r'(apakah\s+sudah\s+ada|ada\s+tidak|ada\s+gak)\s+(kelompok|grup)', prompt_normalized):
        print(f"{debug} → Matched GROUPS_EXIST pattern")
        return _answer_groups_exist(context_data)
    
    # Pattern 2: "berapa kelompok?" - Count groups
    if re.search(r'berapa\s+(jumlah\s+)?kelompok|total\s+kelompok', prompt_normalized):
        print(f"{debug} → Matched GROUP_COUNT pattern")
        return _answer_group_count(context_data)
    
    # Pattern 3: "berapa anggota?" OR "berapa anggotanya?" - Count members
    # Can refer to previous group context if available
    if re.search(r'berapa\s+(jumlah\s+)?(anggota|orang)|total.*anggota|berapa.*anggotanya', prompt_normalized):
        print(f"{debug} → Matched MEMBER_COUNT pattern")
        return _answer_member_count(context_data, group_context)
    
    # Pattern 4: "siapa saja di kelompok?" - List members
    if re.search(r'siapa\s+saja|member\s+siapa|anggota\s+siapa', prompt_normalized):
        print(f"{debug} → Matched LIST_MEMBERS pattern")
        return _answer_list_members(context_data, group_context)
    
    # Pattern 5: "apa aja kelompok?" - List all groups
    if re.search(r'apa\s+aja|list\s+kelompok|tampilkan\s+kelompok|lihat\s+kelompok', prompt_normalized):
        print(f"{debug} → Matched LIST_GROUPS pattern")
        return _answer_list_groups(context_data)
    
    print(f"{debug} → NO PATTERN MATCHED")
    return None


def _answer_groups_exist(context_data):
    """Direct answer: Ada kelompok tidak?"""
    try:
        if not context_data:
            return None
            
        user_id = context_data.get('user_id')
        groups = get_existing_groups_by_user(user_id, context_data) if user_id else []
        
        if not groups:
            return "Belum ada kelompok. Mau saya buat sekarang?"
        
        return f"Sudah ada {len(groups)} kelompok. Mau lihat detailnya?"
    except:
        return None


def _answer_group_count(context_data):
    """Direct answer: Berapa kelompok?"""
    try:
        if not context_data:
            return None
            
        user_id = context_data.get('user_id')
        groups = get_existing_groups_by_user(user_id, context_data) if user_id else []
        
        count = len(groups)
        return f"Ada {count} kelompok." if count > 0 else "Belum ada kelompok."
    except:
        return None


def _answer_member_count(context_data, group_context=None):
    """Direct answer: Berapa anggota? (dapat merujuk ke previous group context)"""
    try:
        # Check if we have group context from previous question
        if group_context:
            members = group_context.get("members", [])
            group_num = group_context.get("kelompok", "N/A")
            count = len(members)
            return f"Kelompok {group_num} ada {count} anggota." if count > 0 else "Tidak ada anggota."
        
        # Otherwise count all members
        if not context_data:
            return None
            
        user_id = context_data.get('user_id')
        groups = get_existing_groups_by_user(user_id, context_data) if user_id else []
        
        total = sum(len(g.get("members", [])) for g in groups)
        return f"Total {total} anggota dalam {len(groups)} kelompok." if total > 0 else "Belum ada anggota."
    except:
        return None


def _answer_list_members(context_data, group_context=None):
    """Direct answer: List members (dari previous group context atau first group)"""
    try:
        members = []
        
        # Use group context from previous question if available
        if group_context and group_context.get("members"):
            members = group_context.get("members", [])
            group_num = group_context.get("kelompok", "N/A")
        else:
            # Otherwise use first group from context
            if not context_data:
                return None
                
            user_id = context_data.get('user_id')
            groups = get_existing_groups_by_user(user_id, context_data) if user_id else []
            
            if not groups or not groups[0].get("members"):
                return "Belum ada data anggota."
            
            members = groups[0].get("members", [])
            group_num = groups[0].get("kelompok", "N/A")
        
        if not members:
            return f"Kelompok {group_num} tidak ada anggota."
        
        members_sample = members[:5]  # First 5 members
        names = ", ".join([m.get("nama", "N/A") for m in members_sample])
        more = f" +{len(members) - 5} lagi" if len(members) > 5 else ""
        return f"Anggota kelompok {group_num}: {names}{more}"
    except:
        return None


def _answer_list_groups(context_data):
    """Direct answer: List all groups"""
    try:
        if not context_data:
            return None
            
        user_id = context_data.get('user_id')
        groups = get_existing_groups_by_user(user_id, context_data) if user_id else []
        
        if not groups:
            return "Belum ada kelompok untuk ditampilkan."
        
        summary = "\n".join([
            f"Kelompok {g.get('kelompok', 'N/A')}: {len(g.get('members', []))} anggota"
            for g in groups[:5]
        ])
        more = f"\n... dan {len(groups) - 5} kelompok lagi" if len(groups) > 5 else ""
        return f"Kelompok yang ada:\n{summary}{more}"
    except:
        return None


# ============================================================
# REMEMBER PATTERN - Simpan ke memory jika pertanyaan berulang
# ============================================================
def _save_recurring_question_pattern(prompt, user_id):
    """Save pertanyaan yang sering ditanya untuk learning"""
    try:
        # Simplified patterns
        if re.search(r'(apakah\s+sudah\s+ada|ada\s+tidak|ada\s+gak)\s+(kelompok|grup)', prompt.lower()):
            pattern = "frekuent_question_groups_exist"
        elif re.search(r'berapa\s+(jumlah\s+)?(kelompok|anggota)', prompt.lower()):
            pattern = "frekuent_question_count"
        else:
            return
        
        save_knowledge(
            knowledge_type="user_pattern",
            topic="frequent_questions",
            statement=f"User sering tanya: {pattern}",
            confidence=0.9,
            source_type="observation"
        )
    except:
        pass


def generate_response(result):
    """Generate natural language explanation untuk hasil kelompok"""
    response = client.chat.completions.create(
        model="gpt-4o-mini",
        messages=[
            {"role": "system", "content": "Jelaskan hasil kelompok dengan rapi dan singkat. Maksimal 2-3 sentences."},
            {"role": "user", "content": str(result)}
        ],
        max_tokens=150
    )

    return response.choices[0].message.content


def respond_to_unrecognized_query(prompt, context_data=None, user_id=None, group_context=None, prompt_history=None):
    """Generate response untuk pertanyaan yang tidak bisa diparsing
    
    Strategy:
    1. Cek direct answer patterns dulu
    2. Jika ada, jawab langsung (singkat!)
    3. Jika tidak, gunakan LLM tapi dengan constraint singkat
    4. Simpan ke memory jika pertanyaan berulang
    
    grup_context: Previous group from last question (untuk follow-up questions like "berapa anggotanya?")
    """
    
    # Step 1: Try direct answer pattern
    direct_answer = _detect_direct_answer_pattern(prompt, context_data, group_context=group_context, user_id=user_id)
    if direct_answer:
        if user_id:
            _save_recurring_question_pattern(prompt, user_id)
        return direct_answer
    
    # Step 2: Use LLM sebagai fallback (dengan constraint singkat!)
    system_prompt = """Kamu adalah AI Agent Koordinator PA.
PENTING: Jawab dengan SINGKAT dan LANGSUNG (max 1-2 sentences)!
- Jangan tanya balik
- Jangan panjang-panjang
- Langsung ke intinya
- Gunakan bahasa casual & to the point"""

    try:
        response = client.chat.completions.create(
            model="gpt-4o-mini",
            messages=[
                {"role": "system", "content": system_prompt},
                {"role": "user", "content": prompt}
            ],
            temperature=0.7,
            max_tokens=100  # LIMIT: max 100 tokens untuk dijaga singkat!
        )
        
        answer = response.choices[0].message.content
        
        # Save recurring pattern
        if user_id:
            _save_recurring_question_pattern(prompt, user_id)
        
        return answer
        
    except Exception as e:
        print(f"[ERROR] Generate response failed: {e}")
        return "Maaf singkat, bisa minta instruksi yang lebih jelas? Misalnya: 'buat 5 kelompok' atau 'berapa kelompok?'"


def _extract_members_from_groups(groups):
    """Extract semua nama mahasiswa dari groups untuk rekomendasi"""
    members = []
    group_assignments = {}  # {nama: kelompok_number}
    
    if not groups:
        return members, group_assignments
    
    for group_data in groups:
        kelompok_num = group_data.get("kelompok")
        group_members = group_data.get("members", [])
        
        for member in group_members:
            nama = member.get("nama", "")
            if nama:
                members.append({
                    "nama": nama,
                    "nim": member.get("nim", ""),
                    "kelompok": kelompok_num
                })
                group_assignments[nama] = kelompok_num
    
    return members, group_assignments


def generate_recommendations(groups):
    """Generate rekomendasi instruksi selanjutnya berdasarkan hasil grouping.
    
    Rekomendasi termasuk:
    1. Action buttons: Simpan, Acak Ulang
    2. Constraint suggestions: Pasangan yang bisa disatukan atau dipisahkan
    3. Instructions untuk modify existing groups
    """
    if not groups:
        return {
            "actions": [],
            "constraints": [],
            "instructions": []
        }
    
    members, group_assignments = _extract_members_from_groups(groups)
    
    if not members:
        return {
            "actions": [],
            "constraints": [],
            "instructions": []
        }
    
    # ==========================================
    # 1. ACTION BUTTONS (Basic actions)
    # ==========================================
    actions = [
        {
            "label": "💾 Simpan ke Database",
            "instruction": "simpan hasil kelompok ke database",
            "type": "save"
        },
        {
            "label": "🔄 Acak Ulang",
            "instruction": "buat kelompok acak",
            "type": "reshuffle"
        },
        {
            "label": "📋 Lihat Detail",
            "instruction": "tampilkan detail semua kelompok",
            "type": "detail"
        }
    ]
    
    # ==========================================
    # 2. CONSTRAINT SUGGESTIONS
    # ==========================================
    # Generate pasangan yang bisa disatukan (dari kelompok berbeda)
    # atau dipisahkan (dari kelompok yang sama)
    
    constraints = []
    
    # Ambil sample pasangan untuk saran
    # Sample: 2-3 pasangan yang bisa disatukan dari kelompok berbeda
    for i in range(min(2, len(groups))):
        if len(groups) > 1:
            # Ambil anggota dari kelompok berbeda
            group_1 = groups[i].get("members", [])
            group_2 = groups[(i + 1) % len(groups)].get("members", [])
            
            if group_1 and group_2:
                member_1 = group_1[0]
                member_2 = group_2[0]
                
                nama_1 = member_1.get("nama", "")
                nama_2 = member_2.get("nama", "")
                
                if nama_1 and nama_2:
                    constraints.append({
                        "type": "must_pair",
                        "person_a": nama_1,
                        "person_b": nama_2,
                        "instruction": f"{nama_1} dan {nama_2} harus satu kelompok",
                        "label": f"🔗 Gabungkan {nama_1} & {nama_2}"
                    })
    
    # Sample pasangan yang bisa dipisahkan dari kelompok yang sama
    for i, group_data in enumerate(groups):
        group_members = group_data.get("members", [])
        if len(group_members) >= 2:
            # Ambil 2 anggota pertama dari group yang besar
            member_1 = group_members[0]
            member_2 = group_members[1]
            
            nama_1 = member_1.get("nama", "")
            nama_2 = member_2.get("nama", "")
            
            if nama_1 and nama_2:
                constraints.append({
                    "type": "avoid_pair",
                    "person_a": nama_1,
                    "person_b": nama_2,
                    "instruction": f"{nama_1} dan {nama_2} jangan satu kelompok",
                    "label": f"🚫 Pisahkan {nama_1} & {nama_2}"
                })
            
            # Limit to 2-3 suggestions
            if len(constraints) >= 4:
                break
    
    return {
        "actions": actions,
        "constraints": constraints,
        "summary": {
            "total_groups": len(groups),
            "total_members": len(members),
            "group_sizes": [len(g.get("members", [])) for g in groups]
        }
    }


def format_recommendations_html(recommendations):
    """Format rekomendasi menjadi HTML untuk ditampilkan di chat"""
    if not recommendations:
        return ""
    
    html = "<div class='recommendations-panel' style='margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;'>"
    
    # ========== SUMMARY ==========
    if "summary" in recommendations:
        summary = recommendations["summary"]
        html += f"""
        <div class='summary-stats' style='margin-bottom: 15px;'>
            <p style='margin: 5px 0;'><strong>📊 Summary:</strong></p>
            <ul style='margin: 5px 0; padding-left: 20px;'>
                <li>Total Kelompok: <strong>{summary.get('total_groups', 0)}</strong></li>
                <li>Total Anggota: <strong>{summary.get('total_members', 0)}</strong></li>
                <li>Distribusi: {', '.join([str(s) for s in summary.get('group_sizes', [])])} orang</li>
            </ul>
        </div>
        """
    
    # ========== ACTION BUTTONS ==========
    if recommendations.get("actions"):
        html += "<div class='action-buttons' style='margin-bottom: 15px;'>"
        html += "<p style='margin: 5px 0; font-weight: bold; color: #333;'>⚡ Aksi Cepat:</p>"
        html += "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>"
        
        for action in recommendations["actions"]:
            instruction = action.get("instruction", "").replace("'", "\\'")
            label = action.get("label", "")
            action_type = action.get("type", "default")
            
            # Different styling per action type
            btn_style = "btn-primary" if action_type == "save" else "btn-outline-primary"
            
            html += f"""
            <button class='recommendation-action btn btn-sm {btn_style}' 
                    data-instruction='{instruction}'
                    style='border-radius: 20px; font-size: 12px; padding: 8px 14px; white-space: nowrap; cursor: pointer;'>
                {label}
            </button>
            """
        
        html += "</div></div>"
    
    # ========== SKIP CONSTRAINT SUGGESTIONS (HIDDEN) ==========
    # Constraints section removed as per user request
    # Follow-up instructions section also removed
    
    html += "</div>"
    
    return html
