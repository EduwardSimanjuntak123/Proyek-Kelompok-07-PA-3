"""
Advanced Memory Operations
Operasi untuk semua tipe memory (working, cache, episodic, semantic, procedural)
"""
from memory.advanced_models import (
    SessionLocal, init_advanced_db,
    WorkingMemory, CacheMemory, EpisodicMemory, 
    SemanticMemory, ProceduralMemory, MemoryIndex, MemoryCleanupLog
)
from datetime import datetime, timedelta
from typing import Dict, List, Optional, Any
import json

# ================= WORKING MEMORY OPERATIONS =================

def create_working_memory(user_id: int, session_id: str, task_id: str, 
                         content: str, ttl_minutes: int = 30):
    """Create working memory untuk current task"""
    db = SessionLocal()
    try:
        working = WorkingMemory(
            user_id=user_id,
            session_id=session_id,
            task_id=task_id,
            content=content,
            context_window={},
            task_state={},
            expires_at=datetime.utcnow() + timedelta(minutes=ttl_minutes)
        )
        db.add(working)
        db.commit()
        db.refresh(working)
        print(f"[WORKING MEMORY] Created for task {task_id}")
        return working
    except Exception as e:
        print(f"[ERROR] Create working memory failed: {e}")
        db.rollback()
        return None
    finally:
        db.close()


def update_working_memory(working_id: int, context: Dict = None, 
                         task_state: Dict = None, results: Dict = None):
    """Update working memory dengan context baru"""
    db = SessionLocal()
    try:
        working = db.query(WorkingMemory).filter(
            WorkingMemory.id == working_id
        ).first()
        
        if working:
            if context:
                working.context_window = context
            if task_state:
                working.task_state = task_state
            if results:
                working.intermediate_results = results
            
            working.last_accessed = datetime.utcnow()
            db.commit()
            return True
        return False
    except Exception as e:
        print(f"[ERROR] Update working memory failed: {e}")
        db.rollback()
        return False
    finally:
        db.close()


def get_active_working_memory(user_id: int, session_id: str):
    """Get active working memory untuk user session"""
    db = SessionLocal()
    try:
        working = db.query(WorkingMemory).filter(
            WorkingMemory.user_id == user_id,
            WorkingMemory.session_id == session_id,
            WorkingMemory.is_active == True,
            WorkingMemory.expires_at > datetime.utcnow()
        ).first()
        return working
    except Exception as e:
        print(f"[ERROR] Get working memory failed: {e}")
        return None
    finally:
        db.close()


def clear_expired_working_memory():
    """Clear expired working memory (untuk auto-cleanup)"""
    db = SessionLocal()
    try:
        now = datetime.utcnow()
        expired = db.query(WorkingMemory).filter(
            WorkingMemory.expires_at < now
        ).all()
        
        count = len(expired)
        for item in expired:
            db.delete(item)
        
        db.commit()
        
        # Log cleanup
        log = MemoryCleanupLog(
            memory_type="working",
            reason="expired",
            count_deleted=count
        )
        db.add(log)
        db.commit()
        
        print(f"[WORKING MEMORY] Cleared {count} expired entries")
        return count
    except Exception as e:
        print(f"[ERROR] Clear expired working memory failed: {e}")
        db.rollback()
        return 0
    finally:
        db.close()


# ================= CACHE MEMORY OPERATIONS =================

def cache_set(user_id: int, cache_key: str, cache_value: Any, 
              data_type: str = "general", ttl_minutes: int = 60):
    """Set cache entry"""
    db = SessionLocal()
    try:
        # Check if key exists
        existing = db.query(CacheMemory).filter(
            CacheMemory.cache_key == cache_key
        ).first()
        
        if existing:
            existing.cache_value = cache_value
            existing.data_type = data_type
            existing.hit_count += 1
            existing.last_accessed = datetime.utcnow()
            existing.expires_at = datetime.utcnow() + timedelta(minutes=ttl_minutes)
        else:
            cache = CacheMemory(
                user_id=user_id,
                cache_key=cache_key,
                cache_value=cache_value,
                data_type=data_type,
                size_bytes=len(str(cache_value)),
                expires_at=datetime.utcnow() + timedelta(minutes=ttl_minutes)
            )
            db.add(cache)
        
        db.commit()
        print(f"[CACHE] Set {cache_key}")
        return True
    except Exception as e:
        print(f"[ERROR] Cache set failed: {e}")
        db.rollback()
        return False
    finally:
        db.close()


def cache_get(cache_key: str):
    """Get cache entry"""
    db = SessionLocal()
    try:
        cache = db.query(CacheMemory).filter(
            CacheMemory.cache_key == cache_key,
            CacheMemory.expires_at > datetime.utcnow()
        ).first()
        
        if cache:
            cache.hit_count += 1
            cache.last_accessed = datetime.utcnow()
            db.commit()
            return cache.cache_value
        
        return None
    except Exception as e:
        print(f"[ERROR] Cache get failed: {e}")
        return None
    finally:
        db.close()


