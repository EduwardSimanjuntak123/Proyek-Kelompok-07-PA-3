from sqlalchemy import Column, Integer, String
from core.database import Base


class TahunAjaran(Base):
    __tablename__ = "tahun_ajaran"

    id = Column(Integer, primary_key=True, index=True)

    tahun_mulai = Column(Integer)
    tahun_selesai = Column(Integer)

    status = Column(String)

    created_at = Column(String)
    updated_at = Column(String)