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

from sqlalchemy import Column, Integer, String, ForeignKey
from core.database import Base


class MataKuliah(Base):
    __tablename__ = "mata_kuliah"

    id = Column(Integer, primary_key=True, index=True)
    kuliah_id = Column(Integer, unique=True)
    kode_mk = Column(String, index=True)
    nama_matkul = Column(String)
    sks = Column(Integer)
    semester = Column(Integer)
    prodi_id = Column(Integer, ForeignKey("prodi.id"))
    tahun_ajaran = Column(Integer)
    semester_ta = Column(Integer)
