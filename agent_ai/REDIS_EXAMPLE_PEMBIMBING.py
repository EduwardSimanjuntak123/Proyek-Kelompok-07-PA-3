"""
EXAMPLE: Updating pembimbing_tools.py untuk Redis Caching
=========================================================

This file shows exactly how to migrate an existing tool to use
the Redis cache-first pattern.

BEFORE (Current) vs AFTER (With Redis) comparison
"""

# ==================== BEFORE: Direct Database Calls ====================

# Current pattern in pembimbing_tools.py:
"""
from core.database import SessionLocal
from models.pembimbing import Pembimbing

def get_pembimbing_list() -> dict:
    try:
        session = SessionLocal()
        pembimbings = session.query(Pembimbing).all()
        session.close()
        
        data = [
            {
                "id": p.id,
                "nama": p.user.nama,
                ...
            }
            for p in pembimbings
        ]
        
        return {"status": "success", "total": len(data), "data": data}
    except Exception as e:
        return {"status": "error", "message": str(e)}
"""


# ==================== AFTER: With Redis Cache ====================

from core.database import SessionLocal
from models.pembimbing import Pembimbing
from core.cache_tools import get_cached_tool_helper
from core.cache_config import CacheConfig, CachePolicies

helper = get_cached_tool_helper()

def get_pembimbing_list() -> dict:
    """Get pembimbing list dengan Redis caching"""
    try:
        cache_key = "pembimbing:all"
        
        def fetch_from_db():
            session = SessionLocal()
            pembimbings = session.query(Pembimbing).all()
            session.close()
            return pembimbings
        
        # Use cache-first pattern
        pembimbings = helper.cached_query(
            cache_key=cache_key,
            db_fetch_fn=fetch_from_db,
            ttl=CacheConfig.TTL_PEMBIMBING,
            use_sync_check=CachePolicies.should_sync_check("pembimbing")
        )
        
        data = [
            {
                "id": p.id,
                "nama": p.user.nama,
                ...
            }
            for p in pembimbings
        ]
        
        return {"status": "success", "total": len(data), "data": data}
    except Exception as e:
        return {"status": "error", "message": str(e)}


# ==================== EXAMPLE 2: Filtered Query ====================

def get_pembimbing_by_context(prodi_id, kategori_pa_id, angkatan_id) -> dict:
    """Get pembimbing dengan specific context"""
    try:
        # Create unique cache key for this context
        cache_key = f"pembimbing:context:{prodi_id}:{kategori_pa_id}:{angkatan_id}"
        
        def fetch_from_db():
            session = SessionLocal()
            query = session.query(Pembimbing).filter(
                Pembimbing.user.has(User.prodi_id == prodi_id),
                # ... more filters
            )
            results = query.all()
            session.close()
            return results
        
        # Get with optional sync validation for sensitive data
        pembimbings = helper.cached_query(
            cache_key=cache_key,
            db_fetch_fn=fetch_from_db,
            ttl=CacheConfig.get_query_ttl("context"),
            use_sync_check=True  # Always validate for context queries
        )
        
        data = [...]
        return {"status": "success", "data": data}
    except Exception as e:
        return {"status": "error", "message": str(e)}


# ==================== EXAMPLE 3: Mutations with Invalidation ====================

def create_pembimbing(user_id, kelompok_id) -> dict:
    """Create pembimbing dan invalidate cache"""
    try:
        session = SessionLocal()
        
        pembimbing = Pembimbing(
            user_id=user_id,
            kelompok_id=kelompok_id,
            created_at=datetime.now(),
            updated_at=datetime.now(),
        )
        
        session.add(pembimbing)
        session.commit()
        session.close()
        
        # ▼ NEW: Invalidate related caches
        helper.invalidate_on_change({
            "pembimbing": [pembimbing.id],  # Invalidate this pembimbing
            "kelompok": [kelompok_id]        # Also invalidate related kelompok
        })
        
        return {
            "status": "success",
            "message": "Pembimbing created",
            "data": {
                "id": pembimbing.id,
                "user_id": user_id,
                "kelompok_id": kelompok_id
            }
        }
    except Exception as e:
        session.rollback()
        session.close()
        
        # If error, don't invalidate
        return {"status": "error", "message": str(e)}