def cleanup_cache_lru(max_size_mb: int = 100):
    """LRU cache cleanup - remove least recently used"""
    db = SessionLocal()
    try:
        # Get total cache size
        all_caches = db.query(CacheMemory).all()
        total_size = sum(c.size_bytes or 0 for c in all_caches)
        total_mb = total_size / (1024 * 1024)
        
        if total_mb > max_size_mb:
            # Sort by last_accessed ascending (least recent first)
            sorted_caches = sorted(all_caches, key=lambda x: x.last_accessed)
            
            count_deleted = 0
            for cache in sorted_caches:
                if total_mb <= max_size_mb:
                    break
                
                db.delete(cache)
                total_mb -= (cache.size_bytes or 0) / (1024 * 1024)
                count_deleted += 1
            
            db.commit()
            
            # Log cleanup
            log = MemoryCleanupLog(
                memory_type="cache",
                reason="lru_eviction",
                count_deleted=count_deleted
            )
            db.add(log)
            db.commit()
            
            print(f"[CACHE] LRU cleanup: removed {count_deleted} entries")
            return count_deleted
        
        return 0
    except Exception as e:
        print(f"[ERROR] Cache LRU cleanup failed: {e}")
        db.rollback()
        return 0
    finally:
        db.close()


# ================= EPISODIC MEMORY OPERATIONS =================

def save_episode(user_id: int, episode_type: str, title: str, 
                description: str, event_data: Dict, outcome: Dict = None,
                importance: float = 1.0):
    """Save episodic memory for events/workflows"""
    db = SessionLocal()
    try:
        episode = EpisodicMemory(
            user_id=user_id,
            episode_type=episode_type,
            title=title,
            description=description,
            event_data=event_data,
            outcome=outcome or {},
            importance_score=min(importance, 1.0),
            episode_time=datetime.utcnow()
        )
        db.add(episode)
        db.commit()
        db.refresh(episode)
        print(f"[EPISODIC] Saved episode: {title}")
        return episode
    except Exception as e:
        print(f"[ERROR] Save episode failed: {e}")
        db.rollback()
        return None
    finally:
        db.close()


def get_user_episodes(user_id: int, limit: int = 10, 
                     episode_type: str = None):
    """Get past episodes for user"""
    db = SessionLocal()
    try:
        query = db.query(EpisodicMemory).filter(
            EpisodicMemory.user_id == user_id
        )
        
        if episode_type:
            query = query.filter(EpisodicMemory.episode_type == episode_type)
        
        episodes = query.order_by(
            EpisodicMemory.created_at.desc()
        ).limit(limit).all()
        
        return episodes
    except Exception as e:
        print(f"[ERROR] Get episodes failed: {e}")
        return []
    finally:
        db.close()


def recall_similar_episode(user_id: int, query_text: str, limit: int = 3):
    """Recall similar episodes based on keywords (semantic search simulation)"""
    db = SessionLocal()
    try:
        episodes = db.query(EpisodicMemory).filter(
            EpisodicMemory.user_id == user_id
        ).all()
        
        # Simple keyword matching
        matches = []
        query_lower = query_text.lower()
        
        for ep in episodes:
            if (query_lower in ep.title.lower() or 
                query_lower in ep.description.lower()):
                matches.append(ep)
        
        # Sort by importance then recency
        matches.sort(
            key=lambda x: (-x.importance_score, -x.created_at.timestamp())
        )
        
        return matches[:limit]
    except Exception as e:
        print(f"[ERROR] Recall episode failed: {e}")
        return []
    finally:
        db.close()


# ================= SEMANTIC MEMORY OPERATIONS =================

def save_knowledge(knowledge_type: str, topic: str, statement: str,
                  confidence: float = 1.0, source_type: str = "observation",
                  examples: List[str] = None):
    """Save semantic knowledge"""
    db = SessionLocal()
    try:
        knowledge = SemanticMemory(
            knowledge_type=knowledge_type,
            topic=topic,
            statement=statement,
            confidence=min(confidence, 1.0),
            source_type=source_type,
            examples=examples or [],
            evidence_count=1
        )
        db.add(knowledge)
        db.commit()
        db.refresh(knowledge)
        print(f"[SEMANTIC] Saved knowledge: {topic}")
        return knowledge
    except Exception as e:
        print(f"[ERROR] Save knowledge failed: {e}")
        db.rollback()
        return None
    finally:
        db.close()


def get_knowledge_by_topic(topic: str, limit: int = 5):
    """Get knowledge related to topic"""
    db = SessionLocal()
    try:
        knowledge = db.query(SemanticMemory).filter(
            SemanticMemory.topic == topic
        ).order_by(
            SemanticMemory.confidence.desc()
        ).limit(limit).all()
        
        return knowledge
    except Exception as e:
        print(f"[ERROR] Get knowledge failed: {e}")
        return []
    finally:
        db.close()


