import json
import os
from datetime import datetime
from typing import List, Dict, Optional
from langgraph.checkpoint.memory import InMemorySaver
from langgraph.store.memory import InMemoryStore

MEMORY_DIR = "conversation_history"

# Buat folder jika tidak ada
if not os.path.exists(MEMORY_DIR):
    os.makedirs(MEMORY_DIR)

# Global memory instances
SHORT_TERM_CHECKPOINTER = InMemorySaver()
LONG_TERM_STORE = InMemoryStore()


class ConversationMemory:
    """
    Sistem memory untuk menyimpan dan load conversation history
    Menggunakan file JSON untuk persistence
    """
    
    def __init__(self, user_id: str = "default"):
        self.user_id = user_id
        self.memory_file = os.path.join(MEMORY_DIR, f"{user_id}_history.json")
    
    def save_conversation(self, messages: List[Dict]) -> bool:
        """
        Simpan conversation history ke file JSON
        """
        try:
            data = {
                "user_id": self.user_id,
                "last_updated": datetime.now().isoformat(),
                "messages": messages
            }
            
            with open(self.memory_file, 'w', encoding='utf-8') as f:
                json.dump(data, f, ensure_ascii=False, indent=2)
            
            return True
        except Exception as e:
            print(f"Error saving conversation: {e}")
            return False
    
    def load_conversation(self) -> List[Dict]:
        """
        Load conversation history dari file JSON
        Return empty list jika file tidak ada
        """
        try:
            if os.path.exists(self.memory_file):
                with open(self.memory_file, 'r', encoding='utf-8') as f:
                    data = json.load(f)
                    return data.get("messages", [])
            return []
        except Exception as e:
            print(f"Error loading conversation: {e}")
            return []
    
    def clear_conversation(self) -> bool:
        """
        Hapus conversation history
        """
        try:
            if os.path.exists(self.memory_file):
                os.remove(self.memory_file)
            return True
        except Exception as e:
            print(f"Error clearing conversation: {e}")
            return False
    
    def get_conversation_summary(self) -> Dict:
        """
        Buat summary dari conversation (last 5 messages)
        Berguna untuk context awareness
        """
        messages = self.load_conversation()
        
        if not messages:
            return {"total_messages": 0, "recent_context": []}
        
        # Ambil 5 pesan terakhir untuk context
        recent_messages = messages[-5:]
        
        return {
            "total_messages": len(messages),
            "last_updated": messages[-1].get("timestamp", "unknown") if messages else None,
            "recent_context": recent_messages
        }
    
    def add_message(self, role: str, content: str) -> bool:
        """
        Tambah satu message ke conversation dan simpan
        """
        try:
            messages = self.load_conversation()
            
            message = {
                "role": role,
                "content": content,
                "timestamp": datetime.now().isoformat()
            }
            
            messages.append(message)
            return self.save_conversation(messages)
        except Exception as e:
            print(f"Error adding message: {e}")
            return False


class SemanticMemory:
    """
    Memory untuk menyimpan facts/context tentang user atau sistem
    Contoh: nama user, prodi, tahun akademik, dll
    """
    
    def __init__(self, user_id: str = "default"):
        self.user_id = user_id
        self.context_file = os.path.join(MEMORY_DIR, f"{user_id}_context.json")
    
    def save_context(self, context: Dict) -> bool:
        """Simpan user context/facts"""
        try:
            data = {
                "user_id": self.user_id,
                "last_updated": datetime.now().isoformat(),
                "context": context
            }
            
            with open(self.context_file, 'w', encoding='utf-8') as f:
                json.dump(data, f, ensure_ascii=False, indent=2)
            
            return True
        except Exception as e:
            print(f"Error saving context: {e}")
            return False
    
    def load_context(self) -> Dict:
        """Load user context/facts"""
        try:
            if os.path.exists(self.context_file):
                with open(self.context_file, 'r', encoding='utf-8') as f:
                    data = json.load(f)
                    return data.get("context", {})
            return {}
        except Exception as e:
            print(f"Error loading context: {e}")
            return {}
    
    def update_context(self, key: str, value) -> bool:
        """Update satu field dalam context"""
        try:
            context = self.load_context()
            context[key] = value
            return self.save_context(context)
        except Exception as e:
            print(f"Error updating context: {e}")
            return False
    
    def get_context_summary(self) -> str:
        """
        Buat summary context untuk di-insert ke system prompt
        """
        context = self.load_context()
        
        if not context:
            return "No additional context available."
        
        summary = "User Context:\n"
        for key, value in context.items():
            summary += f"- {key}: {value}\n"
        
        return summary


