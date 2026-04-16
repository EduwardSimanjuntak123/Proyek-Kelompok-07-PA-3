from sqlalchemy import Column, Integer, ForeignKey
from core.database import Base


class KelompokMahasiswa(Base):
    __tablename__ = "kelompok_mahasiswa"

    id = Column(Integer, primary_key=True, index=True)

    user_id = Column(Integer, ForeignKey("mahasiswa.user_id"))

    kelompok_id = Column(Integer, ForeignKey("kelompok.id"))
