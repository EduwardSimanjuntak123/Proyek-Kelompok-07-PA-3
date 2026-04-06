"""
LLM-based Parser untuk Natural Language User Requests
Menggunakan OpenAI GPT untuk extract structured information dari prompt user
"""

from openai import OpenAI
from app.config import OPENAI_API_KEY
import json
import re

client = OpenAI(api_key=OPENAI_API_KEY)


# =========================
# DOSEN QUERY DETECTION
# =========================
def detect_dosen_query(prompt):
    """Detect jika user menanyakan tentang dosen (informasi saja, bukan assignment)"""
    
    dosen_keywords = [
        "dosen", "profesor", "pengajar", "instruktur", 
        "siapa dosen", "dosen siapa", "list dosen", "daftar dosen",
        "guru"
    ]
    
    # Keywords yang menandakan ini adalah PEMBIMBING assignment (bukan dosen info query)
    pembimbing_keywords = ["Buatkan pembimbing", "assign pembimbing", "tugaskan pembimbing", "tetapkan pembimbing"]
    
    prompt_lower = prompt.lower().strip()
    
    # Jika ada keyword dosen, check apakah ini assignment atau query info
    has_dosen = any(keyword in prompt_lower for keyword in dosen_keywords)
    
    if has_dosen:
        # Check if asking for pembimbing assignment specifically
        for keyword in pembimbing_keywords:
            if keyword in prompt_lower:
                return False  # This is pembimbing command, not dosen info query
        
        # If has dosen keyword but no pembimbing keyword, it's a dosen query
        return True
    
    return False


def parse_dosen_query(prompt, context=None):
    """Parse query dosen untuk extract informasi"""
    
    # If context has prodi_id and no prodi_id extracted, use context
    if context:
        if hasattr(context, 'prodi_id'):
            prodi_id = context.prodi_id
        elif isinstance(context, dict) and 'prodi_id' in context:
            prodi_id = context.get('prodi_id')
        else:
            prodi_id = None
    else:
        prodi_id = None
    
    return {
        "action": "list_dosen_current",
        "prodi_id": prodi_id,
        "prodi_name": None
    }


def detect_question_vs_command(prompt):
    """Detect apakah prompt adalah PERTANYAAN atau INSTRUKSI/COMMAND menggunakan LLM
    
    Questions: "apakah kamu bisa buat kelompok?", "bisa buat kelompok?", "apa itu kelompok?"
    Commands: "buat kelompok", "buat 5 kelompok", "acak kelompok"
    
    Menggunakan LLM untuk lebih robust terhadap typo dan variasi bahasa Indonesia
    
    Returns: "question" atau "command"
    """
    
    system_prompt = """Anda adalah classifier untuk natural language query.
Tugas: Classify apakah user's prompt adalah PERTANYAAN (question) atau INSTRUKSI/PERINTAH (command).

PERTANYAAN (question):
- Asking for information or capability: "bisa buat kelompok?", "apakah Anda bisa...", "berapa jumlah mahasiswa?"
- Asking for clarification: "apa itu?", "bagaimana cara nya?", "maksudnya apa?"
- Asking for permission: "boleh ganti kelompok?", "bisa lihat kelompok saya?"
- Usually ends with ? mark

INSTRUKSI/PERINTAH (command):
- Direct instruction to do something: "buat 5 kelompok", "bagi menjadi 7 orang", "acak ulang"
- Modal verbs imperative: "kelompokkan mereka", "ubah distribusi", "tampilkan hasil"
- Action-oriented statements
- No question mark, or has question mark but still imperative

Classify the following prompt:
"""

    try:
        response = client.chat.completions.create(
            model="gpt-4o-mini",
            messages=[
                {
                    "role": "system",
                    "content": system_prompt
                },
                {
                    "role": "user",
                    "content": f"Prompt: \"{prompt}\"\n\nClassify sebagai QUESTION atau COMMAND. Reply hanya dengan satu kata: QUESTION atau COMMAND"
                }
            ],
            temperature=0.1,
            max_tokens=10
        )
        
        response_text = response.choices[0].message.content.strip().upper()
        
        if "QUESTION" in response_text:
            return "question"
        elif "COMMAND" in response_text:
            return "command"
        else:
            # Fallback ke pattern-based detection
            return _detect_question_fallback(prompt)
        
    except Exception as e:
        print(f"[ERROR] LLM classification failed: {e}, falling back to pattern detection")
        return _detect_question_fallback(prompt)


