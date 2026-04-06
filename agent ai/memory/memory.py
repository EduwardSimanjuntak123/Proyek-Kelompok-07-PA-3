"""
Session-based memory system (NO DATABASE)
Semua data disimpan di memory, akan hilang saat aplikasi restart
Perfect untuk development phase
"""
from memory.models import (
    conversation_store, execution_store, draft_store,
    ConversationMemory, ExecutionResult, GroupingDraft,
    get_next_conversation_id, get_next_execution_id, get_next_draft_id
)
from datetime import datetime, timedelta
from typing import List, Dict, Optional
import json

# Backward compatibility aliases
memory_store = []  # Legacy - tidak digunakan lagi

# ================= DATABASE INITIALIZATION (DUMMY) =================
def initialize_memory_db():
    """Initialize - session-based, no database needed"""
    print("[MEMORY] Session-based memory initialized (no database)")


# ================= CONVERSATION MEMORY OPERATIONS =================
def save_conversation_memory(user_id: int, prompt: str, response: str, 
                           query_type: str = "general", metadata: Dict = None, 
                           status: str = "success"):
    """Simpan conversation ke session memory"""
    try:
        conv_memory = ConversationMemory(
            user_id=user_id,
            prompt=prompt,
            response=response,
            query_type=query_type,
            status=status,
            metadata_json=metadata or {}
        )
        
        # Simpan ke global store
        conversation_store.append({
            "id": conv_memory.id,
            "user_id": user_id,
            "prompt": prompt,
            "response": response,
            "type": query_type,
            "timestamp": conv_memory.created_at.isoformat(),
            "status": status,
            "feedback": None
        })
        
        print(f"[MEMORY] Conversation saved - ID: {conv_memory.id}, Type: {query_type}")
        return conv_memory
        
    except Exception as e:
        print(f"[ERROR] Save conversation memory failed: {e}")
        return None


def get_user_conversation_history(user_id: int, limit: int = 10, days: int = 30):
    """Get conversation history dari session memory"""
    try:
        cutoff_date = datetime.utcnow() - timedelta(days=days)
        
        # Filter conversations
        user_convs = [
            c for c in conversation_store 
            if c.get("user_id") == user_id and 
               datetime.fromisoformat(c.get("timestamp", datetime.utcnow().isoformat())) >= cutoff_date
        ]
        
        # Sort by timestamp descending dan limit
        user_convs = sorted(user_convs, key=lambda x: x.get("timestamp", ""), reverse=True)[:limit]
        
        return [
            {
                "id": c.get("id"),
                "prompt": c.get("prompt"),
                "response": c.get("response"),
                "type": c.get("type"),
                "created_at": c.get("timestamp"),
                "feedback": c.get("feedback")
            }
            for c in user_convs
        ]
    except Exception as e:
        print(f"[ERROR] Get conversation history failed: {e}")
        return []


def get_conversation_context(user_id: int, limit: int = 5):
    """Ambil N conversation terakhir untuk context"""
    conversations = get_user_conversation_history(user_id, limit=limit)
    context_text = ""
    for conv in reversed(conversations):  # Chronological order
        context_text += f"User: {conv['prompt']}\nAgent: {conv['response']}\n\n"
    return context_text


def save_feedback_to_conversation(conversation_id: int, feedback: str):
    """Update feedback untuk conversation"""
    try:
        for conv in conversation_store:
            if conv.get("id") == conversation_id:
                conv["feedback"] = feedback
                print(f"[MEMORY] Feedback saved for conversation {conversation_id}")
                return True
        return False
    except Exception as e:
        print(f"[ERROR] Save feedback failed: {e}")
        return False


# ================= EXECUTION RESULT OPERATIONS =================
def save_execution_result(user_id: int, result_type: str, result_data: Dict, 
                         execution_time: float = None):
    """Simpan hasil eksekusi ke session memory"""
    try:
        exec_result = ExecutionResult(
            user_id=user_id,
            result_type=result_type,
            result_data=result_data,
            execution_time=execution_time,
            is_applied=False,
            modifications={}
        )
        
        # Simpan ke global store
        execution_store.append({
            "id": exec_result.id,
            "user_id": user_id,
            "result_type": result_type,
            "result_data": result_data,
            "execution_time": execution_time,
            "is_applied": False,
            "modifications": {},
            "created_at": exec_result.created_at.isoformat()
        })
        
        print(f"[MEMORY] Execution result saved - ID: {exec_result.id}, Type: {result_type}")
        return exec_result
        
    except Exception as e:
        print(f"[ERROR] Save execution result failed: {e}")
        return None


