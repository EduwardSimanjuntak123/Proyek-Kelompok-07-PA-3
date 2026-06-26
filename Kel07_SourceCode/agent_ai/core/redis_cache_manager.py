"""
Advanced Redis Cache Manager untuk Agent Memory System
Handles all caching, invalidation, dan sync checking dengan database
"""

import redis
import json
import logging
import hashlib
from typing import Any, Dict, Optional, List, Callable
from datetime import datetime, timedelta
from functools import wraps
import os

logger = logging.getLogger(__name__)

class RedisCacheManager:
    """
    Advanced Redis Cache Manager dengan:
    - Multi-level caching (data, metadata, state)
    - Cache invalidation strategy
    - Sync validation dengan database
    - TTL management
    - Query result caching
    - Tool execution caching
    """
    
    # Cache prefixes
    PREFIX_DATA = "cache:data:"
    PREFIX_QUERY = "cache:query:"
    PREFIX_TOOL = "cache:tool:"
    PREFIX_STATE = "cache:state:"
    PREFIX_SYNC = "cache:sync:"
    PREFIX_METADATA = "cache:meta:"
    PREFIX_SESSION = "cache:session:"
    
    # Default TTLs (seconds)
    DEFAULT_TTL = 3600  # 1 hour
    QUERY_TTL = 1800    # 30 minutes
    TOOL_TTL = 900      # 15 minutes
    STATE_TTL = 86400   # 24 hours
    METADATA_TTL = 86400 # 24 hours
    
    def __init__(self, host: str = None, port: int = None, db: int = 0):
        """
        Initialize Redis Cache Manager
        
        Args:
            host: Redis host (default from env REDIS_HOST or localhost)
            port: Redis port (default from env REDIS_PORT or 6379)
            db: Redis database number
        """
        self.host = host or os.getenv("REDIS_HOST", "localhost")
        self.port = port or int(os.getenv("REDIS_PORT", 6379))
        self.db = db
        
        try:
            self.r = redis.Redis(
                host=self.host,
                port=self.port,
                db=db,
                decode_responses=True,
                socket_connect_timeout=5,
                socket_keepalive=True,
            )
            self.r.ping()
            logger.info(f"✓ Redis Cache Manager connected: {self.host}:{self.port}/db{db}")
        except Exception as e:
            logger.error(f"✗ Redis Cache Manager connection failed: {e}")
            raise

    def _make_key(self, prefix: str, *args) -> str:
        """Create cache key dari prefix dan arguments"""
        key_parts = [str(arg) for arg in args]
        return prefix + ":".join(key_parts)

    def _make_hash(self, data: Any) -> str:
        """Create hash dari data untuk validation"""
        try:
            serialized = json.dumps(data, sort_keys=True, default=str)
            return hashlib.md5(serialized.encode()).hexdigest()
        except:
            return hashlib.md5(str(data).encode()).hexdigest()

    # ==================== QUERY CACHING ====================
    
    def cache_query(self, query_key: str, data: Any, ttl: int = None, validate_hash: bool = True) -> bool:
        """
        Cache query result dengan optional hash validation
        
        Args:
            query_key: Unique query identifier
            data: Query result data
            ttl: Time to live (default: QUERY_TTL)
            validate_hash: Store hash untuk cache validation
        
        Returns:
            True if successful
        """
        try:
            ttl = ttl or self.QUERY_TTL
            cache_key = self._make_key(self.PREFIX_QUERY, query_key)
            
            # Store data
            self.r.setex(cache_key, ttl, json.dumps(data, default=str, ensure_ascii=False))
            
            # Store hash untuk validation
            if validate_hash:
                hash_key = self._make_key(self.PREFIX_METADATA, "query_hash", query_key)
                data_hash = self._make_hash(data)
                self.r.setex(hash_key, ttl, data_hash)
            
            logger.debug(f"[CACHE] Query cached: {query_key} (TTL: {ttl}s)")
            return True
        except Exception as e:
            logger.error(f"[CACHE] Error caching query {query_key}: {e}")
            return False

    def get_cached_query(self, query_key: str) -> Optional[Any]:
        """
        Get cached query result
        
        Returns:
            Query data atau None jika cache miss/expired
        """
        try:
            cache_key = self._make_key(self.PREFIX_QUERY, query_key)
            cached = self.r.get(cache_key)
            
            if cached:
                logger.debug(f"[CACHE] Query hit: {query_key}")
                return json.loads(cached)
            
            logger.debug(f"[CACHE] Query miss: {query_key}")
            return None
        except Exception as e:
            logger.error(f"[CACHE] Error getting query {query_key}: {e}")
            return None

    def invalidate_query(self, query_key: str) -> bool:
        """Invalidate specific query cache"""
        try:
            cache_key = self._make_key(self.PREFIX_QUERY, query_key)
            hash_key = self._make_key(self.PREFIX_METADATA, "query_hash", query_key)
            
            self.r.delete(cache_key, hash_key)
            logger.debug(f"[CACHE] Query invalidated: {query_key}")
            return True
        except Exception as e:
            logger.error(f"[CACHE] Error invalidating query {query_key}: {e}")
            return False

    # ==================== DATA CACHING (Entities) ====================
    
    def cache_entity(self, entity_type: str, entity_id: Any, data: Dict, ttl: int = None) -> bool:
        """
        Cache entity data (kelompok, pembimbing, mahasiswa, etc)
        
        Args:
            entity_type: Type of entity (kelompok, pembimbing, mahasiswa, etc)
            entity_id: Entity ID
            data: Entity data
            ttl: Time to live (default: DEFAULT_TTL)
        """
        try:
            ttl = ttl or self.DEFAULT_TTL
            cache_key = self._make_key(self.PREFIX_DATA, entity_type, entity_id)
            
            self.r.setex(cache_key, ttl, json.dumps(data, default=str, ensure_ascii=False))
            
            # Track entity di index
            index_key = self._make_key(self.PREFIX_METADATA, "entities", entity_type)
            self.r.sadd(index_key, str(entity_id))
            
            logger.debug(f"[CACHE] Entity cached: {entity_type}:{entity_id}")
            return True
        except Exception as e:
            logger.error(f"[CACHE] Error caching entity {entity_type}:{entity_id}: {e}")
            return False

    def get_cached_entity(self, entity_type: str, entity_id: Any) -> Optional[Dict]:
        """Get cached entity data"""
        try:
            cache_key = self._make_key(self.PREFIX_DATA, entity_type, entity_id)
            cached = self.r.get(cache_key)
            
            if cached:
                logger.debug(f"[CACHE] Entity hit: {entity_type}:{entity_id}")
                return json.loads(cached)
            
            logger.debug(f"[CACHE] Entity miss: {entity_type}:{entity_id}")
            return None
        except Exception as e:
            logger.error(f"[CACHE] Error getting entity {entity_type}:{entity_id}: {e}")
            return None

    def invalidate_entity_type(self, entity_type: str) -> bool:
        """Invalidate all entities of a type"""
        try:
            index_key = self._make_key(self.PREFIX_METADATA, "entities", entity_type)
            entity_ids = self.r.smembers(index_key)
            
            for entity_id in entity_ids:
                cache_key = self._make_key(self.PREFIX_DATA, entity_type, entity_id)
                self.r.delete(cache_key)
            
            self.r.delete(index_key)
            logger.debug(f"[CACHE] Entity type invalidated: {entity_type}")
            return True
        except Exception as e:
            logger.error(f"[CACHE] Error invalidating entity type {entity_type}: {e}")
            return False

    def invalidate_entity(self, entity_type: str, entity_id: Any) -> bool:
        """Invalidate specific entity"""
        try:
            cache_key = self._make_key(self.PREFIX_DATA, entity_type, entity_id)
            index_key = self._make_key(self.PREFIX_METADATA, "entities", entity_type)
            
            self.r.delete(cache_key)
            self.r.srem(index_key, str(entity_id))
            
            logger.debug(f"[CACHE] Entity invalidated: {entity_type}:{entity_id}")
            return True
        except Exception as e:
            logger.error(f"[CACHE] Error invalidating entity {entity_type}:{entity_id}: {e}")
            return False

    # ==================== TOOL RESULT CACHING ====================
    
    def cache_tool_result(self, tool_name: str, tool_args: Dict, result: Any, ttl: int = None) -> bool:
        """
        Cache tool execution result
        
        Args:
            tool_name: Name of the tool
            tool_args: Arguments passed to tool
            result: Tool result
            ttl: Time to live (default: TOOL_TTL)
        """
        try:
            ttl = ttl or self.TOOL_TTL
            args_hash = self._make_hash(tool_args)
            cache_key = self._make_key(self.PREFIX_TOOL, tool_name, args_hash)
            
            cache_data = {
                "tool_name": tool_name,
                "args": tool_args,
                "result": result,
                "cached_at": datetime.now().isoformat()
            }
            
            self.r.setex(cache_key, ttl, json.dumps(cache_data, default=str, ensure_ascii=False))
            
            # Track tool execution di index
            tool_index_key = self._make_key(self.PREFIX_METADATA, "tools", tool_name)
            self.r.sadd(tool_index_key, args_hash)
            
            logger.debug(f"[CACHE] Tool result cached: {tool_name} (args_hash={args_hash})")
            return True
        except Exception as e:
            logger.error(f"[CACHE] Error caching tool result {tool_name}: {e}")
            return False

    def get_cached_tool_result(self, tool_name: str, tool_args: Dict) -> Optional[Any]:
        """Get cached tool result"""
        try:
            args_hash = self._make_hash(tool_args)
            cache_key = self._make_key(self.PREFIX_TOOL, tool_name, args_hash)
            
            cached = self.r.get(cache_key)
            if cached:
                cache_data = json.loads(cached)
                logger.debug(f"[CACHE] Tool hit: {tool_name} (args_hash={args_hash})")
                return cache_data.get("result")
            
            logger.debug(f"[CACHE] Tool miss: {tool_name} (args_hash={args_hash})")
            return None
        except Exception as e:
            logger.error(f"[CACHE] Error getting tool result {tool_name}: {e}")
            return None

    def invalidate_tool(self, tool_name: str) -> bool:
        """Invalidate all cached results for a tool"""
        try:
            tool_index_key = self._make_key(self.PREFIX_METADATA, "tools", tool_name)
            args_hashes = self.r.smembers(tool_index_key)
            
            for args_hash in args_hashes:
                cache_key = self._make_key(self.PREFIX_TOOL, tool_name, args_hash)
                self.r.delete(cache_key)
            
            self.r.delete(tool_index_key)
            logger.debug(f"[CACHE] Tool invalidated: {tool_name}")
            return True
        except Exception as e:
            logger.error(f"[CACHE] Error invalidating tool {tool_name}: {e}")
            return False

    # ==================== STATE MANAGEMENT ====================
    
    def save_state(self, state_key: str, state_data: Dict, ttl: int = None) -> bool:
        """Save agent state ke Redis"""
        try:
            ttl = ttl or self.STATE_TTL
            cache_key = self._make_key(self.PREFIX_STATE, state_key)
            
            self.r.setex(cache_key, ttl, json.dumps(state_data, default=str, ensure_ascii=False))
            logger.debug(f"[STATE] State saved: {state_key}")
            return True
        except Exception as e:
            logger.error(f"[STATE] Error saving state {state_key}: {e}")
            return False

    def get_state(self, state_key: str) -> Optional[Dict]:
        """Get agent state dari Redis"""
        try:
            cache_key = self._make_key(self.PREFIX_STATE, state_key)
            cached = self.r.get(cache_key)
            
            if cached:
                logger.debug(f"[STATE] State loaded: {state_key}")
                return json.loads(cached)
            return None
        except Exception as e:
            logger.error(f"[STATE] Error getting state {state_key}: {e}")
            return None

    # ==================== SYNC CHECKING ====================
    
    def register_sync_check(self, entity_type: str, entity_id: Any, 
                           db_version: str, check_callback: Callable = None) -> bool:
        """
        Register sync metadata untuk entity
        
        Args:
            entity_type: Type of entity
            entity_id: Entity ID
            db_version: Version/hash dari database data
            check_callback: Optional callback untuk validation
        """
        try:
            sync_key = self._make_key(self.PREFIX_SYNC, entity_type, entity_id)
            
            sync_data = {
                "entity_type": entity_type,
                "entity_id": entity_id,
                "db_version": db_version,
                "last_sync": datetime.now().isoformat(),
                "sync_status": "valid"
            }
            
            self.r.setex(sync_key, self.METADATA_TTL, json.dumps(sync_data, default=str, ensure_ascii=False))
            logger.debug(f"[SYNC] Sync check registered: {entity_type}:{entity_id}")
            return True
        except Exception as e:
            logger.error(f"[SYNC] Error registering sync check: {e}")
            return False

    def check_sync_validity(self, entity_type: str, entity_id: Any, current_db_version: str) -> Dict:
        """
        Check if cached data masih sesuai dengan database
        
        Returns:
            {
                "is_valid": bool,
                "is_synced": bool,
                "cached_version": str,
                "current_version": str,
                "needs_refresh": bool
            }
        """
        try:
            sync_key = self._make_key(self.PREFIX_SYNC, entity_type, entity_id)
            sync_data_str = self.r.get(sync_key)
            
            if not sync_data_str:
                logger.debug(f"[SYNC] No sync metadata for {entity_type}:{entity_id}")
                return {
                    "is_valid": False,
                    "is_synced": False,
                    "needs_refresh": True,
                    "reason": "No sync metadata"
                }
            
            sync_data = json.loads(sync_data_str)
            cached_version = sync_data.get("db_version")
            
            is_synced = cached_version == current_db_version
            
            result = {
                "is_valid": True,
                "is_synced": is_synced,
                "cached_version": cached_version,
                "current_version": current_db_version,
                "needs_refresh": not is_synced,
                "last_sync": sync_data.get("last_sync")
            }
            
            if is_synced:
                logger.debug(f"[SYNC] ✓ Data is synced: {entity_type}:{entity_id}")
            else:
                logger.warning(f"[SYNC] ✗ Data out of sync: {entity_type}:{entity_id}")
            
            return result
        except Exception as e:
            logger.error(f"[SYNC] Error checking sync validity: {e}")
            return {
                "is_valid": False,
                "is_synced": False,
                "needs_refresh": True,
                "error": str(e)
            }

    # ==================== BATCH OPERATIONS ====================
    
    def bulk_invalidate(self, invalidation_map: Dict[str, List[Any]]) -> Dict:
        """
        Bulk invalidate multiple entities
        
        Args:
            invalidation_map: {entity_type: [entity_ids]}
        
        Returns:
            {entity_type: count_invalidated}
        """
        results = {}
        try:
            for entity_type, entity_ids in invalidation_map.items():
                count = 0
                for entity_id in entity_ids:
                    if self.invalidate_entity(entity_type, entity_id):
                        count += 1
                results[entity_type] = count
            
            logger.info(f"[CACHE] Bulk invalidation completed: {results}")
            return results
        except Exception as e:
            logger.error(f"[CACHE] Error in bulk invalidation: {e}")
            return results

    # ==================== UTILITY ====================
    
    def clear_all_cache(self) -> bool:
        """⚠️ WARNING: Clear all cache data (use carefully!)"""
        try:
            pattern = self._make_key("cache", "*")
            keys = self.r.keys(pattern)
            
            if keys:
                self.r.delete(*keys)
                logger.warning(f"[CACHE] Cleared {len(keys)} cache keys")
            
            return True
        except Exception as e:
            logger.error(f"[CACHE] Error clearing cache: {e}")
            return False

    def get_cache_stats(self) -> Dict:
        """Get cache statistics"""
        try:
            info = self.r.info("stats")
            dbsize = self.r.dbsize()
            
            return {
                "total_keys": dbsize,
                "connected_clients": info.get("connected_clients", 0),
                "used_memory": info.get("used_memory_human", "N/A"),
                "keyspace_hits": info.get("keyspace_hits", 0),
                "keyspace_misses": info.get("keyspace_misses", 0),
                "hit_rate": self._calculate_hit_rate(info)
            }
        except Exception as e:
            logger.error(f"[CACHE] Error getting cache stats: {e}")
            return {"error": str(e)}

    def _calculate_hit_rate(self, info: Dict) -> float:
        """Calculate cache hit rate percentage"""
        hits = info.get("keyspace_hits", 0)
        misses = info.get("keyspace_misses", 0)
        total = hits + misses
        
        if total == 0:
            return 0.0
        return round((hits / total) * 100, 2)

    # ==================== DECORATOR FOR AUTOMATIC CACHING ====================
    
    @staticmethod
    def cached_query(cache_key_fn: Callable = None, ttl: int = None):
        """
        Decorator untuk automatically cache query results
        
        Usage:
        @cache_manager.cached_query(
            cache_key_fn=lambda prodi_id: f"pembimbing:{prodi_id}",
            ttl=1800
        )
        def get_pembimbing(prodi_id):
            ...
        """
        def decorator(func):
            @wraps(func)
            def wrapper(*args, **kwargs):
                cache_manager = kwargs.get("_cache_manager") or RedisCacheManager()
                
                # Generate cache key
                if cache_key_fn:
                    # Extract args based on function parameters
                    cache_key = cache_key_fn(*args, **kwargs)
                else:
                    cache_key = f"{func.__name__}:{json.dumps(args + (kwargs,), default=str)}"
                
                # Try to get from cache
                cached = cache_manager.get_cached_query(cache_key)
                if cached is not None:
                    return cached
                
                # Execute function
                result = func(*args, **kwargs)
                
                # Cache the result
                cache_manager.cache_query(cache_key, result, ttl=ttl)
                
                return result
            return wrapper
        return decorator


# Global cache manager instance
_cache_manager_instance = None

def get_cache_manager() -> RedisCacheManager:
    """Get or create global cache manager instance"""
    global _cache_manager_instance
    if _cache_manager_instance is None:
        _cache_manager_instance = RedisCacheManager()
    return _cache_manager_instance
