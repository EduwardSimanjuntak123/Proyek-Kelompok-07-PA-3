"""
Model Penguji - Lecturer (Dosen) examiner assignment to Group (Kelompok)
Relationship: penguji.user_id <-> dosen.user_id
"""

from sqlalchemy import Column, Integer, ForeignKey, DateTime
from core.database import Base


class Penguji(Base):
    __tablename__ = "penguji"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer)  # Links to dosen.user_id
    kelompok_id = Column(Integer, ForeignKey("kelompok.id"))
    created_at = Column(DateTime, nullable=True)
    updated_at = Column(DateTime, nullable=True)