def get_last_execution_result(user_id: int, result_type: str = None):
    """Get hasil eksekusi terakhir dari session memory"""
    try:
        # Filter results
        user_results = [r for r in execution_store if r.get("user_id") == user_id]
        
        if result_type:
            user_results = [r for r in user_results if r.get("result_type") == result_type]
        
        # Get latest
        if user_results:
            result = sorted(user_results, key=lambda x: x.get("created_at", ""), reverse=True)[0]
            return {
                "id": result.get("id"),
                "type": result.get("result_type"),
                "data": result.get("result_data"),
                "execution_time": result.get("execution_time"),
                "is_applied": result.get("is_applied"),
                "created_at": result.get("created_at")
            }
        return None
        
    except Exception as e:
        print(f"[ERROR] Get last execution result failed: {e}")
        return None


def apply_execution_result(result_id: int, modifications: Dict = None):
    """Mark execution result sebagai applied"""
    try:
        for result in execution_store:
            if result.get("id") == result_id:
                result["is_applied"] = True
                result["modifications"] = modifications or {}
                print(f"[MEMORY] Execution result {result_id} marked as applied")
                return True
        return False
        
    except Exception as e:
        print(f"[ERROR] Apply execution result failed: {e}")
        return False


# ================= AGENT INSTRUCTION OPERATIONS (DUMMY) =================
def save_agent_instruction(capability_name: str, description: str, 
                          system_prompt: str, examples: List[str], 
                          keywords: List[str]):
    """Dummy - not used in session-based system"""
    return None


def get_all_agent_instructions(active_only: bool = True):
    """Dummy - returns empty list"""
    return []


def get_agent_instruction(capability_name: str):
    """Dummy - returns None"""
    return None


# ================= USER LEARNING OPERATIONS (DUMMY) =================
def get_or_create_user_learning(user_id: int):
    """Dummy - not used in session-based system"""
    return None


def update_user_learning(user_id: int, **kwargs):
    """Dummy - not used in session-based system"""
    return True


def get_user_preferences(user_id: int):
    """Dummy - returns empty dict"""
    return {}


# ================= AGENT KNOWLEDGE OPERATIONS (DUMMY) =================
def save_agent_knowledge(knowledge_type: str, content: str, source: str, 
                        confidence: float = 1.0):
    """Dummy - not used in session-based system"""
    return None


def get_agent_knowledge(knowledge_type: str = None):
    """Dummy - returns empty list"""
    return []


# ================= BACKWARD COMPATIBILITY FUNCTIONS =================
def get_memory():
    """Legacy function - returns in-memory store"""
    return memory_store


def get_memory_by_user(user_id):
    """Legacy function - get conversations for user"""
    return get_user_conversation_history(user_id, limit=100)


def get_last_result_by_user(user_id):
    """Legacy function - get last execution result"""
    result = get_last_execution_result(user_id)
    if result and result.get("data"):
        return result["data"]
    return None


# ================= USER MEMORY CONTEXT OPERATIONS =================
def get_user_memory_context(user_id: int, limit: int = 3) -> Dict[str, str]:
    """
    Build context dari user memory untuk digunakan dalam prompt.
    
    Returns:
        Dict dengan keys:
        - 'last_instruction': Perintah terakhir user
        - 'last_result': Hasil eksekusi terakhir
        - 'context': Konteks conversation history
    """
    try:
        context = {
            'last_instruction': '',
            'last_result': '',
            'context': ''
        }
        
        # Ambil conversation history
        conversations = get_user_conversation_history(user_id, limit=limit)
        if conversations and len(conversations) > 0:
            # Last instruction = prompt dari conversation terakhir
            last_conv = conversations[0]  # Most recent
            context['last_instruction'] = last_conv.get('prompt', '')
            context['last_result'] = last_conv.get('response', '')
            
            # Build context dari history
            history_text = []
            for conv in reversed(conversations):
                hist_entry = f"Instruksi: {conv.get('prompt', '')}\nHasil: {conv.get('response', '')}"
                history_text.append(hist_entry)
            
            if history_text:
                context['context'] = "\n\n".join(history_text)
        
        return context
        
    except Exception as e:
        print(f"[ERROR] Get user memory context failed: {e}")
        return {'last_instruction': '', 'last_result': '', 'context': ''}