def _detect_question_fallback(prompt):
    """Fallback pattern-based detection jika LLM gagal"""
    prompt_lower = prompt.lower().strip()
    
    # Strong question indicators (priority)
    strong_question_patterns = [
        ("?", 3),  # Question mark = strong question indicator
        ("apakah", 2),
        ("apa ", 2),
        ("berapa", 2),
        ("bagaimana", 2),
        ("mengapa", 2),
        ("siapa", 2),
        ("kapan", 2),
        ("dimana", 2),
        ("bisa gak", 2),
        ("bisa ka", 2),
        ("boleh gak", 2),
        ("boleh ka", 2),
    ]
    
    # Strong command indicators
    strong_command_patterns = [
        ("buat ", 2),
        ("bagi ", 2),
        ("acak ", 2),
        ("ubah ", 2),
        ("kelompokkan", 2),
        ("tampilkan", 2),
        ("lihat ", 1),  # lebih lemah karena bisa question
    ]
    
    question_score = 0
    command_score = 0
    
    for pattern, score in strong_question_patterns:
        if pattern in prompt_lower:
            question_score += score
    
    for pattern, score in strong_command_patterns:
        if pattern in prompt_lower:
            command_score += score
    
    # Decision
    if question_score > command_score:
        return "question"
    elif command_score > question_score:
        return "command"
    else:
        # Default: jika ada ? assume question, otherwise command
        return "question" if "?" in prompt else "command"


def parse_grouping_request(prompt, user_id=None):
    """
    Parse user request menggunakan LLM untuk extract:
    - action: create_group, view_group, modify_group
    - num_groups: jumlah kelompok yang diinginkan
    - group_size: jumlah orang per kelompok
    - must_pairs: pasangan yang HARUS satu kelompok
    - avoid_pairs: pasangan yang JANGAN satu kelompok
    - shuffle: acak atau tidak
    - show_scores: tampilkan nilai atau tidak
    - grouping_strategy: balanced, career-based, score-based, constraint-based
    - other_constraints: constraint lainnya
    
    Returns: dict dengan structured information
    """
    
    system_prompt = """Anda adalah expert parser untuk permintaan pengelompokan mahasiswa.
Tugas Anda adalah extract informasi terstruktur dari prompt user.

CONTOH PARSING:

INPUT: "buat kelompok acak 6 orang perkelompok tantri dan revi harus satu kelompok, yohana dan agus tidak boleh satu kelompok berdasarkan nilai"
OUTPUT: {
    "action": "create_group",
    "group_size": 6,
    "num_groups": null,
    "must_pairs": [["Tantri", "Revi"]],
    "avoid_pairs": [["Yohana", "Agus"]],
    "shuffle": true,
    "show_scores": true,
    "grouping_strategy": "score-based",
    "requirements": ["6 orang perkelompok", "acak", "berdasarkan nilai"]
}

INPUT: "buat revi dan malino satu kelompok, mei dan sahat satu kelompok, frans dan denny satu kelompok diemut acak"
OUTPUT: {
    "action": "create_group",
    "group_size": null,
    "num_groups": null,
    "must_pairs": [["Revi", "Malino"], ["Mei", "Sahat"], ["Frans", "Denny"]],
    "avoid_pairs": [],
    "shuffle": true,
    "show_scores": false,
    "grouping_strategy": "constraint-based",
    "requirements": ["harus acak"]
}

INPUT: "buat kelompok untuk 23 mahasiswa, backend dan frontend harus seimbang, jangan kelompokkan dua backend"
OUTPUT: {
    "action": "create_group",
    "group_size": null,
    "num_groups": null,
    "must_pairs": [],
    "avoid_pairs": [["Backend", "Backend"]],
    "shuffle": false,
    "show_scores": false,
    "grouping_strategy": "career-balanced",
    "requirements": ["career balance", "backend dan frontend seimbang"]
}

INPUT: "tampilkan kelompok 1"
OUTPUT: {
    "action": "view_group",
    "group_number": 1,
    "show_scores": false
}

INSTRUKSI PARSING:
1. Extract ACTION (create_group, view_group, modify_group)
2. Extract GROUP_SIZE dari pattern "N orang perkelompok" atau "N orang setiap kelompok"
3. Extract NUM_GROUPS jika disebutkan (contoh: "22 mahasiswa / 6 = 4 kelompok")
4. Extract MUST_PAIRS (dari pattern "X dan Y satu kelompok" atau "X dengan Y")
5. Extract AVOID_PAIRS (dari pattern "jangan X dan Y", "X dan Y tidak boleh")
6. Extract SHUFFLE dari keyword: acak, random, shuffle, diacak
7. Extract SHOW_SCORES dari keyword: nilai, score, ranking, berdasarkan nilai, menurut nilai
8. Detect GROUPING_STRATEGY dengan PRIORITAS: 
   - score-based: ada keyword "berdasarkan nilai", "berdasarkan score", "menurut nilai"
   - career-balanced: ada keyword karir/backend/frontend/expertise
   - constraint-based: ada must_pairs atau avoid_pairs
   - random: ada keyword acak/random tanpa constraint
   - balanced: default

PENTING:
- GROUP_SIZE harus integer (bukan null) jika tersebut pattern "N orang"
- Nama student HARUS capitalize dengan benar sesuai prompt
- Pairs yang sama tidak duplikat
- Score-based HARUS set ketika "berdasarkan nilai" atau "menurut nilai" ada di prompt
- Return valid JSON tanpa markdown
"""

    try:
        response = client.chat.completions.create(
            model="gpt-4o-mini",
            messages=[
                {
                    "role": "system",
                    "content": system_prompt
                },
                {
                    "role": "user",
                    "content": f"Parse ini: {prompt}"
                }
            ],
            temperature=0.3,
            response_format={"type": "json_object"}
        )
        
        # Clean response
        response_text = response.choices[0].message.content
        if "```" in response_text:
            response_text = response_text.replace("```json", "").replace("```", "").strip()
        
        parsed = json.loads(response_text)
        
        # Validate parsed output
        parsed = validate_parsed_output(parsed)
        
        return parsed
        
    except Exception as e:
        print(f"[ERROR] LLM parsing failed: {e}")
        return {
            "action": "unknown",
            "error": str(e)
        }


