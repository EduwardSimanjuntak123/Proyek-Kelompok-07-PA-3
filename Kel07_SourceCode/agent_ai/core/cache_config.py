"""
Redis Caching Configuration
Centralized configuration untuk Redis cache system
"""

import os
from enum import Enum
from datetime import timedelta

class CacheConfig:
    """
    Redis Cache Configuration
    
    Customize dengan environment variables:
    - REDIS_HOST: Redis server host (default: localhost)
    - REDIS_PORT: Redis server port (default: 6379)
    - REDIS_DB: Redis database number (default: 0)
    - CACHE_ENABLED: Enable caching (default: True)
    - SYNC_CHECK_ENABLED: Enable sync validation (default: True)
    """
    
    # ==================== REDIS CONNECTION ====================
    REDIS_HOST = os.getenv("REDIS_HOST", "localhost")
    REDIS_PORT = int(os.getenv("REDIS_PORT", 6379))
    REDIS_DB = int(os.getenv("REDIS_DB", 0))
    REDIS_TIMEOUT = 5
    
    # ==================== FEATURE FLAGS ====================
    CACHE_ENABLED = os.getenv("CACHE_ENABLED", "true").lower() == "true"
    SYNC_CHECK_ENABLED = os.getenv("SYNC_CHECK_ENABLED", "true").lower() == "true"
    AUTO_INVALIDATE = os.getenv("AUTO_INVALIDATE", "true").lower() == "true"
    
    # ==================== TTL CONFIGURATION ====================
    # Default TTLs (in seconds)
    DEFAULT_TTL = 3600  # 1 hour
    QUERY_TTL = 1800    # 30 minutes  
    TOOL_TTL = 900      # 15 minutes
    STATE_TTL = 86400   # 24 hours
    METADATA_TTL = 86400 # 24 hours
    SESSION_TTL = 28800 # 8 hours
    
    # Entity-specific TTLs
    TTL_PEMBIMBING = 3600  # 1 hour
    TTL_KELOMPOK = 3600    # 1 hour
    TTL_MAHASISWA = 3600   # 1 hour
    TTL_NILAI = 1800       # 30 min (volatile)
    TTL_JADWAL = 3600      # 1 hour
    TTL_DOSEN = 7200       # 2 hours
    TTL_PENGUJI = 3600     # 1 hour
    
    # Query-specific TTLs
    TTL_QUERY_LIST = 1800      # 30 min (collections)
    TTL_QUERY_CONTEXT = 1800   # 30 min (filtered results)
    TTL_QUERY_COUNT = 900      # 15 min (aggregates)
    
    # ==================== BATCH SIZES ====================
    BATCH_SIZE_INVALIDATE = 100  # Invalidate max 100 entities per call
    BATCH_SIZE_SYNC = 50         # Check sync for max 50 entities per call
    
    # ==================== MONITORING ====================
    MONITOR_HIT_RATE = True
    TARGET_HIT_RATE = 0.85  # Target 85% hit rate
    LOG_CACHE_STATS = True
    STATS_INTERVAL = 3600  # Log stats every hour
    
    # ==================== PERFORMANCE ====================
    MAX_CACHE_SIZE_MB = 512  # Max cache size before cleanup
    CLEANUP_THRESHOLD = 0.9  # Cleanup when 90% full
    
    @classmethod
    def get_ttl(cls, entity_type: str) -> int:
        """Get TTL untuk specific entity type"""
        ttl_map = {
            "pembimbing": cls.TTL_PEMBIMBING,
            "kelompok": cls.TTL_KELOMPOK,
            "mahasiswa": cls.TTL_MAHASISWA,
            "nilai": cls.TTL_NILAI,
            "jadwal": cls.TTL_JADWAL,
            "dosen": cls.TTL_DOSEN,
            "penguji": cls.TTL_PENGUJI,
        }
        return ttl_map.get(entity_type, cls.DEFAULT_TTL)
    
    @classmethod
    def get_query_ttl(cls, query_type: str) -> int:
        """Get TTL untuk specific query type"""
        ttl_map = {
            "list": cls.TTL_QUERY_LIST,
            "context": cls.TTL_QUERY_CONTEXT,
            "count": cls.TTL_QUERY_COUNT,
        }
        return ttl_map.get(query_type, cls.QUERY_TTL)
    
    @classmethod
    def print_config(cls):
        """Print current configuration"""
        print("\n" + "="*60)
        print("REDIS CACHE CONFIGURATION")
        print("="*60)
        print(f"\n[CONNECTION]")
        print(f"  Host: {cls.REDIS_HOST}:{cls.REDIS_PORT}")
        print(f"  Database: {cls.REDIS_DB}")
        print(f"\n[FEATURES]")
        print(f"  Cache Enabled: {cls.CACHE_ENABLED}")
        print(f"  Sync Check: {cls.SYNC_CHECK_ENABLED}")
        print(f"  Auto Invalidate: {cls.AUTO_INVALIDATE}")
        print(f"\n[DEFAULT TTLs]")
        print(f"  General: {cls.DEFAULT_TTL}s")
        print(f"  Query: {cls.QUERY_TTL}s")
        print(f"  Tool: {cls.TOOL_TTL}s")
        print(f"  State: {cls.STATE_TTL}s")
        print(f"\n[ENTITY-SPECIFIC TTLs]")
        print(f"  Pembimbing: {cls.TTL_PEMBIMBING}s")
        print(f"  Kelompok: {cls.TTL_KELOMPOK}s")
        print(f"  Mahasiswa: {cls.TTL_MAHASISWA}s")
        print(f"  Nilai: {cls.TTL_NILAI}s")
        print(f"  Jadwal: {cls.TTL_JADWAL}s")
        print(f"\n[MONITORING]")
        print(f"  Hit Rate Target: {cls.TARGET_HIT_RATE*100}%")
        print(f"  Log Stats: {cls.LOG_CACHE_STATS} (every {cls.STATS_INTERVAL}s)")
        print("="*60 + "\n")


