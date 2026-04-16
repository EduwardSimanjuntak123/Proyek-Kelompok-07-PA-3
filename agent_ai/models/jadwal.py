from sqlalchemy import Column, Integer, DateTime, ForeignKey
from core.database import Base


class Jadwal(Base):
    __tablename__ = "jadwal"

    id = Column(Integer, primary_key=True, index=True)
    kelompok_id = Column(Integer, ForeignKey("kelompok.id"), index=True)
    waktu_mulai = Column(DateTime)
    waktu_selesai = Column(DateTime)
    user_id = Column(Integer, index=True)
    ruangan_id = Column(Integer, ForeignKey("ruangan.id"), nullable=True, index=True)
    KPA_id = Column(Integer, ForeignKey("kategori_pa.id"), nullable=True, index=True)
    prodi_id = Column(Integer, ForeignKey("prodi.id"), nullable=True, index=True)
    TM_id = Column(Integer, ForeignKey("tahun_masuk.id"), nullable=True, index=True)