def extract_group_size_from_requirements(requirements):
    """Extract group size dari requirements list"""
    import re
    
    if not requirements or not isinstance(requirements, list):
        return None
    
    for req in requirements:
        if isinstance(req, str):
            # Patterns: "5 orang", "5 orang per kelompok", "5 orang setiap kelompok", dll
            match = re.search(r'(\d+)\s+(?:orang|person|people)', req, re.IGNORECASE)
            if match:
                return int(match.group(1))
    
    return None


def validate_parsed_output(parsed):
    """Validate dan clean parsed output dari LLM"""
    
    # Ensure required fields
    if "action" not in parsed:
        parsed["action"] = "unknown"
    
    if "num_groups" not in parsed:
        parsed["num_groups"] = None
    
    if "must_pairs" not in parsed:
        parsed["must_pairs"] = []
    elif not isinstance(parsed["must_pairs"], list):
        parsed["must_pairs"] = []
    
    if "avoid_pairs" not in parsed:
        parsed["avoid_pairs"] = []
    elif not isinstance(parsed["avoid_pairs"], list):
        parsed["avoid_pairs"] = []
    
    if "shuffle" not in parsed:
        parsed["shuffle"] = False
    
    if "show_scores" not in parsed:
        parsed["show_scores"] = False
    
    if "grouping_strategy" not in parsed:
        parsed["grouping_strategy"] = "balanced"
    
    # Extract group_size dari requirements jika ada
    if "requirements" in parsed and isinstance(parsed["requirements"], list):
        group_size = extract_group_size_from_requirements(parsed["requirements"])
        if group_size:
            parsed["group_size"] = group_size
    
    if "group_size" not in parsed:
        parsed["group_size"] = None
    
    # Remove duplicates from pairs
    must_pairs_set = set()
    for pair in parsed.get("must_pairs", []):
        if isinstance(pair, list) and len(pair) == 2:
            # Normalize: sort names untuk consistency
            normalized = tuple(sorted([str(p).strip() for p in pair]))
            must_pairs_set.add(normalized)
    parsed["must_pairs"] = [list(p) for p in must_pairs_set]
    
    avoid_pairs_set = set()
    for pair in parsed.get("avoid_pairs", []):
        if isinstance(pair, list) and len(pair) == 2:
            normalized = tuple(sorted([str(p).strip() for p in pair]))
            avoid_pairs_set.add(normalized)
    parsed["avoid_pairs"] = [list(p) for p in avoid_pairs_set]
    
    return parsed


