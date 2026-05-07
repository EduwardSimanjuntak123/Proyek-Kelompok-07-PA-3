"""
API Integration - How to integrate Redis caching into API endpoints
===================================================================

This shows how to use Redis caching in the FastAPI layer
"""

from fastapi import FastAPI, HTTPException, Query
from typing import Optional
import logging

from core.database import SessionLocal
from core.cache_tools import get_cached_tool_helper
from core.cache_config import CacheConfig
from core.cache_init import initialize_redis_cache
from tools.pembimbing_tools import (
    get_pembimbing_list,
    generate_pembimbing_assignments_by_context
)

logger = logging.getLogger(__name__)
app = FastAPI()

helper = get_cached_tool_helper()


# ==================== STARTUP HOOKS ====================

@app.on_event("startup")
async def startup_event():
    """Initialize Redis cache on API startup"""
    logger.info("API starting up...")
    
    # Initialize Redis cache system
    if CacheConfig.CACHE_ENABLED:
        if not initialize_redis_cache():
            logger.warning("⚠️  Redis cache initialization failed, will use direct DB")
    else:
        logger.info("Redis caching is disabled")
    
    logger.info("API ready to accept requests")


@app.on_event("shutdown")
async def shutdown_event():
    """Cleanup on shutdown"""
    logger.info("API shutting down...")
    # Cache manager will close connections automatically


# ==================== QUERY ENDPOINTS (Read-Only) ====================

