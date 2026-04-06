"""
Advanced Memory System Models
Mengimplementasikan Short-Term dan Long-Term Memory seperti pada diagram
"""
from sqlalchemy import create_engine, Column, Integer, String, Text, DateTime, Float, Boolean, JSON, Enum
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker
from datetime import datetime, timedelta
import enum

DATABASE_URL = "mysql+pymysql://root:password@localhost:3307/vokasitera_BDv2"

engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()


# ================= ENUM TYPES =================
class MemoryType(str, enum.Enum):
    """Tipe-tipe memory sesuai diagram"""
    WORKING = "working"          # Short-term: Current task
    CACHE = "cache"              # Short-term: Recent data
    EPISODIC = "episodic"        # Long-term: Events/workflows
    SEMANTIC = "semantic"        # Long-term: Knowledge/concepts
    PROCEDURAL = "procedural"    # Long-term: How-to/processes


class MemoryDecay(str, enum.Enum):
    """Decay strategy untuk memory"""
    IMMEDIATE = "immediate"      # Hilang setelah diakses
    SHORT_TERM = "short_term"    # Hilang dalam hitungan menit/jam
    LONG_TERM = "long_term"      # Persistent
    LRU = "lru"                  # Least Recently Used


# ================= SHORT-TERM MEMORY =================

class WorkingMemory(Base):
    """
    SHORT-TERM: Current task & reasoning tracking
    Auto-expire setelah task selesai
    """
    __tablename__ = "working_memory"
    
    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, index=True)
    session_id = Column(String(100), index=True)  # Unique session identifier
    task_id = Column(String(100), index=True)     # Current task being worked on
    
    # Working content
    content = Column(Text)                         # Current reasoning/thoughts
    context_window = Column(JSON)                  # Current context (recent messages)
    task_state = Column(JSON)                      # Current state of task
    intermediate_results = Column(JSON, nullable=True)  # Partial results
    
    # Metadata
    priority = Column(Integer, default=1)         # Task priority (1-10)
    is_active = Column(Boolean, default=True)     # Whether task is active
    
    # Expiration
    created_at = Column(DateTime, default=datetime.utcnow, index=True)
    expires_at = Column(DateTime, index=True)     # Auto-expire time
    last_accessed = Column(DateTime, default=datetime.utcnow)
    
    def __repr__(self):
        return f"<WorkingMemory(task={self.task_id}, active={self.is_active})>"


class CacheMemory(Base):
    """
    SHORT-TERM: Recently used data/results
    LRU (Least Recently Used) cache strategy
    """
    __tablename__ = "cache_memory"
    
    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, index=True)
    
    # Cache key & value
    cache_key = Column(String(255), unique=True, index=True)  # Cache key
    cache_value = Column(JSON)                                # Cached data
    data_type = Column(String(50))  # Type: query_result, user_preference, etc
    
    # Metadata
    size_bytes = Column(Integer)                  # Size for LRU
    hit_count = Column(Integer, default=0)       # How many times accessed
    
    # Timing
    created_at = Column(DateTime, default=datetime.utcnow)
    last_accessed = Column(DateTime, default=datetime.utcnow, index=True)
    expires_at = Column(DateTime)                 # TTL-based expiry
    
    def __repr__(self):
        return f"<CacheMemory(key={self.cache_key}, hits={self.hit_count})>"


# ================= LONG-TERM MEMORY =================

class EpisodicMemory(Base):
    """
    LONG-TERM: Specific events, workflows, interactions
    "Apa yang terjadi ketika" - events dengan konteks lengkap
    """
    __tablename__ = "episodic_memory"
    
    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, index=True)
    
    # Episode details
    episode_type = Column(String(50))  # grouping_event, user_interaction, etc
    title = Column(String(255))        # Brief description
    description = Column(Text)         # Full episode description
    
    # Event context
    workflow_id = Column(String(100), nullable=True)  # Which workflow
    event_data = Column(JSON)                         # Complete event details
    participants = Column(JSON)                       # Who was involved
    outcome = Column(JSON, nullable=True)             # What was the result
    
    # Emotional/importance tag
    importance_score = Column(Float, default=1.0)     # 0-1: How important
    learning_value = Column(Text, nullable=True)      # What we learned
    
    # Timing & metadata
    created_at = Column(DateTime, default=datetime.utcnow, index=True)
    episode_time = Column(DateTime)                    # When it happened
    retrieval_count = Column(Integer, default=0)      # How often recalled
    
    def __repr__(self):
        return f"<EpisodicMemory(type={self.episode_type}, importance={self.importance_score})>"


