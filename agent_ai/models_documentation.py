"""
Generate dan store dokumentasi dari Laravel Models
untuk AI agent awareness tentang schema dan relationships
"""

import os
import re
import json
from pathlib import Path

MODELS_PATH = r".\ui laravel\app\Models"

MODEL_DOCUMENTATION = {
    "Dosen": {
        "table": "dosen",
        "description": "Model representasi data Dosen",
        "fields": {
            "id": "Primary Key",
            "pegawai_id": "ID Pegawai",
            "dosen_id": "ID Dosen unik",
            "nip": "Nomor Induk Pegawai",
            "nama": "Nama lengkap dosen",
            "email": "Email dosen",
            "prodi_id": "ID Program Studi",
            "prodi": "Nama Program Studi",
            "jabatan_akademik": "Jabatan akademik (Lektor, Asisten Ahli, etc)",
            "jabatan_akademik_desc": "Deskripsi jabatan akademik",
            "jenjang_pendidikan": "Jenjang pendidikan (S1, S2, S3)",
            "nidn": "Nomor Induk Dosen Nasional",
            "user_id": "Foreign key ke User"
        },
        "relationships": {
            "roles": "DosenRole - role yang dimiliki dosen",
            "prodi": "Prodi - program studi dosen",
            "pembimbingan": "Pembimbing - kelompok yang dibimbing"
        },
        "use_case": "Query informasi dosen, role, keahlian, program studi"
    },
    
    "Mahasiswa": {
        "table": "mahasiswa",
        "description": "Model representasi data Mahasiswa",
        "fields": {
            "id": "Primary Key",
            "dim_id": "ID dimensi mahasiswa",
            "user_id": "Foreign key ke User",
            "user_name": "Username mahasiswa",
            "nim": "Nomor Induk Mahasiswa",
            "nama": "Nama lengkap mahasiswa",
            "email": "Email mahasiswa",
            "prodi_id": "ID Program Studi",
            "prodi_name": "Nama Program Studi",
            "fakultas": "Nama Fakultas",
            "angkatan": "Tahun angkatan mahasiswa",
            "status": "Status enrollment (Aktif, Cuti, Lulus, Dropout)",
            "asrama": "Info asrama jika ada"
        },
        "relationships": {
            "kelompok": "Kelompok - kelompok yang diikuti",
            "nilai_matakuliah": "NilaiMatkulMahasiswa - nilai per matakuliah",
            "nilai_administrasi": "Nilai_Administrasi - nilai administrasi"
        },
        "use_case": "Query mahasiswa, filtering by prodi/angkatan, cek nilai, cek kelompok"
    },
    
    "Kelompok": {
        "table": "kelompok",
        "description": "Model representasi Kelompok PA",
        "fields": {
            "id": "Primary Key",
            "nomor_kelompok": "Nomor urut kelompok (1, 2, 3, dst)",
            "nama_kelompok": "Nama identitas kelompok (opsional)",
            "prodi_id": "ID Program Studi",
            "kategori_pa_id": "ID Kategori PA",
            "tahun_ajaran_id": "ID Tahun Ajaran",
            "status": "Status kelompok (Aktif, Selesai, Dibatalkan)",
            "judul_pa": "Judul Proyek Akhir",
            "deskripsi": "Deskripsi proyek"
        },
        "relationships": {
            "mahasiswa": "KelompokMahasiswa - anggota kelompok",
            "pembimbing": "Pembimbing - dosen pembimbing",
            "penguji": "Penguji - dosen penguji",
            "tugas": "Tugas - tugas yang diberikan"
        },
        "use_case": "Data kelompok PA, anggota, pembimbing, progress, timeline"
    },
    
    "KelompokMahasiswa": {
        "table": "kelompok_mahasiswa",
        "description": "Pivot table - relasi banyak ke banyak Kelompok <-> Mahasiswa",
        "fields": {
            "id": "Primary Key",
            "kelompok_id": "Foreign key Kelompok",
            "user_id": "Foreign key User (Mahasiswa)",
            "posisi": "Posisi dalam kelompok (Ketua, Anggota, Sekretaris)",
            "status": "Status keanggotaan (Aktif, Keluar, Suspended)",
            "joined_at": "Tanggal bergabung",
            "left_at": "Tanggal keluar (nullable)"
        },
        "relationships": {
            "kelompok": "Kelompok - kelompok yang diikuti",
            "mahasiswa": "Mahasiswa - mahasiswa anggota"
        },
        "use_case": "Query anggota kelompok, posisi, status, timeline keaktifan"
    },
    
    "MataKuliah": {
        "table": "mata_kuliah",
        "description": "Model representasi Mata Kuliah",
        "fields": {
            "id": "Primary Key",
            "kode_mk": "Kode mata kuliah unik",
            "nama": "Nama mata kuliah",
            "sks": "Satuan Kredit Semester (1-4)",
            "semester": "Semester penempatan (1-8)",
            "prodi_id": "ID Program Studi",
            "tipe": "Tipe MK (Teori, Praktik, Teori+Praktik)",
            "deskripsi": "Deskripsi singkat"
        },
        "relationships": {
            "nilai_mahasiswa": "NilaiMatkulMahasiswa - nilai mahasiswa"
        },
        "use_case": "Informasi mata kuliah, SKS, semester, prasyarat"
    },
    
    "DosenRole": {
        "table": "dosen_roles",
        "description": "Relasi Dosen dengan Role di kategori PA tertentu",
        "fields": {
            "id": "Primary Key",
            "user_id": "Foreign key Dosen",
            "role_id": "Foreign key Role",
            "kategori_pa_id": "ID Kategori PA (pembimbing untuk kategori apa)",
            "tahun_masuk_id": "ID Tahun Masuk (berlaku untuk angkatan apa)",
            "prodi_id": "ID Program Studi"
        },
        "relationships": {
            "dosen": "Dosen - data dosen",
            "role": "Role - peran (Pembimbing, Penguji, Koordinator)",
            "kategoriPA": "kategoriPA - kategori PA",
            "prodi": "Prodi - program studi"
        },
        "use_case": "Filter dosen by role, kategori PA, tahun masuk, prodi"
    },
    
    "Pembimbing": {
        "table": "pembimbing",
        "description": "Relasi Dosen Pembimbing dengan Kelompok",
        "fields": {
            "id": "Primary Key",
            "user_id": "Foreign key Dosen (pembimbing)",
            "kelompok_id": "Foreign key Kelompok",
            "tipe_bimbingan": "Tipe (Pembimbing 1, Pembimbing 2, Pendamping)",
            "status": "Status (Aktif, Selesai, Dibatalkan)",
            "tanggal_mulai": "Tanggal mulai membimbing",
            "tanggal_selesai": "Tanggal selesai (nullable jika ongoing)"
        },
        "relationships": {
            "dosen": "Dosen - data dosen pembimbing",
            "kelompok": "Kelompok - kelompok yang dibimbing"
        },
        "use_case": "Cek siapa pembimbing kelompok, daftar kelompok yang dibimbing"
    },
    
    "Penguji": {
        "table": "penguji",
        "description": "Relasi Dosen Penguji dengan Kelompok",
        "fields": {
            "id": "Primary Key",
            "user_id": "Foreign key Dosen (penguji)",
            "kelompok_id": "Foreign key Kelompok",
            "tipe_penguji": "Tipe (Penguji 1, Penguji 2, Ketua Penguji)",
            "status": "Status (Ditugaskan, Sudah Menguji, Dibatalkan)"
        },
        "relationships": {
            "dosen": "Dosen - data dosen penguji",
            "kelompok": "Kelompok - kelompok yang diuji"
        },
        "use_case": "Data penguji, pembagian penguji, hasil ujian"
    },
    
    "Prodi": {
        "table": "prodi",
        "description": "Model Program Studi",
        "fields": {
            "id": "Primary Key",
            "kode_prodi": "Kode program studi",
            "nama_prodi": "Nama lengkap program studi",
            "jenjang": "Jenjang (Diploma, Sarjana, Magister)",
            "akreditasi": "Status akreditasi (A, B, C, Belum)",
            "deskripsi": "Deskripsi program studi"
        },
        "relationships": {
            "dosen": "Dosen - dosen di prodi ini",
            "mahasiswa": "Mahasiswa - mahasiswa di prodi ini"
        },
        "use_case": "Info program studi, mahasiswa per prodi, dosen per prodi"
    },
    
    "NilaiMatkulMahasiswa": {
        "table": "nilai_matkul_mahasiswa",
        "description": "Nilai mata kuliah per mahasiswa",
        "fields": {
            "id": "Primary Key",
            "mahasiswa_id": "Foreign key Mahasiswa",
            "kode_mk": "Kode mata kuliah",
            "nilai_angka": "Nilai angka (0-100)",
            "nilai_huruf": "Nilai huruf (A, B, C, D, E)",
            "bobot": "Bobot untuk IPK",
            "semester": "Semester diambil",
            "status": "Status (Lulus, Tidak Lulus, Prosés)"
        },
        "relationships": {
            "mahasiswa": "Mahasiswa - data mahasiswa",
            "matakuliah": "MataKuliah - data mata kuliah"
        },
        "use_case": "Cek nilai mahasiswa, IPK, mata kuliah yang diambil"
    },
    
    "Tugas": {
        "table": "tugas",
        "description": "Tugas yang diberikan oleh dosen ke kelompok",
        "fields": {
            "id": "Primary Key",
            "kelompok_id": "Foreign key Kelompok",
            "dosen_id": "Foreign key Dosen pembuat tugas",
            "judul_tugas": "Judul atau deskripsi tugas",
            "deskripsi": "Deskripsi detail",
            "tenggat_waktu": "Deadline pengumpulan",
            "tipe": "Tipe tugas (Report, Presentasi, Code, Design, etc)",
            "status": "Status (Aktif, Ditutup, Dibatalkan)"
        },
        "relationships": {
            "kelompok": "Kelompok - kelompok penerima tugas",
            "dosen": "Dosen - pembuat tugas",
            "pengumpulan": "pengumpulan_tugas - status pengumpulan"
        },
        "use_case": "Daftar tugas kelompok, deadline, status pengumpulan"
    },
    
    "Nilai_Administrasi": {
        "table": "nilai_administrasi",
        "description": "Nilai administrasi PA kelompok/mahasiswa",
        "fields": {
            "id": "Primary Key",
            "kelompok_id": "Foreign key Kelompok",
            "kriteria": "Kriteria penilaian (Kelengkapan Dokumen, Kehadiran, etc)",
            "nilai": "Nilai numerik",
            "keterangan": "Catatan/keterangan scorer"
        },
        "relationships": {
            "kelompok": "Kelompok - kelompok dinilai"
        },
        "use_case": "Nilai administrasi, compliance, dokumentasi"
    },
    
    "Role": {
        "table": "roles",
        "description": "Definisi role/peran dalam sistem",
        "fields": {
            "id": "Primary Key",
            "role_name": "Nama role (Pembimbing, Penguji, Koordinator, Admin)",
            "deskripsi": "Deskripsi peran dan tanggung jawab"
        },
        "relationships": {
            "dosen_roles": "DosenRole - dosen dengan role ini"
        },
        "use_case": "Info role, peran dalam PA, tanggung jawab"
    },
    
    "kategoriPA": {
        "table": "kategori_pa",
        "description": "Kategori/tipe Proyek Akhir",
        "fields": {
            "id": "Primary Key",
            "kategori_pa": "Nama kategori (e.g., 'Pengembangan Sistem', 'Riset')",
            "deskripsi": "Deskripsi kategori",
            "durasi_semester": "Durasi normal dalam semester"
        },
        "relationships": {
            "kelompok": "Kelompok - kelompok di kategori ini"
        },
        "use_case": "Tipe-tipe PA, karakteristik, metodologi"
    }
}

