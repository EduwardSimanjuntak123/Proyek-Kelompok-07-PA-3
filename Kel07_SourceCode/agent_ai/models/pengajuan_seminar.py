"""
Model PengajuanSeminar - Seminar Submission from Group
Relationships:
  - pengajuan_seminar.kelompok_id <-> kelompok.id
  - pengajuan_seminar.pembimbing_id <-> pembimbing.id
  - pengajuan_seminar.id <-> pengajuan_seminar_files.pengajuan_seminar_id (1:N)
"""

from sqlalchemy import Column, Integer, ForeignKey, String, Text, DateTime, Enum
from sqlalchemy.orm import relationship
from core.database import Base
import enum


class StatusPengajuan(str, enum.Enum):
    """Status pengajuan seminar"""
    MENUNGGU = "menunggu"
    DISETUJUI = "disetujui"
    DITOLAK = "ditolak"


class PengajuanSeminar(Base):
    __tablename__ = "pengajuan_seminar"

    id = Column(Integer, primary_key=True, index=True)
    
    # Foreign Keys
    kelompok_id = Column(Integer, ForeignKey("kelompok.id"), nullable=False, index=True)
    pembimbing_id = Column(Integer, ForeignKey("pembimbing.id"), nullable=False, index=True)
    
    # Status
    status = Column(Enum(StatusPengajuan), default=StatusPengajuan.MENUNGGU, index=True)
    
    # Notes/Comments
    catatan = Column(Text, nullable=True)
    
    # Timestamps
    created_at = Column(DateTime, nullable=True, index=True)
    updated_at = Column(DateTime, nullable=True)
    
    # Relationships
    files = relationship(
        "PengajuanSeminarFiles",
        back_populates="pengajuan_seminar",
        cascade="all, delete-orphan",
        lazy="joined"
    )
