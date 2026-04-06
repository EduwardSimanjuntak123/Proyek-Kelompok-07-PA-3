"""
ADVANCED QUERY DETECTION FUNCTIONS

Detection untuk:
1. Student information queries (by nama, nim, group status, etc.)
2. Dosen information queries (by nama, jabatan, dll)
3. Kelompok status queries (pembimbing status, dll)
4. Student score/nilai queries
5. Matkul queries
6. Advanced manipulation (roker, tukar, ubah, hapus)
7. Multiple instructions parsing
"""

import re
from openai import OpenAI
from app.config import OPENAI_API_KEY

client = OpenAI(api_key=OPENAI_API_KEY)


# =========================
# 1. STUDENT INFORMATION QUERIES
# =========================

def detect_student_query(prompt):
    """Detect apakah user bertanya tentang mahasiswa tertentu (by nama atau nim)
    
    Examples:
    - "siapa itu ruli"
    - "nim mahasiswa ruli berapa"
    - "tunjukkan data mahasiswa dengan nim 123456"
    - "cari mahasiswa bernama adi"
    """
    
    keywords = [
        "siapa", "nama", "nim", "nim berapa", "berapa nim",
        "data mahasiswa", "profil mahasiswa", "informasi mahasiswa",
        "mahasiswa", "cari mahasiswa", "tunjukkan mahasiswa"
    ]
    
    prompt_lower = prompt.lower()
    has_keyword = any(kw in prompt_lower for kw in keywords)
    
    # Jangan ambil list/daftar requests
    if "list" in prompt_lower or "daftar" in prompt_lower or "tampilkan semua" in prompt_lower:
        return False
    
    return has_keyword and ("siapa" in prompt_lower or "nim" in prompt_lower or "nama" in prompt_lower or "cari" in prompt_lower or "data" in prompt_lower)


def parse_student_query(prompt):
    """Extract student name atau nim dari query
    
    Returns:
        dict dengan keys: search_type (name/nim), query_value, context
    """
    
    prompt_lower = prompt.lower()
    
    # Pattern: "nim X" atau "nim berapa"
    nim_patterns = [
        r'nim\s+(\d+)',           # nim 123456
        r'(\d{6,})',              # just 6+ digits  
        r'nim.*?(\w+)',           # nim <name>
    ]
    
    # Pattern: "nama X" atau "siapa X"
    name_patterns = [
        r'(?:siapa|nama|cari|data)\s+(?:itu|bernama)?\s*(\w+)',
        r'mahasiswa\s*(\w+)',
    ]
    
    # Check NIM patterns
    for pattern in nim_patterns:
        match = re.search(pattern, prompt_lower)
        if match:
            nim = match.group(1)
            if len(nim) >= 6 and nim.isdigit():
                return {
                    "type": "student_query",
                    "search_type": "nim",
                    "query_value": nim
                }
    
    # Check name patterns
    for pattern in name_patterns:
        match = re.search(pattern, prompt_lower)
        if match:
            name = match.group(1)
            return {
                "type": "student_query",
                "search_type": "name",
                "query_value": name
            }
    
    return {"type": "student_query", "search_type": "unknown", "query_value": None}


def detect_student_group_query(prompt):
    """Detect: "mahasiswa X kelompok berapa" atau sebaliknya
    
    Examples:
    - "ruli kelompok berapa"
    - "mahasiswa bernama adi ada di kelompok berapa"
    - "kelompok berapa itu ruli"
    """
    
    keywords = ["kelompok berapa", "di kelompok", "ada kelompok"]
    prompt_lower = prompt.lower()
    
    # Must have both "kelompok" dan nama/nim
    has_group_keyword = any(kw in prompt_lower for kw in keywords)
    has_student_identifier = ("nama" in prompt_lower or "siapa" in prompt_lower or 
                            re.search(r'\d{6,}', prompt_lower) is not None or
                            any(c.isupper() for c in prompt))  # Nama biasanya punya huruf besar
    
    return has_group_keyword and has_student_identifier


