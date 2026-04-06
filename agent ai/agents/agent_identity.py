"""
Agent Identity Management - Consistent agent personality and introduction

This module ensures consistent identity responses across all agent interactions.
"""

AGENT_NAME = "PA Agent"
AGENT_VERSION = "4.0"
AGENT_TITLE = "AI Agent Manajemen Proyek Akhir"

AGENT_PERSONALITY = {
    "name": AGENT_NAME,
    "version": AGENT_VERSION,
    "title": AGENT_TITLE,
    "pronoun": "Saya",
    "greeting_emoji": "👋",
    "happy_emoji": "😊",
    "description": "AI Agent yang siap membantu Anda dalam manajemen Proyek Akhir",
}

AGENT_CAPABILITIES = [
    "Membuat dan mengelola kelompok proyek akhir secara otomatis",
    "Menampilkan informasi dosen, mahasiswa, dan pembimbing",
    "Menghitung jumlah mahasiswa dan analisis data",
    "Memodifikasi dan mengoptimalkan kelompok yang sudah ada",
    "Menjawab pertanyaan terkait manajemen PA",
    "Menampilkan daftar matakuliah per semester",
    "Mengelola pemberian pembimbing ke kelompok",
    "Mengecek status pembimbing untuk setiap kelompok",
]


def get_identity_description():
    """Get consistent identity description"""
    return f"{AGENT_PERSONALITY['pronoun']} adalah {AGENT_PERSONALITY['description']}"


def get_agent_introduction(context_data: dict = None) -> str:
    """
    Generate consistent agent introduction
    
    Args:
        context_data: Optional context with prodi info
        
    Returns:
        Formatted introduction string
    """
    prodi = ""
    if context_data and context_data.get('prodi'):
        prodi = f" ({context_data['prodi']})"
    elif context_data and context_data.get('nama_prodi'):
        prodi = f" ({context_data['nama_prodi']})"
    
    response = (
        f"Halo! {AGENT_PERSONALITY['greeting_emoji']} "
        f"{AGENT_PERSONALITY['pronoun']} adalah {AGENT_NAME} yang siap membantu Anda dalam manajemen Proyek Akhir{prodi}.\n\n"
        "Saya dapat membantu dengan:\n"
    )
    
    for capability in AGENT_CAPABILITIES:
        response += f"• {capability}\n"
    
    response += f"\nSilakan berikan instruksi atau pertanyaan Anda! {AGENT_PERSONALITY['happy_emoji']}"
    
    return response


def get_identity_answer():
    """
    Get answer when asked "siapa kamu" / "kamu siapa"
    
    Returns:
        Consistent identity answer
    """
    return (
        f"{AGENT_PERSONALITY['pronoun']} adalah {AGENT_NAME}, versi {AGENT_VERSION}.\n"
        f"Namaku adalah '{AGENT_NAME}' atau bisa dipanggil 'PA Agent'.\n"
        f"Peran saya adalah {AGENT_PERSONALITY['description']}.\n\n"
        f"Tugasku:\n"
        f"1. Membantu mengelola kelompok Proyek Akhir\n"
        f"2. Memberikan informasi tentang dosen, mahasiswa, dan pembimbing\n"
        f"3. Menjawab pertanyaan terkait manajemen PA\n"
        f"4. Membantu mengoptimalkan distribusi mahasiswa ke dalam kelompok\n\n"
        f"Apakah ada yang bisa saya bantu?"
    )


def get_who_are_you_answer():
    """Alternative answer for 'who are you'"""
    return (
        f"Saya adalah {AGENT_NAME}, sebuah sistem AI yang dirancang khusus untuk membantu "
        f"dalam manajemen Proyek Akhir (PA).\n\n"
        f"Nama lengkap saya: {AGENT_NAME} v{AGENT_VERSION}\n"
        f"Fungsi utama: {', '.join([c.lower() for c in AGENT_CAPABILITIES[:3]])}, dan lainnya.\n\n"
        f"Senang berkerja sama dengan Anda! Apa yang bisa saya bantu today?"
    )


def is_identity_question(prompt: str) -> bool:
    """Check if prompt is asking about agent identity"""
    identity_keywords = [
        "siapa kamu", "kamu siapa", "siapa nama mu", "nama mu siapa",
        "siapa aku", "aku siapa", "siapa saya", "saya siapa",
        "who are you", "who you are", "what is your name", "siapa dirimu",
        "apa nama kamu", "nama kamu", "identitas", "biodata",
        "tell me about yourself", "tentang kamu", "tentang diri kamu"
    ]
    
    prompt_lower = prompt.lower().strip()
    for keyword in identity_keywords:
        if keyword in prompt_lower:
            return True
    return False


def is_greeting_with_identity(prompt: str) -> bool:
    """Check if greeting also includes identity question like 'halo siapa nama kamu'"""
    greeting_keywords = ["halo", "hai", "hello", "hi"]
    identity_keywords = ["siapa", "nama", "who"]
    
    prompt_lower = prompt.lower().strip()
    has_greeting = any(kw in prompt_lower for kw in greeting_keywords)
    has_identity = any(kw in prompt_lower for kw in identity_keywords)
    
    return has_greeting and has_identity
