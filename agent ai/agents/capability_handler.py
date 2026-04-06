"""
Handler untuk capability queries - ketika user bertanya "kamu bisa apa?", "apa tugasmu?", dll
"""
from openai import OpenAI
from app.config import OPENAI_API_KEY

client = OpenAI(api_key=OPENAI_API_KEY)

# ================= AGENT CAPABILITIES DEFINITION =================
AGENT_CAPABILITIES = {
    "grouping": {
        "name": "Pembuatan Kelompok",
        "description": "Membuat pembagian kelompok mahasiswa secara otomatis",
        "examples": [
            "Buat 5 kelompok",
            "Bagi mahasiswa menjadi 3 kelompok",
            "Buat kelompok dengan keseimbangan skill",
            "Acak ulang pembagian kelompok"
        ],
        "keywords": ["buat kelompok", "bagi grup", "pembagian", "acak"]
    },
    "dosen_query": {
        "name": "Informasi Dosen Pembimbing",
        "description": "Mencari dan menampilkan informasi dosen pembimbing",
        "examples": [
            "Siapa dosen pembimbing saya?",
            "Tampilkan semua dosen dari prodi TI",
            "Cari dosen dengan nama Budi"
        ],
        "keywords": ["dosen", "pembimbing", "siapa dosen"]
    },
    "pembimbing_assignment": {
        "name": "Penugasan Dosen Pembimbing",
        "description": "Menentukan dan mengubah dosen pembimbing kelompok",
        "examples": [
            "Berikan pembimbing untuk kelompok 1",
            "Ubah pembimbing ke Ibu Siti",
            "Assign pembimbing otomatis"
        ],
        "keywords": ["pembimbing", "assign", "berikan dosen"]
    },
    "group_modification": {
        "name": "Modifikasi Kelompok",
        "description": "Mengubah anggota kelompok yang sudah ada",
        "examples": [
            "Jangan satukan Andi dan Budi",
            "Satu kelompok Andi dan Citra",
            "Keluarkan Ade dari kelompok 2"
        ],
        "keywords": ["jangan", "harus", "ubah", "tukar", "pindah"]
    },
    "view_groups": {
        "name": "Tampilkan Kelompok",
        "description": "Melihat pembagian kelompok yang sudah dibuat",
        "examples": [
            "Tampilkan kelompok saya",
            "Lihat anggota kelompok 1",
            "Tampilkan semua kelompok"
        ],
        "keywords": ["tampilkan", "lihat", "anggota", "kelompok saya"]
    }
}

# ================= CAPABILITY DETECTION =================
def detect_capability_query(prompt):
    """Detect jika user bertanya tentang kemampuan agent"""
    capability_keywords = [
        "kamu bisa apa",
        "apa yang bisa kamu",
        "apa tugasmu",
        "fungsi kamu",
        "apa fungsi",
        "kemampuan",
        "bisa apa",
        "tugasmu apa",
        "instruksi apa",
        "format apa",
        "perintah apa",
        "cara pakai",
        "cara menggunakan",
        "bagaimana cara",
        "gimana cara",
        "siapa kamu",
        "siapa saya",
        "siapa aku",
        "apa nama kamu",
        "apa nama saya",
        "identitas kamu",
        "identitas saya",
        "perkenalkan diri",
        "kenalkan diri",
        "who are you",
        "help",
        "bantuan",
        "pertolongan"
    ]
    
    prompt_lower = prompt.lower().strip()
    return any(keyword in prompt_lower for keyword in capability_keywords)


def get_capability_summary():
    """Get summary dari semua capabilities dalam format yang readable"""
    summary = "🤖 **Kemampuan Saya:**\n\n"
    
    for i, (key, cap) in enumerate(AGENT_CAPABILITIES.items(), 1):
        summary += f"**{i}. {cap['name']}**\n"
        summary += f"   📝 {cap['description']}\n"
        summary += f"   💡 Contoh: {', '.join(cap['examples'][:2])}\n\n"
    
    return summary


def generate_capability_response(prompt):
    """Generate response tentang capabilities berdasarkan specific capability yang ditanya"""
    system_prompt = f"""Kamu adalah AI Agent Koordinator PA yang helpful dan friendly.

User menanyakan tentang kemampuan atau instruksi yang bisa mereka berikan kepadamu.

KEMAMPUAN AGENT:
{get_capability_summary()}

Gunakan informasi di atas untuk menjawab pertanyaan user dengan:
1. Jelas dan terstruktur
2. Memberikan contoh konkret
3. User-friendly dan tidak technical
4. Singkat (3-5 baris)
5. Bahasa Indonesia yang baik

Jangan suggest memberikan response tentang hal yang bukan capabilities kami."""

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
                    "content": f"User: {prompt}"
                }
            ],
            temperature=0.7,
            max_tokens=300
        )
        
        return {
            "type": "capability_response",
            "response": response.choices[0].message.content,
            "capabilities_shown": list(AGENT_CAPABILITIES.keys())
        }
        
    except Exception as e:
        print(f"[ERROR] Generate capability response failed: {e}")
        return {
            "type": "capability_response",
            "response": get_capability_summary(),
            "capabilities_shown": list(AGENT_CAPABILITIES.keys())
        }


def get_capability_details(capability_name):
    """Get detail information tentang specific capability"""
    if capability_name in AGENT_CAPABILITIES:
        cap = AGENT_CAPABILITIES[capability_name]
        detail = f"""
**{cap['name']}**

{cap['description']}

Contoh penggunaan:
"""
        for ex in cap['examples']:
            detail += f"• {ex}\n"
        return detail
    return None


def generate_help_response():
    """Generate help response dengan semua instructions"""
    help_text = f"""
🎓 **Agent Koordinator PA - Panduan Penggunaan**

{get_capability_summary()}

**Tips Penggunaan:**
• Gunakan instruksi yang jelas dan spesifik
• Contoh: "Buat 5 kelompok dengan keseimbangan skill"
• Jika ada hasil, Anda bisa memodifikasinya dengan instruksi "Jangan satukan [Nama1] dan [Nama2]"
• Setiap instruksi akan disimpan dalam memory untuk pembelajaran agent

Tanya "kemampuan apa saja?" atau "apa yang bisa kamu lakukan?" kapan saja!
"""
    return help_text
