from sqlalchemy import Column, Integer, Float, ForeignKey
from core.database import Base


class NilaiMahasiswa(Base):
    __tablename__ = "nilai_mahasiswa"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, ForeignKey("mahasiswa.user_id"), index=True)
    kelompok_id = Column(Integer, ForeignKey("kelompok.id"), index=True)
    nilai_akhir = Column(Float)
