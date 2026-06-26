"""
REDIS CACHING INTEGRATION GUIDE
================================

Panduan lengkap untuk mengubah semua tools agar menggunakan Redis sebagai primary memory layer.

ARCHITECTURE OVERVIEW:
======================

┌──────────────────────────────────────────────┐
│              PRIMARY SOURCE                  │
│           Database (MySQL/PostgreSQL)        │
│   ← Tools query DIRECTLY from database       │
└──────────────────────┬───────────────────────┘
                       │
                       │ (Always query for fresh data)
                       │
       ┌───────────────┴───────────────┐
       │                               │
       ▼                               ▼
   ┌─────────────┐           ┌──────────────────┐
   │   Agent     │           │ Cache Tools      │
   │  (Planner)  │──────────►│ (Optional Cache) │
   └──────┬──────┘           └────────┬─────────┘
          │                           │
          │ (Direct DB Query)         ▼
          ▼                    ┌──────────────┐
      ┌──────────┐             │  Redis Cache │
      │   Tool   │─────────────┤  (Speed up   │
      │ Execution│ (check if   │  repeated    │
      └──────────┘  available) │  queries)    │
                     └──────────┘


HOW IT WORKS:
=============

1. DATABASE-FIRST PATTERN (Primary):
   - Tool queries database DIRECTLY untuk data fresh
   - Database adalah source of truth
   - Redis adalah OPTIONAL acceleration layer
   - System works even if Redis unavailable

2. OPTIONAL CACHING (Secondary):
   - Jika Redis available, check cache DULU
   - If cache hit → return cached (faster)
   - If cache miss → fetch dari database
   - Auto-cache hasil untuk query berikutnya

3. CACHE INVALIDATION:
   - Pada mutation (create/update/delete)
   - Invalidate cache untuk entity affected
   - Cascade invalidation untuk dependencies
   - Database tetap accurate

4. RESILIENCE:
   - Redis down → system tetap jalan (fallback to DB)
   - No dependency pada Redis
   - Database queries lebih frequent tapi data always fresh


INTEGRATION STEPS:
==================

STEP 1: Import cache tools di tool file
────────────────────────────────────────

from core.cache_tools import (
    get_cached_tool_helper,
    cache_first_query,
    cache_first_entity,
    with_cache,
)

helper = get_cached_tool_helper()


STEP 2: Update tool functions untuk use cache-first
─────────────────────────────────────────────────────

OLD PATTERN (Direct DB):
```python
def get_pembimbing_list() -> dict:
    try:
        session = SessionLocal()
        pembimbings = session.query(Pembimbing).all()
        session.close()
        
        return {"status": "success", "data": pembimbings}
    except Exception as e:
        return {"status": "error", "message": str(e)}
```

NEW PATTERN (Database-First with Optional Cache):
```python
def get_pembimbing_list() -> dict:
    try:
        # QUERY DATABASE FIRST (primary)
        session = SessionLocal()
        pembimbings = session.query(Pembimbing).all()
        session.close()
        
        # OPTIONAL: Cache the result jika Redis available
        if helper.cache_manager:  # Check if Redis is available
            helper.cache_manager.cache_query(
                cache_key="pembimbing:all",
                data=pembimbings,
                ttl=1800
            )
        
        return {"status": "success", "data": pembimbings}
    except Exception as e:
        return {"status": "error", "message": str(e)}
```

OR dengan optional caching yang lebih smart:
```python
def get_pembimbing_list() -> dict:
    try:
        # Check cache first (optional speed-up)
        if helper.cache_manager:
            cached = helper.cache_manager.get_cached_query("pembimbing:all")
            if cached:
                return {"status": "success", "data": cached}  # Fast path
        
        # Query database (primary, always correct)
        session = SessionLocal()
        pembimbings = session.query(Pembimbing).all()
        session.close()
        
        # Update cache
        if helper.cache_manager:
            helper.cache_manager.cache_query(
                "pembimbing:all", pembimbings, ttl=1800
            )
        
        return {"status": "success", "data": pembimbings}
    except Exception as e:
        return {"status": "error", "message": str(e)}
```


STEP 3: Add sync check untuk sensitive queries
────────────────────────────────────────────────

def get_kelompok_with_pembimbing(prodi_id, kategori_pa_id, angkatan_id):
    try:
        cache_key = f"kelompok:pembimbing:{prodi_id}:{kategori_pa_id}:{angkatan_id}"
        
        def fetch_from_db():
            session = SessionLocal()
            result = session.query(Kelompok).filter(
                Kelompok.prodi_id == prodi_id,
                Kelompok.kategori_pa_id == kategori_pa_id,
                Kelompok.angkatan_id == angkatan_id
            ).all()
            session.close()
            return result
        
        # Use sync check untuk ensure cache valid
        kelompoks = helper.cached_query(
            cache_key=cache_key,
            db_fetch_fn=fetch_from_db,
            use_sync_check=True  # Enable validation with database
        )
        
        return {"status": "success", "data": kelompoks}
    except Exception as e:
        return {"status": "error", "message": str(e)}
```


STEP 4: Handle mutations dengan cache invalidation
─────────────────────────────────────────────────────

def create_pembimbing(user_id, kelompok_id):
    try:
        session = SessionLocal()
        pembimbing = Pembimbing(user_id=user_id, kelompok_id=kelompok_id)
        session.add(pembimbing)
        session.commit()
        session.close()
        
        # ▼ NEW: Invalidate related caches
        helper.invalidate_on_change({
            "pembimbing": [pembimbing.id],
            "kelompok": [kelompok_id]  # Cascade invalidation
        })
        
        return {"status": "success", "data": pembimbing}
    except Exception as e:
        session.rollback()
        session.close()
        return {"status": "error", "message": str(e)}
```


STEP 5: Use decorator untuk simple caching
──────────────────────────────────────────────

@with_cache(
    cache_key_fn=lambda prodi_id: f"pembimbing:prodi:{prodi_id}",
    ttl=1800
)
def get_pembimbing_by_prodi(prodi_id):
    session = SessionLocal()
    result = session.query(Pembimbing).filter(
        Pembimbing.prodi_id == prodi_id
    ).all()
    session.close()
    return result


CACHE INVALIDATION STRATEGY:
============================

When to invalidate what:

┌─────────────────────┬────────────────────────────────────────┐
│ Data Modified       │ Invalidate                             │
├─────────────────────┼────────────────────────────────────────┤
│ Pembimbing created  │ pembimbing:*, kelompok:*, query:*      │
│ Pembimbing deleted  │ pembimbing:*, kelompok:*               │
│ Penguji created     │ penguji:*, kelompok:*, jadwal:*        │
│ Kelompok updated    │ kelompok:*, pembimbing:*, penguji:*    │
│ Mahasiswa updated   │ mahasiswa:*, kelompok:*, nilai:*       │
│ Dosen updated       │ dosen:*, pembimbing:*, penguji:*       │
│ Nilai created       │ nilai:*, mahasiswa:*                   │
└─────────────────────┴────────────────────────────────────────┘


ENVIRONMENT VARIABLES:
======================

Tambahkan ke .env file:

REDIS_HOST=localhost        # Redis server address
REDIS_PORT=6379            # Redis server port
REDIS_DB=0                 # Redis database number
CACHE_ENABLED=true         # Enable/disable caching
CACHE_TTL_DEFAULT=3600     # Default cache TTL
SYNC_CHECK_ENABLED=true    # Enable sync validation


MONITORING & DEBUGGING:
=======================

Get cache statistics:

from core.cache_tools import get_cached_tool_helper
helper = get_cached_tool_helper()
stats = helper.cache_manager.get_cache_stats()
print(stats)

Output:
{
    "total_keys": 1234,
    "connected_clients": 5,
    "used_memory": "2.5M",
    "keyspace_hits": 9876,
    "keyspace_misses": 123,
    "hit_rate": 98.77
}


Clear all cache (use carefully!):

helper.cache_manager.clear_all_cache()


BEST PRACTICES:
===============

1. ✓ Use meaningful cache keys (e.g., "pembimbing:prodi:123" NOT "p123")
2. ✓ Set appropriate TTLs (short for volatile data, long for static data)
3. ✓ Always handle cache-miss gracefully
4. ✓ Use sync validation for critical/frequently-queried data
5. ✓ Invalidate related caches (use cascade_invalidate)
6. ✓ Log cache hits/misses untuk debugging
7. ✓ Monitor cache hit rate (target: >85%)
8. ✓ Batch invalidations untuk efficiency
9. ✓ Test cache behavior dengan berbagai TTL values
10. ✓ Document cache keys dan TTL decisions


EXAMPLE: Complete pembimbing_tools.py UPDATE
==============================================

from core.cache_tools import (
    get_cached_tool_helper,
    cache_first_query,
    with_cache,
)

helper = get_cached_tool_helper()

@with_cache(
    cache_key_fn=lambda prodi_id, kategori_pa_id, angkatan_id: 
        f"pembimbing:context:{prodi_id}:{kategori_pa_id}:{angkatan_id}",
    ttl=1800
)
def _get_candidate_pembimbing_by_context(session, prodi_id, kategori_pa_id, angkatan_id):
    # Existing logic
    return results


def generate_pembimbing_assignments_by_context(..., prompt=None):
    try:
        # NEW: Check cache first
        cache_key = f"pembimbing_assignment:{prodi_id}:{kategori_pa_id}:{angkatan_id}"
        
        # Extract constraints from prompt
        constraints = extract_pembimbing_constraints_from_prompt(prompt or "")
        
        # Existing generation logic...
        result = {...}
        
        # NEW: Cache the assignment result
        helper.cache_tool_result(
            "generate_pembimbing_assignments_by_context",
            {
                "prodi_id": prodi_id,
                "kategori_pa_id": kategori_pa_id,
                "angkatan_id": angkatan_id,
                "constraints": constraints
            },
            result,
            ttl=3600
        )
        
        # NEW: Invalidate dependent caches on persist
        if persist and result["status"] == "success":
            helper.invalidate_on_change({
                "pembimbing": [item["user_id"] for item in result.get("groups", [])],
                "kelompok": [item["kelompok_id"] for item in result.get("groups", [])]
            })
        
        return result
    except Exception as e:
        return {"status": "error", "message": str(e)}


TROUBLESHOOTING:
================

Q: Cache always misses
A: Check Redis connection, verify cache_key format, check TTL

Q: Data out of sync
A: Enable sync_check=True, reduce TTL for volatile data

Q: Memory usage high
A: Check for unbounded cache growth, implement TTL cleanup

Q: Performance not improved
A: Verify cache hit rate (aim for >85%), check network latency


MIGRATION CHECKLIST:
====================

□ Install redis package (pip install redis)
□ Start Redis server (docker-compose up -d redis)
□ Create cache_tools.py dan cache_sync.py
□ Update all tool files dengan cache-first pattern
□ Add cache invalidation untuk mutations
□ Test dengan different TTL values
□ Monitor cache hit rate
□ Update documentation
□ Train team on new patterns
□ Deploy to staging
□ Monitor production cache performance
"""

print(__doc__)