def build_memory_aware_prompt(prompt: str, user_id: int = None, system_context: str = "") -> str:
    """
    Build enriched prompt dengan konteks dari user memory.
    
    Args:
        prompt: Original user prompt
        user_id: ID user untuk mengambil memory
        system_context: Additional system context
        
    Returns:
        Enhanced prompt dengan memory context
    """
    try:
        if not user_id:
            return prompt
        
        memory = get_user_memory_context(user_id)
        
        context_prompt = f"""
{system_context}

Konteks dari permintaan sebelumnya:
"""
        
        if memory.get('context'):
            context_prompt += f"\nRiwayat percakapan:\n{memory.get('context')}\n"
        
        if memory.get('last_instruction'):
            context_prompt += f"\nInstruksi terakhir: {memory.get('last_instruction')}\n"
        
        if memory.get('last_result'):
            context_prompt += f"Hasil terakhir: {memory.get('last_result')}\n"
        
        context_prompt += f"\nInstruksi sekarang: {prompt}"
        
        return context_prompt
        
    except Exception as e:
        print(f"[ERROR] Build memory-aware prompt failed: {e}")
        return prompt


# ================= GROUPING DRAFT OPERATIONS =================
def save_grouping_draft(user_id: int, groups_data: list, kategori_pa: int, 
                       prodi_id: int, tm_id: int, tahun_ajaran_id: int,
                       grouping_method: str = "unknown", 
                       grouping_params: Dict = None):
    """Simpan draft hasil grouping ke session memory"""
    try:
        # Hitung statistik
        total_groups = len(groups_data)
        total_members = sum(len(g.get("members", [])) for g in groups_data)
        group_sizes = {}
        for g in groups_data:
            size = len(g.get("members", []))
            group_sizes[size] = group_sizes.get(size, 0) + 1
        
        # Buat draft
        draft = GroupingDraft(
            user_id=user_id,
            kategori_pa=kategori_pa,
            prodi_id=prodi_id,
            tm_id=tm_id,
            tahun_ajaran_id=tahun_ajaran_id,
            groups_data=groups_data,
            grouping_method=grouping_method,
            grouping_params=grouping_params or {},
            status="draft"
        )
        
        # Simpan ke global store
        draft_store.append({
            "id": draft.id,
            "user_id": user_id,
            "kategori_pa": kategori_pa,
            "prodi_id": prodi_id,
            "tm_id": tm_id,
            "tahun_ajaran_id": tahun_ajaran_id,
            "groups_data": groups_data,
            "grouping_method": grouping_method,
            "grouping_params": grouping_params or {},
            "status": "draft",
            "review_notes": None,
            "comments": [],
            "revisions": [],
            "total_groups": total_groups,
            "total_members": total_members,
            "group_sizes": group_sizes,
            "created_at": draft.created_at.isoformat(),
            "updated_at": draft.updated_at.isoformat(),
            "applied_at": None
        })
        
        print(f"[MEMORY] Grouping draft saved - ID: {draft.id}, Groups: {total_groups}, Members: {total_members}")
        return draft.id
        
    except Exception as e:
        print(f"[ERROR] Save grouping draft failed: {e}")
        return None


def get_grouping_draft(draft_id: int):
    """Ambil draft kelompok berdasarkan ID"""
    try:
        for draft in draft_store:
            if draft.get("id") == draft_id:
                return {
                    "id": draft.get("id"),
                    "user_id": draft.get("user_id"),
                    "groups_data": draft.get("groups_data"),
                    "grouping_method": draft.get("grouping_method"),
                    "grouping_params": draft.get("grouping_params"),
                    "status": draft.get("status"),
                    "total_groups": draft.get("total_groups"),
                    "total_members": draft.get("total_members"),
                    "group_sizes": draft.get("group_sizes"),
                    "review_notes": draft.get("review_notes"),
                    "comments": draft.get("comments", []),
                    "created_at": draft.get("created_at"),
                    "updated_at": draft.get("updated_at"),
                    "context": {
                        "kategori_pa": draft.get("kategori_pa"),
                        "prodi_id": draft.get("prodi_id"),
                        "tm_id": draft.get("tm_id"),
                        "tahun_ajaran_id": draft.get("tahun_ajaran_id")
                    }
                }
        return None
    except Exception as e:
        print(f"[ERROR] Get grouping draft failed: {e}")
        return None


