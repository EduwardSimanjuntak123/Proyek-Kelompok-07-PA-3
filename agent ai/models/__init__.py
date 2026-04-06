"""
AI Agent Models Package

Imports all model classes for database entities
"""

from models.dosen import Dosen
from models.kategori_pa import KategoriPA
from models.kelompok import Kelompok
from models.kelompokMahasiswa import KelompokMahasiswa
from models.mahasiswa import Mahasiswa
from models.nilai_matkul_mahasiswa import NilaiMatkulMahasiswa
from models.prodi import Prodi
from models.tahun_ajaran import TahunAjaran
from models.tahun_masuk import TahunMasuk
from models.pembimbing import Pembimbing
from models.matakuliah import MataKuliah

__all__ = [
    "Dosen",
    "KategoriPA",
    "Kelompok",
    "KelompokMahasiswa",
    "Mahasiswa",
    "NilaiMatkulMahasiswa",
    "Prodi",
    "TahunAjaran",
    "TahunMasuk",
    "Pembimbing",
    "MataKuliah",
]