class LongTermMemory:
    """
    Long-term memory menggunakan InMemoryStore dari LangGraph
    Untuk store structured data: entities, relationships, facts
    
    Scope: "user:{user_id}" atau custom scopes
    """
    
    def __init__(self, user_id: str = "default", store=None):
        self.user_id = user_id
        self.store = store if store else LONG_TERM_STORE
        self.base_namespace = f"user:{user_id}"
    
    def save_entity(self, entity_type: str, entity_id: str, data: Dict) -> bool:
        """
        Simpan entity ke long-term store
        
        Args:
            entity_type: "mahasiswa", "dosen", "kelompok", dll
            entity_id: unique identifier untuk entity
            data: dict data entity
        """
        try:
            namespace = (self.base_namespace, entity_type)
            key = f"{entity_type}:{entity_id}"
            
            value = {
                "id": entity_id,
                "type": entity_type,
                "data": data,
                "timestamp": datetime.now().isoformat()
            }
            
            self.store.put(
                namespace,
                key,
                value
            )
            return True
        except Exception as e:
            print(f"Error saving entity: {e}")
            return False
    
    def get_entity(self, entity_type: str, entity_id: str) -> Optional[Dict]:
        """
        Ambil entity dari long-term store
        """
        try:
            namespace = (self.base_namespace, entity_type)
            key = f"{entity_type}:{entity_id}"
            
            items = self.store.get(namespace, key)
            
            # items bisa berupa single Item atau list of Items
            if items:
                if isinstance(items, list):
                    item = items[0] if len(items) > 0 else None
                else:
                    item = items
                
                if item and hasattr(item, 'value'):
                    return item.value
                elif item:
                    return item
            return None
        except Exception as e:
            print(f"Error getting entity: {e}")
            return None
    
    def search_entities(self, entity_type: str, query: str = "") -> List[Dict]:
        """
        Search entities dari long-term store
        """
        try:
            namespace = (self.base_namespace, entity_type)
            
            items = self.store.search(
                namespace,
                query=query,
                limit=50
            )
            
            return [item.value for item in items] if items else []
        except Exception as e:
            print(f"Error searching entities: {e}")
            return []
    
    def delete_entity(self, entity_type: str, entity_id: str) -> bool:
        """
        Hapus entity dari long-term store
        """
        try:
            namespace = (self.base_namespace, entity_type)
            key = f"{entity_type}:{entity_id}"
            
            self.store.delete(namespace, key)
            return True
        except Exception as e:
            print(f"Error deleting entity: {e}")
            return False
    
    def save_fact(self, fact_type: str, content: str, metadata: Dict = None) -> bool:
        """
        Simpan fact/knowledge items
        
        Contoh:
        save_fact("question_history", "Berapa IPK saya?", {"answer_count": 2})
        """
        try:
            import uuid
            fact_id = str(uuid.uuid4())
            namespace = (self.base_namespace, fact_type)
            
            fact = {
                "id": fact_id,
                "type": fact_type,
                "content": content,
                "metadata": metadata or {},
                "timestamp": datetime.now().isoformat()
            }
            
            self.store.put(namespace, fact_id, fact)
            return True
        except Exception as e:
            print(f"Error saving fact: {e}")
            return False
    
    def get_facts(self, fact_type: str, limit: int = 10) -> List[Dict]:
        """
        Ambil facts dari long-term store
        """
        try:
            namespace = (self.base_namespace, fact_type)
            
            items = self.store.search(
                namespace,
                query="",
                limit=limit
            )
            
            return [item.value for item in items] if items else []
        except Exception as e:
            print(f"Error getting facts: {e}")
            return []