def parse_student_group_query(prompt):
    """Extract mahasiswa identifier dari query"""
    prompt_lower = prompt.lower()
    
    # Try to extract name
    name_match = re.search(r'(?:nama|siapa)\s+(?:itu|adalah)?\s*(\w+)', prompt_lower)
    if name_match:
        return {
            "type": "student_group_query",
            "student_type": "name",
            "student_value": name_match.group(1)
        }
    
    # Try to extract nim
    nim_match = re.search(r'nim\s+(\d+)', prompt_lower)
    if nim_match:
        return {
            "type": "student_group_query",
            "student_type": "nim",
            "student_value": nim_match.group(1)
        }
    
    # Try to extract from standalone word (assume it's a name)
    words = prompt.split()
    if len(words) > 0:
        # Get last significant word that might be a name
        for word in reversed(words):
            if len(word) > 2 and word.isalpha():
                return {
                    "type": "student_group_query",
                    "student_type": "name",
                    "student_value": word.lower()
                }
    
    return {"type": "student_group_query", "student_type": "unknown", "student_value": None}


def detect_student_score_query(prompt):
    """Detect: "nilai mahasiswa X" atau "berapa nilai mahasiswa X di matkul Y"
    
    Examples:
    - "nilai ruli berapa"
    - "nilai mahasiswa adi"
    - "nilai ruli semester 1"
    - "rata-rata nilai ruli"
    - "nilai ruli di pemrograman"
    
    PENTING: Jangan trigger jika user sedang membuat kelompok dengan constraint "berdasarkan nilai"
    """
    
    keywords = ["nilai", "score", "poin", "hasil", "prestasi", "rata-rata"]
    prompt_lower = prompt.lower()
    
    # Check jika ini GROUP CREATION command - jangan trigger student score detection
    group_creation_keywords = [
        "buatkan kelompok", "buat kelompok", "bagi kelompok",
        "buat", "bagi", "acak", "kelompokkan", "grouping"
    ]
    
    if any(keyword in prompt_lower for keyword in group_creation_keywords):
        return False
    
    has_score_keyword = any(kw in prompt_lower for kw in keywords)
    has_student = "mahasiswa" in prompt_lower or re.search(r'\w+\s+(?:nilai|score)', prompt_lower)
    
    return has_score_keyword and (has_student or re.search(r'\d{6,}', prompt_lower) is not None)


def detect_unscheduled_student_query(prompt):
    """Detect: "mahasiswa belum punya kelompok siapa" atau "siapa yang belum kelompok"
    
    Examples:
    - "mahasiswa belum punya kelompok siapa"
    - "siapa yang belum ada kelompok"
    - "mahasiswa mana yang tidak punya kelompok"
    - "list mahasiswa tanpa kelompok"
    """
    
    keywords = [
        "belum punya kelompok", "belum ada kelompok", "tidak punya kelompok",
        "tanpa kelompok", "belum kelompok", "termasuk siapa"
    ]
    
    prompt_lower = prompt.lower()
    return any(kw in prompt_lower for kw in keywords)


def parse_student_score_query(prompt):
    """Extract student name/nim and score query parameters"""
    
    import re
    
    # Extract student name or nim
    nim_patterns = [r'nim\s+(\d+)', r'(\d{6,})', r'nim.*?(\w+)']
    name_patterns = [r'(:?nilai)\s+(?:mahasiswa\s+)?(\w+)', r'(\w+)\s+(:?nilai)', r'(?:nilai|score)\s+(\w+)']
    
    student_value = None
    search_type = "name"
    
    # Check NIM first
    for pattern in nim_patterns:
        match = re.search(pattern, prompt, re.IGNORECASE)
        if match:
            student_value = match.group(1)
            search_type = "nim"
            break
    
    # If no NIM, check name
    if not student_value:
        for pattern in name_patterns:
            match = re.search(pattern, prompt, re.IGNORECASE)
            if match:
                # Get the non-keyword group
                for group in match.groups():
                    if group and group.lower() not in ['nilai', 'score', 'mahasiswa']:
                        student_value = group
                        search_type = "name"
                        break
                if student_value:
                    break
    
    # Extract semester if exists
    semester_match = re.search(r'semester\s+(\d+)', prompt, re.IGNORECASE)
    semester = semester_match.group(1) if semester_match else None
    
    # Extract matkul if exists
    matkul_match = re.search(r'(?:di|di\s+)?matkul\s+(\w+)', prompt, re.IGNORECASE)
    matkul = matkul_match.group(1) if matkul_match else None
    
    return {
        "type": "student_score_query",
        "search_type": search_type,
        "student_value": student_value or "unknown",
        "semester": semester,
        "matkul": matkul
    }


