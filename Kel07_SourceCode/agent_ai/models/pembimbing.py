"""
Model Pembimbing - Lecturer (Dosen) assignment to Group (Kelompok)
Relationship: pembimbing.user_id <-> dosen.user_id
"""

from sqlalchemy import Column, Integer, ForeignKey, DateTime
from core.database import Base


class Pembimbing(Base):
    __tablename__ = "pembimbing"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer)  # Links to dosen.user_id
    kelompok_id = Column(Integer, ForeignKey("kelompok.id"))
    created_at = Column(DateTime, nullable=True)
    updated_at = Column(DateTime, nullable=True)
