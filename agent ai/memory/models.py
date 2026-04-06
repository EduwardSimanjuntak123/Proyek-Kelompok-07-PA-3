"""
Session-based memory system - semua data disimpan di memory (session), bukan database
Data akan hilang saat aplikasi restart
Lebih simple, cepat, dan perfect untuk development
"""
from datetime import datetime
from typing import List, Dict, Optional

# ================= GLOBAL IN-MEMORY STORAGE =================
conversation_store = []  # Simpan semua conversation
execution_store = []  # Simpan hasil execution
draft_store = []  # Simpan grouping draft

_conversation_id_counter = 1
_execution_id_counter = 1
_draft_id_counter = 1


# ================= MODEL HELPERS (Session-Based) =================

def get_next_conversation_id():
    global _conversation_id_counter
    id = _conversation_id_counter
    _conversation_id_counter += 1
    return id

def get_next_execution_id():
    global _execution_id_counter
    id = _execution_id_counter
    _execution_id_counter += 1
    return id

def get_next_draft_id():
    global _draft_id_counter
    id = _draft_id_counter
    _draft_id_counter += 1
    return id


class ConversationMemory:
    """Menyimpan setiap percakapan user dengan agent"""
    def __init__(self, user_id: int, prompt: str, response: str, query_type: str, 
                 status: str = "success", metadata_json: Optional[Dict] = None, 
                 feedback: Optional[str] = None):
        self.id = get_next_conversation_id()
        self.user_id = user_id
        self.prompt = prompt
        self.response = response
        self.query_type = query_type
        self.status = status
        self.metadata_json = metadata_json or {}
        self.feedback = feedback
        self.created_at = datetime.utcnow()
    
    def __repr__(self):
        return f"<ConversationMemory(user_id={self.user_id}, type={self.query_type})>"


class ExecutionResult:
    """Menyimpan hasil eksekusi (grouping results, assignments, dll)"""
    def __init__(self, user_id: int, result_type: str, result_data: Dict,
                 execution_time: Optional[float] = None, is_applied: bool = False,
                 modifications: Optional[Dict] = None):
        self.id = get_next_execution_id()
        self.user_id = user_id
        self.result_type = result_type
        self.result_data = result_data
        self.execution_time = execution_time
        self.is_applied = is_applied
        self.modifications = modifications or {}
        self.created_at = datetime.utcnow()
    
    def __repr__(self):
        return f"<ExecutionResult(user_id={self.user_id}, type={self.result_type})>"


class GroupingDraft:
    """Menyimpan draft kelompok yang belum disimpan ke database
    
    Workflow:
    1. Generate kelompok → Simpan as draft
    2. User review & comment
    3. User approve/reject/revise → Update draft
    4. User confirm → Save to actual kelompok table
    """
    def __init__(self, user_id: int, kategori_pa: int, prodi_id: int, tm_id: int,
                 tahun_ajaran_id: int, groups_data: List[Dict], grouping_method: str,
                 grouping_params: Optional[Dict] = None, status: str = "draft"):
        self.id = get_next_draft_id()
        self.user_id = user_id
        self.kategori_pa = kategori_pa
        self.prodi_id = prodi_id
        self.tm_id = tm_id
        self.tahun_ajaran_id = tahun_ajaran_id
        self.groups_data = groups_data
        self.grouping_method = grouping_method
        self.grouping_params = grouping_params or {}
        self.status = status
        self.review_notes = None
        self.comments = []
        self.revisions = []
        self.total_groups = len(groups_data)
        self.total_members = sum(len(g.get("members", [])) for g in groups_data)
        self.group_sizes = [len(g.get("members", [])) for g in groups_data]
        self.created_at = datetime.utcnow()
        self.updated_at = datetime.utcnow()
        self.applied_at = None
    
    def __repr__(self):
        return f"<GroupingDraft(user_id={self.user_id}, status={self.status}, groups={self.total_groups})>"


# ================= HELPER FUNCTIONS =================

def dict_from_model(obj: any) -> Dict:
    """Convert model instance to dict"""
    if hasattr(obj, '__dict__'):
        return {k: v for k, v in obj.__dict__.items() if not k.startswith('_')}
    return obj


def get_db():
    """Dummy function - maintained for compatibility"""
    return None


def init_db():
    """Dummy function - maintained for compatibility"""
    print("[DB] Using session-based memory - no database initialization needed")
