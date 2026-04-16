from sqlalchemy import Column, Integer, String
from core.database import Base


class Prodi(Base):
    __tablename__ = "prodi"

    id = Column(Integer, primary_key=True, index=True)

    nama_prodi = Column(String)
    maks_project = Column(Integer)

    created_at = Column(String)
    updated_at = Column(String)