def parse_unscheduled_student_query(prompt):
    """Parse unscheduled student query"""
    
    return {
        "type": "unscheduled_student_query",
        "action": "find_unscheduled_students",
        "query": prompt
    }


# =========================
# 2. DOSEN INFORMATION QUERIES  
# =========================

def detect_dosen_detail_query(prompt):
    """Detect: "dosen X apa jabatannya" atau "siapa itu dosen X"
    
    Examples:
    - "jabatan dosen adi apa"
    - "apa jabatan dosen budi"
    - "dosen ruli mengajar apa"
    - "siapa itu dosen dengan nim..."
    """
    
    keywords = [
        "jabatan", "mengajar", "mengampu", "posisi", "peran",
        "siapa", "apa", "data dosen"
    ]
    
    prompt_lower = prompt.lower()
    
    has_dosen_keyword = "dosen" in prompt_lower or "pengajar" in prompt_lower
    has_query_keyword = any(kw in prompt_lower for kw in keywords)
    
    return has_dosen_keyword and has_query_keyword


def parse_dosen_detail_query(prompt):
    """Extract dosen name dan query type"""
    
    prompt_lower = prompt.lower()
    
    # Pattern untuk jabatan
    if "jabatan" in prompt_lower:
        name_match = re.search(r'dosen\s+(\w+)\s+(?:apa|jabatan)', prompt_lower)
        if name_match:
            return {
                "type": "dosen_detail_query",
                "query_type": "jabatan",
                "dosen_name": name_match.group(1)
            }
    
    # Pattern untuk mengajar/mengampu
    if "mengajar" in prompt_lower or "mengampu" in prompt_lower:
        name_match = re.search(r'dosen\s+(\w+)\s+(?:mengajar|mengampu)', prompt_lower)
        if name_match:
            return {
                "type": "dosen_detail_query",
                "query_type": "mengajar",
                "dosen_name": name_match.group(1)
            }
    
    # Generic dosen name extract
    name_match = re.search(r'dosen\s+(\w+)', prompt_lower)
    if name_match:
        return {
            "type": "dosen_detail_query",
            "query_type": "general_info",
            "dosen_name": name_match.group(1)
        }
    
    return {"type": "dosen_detail_query", "query_type": "unknown", "dosen_name": None}


# =========================
# 3. KELOMPOK STATUS QUERIES
# =========================

def detect_group_pembimbing_status_query(prompt):
    """Detect: "kelompok X belum punya pembimbing" atau query status pembimbing
    
    Examples:
    - "kelompok 1 belum punya pembimbing siapa"
    - "siapa yang belum punya pembimbing"
    - "kelompok mana yang belum punya pembimbing"
    - "pembimbing kelompok 1 siapa"
    """
    
    keywords = [
        "belum punya pembimbing", "tidak ada pembimbing", "pembimbing", "tanpa pembimbing"
    ]
    
    prompt_lower = prompt.lower()
    has_pembimbing_keyword = any(kw in prompt_lower for kw in keywords)
    has_group_reference = "kelompok" in prompt_lower
    
    return has_pembimbing_keyword or (has_group_reference and "pembimbing" in prompt_lower)


def parse_group_pembimbing_status_query(prompt):
    """Extract group number atau query type"""
    
    prompt_lower = prompt.lower()
    
    # Pattern: kelompok X
    group_match = re.search(r'kelompok\s+(\d+)', prompt_lower)
    group_number = group_match.group(1) if group_match else None
    
    # Query type
    query_type = "unassigned" if "belum" in prompt_lower or "tidak ada" in prompt_lower else "assigned"
    
    return {
        "type": "group_pembimbing_status_query",
        "group_number": group_number,
        "query_type": query_type
    }


