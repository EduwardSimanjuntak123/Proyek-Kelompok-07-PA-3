"""
Model Pembimbing - Lecturer (Dosen) assignment to Group (Kelompok)

Relationships:
- user_id → Dosen (via User)
- kelompok_id → Kelompok
- Relationships with: KartuBimbingan (guidance notes), Role
"""

from typing import Optional, List
from datetime import datetime


class Pembimbing:
    """
    Pembimbing model - represents lecturer-group assignment
    
    Attributes:
        id: Primary key
        user_id: Reference to User/Dosen
        kelompok_id: Reference to group
        role_id: Role of pembimbing (optional)
        created_at: Created timestamp
        updated_at: Updated timestamp
        
    Relations:
        - dosen: Related Dosen through user_id
        - kelompok: Related Kelompok
        - role: Related Role
        - kartu_bimbingan: Related KartuBimbingan records (guidance notes)
    """
    
    def __init__(
        self,
        user_id: int,
        kelompok_id: int,
        id: Optional[int] = None,
        role_id: Optional[int] = None,
        created_at: Optional[str] = None,
        updated_at: Optional[str] = None,
        _dosen_rel: Optional[dict] = None,
        _kelompok_rel: Optional[dict] = None,
    ):
        self.id = id
        self.user_id = user_id
        self.kelompok_id = kelompok_id
        self.role_id = role_id
        self.created_at = created_at
        self.updated_at = updated_at
        
        # Relations (lazy loaded)
        self._dosen_rel = _dosen_rel  # Dosen info
        self._kelompok_rel = _kelompok_rel  # Kelompok info
    
    def to_dict(self) -> dict:
        """Convert to dictionary"""
        return {
            "id": self.id,
            "user_id": self.user_id,
            "kelompok_id": self.kelompok_id,
            "role_id": self.role_id,
            "created_at": self.created_at,
            "updated_at": self.updated_at,
            "dosen": self._dosen_rel,
            "kelompok": self._kelompok_rel,
        }
    
    def __repr__(self):
        doseninfo = f" - {self._dosen_rel.get('nama', '?')}" if self._dosen_rel else ""
        kelompokinfo = f" - Kelompok {self._kelompok_rel.get('nomor_kelompok', '?')}" if self._kelompok_rel else ""
        return f"Pembimbing(id={self.id}, user_id={self.user_id}{doseninfo}{kelompokinfo})"


# Schema for validation
PEMBIMBING_SCHEMA = {
    "id": "int",
    "user_id": "int",
    "kelompok_id": "int",
    "role_id": "int|null",
    "created_at": "timestamp",
    "updated_at": "timestamp",
}


def create_pembimbing(
    user_id: int,
    kelompok_id: int,
    **kwargs
) -> Pembimbing:
    """Factory function to create Pembimbing instance"""
    return Pembimbing(user_id=user_id, kelompok_id=kelompok_id, **kwargs)
