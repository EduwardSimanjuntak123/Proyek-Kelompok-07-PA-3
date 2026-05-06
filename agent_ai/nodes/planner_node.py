import json
import logging
import re
import traceback
from core.llm import call_llm
from core.memory import SemanticMemory, LongTermMemory, LONG_TERM_STORE
from models_documentation import get_full_model_documentation, get_model_awareness_context

logger = logging.getLogger(__name__)
logger.setLevel(logging.DEBUG)

def planner_node(state):
    try:
        user_id = state.get("user_id", "default")
        
        prompt = state["messages"][-1]["content"]
        prompt_lower = prompt.lower()

        create_verbs = ["buat", "buatkan", "bagi", "generate", "kelompokkan", "susun", "tambahkan", "masukkan", "alokasikan"]
        group_terms = ["kelompok", "group", "grup"]

        query_only_terms = ["daftar", "list", "data", "cek", "lihat", "tampilkan", "berapa"]
        create_pattern = r"\b(buat|buatkan|bagi|generate|kelompokkan|susun|tambahkan|masukkan|alokasikan)\b.*\b(kelompok|group|grup|n\+1)\b"

        anggota_pattern = r"(?:siapa\s+|lihat\s+|tampilkan\s+|cek\s+)?anggot?a\s+kelompok\s*(?:nomor\s*)?(\d+)"
        anggota_match = re.search(anggota_pattern, prompt_lower)
        if anggota_match:
            nomor_kelompok = anggota_match.group(1)
            plan = {"action": "query_anggota_kelompok", "nomor_kelompok": nomor_kelompok}
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → query_anggota_kelompok ✓ (nomor={nomor_kelompok})")
            state["plan"] = plan
            return state

        pembimbing_kelompok_pattern = r"(?:siapa\s+)?pembimbing\s+kelompok\s*(?:nomor\s*)?(\d+)"
        pembimbing_kelompok_match = re.search(pembimbing_kelompok_pattern, prompt_lower)
        if pembimbing_kelompok_match:
            nomor_kelompok = pembimbing_kelompok_match.group(1)
            plan = {"action": "query_pembimbing", "nomor_kelompok": nomor_kelompok}
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → query_pembimbing ✓ (kelompok={nomor_kelompok})")
            state["plan"] = plan
            return state

        # Intent khusus: tampilkan kelompok beserta pembimbing dan penguji
        if (
            "kelompok" in prompt_lower
            and "pembimbing" in prompt_lower
            and "penguji" in prompt_lower
            and any(term in prompt_lower for term in ["tampilkan", "lihat", "daftar", "list", "beserta"])
        ):
            plan = {"action": "query_kelompok"}
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → query_kelompok ✓ (with pembimbing+penguji)")
            state["plan"] = plan
            return state

        # Intent khusus: pembimbing assignment HARUS dicheck lebih dulu
        # SEBELUM "daftar dosen pembimbing" karena lebih spesifik
        if ("generate" in prompt_lower or "buat" in prompt_lower or "assign" in prompt_lower) and "pembimbing" in prompt_lower and not any(term in prompt_lower for term in ["daftar", "list", "tampilkan", "lihat"]):
            plan = {"action": "generate_pembimbing"}
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → generate_pembimbing ✓")
            state["plan"] = plan
            return state

        # Intent khusus: penguji assignment HARUS dicheck lebih dulu
        # SEBELUM "daftar penguji" karena lebih spesifik
        if ("generate" in prompt_lower or "buat" in prompt_lower or "assign" in prompt_lower) and "penguji" in prompt_lower and not any(term in prompt_lower for term in ["daftar", "list", "tampilkan", "lihat"]):
            plan = {"action": "generate_penguji"}
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → generate_penguji ✓")
            state["plan"] = plan
            return state

        # Intent khusus: jadwal seminar HARUS dicheck lebih dulu
        # SEBELUM "query jadwal" karena generate_jadwal lebih spesifik
        if ("buat" in prompt_lower or "generate" in prompt_lower) and ("jadwal" in prompt_lower or "seminar" in prompt_lower) and not any(term in prompt_lower for term in ["daftar", "list", "tampilkan", "lihat", "query"]):
            plan = {"action": "generate_jadwal"}
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → generate_jadwal ✓")
            state["plan"] = plan
            return state

        # Intent khusus: save jadwal HARUS dicheck sebelum generic "jadwal" keyword
        # SEBELUM "query jadwal" karena save_jadwal lebih spesifik untuk confirmation
        if ("simpan" in prompt_lower or "save" in prompt_lower) and ("jadwal" in prompt_lower or "selesai" in prompt_lower):
            plan = {"action": "save_jadwal"}
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → save_jadwal ✓")
            state["plan"] = plan
            return state

        # Intent khusus: daftar dosen pembimbing (beda dengan daftar dosen umum)
        if "dosen pembimbing" in prompt_lower or "daftar pembimbing" in prompt_lower:
            plan = {"action": "query_pembimbing"}
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → query_pembimbing ✓ (dosen pembimbing list)")
            state["plan"] = plan
            return state

        # Intent khusus: daftar dosen penguji
        if "dosen penguji" in prompt_lower or "daftar penguji" in prompt_lower:
            plan = {"action": "query_penguji"}
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → query_penguji ✓ (dosen penguji list)")
            state["plan"] = plan
            return state

        if "sudah ada pembimbing" in prompt_lower or "apakah sudah ada pembimbing" in prompt_lower:
            plan = {"action": "check_pembimbing"}
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → check_pembimbing ✓")
            state["plan"] = plan
            return state

        # ✨ CHECK FOR HYBRID GROUPING FIRST (constraint + optional grades)
        # Pattern: "X, Y, Z satu kelompok" or "nim1, nim2 harus satu" 
        # OR: Presence of "harus satu" OR "satu kelompok" + create verbs
        # THIS MUST COME BEFORE grade-based check because it's more specific!
        hybrid_constraint_pattern = r"(?:harus\s+satu|satu\s+kelompok|satu\s+grup|harus\s+satu\s+kelompok)"
        if any(verb in prompt_lower for verb in create_verbs) and re.search(hybrid_constraint_pattern, prompt_lower) and any(term in prompt_lower for term in group_terms):
            plan = {"action": "create_group_hybrid"}
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → create_group_hybrid ✓ (constraint PRIORITY)")
            state["plan"] = plan
            return state

        # CHECK FOR GRADE-BASED GROUPING (before generic create_group)
        # This comes AFTER hybrid check to allow hybrid to take priority
        grade_keywords = ["berdasarkan nilai", "by grades", "nilai", "rata-rata nilai", "average grade", "grade-based", "berdasarkan nilai"]
        if ("buat" in prompt_lower or "generate" in prompt_lower or "bagi" in prompt_lower) and any(kw in prompt_lower for kw in grade_keywords) and any(term in prompt_lower for term in ["kelompok", "grup", "group"]):
            plan = {"action": "create_group_by_grades"}
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → create_group_by_grades ✓ (grade-based)")
            state["plan"] = plan
            return state

        if re.search(create_pattern, prompt_lower) and not any(term in prompt_lower for term in query_only_terms):
            plan = {"action": "create_group"}
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → create_group ✓ (verb heuristic)")
            state["plan"] = plan
            return state

        if any(verb in prompt_lower for verb in create_verbs) and any(term in prompt_lower for term in group_terms):
            plan = {"action": "create_group"}
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → create_group ✓ (fallback heuristic)")
            state["plan"] = plan
            return state

        if "n+1" in prompt_lower and "kelompok" in prompt_lower:
            plan = {"action": "create_group"}
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → create_group ✓ (n+1 pattern)")
            state["plan"] = plan
            return state
        
        # KEYWORD MATCHING - Deterministic routing (lebih reliable dari LLM)
        # PENTING: Order matters - check lebih specific patterns dulu (create_group recreate patterns)
        routing_rules = {
            "create_group_by_grades": ["buat kelompok berdasarkan nilai", "kelompok berdasarkan nilai", "grouping nilai", "kelompok nilai", "generate kelompok nilai", "buat kelompok nilai"],
            "create_group": ["buat kelompok", "bagi kelompok", "generate grup", "generate kelompok", "kelompokkan", "pembagian kelompok", "acak kembali", "acak ulang", "buat ulang", "ganti kelompok", "tambahkan ke kelompok", "kelompok n+1", "kelompok berikutnya"],
            "delete_kelompok": ["hapus kelompok", "delete kelompok", "kosongkan kelompok"],
            "check_kelompok": ["cek kelompok", "cek data kelompok", "sudah ada kelompok", "apakah ada kelompok", "status kelompok"],
            "query_pembimbing": ["dosen pembimbing", "daftar dosen pembimbing", "list dosen pembimbing", "pembimbing", "advisor", "list pembimbing", "siapa pembimbing", "kelompok belum punya pembimbing", "kelompok dengan 2 pembimbing", "kelompok dengan 1 pembimbing"],
            "query_dosen": ["dosen", "daftar dosen", "list dosen", "siapa dosen"],
            "query_dosen_role": ["role dosen", "dosen role", "peran dosen", "hak akses dosen"],
            "query_mahasiswa": ["mahasiswa", "siswa", "daftar mahasiswa", "list mahasiswa", "siapa mahasiswa", "nim"],
            "query_kelompok": ["daftar kelompok", "list kelompok", "data kelompok"],
            "query_matakuliah": ["mata kuliah", "matakuliah", "mk", "course", "daftar mk", "list mk"],
            "query_prodi": ["prodi", "program studi", "jurusan", "daftar prodi", "list prodi"],
            "query_roles": ["daftar role", "list role", "master role", "roles sistem"],
            "query_kategori_pa": ["kategori pa", "kategori proyek akhir", "pa-1", "pa-2", "pa-3"],
            "query_tahun_ajaran": ["tahun ajaran", "tahun akademik"],
            "query_ruangan": ["ruangan", "daftar ruangan", "room"],
            "query_jadwal": ["jadwal", "jadwal sidang", "jadwal bimbingan", "schedule"],
            "query_penguji": ["penguji", "list penguji", "siapa penguji", "daftar penguji"],
            "generate_pembimbing": ["generate pembimbing", "buat pembimbing", "assign pembimbing", "atur pembimbing", "pembagian pembimbing"],
            "check_pembimbing": ["sudah ada pembimbing", "apakah sudah ada pembimbing", "cek pembimbing"],
            "generate_penguji": ["generate penguji", "buat penguji", "assign penguji", "atur penguji", "pembagian penguji"],
            "check_penguji": ["sudah ada penguji", "apakah sudah ada penguji", "cek penguji"],
            "generate_jadwal": ["buat jadwal", "generate jadwal", "jadwal seminar", "buat seminar", "atur jadwal"],
            "save_jadwal": ["simpan jadwal", "save jadwal", "ok simpan", "ya simpan", "selesai", "konfirmasi", "yes", "ok"],
            "query_nilai": ["nilai", "grade", "ipk", "skor"],
            "generate_excel": ["buat excel", "export excel", "spreadsheet", "buatkan excel", "buat spreadsheet", "export ke excel", "unduh data excel"],
        }
        
        # Check keyword match
        matched_action = None
        for action, keywords in routing_rules.items():
            for keyword in keywords:
                if keyword in prompt_lower:
                    matched_action = action
                    break
            if matched_action:
                break
        
        # Jika ada keyword match, langsung gunakan itu
        if matched_action:
            plan = {"action": matched_action}
            logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → {matched_action} ✓")
            state["plan"] = plan
            return state
        
        # Jika tidak match keyword, panggil LLM
        logger.info(f"[{user_id}] 📋 PLANNER: '{prompt[:50]}...' → LLM (no keyword match)")
        
        # Load semantic memory untuk context
        semantic_memory = SemanticMemory(user_id)
        context_summary = semantic_memory.get_context_summary()
        
        # Get dosen context
        dosen_context = state.get("context", {}).get("dosen_context", [])
        role = dosen_context[0].get("role", "User") if dosen_context else "User"
        prodi = dosen_context[0].get("prodi", "Unknown") if dosen_context else "Unknown"
        kategori_pa = dosen_context[0].get("kategori_pa", "Unknown") if dosen_context else "Unknown"
        
        # Initialize long-term memory
        long_term_memory = LongTermMemory(user_id, LONG_TERM_STORE)
        
        # Generate model-aware context
        model_context = get_model_awareness_context(role, prodi, kategori_pa)

        system_prompt = f"""
Kamu adalah AI Router - tentukan action untuk query.

## ROUTING RULES:

- Jika ada kata: cek kelompok, status kelompok, sudah ada kelompok → check_kelompok
- Jika ada kata: hapus kelompok, delete kelompok, reset kelompok → delete_kelompok
- Jika ada kata: dosen, daftar dosen → query_dosen
- Jika ada kata: role dosen, peran dosen → query_dosen_role
- Jika ada kata: mahasiswa, siswa, nim → query_mahasiswa
- Jika ada kata: kelompok, group → query_kelompok
- Jika ada kata: mata kuliah, matakuliah, mk → query_matakuliah 
- Jika ada kata: prodi, program studi → query_prodi
- Jika ada kata: daftar role, list role, roles sistem → query_roles
- Jika ada kata: kategori pa, pa-1, pa-2, pa-3 → query_kategori_pa
- Jika ada kata: tahun ajaran, tahun akademik → query_tahun_ajaran
- Jika ada kata: ruangan, room → query_ruangan
- Jika ada kata: jadwal, schedule, jadwal sidang, jadwal bimbingan → query_jadwal
- Jika ada kata: pembimbing, advisor → query_pembimbing
- Jika ada kata: penguji, daftar penguji → query_penguji
- Jika ada kata: generate pembimbing, assign pembimbing, buat pembimbing → generate_pembimbing
- Jika ada kata: sudah ada pembimbing, cek pembimbing → check_pembimbing
- Jika ada kata: generate penguji, assign penguji, buat penguji → generate_penguji
- Jika ada kata: sudah ada penguji, cek penguji → check_penguji
- Jika ada kata: buat jadwal, jadwal seminar, buat seminar → generate_jadwal
- Jika ada kata: nilai, grade, ipk → query_nilai
- Jika ada kata: NIM1,NIM2 satu kelompok + nilai → create_group_hybrid
- Jika ada kata: buat kelompok berdasarkan nilai, kelompok nilai → create_group_by_grades
- Jika ada kata: buat kelompok, grouping → create_group
- Jika ada kata: buat excel, export excel, spreadsheet → generate_excel
- Lainnya: chat

## OUTPUT (JSON ONLY):
{{"action": "check_kelompok"|"delete_kelompok"|"query_dosen"|"query_dosen_role"|"query_mahasiswa"|"query_kelompok"|"query_anggota_kelompok"|"query_matakuliah"|"query_prodi"|"query_roles"|"query_kategori_pa"|"query_tahun_ajaran"|"query_ruangan"|"query_jadwal"|"query_nilai"|"query_pembimbing"|"query_penguji"|"generate_pembimbing"|"check_pembimbing"|"generate_penguji"|"check_penguji"|"generate_jadwal"|"save_jadwal"|"create_group"|"create_group_hybrid"|"create_group_by_grades"|"generate_excel"|"chat"}}
"""
        
        planner_messages = [{
            "role": "user",
            "content": prompt
        }]

        response = call_llm(
            planner_messages,
            system_prompt=system_prompt,
            context=context_summary
        )
        
        try:
            plan = json.loads(response)
        except json.JSONDecodeError:
            plan = {"action": "chat"}
        
        # Save query fact
        long_term_memory.save_fact(
            "query_history",
            prompt,
            {"action": plan.get("action")}
        )

        state["plan"] = plan
        return state
        
    except Exception as e:
        logger.error(f"[{state.get('user_id', 'unknown')}] ❌ ERROR IN PLANNER_NODE")
        logger.error(f"Traceback:\n{traceback.format_exc()}")
        raise