def extract_detailed_requirements(parsed_output):
    """Extract additional requirements dari parsed output"""
    
    requirements = {}
    
    # Strategy-specific requirements
    if parsed_output.get("grouping_strategy") == "career-balanced":
        requirements["balance_careers"] = True
    
    if parsed_output.get("shuffle"):
        requirements["randomize"] = True


# =========================
# PEMBIMBING COMMAND DETECTION
# =========================

def detect_pembimbing_command(prompt):
    """Detect jika user menanyakan tentang pembimbing (lecturer assignment)"""
    
    pembimbing_keywords = [
        "pembimbing",
        "buat pembimbing",
        "buat penugasan",
        "assign dosen",
        "berikan pembimbing",
        "siapa pembimbing",
        "pembimbing siapa",
        "ubah pembimbing",
        "edit pembimbing",
        "tampilkan kelompok dan pembimbing",
        "lihat kelompok dan pembimbing",
        "tampilkan pembimbing",
        "lihat pembimbing",
        "dosen pembimbing",
        "profesor",
        "doktor",
        "master",
        "dr.",
        "prof.",
        "jabatan",
        "rank dosen"
    ]
    
    prompt_lower = prompt.lower().strip()
    
    # Check keyswords
    for keyword in pembimbing_keywords:
        if keyword in prompt_lower:
            return True
    
    # Additional check: jika ada "buatkan/buat/assign" + kombinasi "dosen" atau "pengajar" atau "pembimbing"
    # TAPI: jika ada "daftar" atau "list", ini bukan pembimbing command, tapi dosen info query
    
    # Early exit: jika ada "daftar" atau "list", ini bukan pembimbing assignment
    if "daftar" in prompt_lower or "list" in prompt_lower:
        return False
    
    action_keywords = ["buat", "buatkan", "buatan", "assign", "berikan", "tetapkan", "gunakan"]
    role_keywords = ["dosen", "pengajar", "pembimbing", "advisor"]
    
    has_action = any(action_kw in prompt_lower for action_kw in action_keywords)
    has_role = any(role_kw in prompt_lower for role_kw in role_keywords)
    
    # Jika ada action + role keywords, ini likely pembimbing command
    if has_action and has_role:
        return True
    
    return False


def parse_pembimbing_command(prompt, context=None):
    """Parse pembimbing command untuk extract aksi yang diinginkan
    
    Returns: {
        "action": "auto_assign" | "view_group_pembimbing" | "view_all_pembimbing" | "edit_pembimbing",
        "kelompok_id": int (optional),
        "kelompok_number": str (optional),
        "prodi_id": int,
        "kpa_id": int,
        "tm_id": int
    }
    """
    
    # Extract context values
    if context:
        if hasattr(context, 'prodi_id'):
            prodi_id = context.prodi_id
            kpa_id = context.kategori_pa
            tm_id = context.angkatan  # angkatan is the year/ID from DosenContext
        elif isinstance(context, dict):
            prodi_id = context.get('prodi_id')
            kpa_id = context.get('kategori_pa') or context.get('kpa_id')
            tm_id = context.get('angkatan') or context.get('tahun_masuk') or context.get('tm_id')
        else:
            prodi_id, kpa_id, tm_id = None, None, None
    else:
        prodi_id, kpa_id, tm_id = None, None, None
    
    prompt_lower = prompt.lower().strip()
    
    # Determine action
    action = "view_all_pembimbing"  # default
    
    if "buat" in prompt_lower or "assign" in prompt_lower or "berikan" in prompt_lower:
        action = "auto_assign"
    elif ("tampilkan kelompok" in prompt_lower or "lihat kelompok" in prompt_lower) and ("pembimbing" in prompt_lower):
        action = "view_all_pembimbing"
    elif ("ubah" in prompt_lower or "edit" in prompt_lower) and "pembimbing" in prompt_lower:
        action = "edit_pembimbing"
    elif "siapa pembimbing" in prompt_lower or "pembimbing siapa" in prompt_lower:
        # Extract group number from prompt (e.g., "siapa pembimbing kelompok 5")
        import re
        match = re.search(r'kelompok\s+(\d+)', prompt_lower)
        if match:
            action = "view_group_pembimbing"
    
    result = {
        "action": action,
        "prodi_id": prodi_id,
        "kpa_id": kpa_id,
        "tm_id": tm_id,
        "kelompok_number": None,
        "kelompok_id": None,
        "jabatan_filter": None  # For filtering pembimbing by academic rank
    }
    
    # Extract jabatan/rank filter if mentioned in prompt
    jabatan_keywords = {
        "profesor": "Prof",
        "prof": "Prof",
        "doktor": "Dr",
        "dr": "Dr",
        "master": "M.T",
        "m.t": "M.T",
        "engineer": "S.T",
        "s.t": "S.T",
        "s.kom": "S.Kom",
        "s.si": "S.Si",
        "senior": "Dr",  # Assuming senior means doctor or Professor
        "junior": "S.T"  # Assuming junior means bachelor
    }
    
    for keyword, rank_code in jabatan_keywords.items():
        if keyword in prompt_lower:
            result["jabatan_filter"] = rank_code
            break
    
    # Try to extract group number/ID from prompt
    import re
    match = re.search(r'kelompok\s+(\d+)', prompt_lower)
    if match:
        result['kelompok_number'] = int(match.group(1))
    
    return result


