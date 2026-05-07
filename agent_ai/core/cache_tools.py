"""
Cache Tools - Helper utilities untuk integrate caching ke tools
Provides simplified API untuk tools untuk use cache-first pattern
"""

import logging
import functools
from typing import Any, Dict, Optional, Callable, List, Tuple
from core.redis_cache_manager import get_cache_manager
from core.cache_sync import get_sync_validator

logger = logging.getLogger(__name__)

class CachedToolHelper:
    """
    Helper class untuk simplify caching di tools
    """
    
    def __init__(self):
        self.cache_manager = get_cache_manager()
        self.sync_validator = get_sync_validator()
    
    def cached_db_call(self, entity_type: str, entity_id: Any, 
                       db_fetch_fn: Callable, use_sync_check: bool = True) -> Any:
        """
        Execute database call with cache-first pattern
        
        Args:
            entity_type: Type of entity (pembimbing, kelompok, etc)
            entity_id: Entity ID
            db_fetch_fn: Function yang fetch dari database
            use_sync_check: Enable sync validation with database
        
        Returns:
            Data from cache or database
        """
        try:
            # Try cache first
            cached = self.cache_manager.get_cached_entity(entity_type, entity_id)
            
            if cached and not use_sync_check:
                logger.debug(f"[TOOL-CACHE] Cache hit (no sync check): {entity_type}:{entity_id}")
                return cached
            
            # Fetch from database
            db_data = db_fetch_fn()
            
            if cached and use_sync_check:
                # Validate cache
                validation = self.sync_validator.validate_entity_cache(
                    entity_type, entity_id, db_data
                )
                
                if validation.get("is_valid"):
                    logger.debug(f"[TOOL-CACHE] Cache hit and valid: {entity_type}:{entity_id}")
                    return cached
                else:
                    logger.debug(f"[TOOL-CACHE] Cache stale, using DB: {entity_type}:{entity_id}")
                    # Update cache
                    db_version = self.sync_validator.generate_version_hash(db_data)
                    self.cache_manager.cache_entity(entity_type, entity_id, db_data)
                    self.cache_manager.register_sync_check(entity_type, entity_id, db_version)
                    return db_data
            
            # Cache miss - cache the result
            if db_data:
                db_version = self.sync_validator.generate_version_hash(db_data)
                self.cache_manager.cache_entity(entity_type, entity_id, db_data)
                self.cache_manager.register_sync_check(entity_type, entity_id, db_version)
                logger.debug(f"[TOOL-CACHE] Cache miss, cached result: {entity_type}:{entity_id}")
            
            return db_data
        except Exception as e:
            logger.error(f"[TOOL-CACHE] Error in cached_db_call: {e}")
            # Fallback to direct DB call
            return db_fetch_fn()
    
    def cached_query(self, cache_key: str, db_fetch_fn: Callable, 
                    ttl: int = None, use_sync_check: bool = True) -> Any:
        """
        Execute query with cache-first pattern
        
        Args:
            cache_key: Unique cache key untuk query
            db_fetch_fn: Function yang fetch dari database
            ttl: Custom TTL untuk cache
            use_sync_check: Enable sync validation
        
        Returns:
            Data from cache or database
        """
        try:
            # Try cache
            cached = self.cache_manager.get_cached_query(cache_key)
            
            if cached and not use_sync_check:
                logger.debug(f"[TOOL-QUERY-CACHE] Hit: {cache_key}")
                return cached
            
            # Fetch from database
            db_data = db_fetch_fn()
            
            if cached and use_sync_check:
                # Validate
                validation = self.sync_validator.validate_query_cache(cache_key, db_data)
                
                if validation.get("is_valid"):
                    logger.debug(f"[TOOL-QUERY-CACHE] Hit and valid: {cache_key}")
                    return cached
                else:
                    logger.debug(f"[TOOL-QUERY-CACHE] Stale, using DB: {cache_key}")
                    db_version = self.sync_validator.generate_collection_version(db_data) if isinstance(db_data, list) else self.sync_validator.generate_version_hash(db_data)
                    self.cache_manager.cache_query(cache_key, db_data, ttl=ttl)
                    self.cache_manager.register_sync_check("query", cache_key, db_version)
                    return db_data
            
            # Cache miss
            if db_data is not None:
                db_version = self.sync_validator.generate_collection_version(db_data) if isinstance(db_data, list) else self.sync_validator.generate_version_hash(db_data)
                self.cache_manager.cache_query(cache_key, db_data, ttl=ttl)
                self.cache_manager.register_sync_check("query", cache_key, db_version)
                logger.debug(f"[TOOL-QUERY-CACHE] Miss, cached: {cache_key}")
            
            return db_data
        except Exception as e:
            logger.error(f"[TOOL-QUERY-CACHE] Error: {e}")
            return db_fetch_fn()
    
    def cache_tool_result(self, tool_name: str, tool_args: Dict, result: Any, ttl: int = None) -> None:
        """Cache tool execution result"""
        try:
            self.cache_manager.cache_tool_result(tool_name, tool_args, result, ttl=ttl)
            logger.debug(f"[TOOL-RESULT] Cached: {tool_name}")
        except Exception as e:
            logger.error(f"[TOOL-RESULT] Error caching: {e}")
    
    def get_cached_tool_result(self, tool_name: str, tool_args: Dict) -> Optional[Any]:
        """Get cached tool result"""
        try:
            result = self.cache_manager.get_cached_tool_result(tool_name, tool_args)
            if result:
                logger.debug(f"[TOOL-RESULT] Hit: {tool_name}")
            return result
        except Exception as e:
            logger.error(f"[TOOL-RESULT] Error getting: {e}")
            return None
    
    def invalidate_on_change(self, changes: Dict[str, List[Any]]) -> Dict:
        """
        Signal that data changed dan invalidate related caches
        
        Args:
            changes: {entity_type: [entity_ids]}
        """
        try:
            return self.sync_validator.cascade_invalidate(changes)
        except Exception as e:
            logger.error(f"[TOOL-INVALIDATE] Error: {e}")
            return {"error": str(e)}


