"""
Cache Sync Validator - Ensures cached data matches database
Provides database version checking dan automatic cache invalidation
"""

import logging
import hashlib
from typing import Any, Dict, Optional, Callable, Tuple
from datetime import datetime, timedelta
from core.redis_cache_manager import get_cache_manager
from sqlalchemy import inspect

logger = logging.getLogger(__name__)

class CacheSyncValidator:
    """
    Validates dan syncs cache dengan database
    - Generate version hash dari database records
    - Check if cached data is still valid
    - Auto-invalidate stale cache
    - Track data mutations
    """
    
    def __init__(self):
        self.cache_manager = get_cache_manager()
    
    # ==================== VERSION HASHING ====================
    
    @staticmethod
    def generate_version_hash(data: Any) -> str:
        """
        Generate deterministic hash dari data
        Used untuk detect changes
        """
        try:
            if hasattr(data, '__dict__'):
                # SQLAlchemy model
                serializable = {k: v for k, v in data.__dict__.items() if not k.startswith('_')}
            else:
                serializable = data
            
            import json
            serialized = json.dumps(serializable, sort_keys=True, default=str)
            return hashlib.sha256(serialized.encode()).hexdigest()[:16]
        except:
            return hashlib.sha256(str(data).encode()).hexdigest()[:16]
    
    @staticmethod
    def generate_collection_version(items: list) -> str:
        """Generate version hash untuk collection of items"""
        try:
            versions = [CacheSyncValidator.generate_version_hash(item) for item in items]
            import json
            combined = json.dumps(versions)
            return hashlib.sha256(combined.encode()).hexdigest()[:16]
        except:
            return hashlib.sha256(str(items).encode()).hexdigest()[:16]
    
    # ==================== CACHE VALIDATION ====================
    
    def validate_entity_cache(self, entity_type: str, entity_id: Any, 
                             db_entity: Any) -> Dict:
        """
        Validate if cached entity masih sesuai dengan database
        
        Returns:
            {
                "is_valid": bool,
                "needs_refresh": bool,
                "cached_version": str,
                "db_version": str,
                "last_sync": str,
                "synced": bool
            }
        """
        try:
            db_version = self.generate_version_hash(db_entity)
            cached_data = self.cache_manager.get_cached_entity(entity_type, entity_id)
            
            if not cached_data:
                return {
                    "is_valid": False,
                    "needs_refresh": True,
                    "reason": "Cache miss",
                    "db_version": db_version
                }
            
            # Check sync validity
            sync_result = self.cache_manager.check_sync_validity(
                entity_type, entity_id, db_version
            )
            
            return {
                **sync_result,
                "is_valid": sync_result.get("is_synced", False),
                "needs_refresh": not sync_result.get("is_synced", False),
                "db_version": db_version
            }
        except Exception as e:
            logger.error(f"[SYNC] Error validating entity {entity_type}:{entity_id}: {e}")
            return {
                "is_valid": False,
                "needs_refresh": True,
                "error": str(e)
            }
    
    def validate_query_cache(self, query_key: str, db_data: Any) -> Dict:
        """
        Validate if cached query result masih sesuai dengan database result
        """
        try:
            if isinstance(db_data, list):
                db_version = self.generate_collection_version(db_data)
            else:
                db_version = self.generate_version_hash(db_data)
            
            cached_data = self.cache_manager.get_cached_query(query_key)
            
            if cached_data is None:
                return {
                    "is_valid": False,
                    "needs_refresh": True,
                    "reason": "Cache miss",
                    "db_version": db_version
                }
            
            sync_result = self.cache_manager.check_sync_validity(
                "query", query_key, db_version
            )
            
            return {
                **sync_result,
                "is_valid": sync_result.get("is_synced", False),
                "db_version": db_version
            }
        except Exception as e:
            logger.error(f"[SYNC] Error validating query {query_key}: {e}")
            return {
                "is_valid": False,
                "needs_refresh": True,
                "error": str(e)
            }
    
    # ==================== CACHE-FIRST PATTERN ====================
    
    def get_or_fetch(self, entity_type: str, entity_id: Any,
                    fetch_fn: Callable) -> Tuple[Any, Dict]:
        """
        Get dari cache, if miss/invalid fetch dari database
        
        Args:
            entity_type: Type of entity
            entity_id: Entity ID
            fetch_fn: Callback untuk fetch dari database
        
        Returns:
            (data, sync_status_dict)
        """
        try:
            # Try cache first
            cached = self.cache_manager.get_cached_entity(entity_type, entity_id)
            
            if cached:
                # Fetch dari database untuk version check
                db_data = fetch_fn()
                validation = self.validate_entity_cache(entity_type, entity_id, db_data)
                
                if validation.get("is_valid"):
                    logger.info(f"[CACHE-FIRST] ✓ Cache hit and valid: {entity_type}:{entity_id}")
                    return cached, {"source": "cache", "synced": True}
                else:
                    logger.info(f"[CACHE-FIRST] Cache hit but stale: {entity_type}:{entity_id}")
                    # Update cache
                    db_version = validation.get("db_version")
                    self.cache_manager.cache_entity(entity_type, entity_id, db_data)
                    self.cache_manager.register_sync_check(entity_type, entity_id, db_version)
                    return db_data, {"source": "database", "reason": "cache_stale"}
            else:
                # Cache miss - fetch and cache
                db_data = fetch_fn()
                db_version = self.generate_version_hash(db_data) if db_data else "null"
                
                self.cache_manager.cache_entity(entity_type, entity_id, db_data)
                self.cache_manager.register_sync_check(entity_type, entity_id, db_version)
                
                logger.info(f"[CACHE-FIRST] Cache miss, fetched from DB: {entity_type}:{entity_id}")
                return db_data, {"source": "database", "reason": "cache_miss"}
        except Exception as e:
            logger.error(f"[CACHE-FIRST] Error in get_or_fetch: {e}")
            # Fallback to direct database fetch
            return fetch_fn(), {"source": "database", "error": str(e)}
    
    def get_or_fetch_collection(self, collection_type: str,
                               fetch_fn: Callable,
                               cache_key: str = None) -> Tuple[list, Dict]:
        """
        Get collection dari cache atau fetch dari database
        
        Args:
            collection_type: Type of collection (pembimbing, kelompok, etc)
            fetch_fn: Callback untuk fetch collection dari database
            cache_key: Custom cache key
        
        Returns:
            (items, sync_status_dict)
        """
        try:
            cache_key = cache_key or f"collection:{collection_type}"
            
            # Try cache first
            cached = self.cache_manager.get_cached_query(cache_key)
            
            if cached is not None:
                # Fetch untuk version check
                db_data = fetch_fn()
                validation = self.validate_query_cache(cache_key, db_data)
                
                if validation.get("is_valid"):
                    logger.info(f"[CACHE-FIRST] ✓ Collection cache hit: {collection_type}")
                    return cached, {"source": "cache", "synced": True, "count": len(cached)}
                else:
                    logger.info(f"[CACHE-FIRST] Collection cache stale: {collection_type}")
                    # Update cache
                    db_version = self.generate_collection_version(db_data) if db_data else "empty"
                    self.cache_manager.cache_query(cache_key, db_data)
                    self.cache_manager.register_sync_check("query", cache_key, db_version)
                    return db_data, {"source": "database", "reason": "cache_stale", "count": len(db_data) if db_data else 0}
            else:
                # Cache miss
                db_data = fetch_fn()
                db_version = self.generate_collection_version(db_data) if db_data else "empty"
                
                self.cache_manager.cache_query(cache_key, db_data)
                self.cache_manager.register_sync_check("query", cache_key, db_version)
                
                logger.info(f"[CACHE-FIRST] Collection cache miss: {collection_type}")
                return db_data, {"source": "database", "reason": "cache_miss", "count": len(db_data) if db_data else 0}
        except Exception as e:
            logger.error(f"[CACHE-FIRST] Error fetching collection: {e}")
            return fetch_fn(), {"source": "database", "error": str(e)}
    
    # ==================== CACHE INVALIDATION ====================
    
    def invalidate_on_mutation(self, entity_type: str, entity_id: Any = None,
                              related_types: list = None) -> Dict:
        """
        Invalidate cache when data changes
        
        Args:
            entity_type: Type of entity yang berubah
            entity_id: Optional specific entity ID
            related_types: List of related entity types yang juga perlu di-invalidate
        
        Returns:
            {entity_type: count_invalidated}
        """
        try:
            results = {}
            
            # Invalidate primary entity
            if entity_id:
                if self.cache_manager.invalidate_entity(entity_type, entity_id):
                    results[entity_type] = 1
            else:
                count = len([1 for _ in self.cache_manager.r.smembers(
                    f"cache:meta:entities:{entity_type}"
                )])
                if self.cache_manager.invalidate_entity_type(entity_type):
                    results[entity_type] = count
            
            # Invalidate related types
            if related_types:
                for related_type in related_types:
                    if self.cache_manager.invalidate_entity_type(related_type):
                        results[related_type] = "all"
            
            logger.info(f"[SYNC] Cache invalidated on mutation: {results}")
            return results
        except Exception as e:
            logger.error(f"[SYNC] Error invalidating on mutation: {e}")
            return {"error": str(e)}
    
    def cascade_invalidate(self, invalidation_map: Dict[str, list]) -> Dict:
        """
        Cascade invalidation untuk dependent data
        
        Contoh:
        - Hapus pembimbing → invalidate kelompok yang affected
        - Update dosen → invalidate pembimbing, penguji
        """
        try:
            # Define cascade rules
            cascade_rules = {
                "pembimbing": ["kelompok"],
                "penguji": ["kelompok"],
                "dosen": ["pembimbing", "penguji", "jadwal"],
                "kelompok": ["pembimbing", "penguji", "jadwal", "mahasiswa"],
                "mahasiswa": ["kelompok", "nilai"],
            }
            
            results = {}
            processed = set()
            
            def process_type(entity_type: str):
                if entity_type in processed:
                    return
                processed.add(entity_type)
                
                if entity_type in invalidation_map:
                    # Invalidate this type
                    entity_ids = invalidation_map[entity_type]
                    for entity_id in entity_ids:
                        self.cache_manager.invalidate_entity(entity_type, entity_id)
                    results[entity_type] = len(entity_ids)
                
                # Process cascade
                if entity_type in cascade_rules:
                    for related_type in cascade_rules[entity_type]:
                        process_type(related_type)
            
            # Start cascade from all entities in invalidation_map
            for entity_type in invalidation_map:
                process_type(entity_type)
            
            logger.info(f"[SYNC] Cascade invalidation completed: {results}")
            return results
        except Exception as e:
            logger.error(f"[SYNC] Error in cascade invalidation: {e}")
            return {"error": str(e)}


# Global sync validator instance
_sync_validator_instance = None

def get_sync_validator() -> CacheSyncValidator:
    """Get or create global sync validator instance"""
    global _sync_validator_instance
    if _sync_validator_instance is None:
        _sync_validator_instance = CacheSyncValidator()
    return _sync_validator_instance