# ==================== EXAMPLE 4: Using Decorator ====================

from core.cache_tools import with_cache

@with_cache(
    cache_key_fn=lambda user_id: f"pembimbing:user:{user_id}",
    ttl=CacheConfig.TTL_PEMBIMBING
)
def get_pembimbing_by_user_id(user_id):
    """Get pembimbing dengan decorator-based caching"""
    session = SessionLocal()
    pembimbing = session.query(Pembimbing).filter(
        Pembimbing.user_id == user_id
    ).first()
    session.close()
    return pembimbing


# ==================== EXAMPLE 5: Complex Generation with Assignment Caching ====================

def generate_pembimbing_assignments_by_context(
    prodi_id, kategori_pa_id, angkatan_id, 
    min_per_group=1, max_per_group=2,
    replace_existing=False, persist=False, prompt=None
) -> dict:
    """Generate pembimbing assignments dengan caching dan sync validation"""
    
    try:
        # Create cache key for this specific assignment request
        cache_key = (
            f"pembimbing:assignment:{prodi_id}:{kategori_pa_id}:{angkatan_id}:"
            f"{min_per_group}:{max_per_group}:{hash(prompt or '')}"
        )
        
        # Check if result already cached
        cached_result = helper.get_cached_tool_result(
            "generate_pembimbing_assignments_by_context",
            {
                "prodi_id": prodi_id,
                "kategori_pa_id": kategori_pa_id,
                "angkatan_id": angkatan_id,
                "prompt": prompt
            }
        )
        
        if cached_result and not replace_existing:
            logger.info(f"Using cached assignment result for {cache_key}")
            return cached_result
        
        # Extract constraints from prompt
        constraints = extract_pembimbing_constraints_from_prompt(prompt or "")
        
        # Fetch candidates dengan cache
        def fetch_candidates():
            session = SessionLocal()
            # ... existing query logic
            session.close()
            return candidates
        
        candidates = helper.cached_query(
            cache_key=f"pembimbing:candidates:{prodi_id}:{kategori_pa_id}:{angkatan_id}",
            db_fetch_fn=fetch_candidates,
            use_sync_check=True  # Important for generation to stay accurate
        )
        
        # Existing assignment generation logic...
        result = {
            "status": "success",
            "groups": [...],
            "summary": {...}
        }
        
        # ▼ NEW: Cache the assignment result
        helper.cache_tool_result(
            "generate_pembimbing_assignments_by_context",
            {
                "prodi_id": prodi_id,
                "kategori_pa_id": kategori_pa_id,
                "angkatan_id": angkatan_id,
                "prompt": prompt
            },
            result,
            ttl=CacheConfig.DEFAULT_TTL
        )
        
        # ▼ NEW: If persisting to database, invalidate caches
        if persist and result["status"] == "success":
            helper.invalidate_on_change({
                "pembimbing": [item.get("user_id") for item in result.get("groups", [])],
                "kelompok": [item.get("kelompok_id") for item in result.get("groups", [])]
            })
        
        return result
    
    except Exception as e:
        return {"status": "error", "message": str(e)}


# ==================== MIGRATION CHECKLIST ====================

"""
When migrating existing tools to Redis caching:

□ Import cache tools:
  from core.cache_tools import get_cached_tool_helper
  from core.cache_config import CacheConfig, CachePolicies
  
  helper = get_cached_tool_helper()

□ For each query function:
  - Create meaningful cache_key (e.g., "pembimbing:prodi:123")
  - Wrap database call in fetch function
  - Use helper.cached_query() instead of direct db call
  - Use appropriate TTL (from CacheConfig.get_ttl())
  - Enable sync_check for sensitive data

□ For each mutation function (create/update/delete):
  - Execute database operation
  - Call helper.invalidate_on_change() with affected entities
  - Return result

□ Test:
  - Verify cache hits after first call
  - Verify cache invalidation on mutations
  - Monitor cache hit rate
  - Check sync validation working correctly

□ Monitor:
  - Run cache_init.py monitor mode
  - Check cache statistics
  - Verify hit rate > 85%
"""
