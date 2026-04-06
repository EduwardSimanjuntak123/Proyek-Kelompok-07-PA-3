from pydantic import BaseModel
from typing import List, Optional

class DosenContext(BaseModel):
    user_id: int
    angkatan: Optional[int]
    prodi: Optional[str]
    prodi_id: Optional[int]
    role: Optional[str]
    kategori_pa: Optional[int]

class GenerateRequest(BaseModel):
    prompt: str
    dosen_context: List[DosenContext]
    feedback: Optional[str] = None
    group_size: Optional[int] = 6
    prodi_filter: Optional[str] = None
    avoid_pairs: Optional[List[List[str]]] = None
    must_pairs: Optional[List[List[str]]] = None
    count_only: Optional[bool] = False
    shuffle: Optional[bool] = False
    show_fields: Optional[List[str]] = None  # Field mana saja yang ditampilkan di tabel