# =========================
# ADDITIONAL DETECTION FUNCTIONS
# =========================

def detect_greeting_keywords(prompt):
    """Detect apakah user hanya menyapa/greeting"""
    greeting_keywords = [
        "halo", "hello", "hi", "hai", "pagi", "siang", "sore", "malam",
        "assalamualaikum", "assalamu", "apa kabar", "kabar apa",
        "gimana", "bagaimana", "siapa aku", "siapa saya", "siapa nama saya",
        "nama saya", "nama aku", "identitas", "biodata"
    ]
    prompt_lower = prompt.lower().strip()
    
    # Check if prompt matches greeting keywords
    for keyword in greeting_keywords:
        # Exact match or as whole word (not part of another word)
        if keyword in prompt_lower:
            # Allow some short follow-up words with greeting
            words = prompt_lower.split()
            # If prompt is just greeting keywords (1-3 words), it's a greeting
            if len(words) <= 3:
                return True
            # If more than 3 words, check if it's still just greeting + punctuation
            # e.g. "halo, apa kabar?" (4 words including punctuation)
            if len(words) <= 4 and any(keyword in " ".join(words) for keyword in greeting_keywords):
                continue
    
    return False


def detect_view_existing_groups(prompt):
    """Detect apakah user ingin lihat kelompok yang sudah ada"""
    view_keywords = [
        # Basic view commands
        "lihat kelompok", "lihat group", "lihat grup", "kelompok saya",
        "kelompok apa", "kelompok siapa", "anggota kelompok", "siapa saja di kelompok",
        "show group", "show kelompok", "display group", "current group", "sekarang",
        "tampilkan kelompok", "berikan daftar kelompok", "daftar kelompok", "berikan kelompok",
        "tampilkan kelompok lama", "kelompok lama",
        # NEW: Additional variations
        "kelompok yang sudah ada",           # tampilkan kelompok yang sudah ada
        "kelompok sudah ada",                # tampilkan kelompok sudah ada
        "sudah ada kelompok",                # sudah ada kelompok yang dibuat
        "kelompok saat ini",                 # tampilkan kelompok saat ini
        "kelompok sekarang",                 # tampilkan kelompok sekarang
        "kelompok sekarang ini",             # tampilkan kelompok sekarang ini
        "kelompok di database",              # tampilkan kelompok di database
        "kelompok di sistem",                # tampilkan kelompok di sistem
        "kelompok dalam database",           # tampilkan kelompok dalam database
        "lihat kelompok ada",                # lihat kelompok ada
        "tampilkan kelompok beserta",        # tampilkan kelompok beserta pembimbingnya
    ]
    prompt_lower = prompt.lower()
    return any(keyword in prompt_lower for keyword in view_keywords)


def extract_group_number(prompt):
    """Extract group number dari prompt seperti 'tampilkan anggota kelompok 1'"""
    import re
    
    # Pattern: "kelompok X" atau "group X" 
    patterns = [
        r'kelompok\s+(\d+)',
        r'group\s+(\d+)',
        r'grup\s+(\d+)',
        r'anggota\s+(?:kelompok|group|grup)\s+(\d+)',
        r'tampilkan\s+(?:anggota\s+)?(?:kelompok|group|grup)\s+(\d+)',
    ]
    
    prompt_lower = prompt.lower()
    
    for pattern in patterns:
        match = re.search(pattern, prompt_lower)
        if match:
            return int(match.group(1))
    
    return None


