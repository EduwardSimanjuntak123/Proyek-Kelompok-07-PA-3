from sqlalchemy import Column, Integer, String, DECIMAL, ForeignKey
from app.db.base import Base

class NilaiMatkulMahasiswa(Base):
    __tablename__ = "nilai_matkul_mahasiswa"

    id = Column(Integer, primary_key=True)
    mahasiswa_id = Column(Integer, ForeignKey("mahasiswa.id"))
    kode_mk = Column(String)
    nilai_angka = Column(DECIMAL)
    nilai_huruf = Column(String)
    bobot_nilai = Column(DECIMAL)
    semester = Column(Integer)
    tahun_ajaran = Column(Integer)