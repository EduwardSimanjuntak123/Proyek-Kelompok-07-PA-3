from sqlalchemy import Column, Integer, String
from app.db.base import Base


class KategoriPA(Base):
    __tablename__ = "kategori_pa"

    id = Column(Integer, primary_key=True, index=True)
    kategori_pa = Column(String)