from sqlalchemy import Column, Integer, String, ForeignKey
from core.database import Base


class DosenRole(Base):
    __tablename__ = "dosen_roles"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, ForeignKey("dosen.user_id"), index=True)
    KPA_id = Column(Integer, ForeignKey("kategori_pa.id"), index=True)
    prodi_id = Column(Integer, ForeignKey("prodi.id"), index=True)
    role_id = Column(Integer, ForeignKey("roles.id"), index=True)
    TM_id = Column(Integer, ForeignKey("tahun_masuk.id"), index=True)
    tahun_ajaran_id = Column(Integer, ForeignKey("tahun_ajaran.id"), index=True)
    status = Column(String, index=True)