# =========================
# 4. MATKUL INFORMATION
# =========================

def detect_student_matkul_query(prompt):
    """Detect: "matkul mahasiswa X semester Y" atau "mahasiswa X punya matkul apa"
    
    Examples:
    - "matkul mahasiswa ruli semester 1"
    - "ruli ambil mata kuliah apa semester 2"
    - "apa saja matkul di semester 1"
    - "mata kuliah apa yang diambil ruli"
    """
    
    keywords = [
        "matkul", "mata kuliah", "semester", "ambil", "punya",
        "mengambil", "kursus", "bidang studi"
    ]
    
    prompt_lower = prompt.lower()
    has_matkul_keyword = any(kw in prompt_lower for kw in keywords)
    has_student_ref = "mahasiswa" in prompt_lower or re.search(r'\w+\s+(?:semester|matkul|mata)', prompt_lower) is not None
    
    return has_matkul_keyword and (has_student_ref or "semester" in prompt_lower)


def parse_student_matkul_query(prompt):
    """Extract student name, semester, dan matkul name"""
    
    prompt_lower = prompt.lower()
    
    # Extract semesters (multiple or single)
    semesters = []
    for match in re.finditer(r"\b([1-8])\b", prompt_lower):
        sem = int(match.group(1))
        if sem not in semesters:
            semesters.append(sem)
    
    semesters.sort()
    semester = semesters[0] if semesters else None
    
    # Extract student name
    student_match = re.search(r'(?:mahasiswa|ruli|adi|budi)\s+(\w+)', prompt_lower)
    student_name = student_match.group(1) if student_match else None
    
    # Extract matkul name if specified
    matkul_match = re.search(r'(?:di|matkul)\s+(\w+\s*\w*)', prompt_lower)
    matkul_name = matkul_match.group(1) if matkul_match else None
    
    return {
        "type": "student_matkul_query",
        "student_name": student_name,
        "semester": semester,
        "matkul_name": matkul_name
    }


# =========================
# 5. MANIPULATION QUERIES
# =========================

def detect_student_manipulation(prompt):
    """Detect: roker, tukar, ubah, hapus mahasiswa dari kelompok
    
    Examples:
    - "roker mahasiswa kelompok 1 dan 2"
    - "tukar si ruli dengan adi"
    - "ubah mahasiswa ruli dari kelompok 1 ke 2"
    - "hapus ruli dari kelompok 1"
    - "pindahkan adi ke kelompok 3"
    - "tukar arizona dan edwin"
    """
    
    keywords = [
        "roker", "tukar", "ubah", "hapus", "pindahkan", "ganti",
        "move", "remove", "swap", "shuffle"
    ]
    
    prompt_lower = prompt.lower()
    has_manipulation_keyword = any(kw in prompt_lower for kw in keywords)
    
    # Either must have mahasiswa/kelompok OR have enough words for names (tukar name1 dan name2)
    has_student_context = "mahasiswa" in prompt_lower or "kelompok" in prompt_lower
    has_multiple_operands = len(prompt_lower.split()) >= 4  # e.g., "tukar arizona dan edwin" = 4 words
    
    return has_manipulation_keyword and (has_student_context or has_multiple_operands)


def parse_student_manipulation(prompt):
    """Extract operation type dan affected students"""
    
    prompt_lower = prompt.lower()
    
    # Determine operation type
    if "roker" in prompt_lower:
        operation = "roker"
    elif "tukar" in prompt_lower or "swap" in prompt_lower:
        operation = "swap"
    elif "hapus" in prompt_lower or "remove" in prompt_lower:
        operation = "remove"
    elif "pindahkan" in prompt_lower or "move" in prompt_lower:
        operation = "move"
    elif "ubah" in prompt_lower or "ganti" in prompt_lower:
        operation = "update"
    else:
        operation = "unknown"
    
    # Extract group numbers
    groups = re.findall(r'kelompok\s+(\d+)', prompt_lower)
    
    # Extract student names
    students = re.findall(r'(?:^|\s)([a-z]+)(?:\s|$)', prompt_lower)
    students = [s for s in students if len(s) > 2]  # Filter short words
    
    return {
        "type": "student_manipulation",
        "operation": operation,
        "groups": groups,
        "students": students
    }