class CachePolicies:
    """
    Cache invalidation policies
    Define when dan how caches should be invalidated
    """
    
    # Cascade rules: when entity_type changes, invalidate related types
    CASCADE_RULES = {
        "pembimbing": ["kelompok", "query:pembimbing*"],
        "penguji": ["kelompok", "query:penguji*"],
        "dosen": ["pembimbing", "penguji", "jadwal", "dosen_role"],
        "kelompok": ["pembimbing", "penguji", "jadwal", "mahasiswa"],
        "mahasiswa": ["kelompok", "nilai", "mahasiswa*"],
        "nilai": ["mahasiswa", "analisis*"],
        "jadwal": ["kelompok"],
    }
    
    # TTL overrides untuk specific scenarios
    TTL_OVERRIDES = {
        "pembimbing:generated": 7200,  # Generated assignments live longer
        "query:pembimbing:assignment": 3600,
        "query:kelompok:with_members": 1800,
        "query:nilai:aggregated": 900,  # Aggregates refresh more often
    }
    
    # Entities yang sensitive dan need sync checking
    SENSITIVE_ENTITIES = {
        "pembimbing",
        "penguji",
        "nilai",
        "jadwal",
        "kelompok"
    }
    
    # Queries yang sangat sering accessed (keep longer TTL)
    HOT_QUERIES = {
        "pembimbing:all",
        "kelompok:all",
        "dosen:all",
        "mahasiswa:all",
    }
    
    @classmethod
    def should_sync_check(cls, entity_type: str) -> bool:
        """Check if entity should use sync validation"""
        return entity_type in cls.SENSITIVE_ENTITIES
    
    @classmethod
    def get_ttl_override(cls, cache_key: str) -> int:
        """Get TTL override untuk specific cache key"""
        return cls.TTL_OVERRIDES.get(cache_key)
    
    @classmethod
    def get_cascade_invalidations(cls, entity_type: str) -> list:
        """Get list of entity types to invalidate when entity_type changes"""
        return cls.CASCADE_RULES.get(entity_type, [])


# Default configuration print
if __name__ == "__main__":
    CacheConfig.print_config()