@app.get("/api/pembimbing/list")
async def list_pembimbing(
    use_cache: bool = Query(True, description="Use Redis cache"),
    bypass_cache: bool = Query(False, description="Bypass cache and fetch from DB"),
):
    """
    Get list of pembimbing dengan Redis caching
    
    Query params:
    - use_cache: Enable/disable caching (default: True)
    - bypass_cache: Force database fetch (default: False)
    """
    try:
        if bypass_cache and CacheConfig.CACHE_ENABLED:
            # Clear cache for this query
            helper.cache_manager.invalidate_query("pembimbing:all")
        
        result = get_pembimbing_list()
        return result
    
    except Exception as e:
        logger.error(f"Error listing pembimbing: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@app.get("/api/pembimbing/context")
async def get_pembimbing_by_context(
    prodi_id: int,
    kategori_pa_id: int,
    angkatan_id: int,
    use_cache: bool = Query(True),
):
    """
    Get pembimbing for specific context dengan caching
    
    Cache key: pembimbing:context:{prodi_id}:{kategori_pa_id}:{angkatan_id}
    Cache TTL: 30 minutes
    """
    try:
        cache_key = f"pembimbing:context:{prodi_id}:{kategori_pa_id}:{angkatan_id}"
        
        def fetch_from_db():
            session = SessionLocal()
            from tools.pembimbing_tools import _get_candidate_pembimbing_by_context
            result = _get_candidate_pembimbing_by_context(
                session, prodi_id, kategori_pa_id, angkatan_id
            )
            session.close()
            return result
        
        if use_cache and CacheConfig.CACHE_ENABLED:
            data = helper.cached_query(
                cache_key=cache_key,
                db_fetch_fn=fetch_from_db,
                use_sync_check=True
            )
        else:
            data = fetch_from_db()
        
        return {
            "status": "success",
            "cache_key": cache_key,
            "cached": use_cache,
            "count": len(data) if data else 0,
            "data": data
        }
    
    except Exception as e:
        logger.error(f"Error getting pembimbing by context: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@app.get("/api/cache/stats")
async def get_cache_stats():
    """Get current cache statistics"""
    try:
        if not CacheConfig.CACHE_ENABLED:
            return {"status": "disabled", "message": "Cache is disabled"}
        
        stats = helper.cache_manager.get_cache_stats()
        return {
            "status": "success",
            "stats": stats,
            "config": {
                "cache_enabled": CacheConfig.CACHE_ENABLED,
                "sync_check_enabled": CacheConfig.SYNC_CHECK_ENABLED,
                "target_hit_rate": CacheConfig.TARGET_HIT_RATE,
            }
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


# ==================== MUTATION ENDPOINTS (Create/Update/Delete) ====================

@app.post("/api/pembimbing/generate")
async def generate_pembimbing(
    prodi_id: int,
    kategori_pa_id: int,
    angkatan_id: int,
    min_per_group: int = 1,
    max_per_group: int = 2,
    replace_existing: bool = False,
    persist: bool = False,
    prompt: Optional[str] = None,
):
    """
    Generate pembimbing assignments dengan Redis caching
    
    Cache result untuk reuse dengan same parameters
    Invalidate caches on successful persist
    """
    try:
        # Generate assignments (uses cache internally)
        result = generate_pembimbing_assignments_by_context(
            prodi_id=prodi_id,
            kategori_pa_id=kategori_pa_id,
            angkatan_id=angkatan_id,
            min_per_group=min_per_group,
            max_per_group=max_per_group,
            replace_existing=replace_existing,
            persist=persist,
            prompt=prompt
        )
        
        # Log cache impact
        logger.info(f"[API] Generate pembimbing - Status: {result['status']}")
        
        return result
    
    except Exception as e:
        logger.error(f"Error generating pembimbing: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/api/cache/invalidate")
async def invalidate_cache(
    entity_type: str,
    entity_ids: Optional[list] = None,
):
    """
    Manually invalidate cache untuk specific entities
    
    Useful untuk emergency cache clearing
    """
    try:
        if not CacheConfig.CACHE_ENABLED:
            return {"status": "disabled"}
        
        if entity_ids:
            invalidations = {entity_type: entity_ids}
        else:
            # Clear all entities of type
            invalidations = {entity_type: []}
        
        result = helper.invalidate_on_change(invalidations)
        
        logger.info(f"[API] Manual cache invalidation: {result}")
        
        return {
            "status": "success",
            "invalidations": result
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/api/cache/clear")
async def clear_all_cache(confirm: bool = False):
    """
    ⚠️ DANGEROUS: Clear all cache data
    Requires confirm=true parameter
    """
    try:
        if not CacheConfig.CACHE_ENABLED:
            return {"status": "disabled"}
        
        if not confirm:
            return {
                "status": "warning",
                "message": "Provide confirm=true to clear all cache"
            }
        
        if helper.cache_manager.clear_all_cache():
            logger.warning("[API] ALL CACHE CLEARED")
            return {"status": "success", "message": "All cache cleared"}
        else:
            raise HTTPException(status_code=500, detail="Failed to clear cache")
    
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


# ==================== HEALTH CHECKS ====================

@app.get("/api/health/cache")
async def health_check_cache():
    """Check Redis cache health"""
    try:
        if not CacheConfig.CACHE_ENABLED:
            return {"status": "ok", "cache": "disabled"}
        
        # Test Redis connection
        helper.cache_manager.r.ping()
        
        # Get stats
        stats = helper.cache_manager.get_cache_stats()
        hit_rate = stats.get('hit_rate', 0)
        
        health_status = "healthy" if hit_rate >= CacheConfig.TARGET_HIT_RATE * 100 else "degraded"
        
        return {
            "status": "ok",
            "cache": health_status,
            "hit_rate": hit_rate,
            "target_hit_rate": CacheConfig.TARGET_HIT_RATE * 100,
            "redis": "connected"
        }
    
    except Exception as e:
        return {
            "status": "error",
            "cache": "unhealthy",
            "error": str(e),
            "redis": "disconnected"
        }


# ==================== TESTING ENDPOINTS ====================

@app.get("/api/test/cache-miss")
async def test_cache_miss():
    """Test cache miss scenario"""
    try:
        # Clear cache first
        helper.cache_manager.invalidate_query("test:cache_miss")
        
        test_data = {"test": "data"}
        
        # First call - cache miss
        helper.cached_query("test:cache_miss", lambda: test_data)
        cached1 = helper.cache_manager.get_cached_query("test:cache_miss")
        
        return {
            "status": "success",
            "test": "cache_miss",
            "result": cached1 is not None
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@app.get("/api/test/cache-hit")
async def test_cache_hit():
    """Test cache hit scenario"""
    try:
        # Pre-populate cache
        test_data = {"test": "data"}
        helper.cache_manager.cache_query("test:cache_hit", test_data)
        
        # Second call - cache hit
        cached = helper.cache_manager.get_cached_query("test:cache_hit")
        
        return {
            "status": "success",
            "test": "cache_hit",
            "result": cached is not None and cached["test"] == "data"
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


# ==================== MIDDLEWARE FOR CACHE LOGGING ====================

from fastapi import Request
from fastapi.middleware.cors import CORSMiddleware
import time

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


@app.middleware("http")
async def log_cache_usage(request: Request, call_next):
    """Middleware untuk log cache usage pada setiap request"""
    
    start_time = time.time()
    response = await call_next(request)
    process_time = time.time() - start_time
    
    # Log dengan cache stats jika available
    if CacheConfig.CACHE_ENABLED and "/api" in request.url.path:
        try:
            stats = helper.cache_manager.get_cache_stats()
            logger.debug(
                f"[REQUEST] {request.method} {request.url.path} "
                f"- Time: {process_time:.3f}s - "
                f"Cache hit rate: {stats.get('hit_rate', 0):.2f}%"
            )
        except:
            pass
    
    return response


# ==================== USAGE EXAMPLES ====================

"""
cURL examples untuk testing cache integration:

1. Get list of pembimbing (cached):
   curl "http://localhost:8000/api/pembimbing/list"

2. Force cache bypass:
   curl "http://localhost:8000/api/pembimbing/list?bypass_cache=true"

3. Get pembimbing by context:
   curl "http://localhost:8000/api/pembimbing/context?prodi_id=1&kategori_pa_id=1&angkatan_id=2020"

4. Generate pembimbing assignments:
   curl -X POST "http://localhost:8000/api/pembimbing/generate?prodi_id=1&kategori_pa_id=1&angkatan_id=2020&persist=false" \
     -H "Content-Type: application/json" \
     -d '{"prompt": "dosen Ana Muliyana hanya pembimbing 2"}'

5. Check cache statistics:
   curl "http://localhost:8000/api/cache/stats"

6. Check cache health:
   curl "http://localhost:8000/api/health/cache"

7. Clear all cache (DANGEROUS!):
   curl -X POST "http://localhost:8000/api/cache/clear?confirm=true"
"""
