from sqlalchemy import Column, Integer, String, ForeignKey
from app.db.base import Base
from app.models.prodi import Prodi
from app.models.tahun_ajaran import TahunAjaran
from app.models.kategori_pa import KategoriPA
from app.models.tahun_masuk import TahunMasuk




class Kelompok(Base):
    __tablename__ = "kelompok"

    id = Column(Integer, primary_key=True, index=True)

    nomor_kelompok = Column(String(100))

    KPA_id = Column(Integer, ForeignKey("kategori_pa.id"))
    prodi_id = Column(Integer, ForeignKey("prodi.id"))
    TM_id = Column(Integer, ForeignKey("tahun_masuk.id"))
    tahun_ajaran_id = Column(Integer, ForeignKey("tahun_ajaran.id"))

    status = Column(String)