def detect_list_mahasiswa_request(prompt):
    """Detect apakah user ingin melihat daftar/list mahasiswa
    
    IMPORTANT: This should NOT match "berapa total/jumlah" patterns - those are COUNT queries
    """
    # Check if it's a count query first (more specific)
    count_patterns = ["berapa total", "berapa jumlah", "total berapa", "jumlah berapa", "ada berapa"]
    if any(pattern in prompt.lower() for pattern in count_patterns):
        return False  # This is a count query, not list
    
    # List keywords - WITHOUT counting patterns
    list_keywords = [
        "list mahasiswa", "daftar mahasiswa", "data mahasiswa",
        "show mahasiswa", "tampilkan mahasiswa", "lihat daftar mahasiswa",
        "semua mahasiswa", "lihat semua", "tampilkan semua"
    ]
    prompt_lower = prompt.lower()
    return any(keyword in prompt_lower for keyword in list_keywords)


def detect_count_mahasiswa_request(prompt):
    """Detect apakah user ingin menghitung/count mahasiswa
    
    Patterns that indicate COUNT/TOTAL request:
    - "berapa total mahasiswa?"
    - "jumlah mahasiswa berapa?"
    - "ada berapa mahasiswa?"
    - "total berapa orang?"
    """
    count_keywords = [
        "berapa total", "total berapa", "berapa jumlah", "jumlah berapa",
        "ada berapa", "count mahasiswa", "hitung mahasiswa",
        "total mahasiswa", "jumlah total", "banyak mahasiswa seluruh"
    ]
    prompt_lower = prompt.lower()
    return any(keyword in prompt_lower for keyword in count_keywords)


def detect_view_scores_request(prompt):
    """Detect apakah user ingin melihat nilai/scores mahasiswa
    
    PENTING: Jangan trigger jika user sedang membuat/merancang kelompok
    dengan constraint "berdasarkan nilai" - itu adalah GROUPING COMMAND, bukan VIEW SCORES
    """
    # Keywords untuk indicate score viewing
    scores_keywords = [
        "nilai", "nilai mahasiswa", "score", "ranking", "prestasi",
        "nilai rata-rata", "rata-rata nilai", "lihat nilai", "tampilkan nilai",
        "nilai berapa", "nilai siswa", "daftar nilai", "hasil nilai"
    ]
    
    # Keywords yang indicate group CREATION (override scores detection)
    group_creation_keywords = [
        "buatkan kelompok", "buat kelompok", "bagi kelompok",
        "buat", "bagi", "acak", "kelompokkan", "grouping",
        "rancang kelompok", "rancang pembagian"
    ]
    
    prompt_lower = prompt.lower()
    
    # Check jika ini GROUP CREATION command - jangan trigger scores detection
    if any(keyword in prompt_lower for keyword in group_creation_keywords):
        return False
    
    # Otherwise, check scores keywords
    return any(keyword in prompt_lower for keyword in scores_keywords)


def detect_score_based_grouping(prompt):
    """Detect apakah user ingin membuat kelompok BERDASARKAN NILAI/SCORE
    
    Patterns:
    - "buat kelompok berdasarkan nilai"
    - "buat kelompok berdasarkan score"
    - "kelompokkan berdasarkan nilai"
    - "grouping berdasarkan nilai"
    - "bagi kelompok menurut nilai"
    
    This is more specific than general grouping - user explicitly wants score-based
    """
    score_grouping_keywords = [
        "berdasarkan nilai", "berdasarkan score", 
        "menurut nilai", "menurut score",
        "sesuai nilai", "sesuai score"
    ]
    
    grouping_keywords = [
        "buat kelompok", "buat", "bagi kelompok", "bagi",
        "kelompokkan", "grouping", "rancang kelompok"
    ]
    
    prompt_lower = prompt.lower()
    
    # Must have BOTH score keywords AND grouping keywords
    has_score_keyword = any(kw in prompt_lower for kw in score_grouping_keywords)
    has_grouping_keyword = any(kw in prompt_lower for kw in grouping_keywords)
    
    return has_score_keyword and has_grouping_keyword


