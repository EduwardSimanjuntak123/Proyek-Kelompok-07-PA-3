from sqlalchemy import Column, Integer, String
from core.database import Base


class Mahasiswa(Base):
    __tablename__ = "mahasiswa"

    id = Column(Integer, primary_key=True, index=True)
    dim_id = Column(Integer, unique=True)
    user_id = Column(Integer, unique=True)

    user_name = Column(String)
    nim = Column(String, unique=True)
    nama = Column(String)
    email = Column(String)

    prodi_id = Column(Integer, index=True)
    prodi_name = Column(String)

    fakultas = Column(String)
    angkatan = Column(Integer, index=True)

    status = Column(String, index=True)
    asrama = Column(String, nullable=True)