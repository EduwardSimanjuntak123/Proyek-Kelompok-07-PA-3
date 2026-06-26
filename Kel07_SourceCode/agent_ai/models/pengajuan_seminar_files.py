"""
Model PengajuanSeminarFiles - Files uploaded for seminar submission
Relationship: pengajuan_seminar_files.pengajuan_seminar_id <-> pengajuan_seminar.id
"""

from sqlalchemy import Column, Integer, ForeignKey, String, BigInteger, DateTime
from sqlalchemy.orm import relationship
from core.database import Base


class PengajuanSeminarFiles(Base):
    __tablename__ = "pengajuan_seminar_files"

    id = Column(Integer, primary_key=True, index=True)
    
    # Foreign Key
    pengajuan_seminar_id = Column(
        Integer, 
        ForeignKey("pengajuan_seminar.id"), 
        nullable=False, 
        index=True
    )
    
    # File Information
    file_path = Column(String(255), nullable=False)
    file_name = Column(String(255), nullable=False)
    file_type = Column(String(50), nullable=False)  # e.g., 'pdf', 'docx', 'xlsx'
    file_size = Column(BigInteger, nullable=False)  # Size in bytes
    
    # Timestamps
    created_at = Column(DateTime, nullable=True)
    updated_at = Column(DateTime, nullable=True)
    
    # Relationships
    pengajuan_seminar = relationship(
        "PengajuanSeminar",
        back_populates="files"
    )