def detect_save_groups_request(prompt):
    """Detect apakah user ingin menyimpan/save kelompok"""
    import re
    
    # Keywords yang HANYA untuk save/confirm (bukan substring dari kata lain)
    # Menggunakan word boundaries (\b) untuk accuracy
    save_keywords = [
        r'\bsimpan\b',           # simpan
        r'\bsave\b',             # save
        r'\btetapkan\b',         # tetapkan
        r'\bterapkan\b',         # terapkan
        r'\bconfirm\b',          # confirm
        r'\b[ok]{2}\b',          # ok (exactly 'ok', not 'pok' or 'kok')
        r'\blanjut\b',           # lanjut
        r'\bnext\b',             # next
        r'\bselanjutnya\b',      # selanjutnya
        r'\bapply\b',            # apply
        r'\byes\b',              # yes
        r'\boke\b',              # oke
        r'\bbaik\b',             # baik (when used standalone)
        r'\biya\b',              # iya
        r'simpan\s+kelompok',    # simpan kelompok
        r'simpan\s+pembagian',   # simpan pembagian
    ]
    
    prompt_lower = prompt.lower()
    for pattern in save_keywords:
        if re.search(pattern, prompt_lower):
            # print(f"[DEBUG] detect_save_groups_request matched '{pattern}' in '{prompt_lower}'")
            return True
    return False


def detect_delete_groups_request(prompt):
    """Detect apakah user ingin menghapus/delete kelompok"""
    import re
    
    # Keywords untuk delete/cancel dengan word boundaries
    delete_keywords = [
        r'\bhapus\b',            # hapus
        r'\bdelete\b',           # delete
        r'\bbatal\b',            # batal
        r'\bcancel\b',           # cancel
        r'\breset\b',            # reset
        r'\bclear\b',            # clear
        r'\bremove\b',           # remove
        r'\bno\b',               # no (standalone)
        r'tidak\s+jadi',        # tidak jadi (phrase)
        r'jangan\s+disimpan',   # jangan disimpan
        r'batalkan\s+pembagian', # batalkan pembagian
        r'hapus\s+kelompok',     # hapus kelompok
        r'delete\s+kelompok',    # delete kelompok
        r'clear\s+kelompok',     # clear kelompok
        r'cancel\s+pembagian',   # cancel pembagian
    ]
    
    prompt_lower = prompt.lower()
    for pattern in delete_keywords:
        if re.search(pattern, prompt_lower):
            return True
    return False


# Test cases
if __name__ == "__main__":
    test_prompts = [
        "buat revi dan malino satu kelompok, mei dan sahat satu kelompok, frans dan denny satu kelompok, harus acak",
        "buat kelompok untuk 23 mahasiswa, backend dan frontend harus seimbang",
        "tampilkan kelompok 1",
        "ubah constraints: jangan kelompokkan adi dengan budi",
        "buat 4 kelompok, tampilkan nilainya, harus acak"
    ]
    
    print("=" * 70)
    print("LLM-BASED PARSER TEST")
    print("=" * 70)
    
# ========== NEW DETECTION FUNCTIONS FOR REQUESTS 1-4 ==========

def detect_check_groups_exist(prompt: str) -> bool:
    """
    Detect if user is asking whether any groups exist
    
    Patterns:
    - "apakah sudah ada kelompok?"
    - "ada kelompok?"
    - "sudah ada kelompok?"
    - "berapa banyak kelompok?"
    - "ada berapa kelompok?"
    - "berapa kelompok?"
    """
    keywords = [
        "ada", "sudah ada", "apakah", "berapa", "kelompok",
        "ada berapa", "sudah berapa", "berapa banyak"
    ]
    
    patterns = [
        r"(ada|sudah\s+ada|apakah\s+ada)\s+(kelompok|grup)",
        r"berapa\s+(banyak|jumlah|total)\s+(kelompok|grup)",
        r"(kelompok|grup)\s+berapa",
    ]
    
    prompt_lower = prompt.lower().strip()
    
    for pattern in patterns:
        if re.search(pattern, prompt_lower):
            return True
    
    return False