def update_knowledge_confidence(knowledge_id: int, new_confidence: float):
    """Increase confidence untuk knowledge (when confirmed)"""
    db = SessionLocal()
    try:
        knowledge = db.query(SemanticMemory).filter(
            SemanticMemory.id == knowledge_id
        ).first()
        
        if knowledge:
            knowledge.confidence = min(new_confidence, 1.0)
            knowledge.evidence_count += 1
            knowledge.updated_at = datetime.utcnow()
            db.commit()
            return True
        return False
    except Exception as e:
        print(f"[ERROR] Update knowledge confidence failed: {e}")
        db.rollback()
        return False
    finally:
        db.close()


# ================= PROCEDURAL MEMORY OPERATIONS =================

def save_procedure(procedure_name: str, description: str, 
                  category: str, steps: List[Dict],
                  success_rate: float = 1.0):
    """Save procedural knowledge (how-to)"""
    db = SessionLocal()
    try:
        procedure = ProceduralMemory(
            procedure_name=procedure_name,
            description=description,
            category=category,
            steps=steps,
            success_rate=min(success_rate, 1.0)
        )
        db.add(procedure)
        db.commit()
        db.refresh(procedure)
        print(f"[PROCEDURAL] Saved procedure: {procedure_name}")
        return procedure
    except Exception as e:
        print(f"[ERROR] Save procedure failed: {e}")
        db.rollback()
        return None
    finally:
        db.close()


def get_procedures_by_category(category: str):
    """Get all procedures in category"""
    db = SessionLocal()
    try:
        procedures = db.query(ProceduralMemory).filter(
            ProceduralMemory.category == category
        ).order_by(
            ProceduralMemory.success_rate.desc()
        ).all()
        
        return procedures
    except Exception as e:
        print(f"[ERROR] Get procedures failed: {e}")
        return []
    finally:
        db.close()


def update_procedure_execution(procedure_id: int, success: bool, 
                               execution_time: float):
    """Update procedure after execution"""
    db = SessionLocal()
    try:
        proc = db.query(ProceduralMemory).filter(
            ProceduralMemory.id == procedure_id
        ).first()
        
        if proc:
            old_rate = proc.success_rate
            old_count = proc.execution_count
            
            # Update success rate (running average)
            new_count = old_count + 1
            new_rate = ((old_rate * old_count) + (1.0 if success else 0.0)) / new_count
            
            proc.success_rate = new_rate
            proc.execution_count = new_count
            
            if proc.average_execution_time:
                proc.average_execution_time = (
                    (proc.average_execution_time * old_count) + execution_time
                ) / new_count
            else:
                proc.average_execution_time = execution_time
            
            proc.updated_at = datetime.utcnow()
            db.commit()
            return True
        return False
    except Exception as e:
        print(f"[ERROR] Update procedure execution failed: {e}")
        db.rollback()
        return False
    finally:
        db.close()


# ================= MEMORY INDEX OPERATIONS =================

def index_memory(memory_type: str, memory_id: int, keywords: List[str],
                content_summary: str):
    """Create index entry untuk memory for fast retrieval"""
    db = SessionLocal()
    try:
        index = MemoryIndex(
            memory_type=memory_type,
            memory_id=memory_id,
            keywords=keywords,
            content_summary=content_summary
        )
        db.add(index)
        db.commit()
        print(f"[INDEX] Indexed {memory_type}:{memory_id}")
        return True
    except Exception as e:
        print(f"[ERROR] Index memory failed: {e}")
        db.rollback()
        return False
    finally:
        db.close()


def search_memory_by_keywords(keywords: List[str], memory_type: str = None):
    """Search memory by keywords"""
    db = SessionLocal()
    try:
        query = db.query(MemoryIndex)
        
        if memory_type:
            query = query.filter(MemoryIndex.memory_type == memory_type)
        
        # Simple keyword matching
        results = []
        for index in query.all():
            for keyword in keywords:
                if keyword.lower() in [k.lower() for k in index.keywords or []]:
                    if index not in results:
                        results.append(index)
                    break
        
        return results
    except Exception as e:
        print(f"[ERROR] Search memory failed: {e}")
        return []
    finally:
        db.close()


# ================= MEMORY STATISTICS =================

def get_memory_statistics(user_id: int = None):
    """Get statistics tentang all memory"""
    db = SessionLocal()
    try:
        stats = {
            "working": db.query(WorkingMemory).filter(
                WorkingMemory.is_active == True
            ).count() if not user_id else db.query(WorkingMemory).filter(
                WorkingMemory.user_id == user_id,
                WorkingMemory.is_active == True
            ).count(),
            
            "cache": db.query(CacheMemory).count() if not user_id else db.query(
                CacheMemory
            ).filter(CacheMemory.user_id == user_id).count(),
            
            "episodic": db.query(EpisodicMemory).count() if not user_id else db.query(
                EpisodicMemory
            ).filter(EpisodicMemory.user_id == user_id).count(),
            
            "semantic": db.query(SemanticMemory).count(),
            "procedural": db.query(ProceduralMemory).count(),
        }
        
        return stats
    except Exception as e:
        print(f"[ERROR] Get memory stats failed: {e}")
        return {}
    finally:
        db.close()


# ================= INITIALIZATION =================
def initialize_advanced_memory():
    """Initialize advanced memory system"""
    init_advanced_db()
    print("[MEMORY] Advanced memory system initialized")