def detect_pembimbing_manipulation(prompt):
    """Detect: roker, hapus, update pembimbing
    
    Examples:
    - "roker pembimbing kelompok 1"
    - "hapus pembimbing kelompok 1"
    - "update pembimbing kelompok 1 jadi dosen adi"
    - "ganti pembimbing kelompok 1 dengan dosen budi"
    """
    
    keywords = [
        "roker pembimbing", "hapus pembimbing", "update pembimbing",
        "ganti pembimbing", "tukar pembimbing"
    ]
    
    prompt_lower = prompt.lower()
    has_pembimbing_keyword = any(kw in prompt_lower for kw in keywords)
    has_group_ref = "kelompok" in prompt_lower
    
    return has_pembimbing_keyword and has_group_ref


def parse_pembimbing_manipulation(prompt):
    """Extract operation dan affected groups"""
    
    prompt_lower = prompt.lower()
    
    # Determine operation
    if "hapus" in prompt_lower or "remove" in prompt_lower:
        operation = "remove"
    elif "roker" in prompt_lower:
        operation = "roker"
    elif "update" in prompt_lower or "ganti" in prompt_lower or "tukar" in prompt_lower:
        operation = "update"
    else:
        operation = "unknown"
    
    # Extract group number
    group_match = re.search(r'kelompok\s+(\d+)', prompt_lower)
    group_number = group_match.group(1) if group_match else None
    
    # Extract new dosen if update
    dosen_match = re.search(r'(?:jadi|dengan|ke)\s+dosen\s+(\w+)', prompt_lower)
    new_dosen = dosen_match.group(1) if dosen_match else None
    
    return {
        "type": "pembimbing_manipulation",
        "operation": operation,
        "group_number": group_number,
        "new_dosen": new_dosen
    }


# =========================
# 6. MULTIPLE INSTRUCTIONS
# =========================

def detect_multiple_instructions(prompt):
    """Detect apakah user punya MULTIPLE distinct instructions dengan koneksi seperti 'juga', dll
    TIDAK termasuk 'dan' yang dipakai untuk combining operands (misal: tukar A dan B = 1 operasi)
    
    Examples:
    - "tampilkan kelompok 1 dan 2, juga pembimbingnya" (2 operations)
    - "hapus mahasiswa ruli , juga tambah adi ke kelompok" (2 operations)
    - "tampilkan nilai ruli, juga nama kelompoknya" (2 operations)
    
    NOT examples (single operation with 'dan' for operands):
    - "tukar arizona dan edwin" (1 swap operation)
    - "tampilkan kelompok 1 dan 2" (1 view operation)
    """
    
    # Only check for REAL multiple instruction connectors, NOT 'dan' for operands
    connectors = [", juga", " juga "]
    prompt_lower = prompt.lower()
    
    return any(con in prompt_lower for con in connectors)


def parse_multiple_instructions(prompt):
    """Split multiple instructions"""
    
    # Split by common connectors
    instructions = re.split(r'(?:,\s*juga|\s+dan\s+|\s+juga\b|;\s*|sekaligus|plus)', prompt, flags=re.IGNORECASE)
    instructions = [i.strip() for i in instructions if i.strip()]
    
    return {
        "type": "multiple_instructions",
        "instructions": instructions,
        "count": len(instructions)
    }


if __name__ == "__main__":
    # Test cases
    tests = [
        "siapa itu ruli",
        "ruli kelompok berapa",
        "nilai ruli semester 1",
        "siapa mahasiswa yang belum kelompok",
        "jabatan dosen adi apa",
        "kelompok 1 belum punya pembimbing siapa",
        "roker mahasiswa kelompok 1 dan 2",
        "tampilkan kelompok 1 dan 2 beserta pembimbingnya",
    ]
    
    for test in tests:
        print(f"\n✓ {test}")
        print(f"  is_student_query: {detect_student_query(test)}")
        print(f"  is_group_query: {detect_student_group_query(test)}")
        print(f"  is_score_query: {detect_student_score_query(test)}")