def detect_pembimbing_status_query(prompt: str) -> bool:
    """
    Detect if user is asking about pembimbing (lecturer) assignment status
    
    Patterns:
    - "apakah sudah ada pembimbing?"
    - "kelompok mana yang belum ada pembimbing?"
    - "siapa pembimbing kelompok 1?"
    - "kelompok mana yang memiliki 1 pembimbing?"
    - "kelompok mana yang memiliki 2 pembimbing?"
    - "tampilkan semua pembimbing"
    - "ada pembimbing berapa"
    """
    pembimbing_keywords = [
        "pembimbing", "dosen pembimbing", "sudah ada pembimbing",
        "kelompok mana", "siapa pembimbing", "berapa pembimbing",
        "tampilkan pembimbing", "lihat pembimbing", "status pembimbing",
        "coverage pembimbing"
    ]
    
    prompt_lower = prompt.lower().strip()
    
    for keyword in pembimbing_keywords:
        if keyword in prompt_lower:
            # Make sure it's not "assign pembimbing" or "buat pembimbing"
            if not any(action in prompt_lower for action in ["buat", "assign", "buatkan", "berikan"]):
                return True
    
    return False


def detect_dynamic_course_query(prompt: str) -> bool:
    """
    Detect if user is asking for courses in specific semester(s)
    
    Patterns:
    - "matakuliah semester 4"
    - "matkul semester 5"
    - "mata kuliah semester 4 dan 5"
    - "courses semester 4 dan 5"
    - "kuliah semester 4, 5"
    """
    course_keywords = [
        "matakuliah", "matkul", "mtkul", "mata kuliah", "course", "kuliah",
        "courses", "matkuliah"
    ]
    
    semester_patterns = [
        r"semester\s+[0-8]",  # semester 1-8
        r"[0-8]\s*(?:dan|,|\|)\s*[0-8]",  # multiple semesters with flexible spacing
        r"[0-8](?:\s*[,/]\s*[0-8])+",  # comma/slash separated list: 1,3,5 or 1/3/5
    ]
    
    prompt_lower = prompt.lower().strip()
    
    # Check if it has course keyword AND semester pattern
    has_course = any(kw in prompt_lower for kw in course_keywords)
    has_semester = any(re.search(pattern, prompt_lower) for pattern in semester_patterns)
    
    return has_course and has_semester


def parse_pembimbing_status_query(prompt: str, context=None) -> dict:
    """Parse pembimbing status query to extract what user is asking"""
    
    prompt_lower = prompt.lower().strip()
    
    # Determine the specific query type
    if any(word in prompt_lower for word in ["apakah", "ada", "sudah"]):
        query_type = "check_any_exist"
    elif any(word in prompt_lower for word in ["mana", "belum", "tanpa", "tidak ada"]):
        query_type = "groups_without"
    elif any(word in prompt_lower for word in ["siapa", "nama"]):
        query_type = "get_for_group"
    elif any(word in prompt_lower for word in ["memiliki", "ada", "punya"]):
        if "1" in prompt_lower or "satu" in prompt_lower:
            query_type = "groups_with_one"
        elif "2" in prompt_lower or "dua" in prompt_lower:
            query_type = "groups_with_two"
        else:
            query_type = "groups_by_count"
    elif any(word in prompt_lower for word in ["tampilkan", "lihat", "display", "show", "semua", "all"]):
        query_type = "list_all"
    else:
        query_type = "summary"
    
    # Extract group number if mentioned
    group_number = None
    group_match = re.search(r"kelompok\s+(\d+)", prompt_lower)
    if group_match:
        group_number = group_match.group(1)
    
    return {
        "query_type": query_type,  # check_any_exist, groups_without, get_for_group, groups_with_one, etc.
        "group_number": group_number,
        "pembimbing_count": None if query_type != "groups_by_count" else int(re.search(r"\d+", prompt_lower).group(0)) if re.search(r"\d+", prompt_lower) else None
    }


def parse_dynamic_course_query(prompt: str, context=None) -> dict:
    """Parse course query to extract semesters requested"""
    
    prompt_lower = prompt.lower().strip()
    
    # Find all semester numbers mentioned
    semesters = []
    for match in re.finditer(r"\b([1-8])\b", prompt_lower):
        sem = int(match.group(1))
        if sem not in semesters:
            semesters.append(sem)
    
    # Sort semesters
    semesters.sort()
    
    return {
        "semesters": semesters,
        "format": "grouped_by_semester" if len(semesters) > 1 else "single"
    }


if __name__ == "__main__":
    for prompt in test_prompts:
        print(f"\nINPUT: {prompt}")
        parsed = parse_grouping_request(prompt)
        print(f"OUTPUT: {json.dumps(parsed, indent=2)}")
