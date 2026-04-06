"""
Model MataKuliah - Course/Subject

Represents academic courses offered in each semester for each study program.

Attributes:
    - kode_mk: Course code
    - nama_matkul: Course name
    - sks: Credit points
    - semester: Semester number (1-8)
    - prodi_id: Study program reference
    - tahun_ajaran: Academic year
"""

from typing import Optional


class MataKuliah:
    """
    MataKuliah model - represents academic course
    
    Attributes:
        id: Primary key
        kuliah_id: Alternative course ID
        kode_mk: Course code (e.g., "INT6125")
        nama_matkul: Course name
        sks: Credit points (usually 2-4)
        semester: Semester number (1, 2, 3, 4, 5, 6, 7, or 8)
        prodi_id: Study program ID
        tahun_ajaran: Academic year
        semester_ta: Semester in academic year (1 or 2)
        created_at: Created timestamp
        updated_at: Updated timestamp
    """
    
    def __init__(
        self,
        kode_mk: str,
        nama_matkul: str,
        sks: int,
        semester: int,
        prodi_id: int,
        tahun_ajaran: str,
        id: Optional[int] = None,
        kuliah_id: Optional[int] = None,
        semester_ta: Optional[int] = None,
        created_at: Optional[str] = None,
        updated_at: Optional[str] = None,
    ):
        self.id = id
        self.kuliah_id = kuliah_id
        self.kode_mk = kode_mk
        self.nama_matkul = nama_matkul
        self.sks = sks
        self.semester = semester
        self.prodi_id = prodi_id
        self.tahun_ajaran = tahun_ajaran
        self.semester_ta = semester_ta  # 1 = odd semester, 2 = even semester
        self.created_at = created_at
        self.updated_at = updated_at
    
    def to_dict(self) -> dict:
        """Convert to dictionary"""
        return {
            "id": self.id,
            "kuliah_id": self.kuliah_id,
            "kode_mk": self.kode_mk,
            "nama_matkul": self.nama_matkul,
            "sks": self.sks,
            "semester": self.semester,
            "prodi_id": self.prodi_id,
            "tahun_ajaran": self.tahun_ajaran,
            "semester_ta": self.semester_ta,
            "created_at": self.created_at,
            "updated_at": self.updated_at,
        }
    
    def __repr__(self):
        return f"MataKuliah(kode={self.kode_mk}, nama={self.nama_matkul}, semester={self.semester}, sks={self.sks})"


# Schema for validation
MATAKULIAH_SCHEMA = {
    "id": "int",
    "kuliah_id": "int|null",
    "kode_mk": "string",
    "nama_matkul": "string",
    "sks": "int",
    "semester": "int",
    "prodi_id": "int",
    "tahun_ajaran": "string",
    "semester_ta": "int|null",
    "created_at": "timestamp",
    "updated_at": "timestamp",
}


def create_matakuliah(
    kode_mk: str,
    nama_matkul: str,
    sks: int,
    semester: int,
    prodi_id: int,
    tahun_ajaran: str,
    **kwargs
) -> MataKuliah:
    """Factory function to create MataKuliah instance"""
    return MataKuliah(
        kode_mk=kode_mk,
        nama_matkul=nama_matkul,
        sks=sks,
        semester=semester,
        prodi_id=prodi_id,
        tahun_ajaran=tahun_ajaran,
        **kwargs
    )
