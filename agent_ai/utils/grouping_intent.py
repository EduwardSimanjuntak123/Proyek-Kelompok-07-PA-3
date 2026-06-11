from rapidfuzz import fuzz
import re

# ============================================================
# KEYWORDS
# ============================================================

GROUP_WORDS = [
    "kelompok",
    "group",
    "grup",
    "tim",
    "team",
]

GROUPING_PATTERNS = [
    "buat kelompok",
    "buatkan kelompok",
    "bentuk kelompok",
    "susun kelompok",
    "generate kelompok",
    "kelompokkan mahasiswa",
    "bagi mahasiswa",
    "buat grup",
    "buat tim",
    "susun tim",
    "susun grup",
]

# ============================================================
# LAYER 1 + 2
# Exact + Fuzzy Matching
# ============================================================

def detect_grouping_request(text: str) -> bool:
    """
    Deteksi cepat tanpa LLM.

    Menangani:
    - buatkan kelompok
    - buatkannn kelompok
    - buatkan kelompoook
    - buatkan ekelompoook
    - susun tim
    - buat grup
    """

    if not text:
        return False

    text = text.lower().strip()

    # --------------------------------------------------------
    # Layer 1 : Exact keyword
    # --------------------------------------------------------

    for word in GROUP_WORDS:
        if word in text:
            return True

    # --------------------------------------------------------
    # Layer 2A : Fuzzy seluruh kalimat
    # --------------------------------------------------------

    compact_text = text.replace(" ", "")

    for target in GROUP_WORDS:

        score = fuzz.partial_ratio(
            compact_text,
            target
        )

        if score >= 85:
            return True

    # --------------------------------------------------------
    # Layer 2B : Fuzzy per token
    # --------------------------------------------------------

    tokens = text.split()

    for token in tokens:
        for target in GROUP_WORDS:

            score = fuzz.ratio(
                token,
                target
            )

            if score >= 80:
                return True

    # --------------------------------------------------------
    # Layer 2C : Pattern matching
    # --------------------------------------------------------

    for pattern in GROUPING_PATTERNS:

        score = fuzz.partial_ratio(
            pattern,
            text
        )

        if score >= 85:
            return True

    return False


# ============================================================
# LAYER 3
# LLM Fallback
# ============================================================

async def detect_grouping_with_llm(
    text: str,
    llm
) -> bool:
    """
    Fallback ketika keyword/fuzzy tidak yakin.
    """

    prompt = f"""
Tentukan apakah pengguna ingin membuat kelompok mahasiswa.

Contoh YA:
- buatkan kelompok
- bantu saya membentuk kelompok
- susun mahasiswa menjadi beberapa tim
- bagi mahasiswa menjadi grup
- generate kelompok
- kelompokkan mahasiswa berikut

Contoh TIDAK:
- tampilkan data mahasiswa
- siapa ketua kelas
- berapa jumlah mahasiswa
- tampilkan nilai mahasiswa

User:
{text}

Jawab hanya:

YES

atau

NO
"""

    response = await llm.ainvoke(prompt)

    answer = (
        response.content
        .strip()
        .upper()
    )

    return answer.startswith("YES")


# ============================================================
# MAIN ENTRY
# ============================================================

async def is_grouping_intent(
    text: str,
    llm=None
) -> bool:
    """
    Layered detection:
    1. Exact match
    2. Fuzzy match
    3. LLM fallback
    """

    # Layer 1 + 2
    if detect_grouping_request(text):
        return True

    # Layer 3
    if llm:
        try:
            return await detect_grouping_with_llm(
                text,
                llm
            )

        except Exception as e:
            print(
                f"[GROUPING INTENT] LLM ERROR: {e}"
            )

    return False


def has_grouping_configuration(prompt: str) -> bool:
    prompt = prompt.lower()

    has_method = (
        "nilai" in prompt
        or "acak" in prompt
        or "random" in prompt
    )

    has_fixed_size = bool(
        re.search(r'(\d+)\s*(orang|anggota)', prompt)
    )

    has_range = (
        bool(re.search(r'minimal\s*\d+', prompt))
        or bool(re.search(r'maksimal\s*\d+', prompt))
    )

    return (
        (has_method and has_fixed_size)
        or (has_method and has_range)
        or has_fixed_size
    )