def get_model_awareness_context(role: str, prodi: str, kategori_pa: str) -> str:
    """
    Hasilkan context awareness untuk model berdasarkan role dan context user
    
    Args:
        role: Role dosen (Koordinator, Pembimbing, Penguji, etc)
        prodi: Program studi dosen
        kategori_pa: Kategori PA yang ditangani
    
    Returns:
        String dokumentasi model yang relevan dengan role/prodi
    """
    
    # Filter model berdasarkan role
    relevant_models = {}
    
    if role == "Koordinator":
        # Koordinator butuh lihat semua model
        relevant_models = {
            "Kelompok": MODEL_DOCUMENTATION["Kelompok"],
            "KelompokMahasiswa": MODEL_DOCUMENTATION["KelompokMahasiswa"],
            "Mahasiswa": MODEL_DOCUMENTATION["Mahasiswa"],
            "Dosen": MODEL_DOCUMENTATION["Dosen"],
            "DosenRole": MODEL_DOCUMENTATION["DosenRole"],
            "Pembimbing": MODEL_DOCUMENTATION["Pembimbing"],
            "Penguji": MODEL_DOCUMENTATION["Penguji"],
            "Nilai_Administrasi": MODEL_DOCUMENTATION["Nilai_Administrasi"],
            "Prodi": MODEL_DOCUMENTATION["Prodi"],
            "kategoriPA": MODEL_DOCUMENTATION["kategoriPA"]
        }
    elif role == "Pembimbing":
        relevant_models = {
            "Kelompok": MODEL_DOCUMENTATION["Kelompok"],
            "KelompokMahasiswa": MODEL_DOCUMENTATION["KelompokMahasiswa"],
            "Mahasiswa": MODEL_DOCUMENTATION["Mahasiswa"],
            "Pembimbing": MODEL_DOCUMENTATION["Pembimbing"],
            "Tugas": MODEL_DOCUMENTATION["Tugas"],
            "MataKuliah": MODEL_DOCUMENTATION["MataKuliah"],
            "NilaiMatkulMahasiswa": MODEL_DOCUMENTATION["NilaiMatkulMahasiswa"]
        }
    elif role == "Penguji":
        relevant_models = {
            "Kelompok": MODEL_DOCUMENTATION["Kelompok"],
            "KelompokMahasiswa": MODEL_DOCUMENTATION["KelompokMahasiswa"],
            "Mahasiswa": MODEL_DOCUMENTATION["Mahasiswa"],
            "Penguji": MODEL_DOCUMENTATION["Penguji"],
            "Nilai_Administrasi": MODEL_DOCUMENTATION["Nilai_Administrasi"]
        }
    
    # Build context string
    context_str = f"## Model Schema Awareness\n\n"
    context_str += f"Role Pengguna: {role}\n"
    context_str += f"Program Studi: {prodi}\n"
    context_str += f"Kategori PA: {kategori_pa}\n\n"
    
    context_str += "### Available Models:\n\n"
    
    for model_name, model_info in relevant_models.items():
        context_str += f"**{model_name}** (Table: `{model_info['table']}`)\n"
        context_str += f"- {model_info['description']}\n"
        context_str += f"- Fields: {', '.join(list(model_info['fields'].keys())[:5])}{'...' if len(model_info['fields']) > 5 else ''}\n"
        context_str += f"- Use: {model_info['use_case']}\n\n"
    
    return context_str


def get_full_model_documentation() -> str:
    """
    Get full model documentation sebagai string
    """
    doc_str = "# DATABASE MODELS DOCUMENTATION\n\n"
    
    for model_name, model_info in MODEL_DOCUMENTATION.items():
        doc_str += f"## {model_name}\n\n"
        doc_str += f"**Table**: `{model_info['table']}`\n\n"
        doc_str += f"**Description**: {model_info['description']}\n\n"
        
        doc_str += "### Fields:\n"
        for field_name, field_desc in model_info['fields'].items():
            doc_str += f"- `{field_name}`: {field_desc}\n"
        
        doc_str += "\n### Relationships:\n"
        for rel_name, rel_desc in model_info['relationships'].items():
            doc_str += f"- `{rel_name}`: {rel_desc}\n"
        
        doc_str += f"\n### Use Cases:\n- {model_info['use_case']}\n\n"
    
    return doc_str


if __name__ == "__main__":
    from core.llm import call_llm
    
    # Test documentation generation
    print("=== Full Documentation ===")
    print(get_full_model_documentation())
    
    print("\n=== Role-Based Context ===")
    context = get_model_awareness_context("Koordinator", "DIV Teknologi Rekayasa Perangkat Lunak", "Pengembangan Sistem")
    print(context)