# Global helper instance
_helper_instance = None

def get_cached_tool_helper() -> CachedToolHelper:
    """Get or create global cached tool helper"""
    global _helper_instance
    if _helper_instance is None:
        _helper_instance = CachedToolHelper()
    return _helper_instance


# ==================== DECORATORS ====================

def with_cache(entity_type: str = None, cache_key_fn: Callable = None, ttl: int = None):
    """
    Decorator untuk automatically cache tool results
    
    Usage:
    @with_cache(cache_key_fn=lambda prodi_id: f"pembimbing:{prodi_id}")
    def get_pembimbing(prodi_id):
        ...
    
    Or:
    @with_cache(entity_type="pembimbing")
    def get_pembimbing_by_id(pembimbing_id):
        ...
    """
    def decorator(func):
        @functools.wraps(func)
        def wrapper(*args, **kwargs):
            helper = get_cached_tool_helper()
            
            # Determine cache key
            if cache_key_fn:
                # Extract positional args based on function signature
                import inspect
                sig = inspect.signature(func)
                params = list(sig.parameters.keys())
                bound_args = inspect.signature(func).bind_partial(*args, **kwargs)
                bound_args.apply_defaults()
                
                cache_key = cache_key_fn(*[bound_args.arguments.get(p) for p in params[:cache_key_fn.__code__.co_argcount]])
            elif entity_type and len(args) > 0:
                cache_key = f"{entity_type}:{args[0]}"
            else:
                import json
                cache_key = f"{func.__name__}:{json.dumps(str(args) + str(kwargs), default=str)}"
            
            # Try cache first
            cached = helper.get_cached_tool_result(func.__name__, {"cache_key": cache_key})
            if cached:
                return cached
            
            # Execute and cache
            result = func(*args, **kwargs)
            helper.cache_tool_result(func.__name__, {"cache_key": cache_key}, result, ttl=ttl)
            return result
        
        return wrapper
    return decorator


# ==================== QUICK HELPERS ====================

def cache_first_query(cache_key: str, db_fetch_fn: Callable, ttl: int = None) -> Any:
    """Quick helper untuk cache-first query pattern"""
    helper = get_cached_tool_helper()
    return helper.cached_query(cache_key, db_fetch_fn, ttl=ttl)


def cache_first_entity(entity_type: str, entity_id: Any, db_fetch_fn: Callable) -> Any:
    """Quick helper untuk cache-first entity pattern"""
    helper = get_cached_tool_helper()
    return helper.cached_db_call(entity_type, entity_id, db_fetch_fn)


def invalidate_cache(*entity_types: str) -> Dict:
    """Quick helper untuk invalidate cache untuk entity types"""
    helper = get_cached_tool_helper()
    invalidation_map = {et: [] for et in entity_types}
    return helper.invalidate_on_change(invalidation_map)
