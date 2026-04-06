from pydantic import BaseModel
from typing import List, Optional, Dict, Any


class StudentDetail(BaseModel):
    """Detail satu mahasiswa dalam kelompok"""
    nama: str
    nim: str
    nilai_rata_rata: Optional[float] = None
    semesters: Optional[List[int]] = None


class GroupDetail(BaseModel):
    """Detail satu kelompok hasil grouping"""
    kelompok: int
    members: List[StudentDetail]
    group_average: Optional[float] = None
    deviation_from_class: Optional[float] = None
    member_count: int


class GroupingResult(BaseModel):
    """Hasil pengelompokan"""
    result: str  # HTML formatted result
    plan: str  # JSON string dari plan
    memory: List[Dict[str, Any]]  # User memory history