class SemanticMemory(Base):
    """
    LONG-TERM: Knowledge, concepts, facts
    "Apa yang diketahui" - general knowledge
    """
    __tablename__ = "semantic_memory"
    
    id = Column(Integer, primary_key=True, index=True)
    
    # Knowledge details
    knowledge_type = Column(String(50), index=True)   # user_preference, pattern, rule, etc
    topic = Column(String(100), index=True)           # Topic/category
    
    # Knowledge content
    statement = Column(Text)                           # The actual knowledge
    explanation = Column(Text, nullable=True)         # Why this is true
    examples = Column(JSON, nullable=True)            # Example cases
    
    # Relations & context
    related_topics = Column(JSON, nullable=True)      # Related knowledge
    prerequisites = Column(JSON, nullable=True)       # What needs to know first
    
    # Confidence & metadata
    confidence = Column(Float, default=1.0)           # 0-1: How confident
    source_type = Column(String(50))                  # Where learned from (user_feedback, observation, etc)
    evidence_count = Column(Integer, default=1)       # Times confirmed
    
    # Timing
    created_at = Column(DateTime, default=datetime.utcnow, index=True)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    last_used = Column(DateTime, nullable=True)
    
    def __repr__(self):
        return f"<SemanticMemory(topic={self.topic}, confidence={self.confidence})>"


class ProceduralMemory(Base):
    """
    LONG-TERM: How-to knowledge, processes, skills
    "Bagaimana cara" - Procedural knowledge for actions
    """
    __tablename__ = "procedural_memory"
    
    id = Column(Integer, primary_key=True, index=True)
    
    # Procedure details
    procedure_name = Column(String(255), index=True)     # Name of procedure
    description = Column(Text)                            # What it does
    category = Column(String(100), index=True)           # Category (grouping, assignment, etc)
    
    # Steps
    steps = Column(JSON)                                  # List of steps
    prerequisites = Column(JSON, nullable=True)          # What needed before
    postconditions = Column(JSON, nullable=True)         # What should be true after
    
    # Conditions & context
    preconditions = Column(JSON, nullable=True)          # When applicable
    constraints = Column(JSON, nullable=True)            # Limitations
    
    # Performance metrics
    success_rate = Column(Float, default=1.0)            # % times it worked
    average_execution_time = Column(Float, nullable=True)  # Avg time in seconds
    execution_count = Column(Integer, default=0)         # Times executed
    
    # Optimization
    optimization_notes = Column(Text, nullable=True)     # How to improve
    
    # Timing
    created_at = Column(DateTime, default=datetime.utcnow, index=True)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    def __repr__(self):
        return f"<ProceduralMemory(name={self.procedure_name}, success_rate={self.success_rate})>"


# ================= MEMORY MANAGEMENT =================

class MemoryIndex(Base):
    """
    Index untuk cepat menemukan memory
    Like a catalog/index system
    """
    __tablename__ = "memory_index"
    
    id = Column(Integer, primary_key=True, index=True)
    
    # Index entry
    memory_type = Column(String(50), index=True)        # working, cache, episodic, etc
    memory_id = Column(Integer, index=True)             # ID di table terkait
    
    # Search keywords
    keywords = Column(JSON)                             # Search keywords
    content_summary = Column(Text)                      # Brief summary
    
    # Relationships
    related_memories = Column(JSON, nullable=True)      # IDs of related memories
    
    # Metadata
    relevance_score = Column(Float, default=1.0)        # 0-1
    created_at = Column(DateTime, default=datetime.utcnow, index=True)
    
    def __repr__(self):
        return f"<MemoryIndex(type={self.memory_type}, memory_id={self.memory_id})>"


class MemoryCleanupLog(Base):
    """
    Log untuk tracking memory cleanup
    Untuk debugging & optimization
    """
    __tablename__ = "memory_cleanup_log"
    
    id = Column(Integer, primary_key=True, index=True)
    
    # Cleanup details
    memory_type = Column(String(50))
    reason = Column(String(100))                    # expired, lru_eviction, manual_delete
    count_deleted = Column(Integer)                 # How many deleted
    
    # Metadata
    created_at = Column(DateTime, default=datetime.utcnow, index=True)
    
    def __repr__(self):
        return f"<MemoryCleanupLog(type={self.memory_type}, count={self.count_deleted})>"


# ================= DATABASE INITIALIZATION =================
def init_advanced_db():
    """Create all advanced memory tables"""
    Base.metadata.create_all(bind=engine)
    print("[DB] Advanced memory tables initialized successfully")


def get_advanced_db():
    """Get database session"""
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()


if __name__ == "__main__":
    init_advanced_db()