def get_latest_grouping_draft(user_id: int):
    """Ambil draft kelompok terbaru untuk user"""
    try:
        # Filter drafts for user
        user_drafts = [d for d in draft_store if d.get("user_id") == user_id]
        
        if not user_drafts:
            return None
        
        # Get latest by created_at
        draft = sorted(user_drafts, key=lambda x: x.get("created_at", ""), reverse=True)[0]
        
        return {
            "id": draft.get("id"),
            "user_id": draft.get("user_id"),
            "groups_data": draft.get("groups_data"),
            "grouping_method": draft.get("grouping_method"),
            "status": draft.get("status"),
            "total_groups": draft.get("total_groups"),
            "total_members": draft.get("total_members"),
            "review_notes": draft.get("review_notes"),
            "comments": draft.get("comments", []),
            "created_at": draft.get("created_at"),
            "context": {
                "kategori_pa": draft.get("kategori_pa"),
                "prodi_id": draft.get("prodi_id"),
                "tm_id": draft.get("tm_id"),
                "tahun_ajaran_id": draft.get("tahun_ajaran_id")
            }
        }
    except Exception as e:
        print(f"[ERROR] Get latest grouping draft failed: {e}")
        return None


def add_comment_to_draft(draft_id: int, user_id: int, comment: str, 
                        comment_type: str = "review"):
    """Tambah komentar ke draft"""
    try:
        for draft in draft_store:
            if draft.get("id") == draft_id:
                new_comment = {
                    "timestamp": datetime.utcnow().isoformat(),
                    "user_id": user_id,
                    "comment": comment,
                    "type": comment_type
                }
                draft["comments"].append(new_comment)
                draft["updated_at"] = datetime.utcnow().isoformat()
                print(f"[MEMORY] Comment added to draft {draft_id}")
                return True
        
        print(f"[ERROR] Draft {draft_id} not found")
        return False
        
    except Exception as e:
        print(f"[ERROR] Add comment to draft failed: {e}")
        return False


def update_draft_status(draft_id: int, new_status: str, notes: str = ""):
    """Update status draft"""
    try:
        for draft in draft_store:
            if draft.get("id") == draft_id:
                old_status = draft.get("status")
                draft["status"] = new_status
                draft["review_notes"] = notes
                draft["updated_at"] = datetime.utcnow().isoformat()
                
                if new_status == "applied":
                    draft["applied_at"] = datetime.utcnow().isoformat()
                
                print(f"[MEMORY] Draft {draft_id} status updated: {old_status} → {new_status}")
                return True
        
        return False
        
    except Exception as e:
        print(f"[ERROR] Update draft status failed: {e}")
        return False


def update_draft_groups(draft_id: int, new_groups_data: list, reason: str = ""):
    """Update data kelompok di draft"""
    try:
        for draft in draft_store:
            if draft.get("id") == draft_id:
                # Hitung statistik baru
                new_total_groups = len(new_groups_data)
                new_total_members = sum(len(g.get("members", [])) for g in new_groups_data)
                
                # Simpan revisi info
                revision_record = {
                    "timestamp": datetime.utcnow().isoformat(),
                    "reason": reason,
                    "old_groups_count": draft.get("total_groups"),
                    "new_groups_count": new_total_groups,
                    "old_members_count": draft.get("total_members"),
                    "new_members_count": new_total_members
                }
                
                draft["revisions"].append(revision_record)
                draft["groups_data"] = new_groups_data
                draft["total_groups"] = new_total_groups
                draft["total_members"] = new_total_members
                draft["updated_at"] = datetime.utcnow().isoformat()
                
                print(f"[MEMORY] Draft {draft_id} groups updated - Revision: {reason}")
                return True
        
        return False
        
    except Exception as e:
        print(f"[ERROR] Update draft groups failed: {e}")
        return False


def get_draft_history(user_id: int, limit: int = 10):
    """Ambil history draft kelompok user"""
    try:
        # Filter drafts for user
        user_drafts = [d for d in draft_store if d.get("user_id") == user_id]
        
        # Sort by created_at descending and limit
        user_drafts = sorted(user_drafts, key=lambda x: x.get("created_at", ""), reverse=True)[:limit]
        
        return [
            {
                "id": d.get("id"),
                "grouping_method": d.get("grouping_method"),
                "status": d.get("status"),
                "total_groups": d.get("total_groups"),
                "total_members": d.get("total_members"),
                "comment_count": len(d.get("comments", [])),
                "created_at": d.get("created_at"),
                "updated_at": d.get("updated_at")
            }
            for d in user_drafts
        ]
    except Exception as e:
        print(f"[ERROR] Get draft history failed: {e}")
        return []


def save_memory(entry):
    """Legacy function - save to in-memory store"""
    memory_store.append(entry)


def clear_memory_by_user(user_id):
    """Legacy function - clear in-memory entries for user"""
    global memory_store
    memory_store = [m for m in memory_store if m.get("user_id") != user_id]


# Initialize memory on import
initialize_memory_db()