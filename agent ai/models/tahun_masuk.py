from sqlalchemy import Column, Integer, String
from app.db.base import Base


class TahunMasuk(Base):
    __tablename__ = "tahun_masuk"

    id = Column(Integer, primary_key=True, index=True)
    Tahun_Masuk = Column(Integer)