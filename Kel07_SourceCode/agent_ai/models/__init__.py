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
from models.penguji import Penguji
from models.matakuliah import MataKuliah
from models.dosen_role import DosenRole
from models.jadwal import Jadwal
from models.nilai_mahasiswa import NilaiMahasiswa
from models.role import Role
from models.ruangan import Ruangan
from models.pengajuan_seminar import PengajuanSeminar, StatusPengajuan
from models.pengajuan_seminar_files import PengajuanSeminarFiles

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
    "Penguji",
    "MataKuliah",
    "DosenRole",
    "Jadwal",
    "NilaiMahasiswa",
    "Role",
    "Ruangan",
    "PengajuanSeminar",
    "StatusPengajuan",
    "PengajuanSeminarFiles",
]
