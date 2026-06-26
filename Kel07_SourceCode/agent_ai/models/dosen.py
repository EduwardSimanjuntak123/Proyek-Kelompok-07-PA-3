from sqlalchemy import Column, Integer, String
from core.database import Base


class Dosen(Base):
    __tablename__ = "dosen"

    id = Column(Integer, primary_key=True, index=True)
    pegawai_id = Column(Integer, nullable=True)
    dosen_id = Column(String, nullable=True, unique=True)
    nip = Column(String, nullable=True, unique=True)
    nama = Column(String)
    email = Column(String, nullable=True)
    prodi_id = Column(Integer, nullable=True, index=True)
    prodi = Column(String, nullable=True)
    jabatan_akademik = Column(String, nullable=True)
    jabatan_akademik_desc = Column(String, nullable=True)
    jenjang_pendidikan = Column(String, nullable=True)
    nidn = Column(String, nullable=True, unique=True)
    user_id = Column(Integer, unique=True, index=True)
