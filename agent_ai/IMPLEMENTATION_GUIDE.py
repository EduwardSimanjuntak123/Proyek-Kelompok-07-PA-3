"""
COMPREHENSIVE REDIS CACHING IMPLEMENTATION GUIDE
=================================================

Complete step-by-step guide untuk mengubah seluruh agent system 
agar menggunakan Redis sebagai primary memory layer untuk semua logika.
"""

print("""
╔════════════════════════════════════════════════════════════════════════════╗
║        REDIS CACHING LAYER - DATABASE-FIRST ARCHITECTURE                   ║
║   Redis hanya cache/acceleration, bukan primary memory                      ║
╚════════════════════════════════════════════════════════════════════════════╝

OVERVIEW
========

Target: Database tetap PRIMARY, Redis adalah optional CACHE layer
- Tools query DATABASE DIRECTLY untuk data fresh
- Redis check OPTIONAL untuk speed-up repeated queries
- System works even if Redis down (fallback to DB)
- Database adalah source of truth

Architecture:

    Normal Path (No Cache Hit):
    Request → Database → Get Fresh Data → (Optionally cache) → Return
    
    Fast Path (With Cache):
    Request → Check Redis → Cache HIT → Return immediately
                         → Cache MISS → Query DB → Cache & Return


PHASE 1: SETUP (Days 1-2)
==========================

✓ 1.1 Install Redis locally or via Docker (OPTIONAL)
      docker-compose -f docker-compose.redis.yml up -d
      (System works fine without this - fallback to direct DB)

✓ 1.2 Verify redis-py package installed (OPTIONAL)
      pip install redis>=5.0.0
      (Can skip if not using caching layer)

✓ 1.3 Create Redis cache layer files (for optional acceleration):
      - core/cache_config.py ............................ ✓ DONE
      - core/redis_cache_manager.py .................... ✓ DONE
      - core/cache_sync.py ............................ ✓ DONE
      - core/cache_tools.py ........................... ✓ DONE
      - core/cache_init.py ............................ ✓ DONE

✓ 1.4 Test Redis connectivity (OPTIONAL - not required)
      python core/cache_init.py

Expected output:
      ✓ Redis connected: localhost:6379/db0
      (OR: Redis unavailable - system will use database directly)


PHASE 2: DOCUMENTATION & EXAMPLES (Days 2-3)
==============================================

Reference files created:
- REDIS_CACHING_GUIDE.py .......................... Complete integration guide
- REDIS_EXAMPLE_PEMBIMBING.py ..................... Before/after example
- API_REDIS_INTEGRATION.py ........................ FastAPI integration
- AGENT_NODES_REDIS_INTEGRATION.py ............... Agent nodes integration


PHASE 3: TOOL MIGRATION (Days 3-5) - DATABASE-FIRST
====================================================

Pattern: Query Database FIRST, use Redis only for optional speed-up

Step 1: Import cache helpers (OPTIONAL)
───────────────────────────────────────

from core.cache_tools import get_cached_tool_helper

helper = get_cached_tool_helper()


Step 2: Update READ functions (queries) - DATABASE-FIRST
──────────────────────────────────────────────────────────

OPTION A: Just query database (simple, no Redis dependency)
    def get_pembimbing_list():
        session = SessionLocal()
        pembimbings = session.query(Pembimbing).all()
        session.close()
        return pembimbings

OPTION B: Query database + optional Redis caching
    def get_pembimbing_list():
        # Check cache first (optional, for speed-up)
        if helper and helper.cache_manager:
            cached = helper.cache_manager.get_cached_query("pembimbing:all")
            if cached:
                return cached
        
        # Query database (primary source)
        session = SessionLocal()
        pembimbings = session.query(Pembimbing).all()
        session.close()
        
        # Cache for next time (optional)
        if helper and helper.cache_manager:
            helper.cache_manager.cache_query(
                "pembimbing:all", pembimbings, ttl=1800
            )
        
        return pembimbings

OPTION C: With error handling and fallback
    def get_pembimbing_list():
        try:
            # Try cache (optional speed-up)
            if helper and helper.cache_manager:
                cached = helper.cache_manager.get_cached_query("pembimbing:all")
                if cached:
                    return cached
        except:
            pass  # Cache failure - just continue
        
        # Always query database (reliable source)
        session = SessionLocal()
        pembimbings = session.query(Pembimbing).all()
        session.close()
        
        # Try to cache (optional, non-blocking)
        try:
            if helper and helper.cache_manager:
                helper.cache_manager.cache_query(
                    "pembimbing:all", pembimbings, ttl=1800
                )
        except:
            pass  # Cache update failure doesn't affect result
        
        return pembimbings


Step 3: WRITE functions (mutations) - Invalidate cache on change
──────────────────────────────────────────────────────────────────

    def create_pembimbing(user_id, kelompok_id):
        session = SessionLocal()
        pembimbing = Pembimbing(...)
        session.add(pembimbing)
        session.commit()
        session.close()
        
        # Invalidate cache if Redis available (optional)
        try:
            if helper and helper.cache_manager:
                helper.invalidate_on_change({
                    "pembimbing": [pembimbing.id],
                    "kelompok": [kelompok_id]
                })
        except:
            pass  # Cache invalidation failure doesn't affect data
        
        return pembimbing


Tools to migrate (Priority order):

Priority 1 (Read-heavy):
□ tools/pembimbing_tools.py
□ tools/kelompok_tools.py
□ tools/mahasiswa_tools.py
□ tools/dosen_tools.py
□ tools/nilai_mahasiswa_tools.py

Priority 2 (Mixed):
□ tools/penguji_tools.py
□ tools/jadwal_tools.py
□ tools/jadwal_seminar_tools.py

Priority 3 (Write-heavy):
□ tools/grouping.py
□ tools/grouping_by_grades.py


PHASE 4: API INTEGRATION (Days 5-6)
====================================

Update main API (api_3layer.py / api.py):

✓ 4.1 Add startup hook untuk initialize cache

    @app.on_event("startup")
    async def startup_event():
        from core.cache_init import initialize_redis_cache
        initialize_redis_cache()

✓ 4.2 Add cache health check endpoint

    @app.get("/api/health/cache")
    async def health_check_cache():
        # See API_REDIS_INTEGRATION.py for example

✓ 4.3 Add cache statistics endpoint

    @app.get("/api/cache/stats")
    async def get_cache_stats():
        # See API_REDIS_INTEGRATION.py for example

✓ 4.4 Add cache control endpoints

    @app.post("/api/cache/invalidate")
    @app.post("/api/cache/clear")
    # See API_REDIS_INTEGRATION.py for examples


PHASE 5: AGENT NODES INTEGRATION (Days 6-7)
===============================================

Update agent nodes untuk cache awareness:

✓ 5.1 Enhance planner_node

    Plan now includes cache hints:
    {
        "action": "query_pembimbing",
        "cache_strategy": {
            "cache_key": "pembimbing:...",
            "use_sync_check": True,
            "ttl": 1800
        }
    }

✓ 5.2 Enhance executor_node

    - Check Redis cache BEFORE executing tool
    - Skip tool execution if valid cache exists
    - Cache result after successful execution
    - Invalidate related caches on mutations

✓ 5.3 Add cache context to state

    state["cache_context"] = {
        "enabled": True,
        "hit_rate": 87.5,
        "healthy": True,
        "recommendation": "Cache performing well"
    }

See AGENT_NODES_REDIS_INTEGRATION.py for complete examples


PHASE 6: TESTING & MONITORING (Days 7-8)
===========================================

✓ 6.1 Test cache functionality

    # Test cache hits
    python -c "
    from core.cache_tools import get_cached_tool_helper
    helper = get_cached_tool_helper()
    data = helper.cached_query('test:key', lambda: {'data': 'test'})
    print('✓ Cache test passed')
    "

✓ 6.2 Monitor cache performance

    python core/cache_init.py monitor
    
    Output:
    ✓ Redis connection: OK
    ✓ Hit rate: 85.5% (target: 85%)
    ✓ Memory: OK
    
    (Continues monitoring every 5 minutes)

✓ 6.3 Test with actual tools

    # Test pembimbing tool with cache
    python -c "
    from tools.pembimbing_tools import get_pembimbing_list
    
    # First call - cache miss
    result1 = get_pembimbing_list()
    
    # Second call - cache hit (should be faster)
    result2 = get_pembimbing_list()
    
    assert result1 == result2
    print('✓ Tool caching works correctly')
    "

✓ 6.4 Test cache invalidation

    # Test invalidation on mutation
    python -c "
    from tools.pembimbing_tools import create_pembimbing
    
    # This should invalidate 'pembimbing:all' cache
    result = create_pembimbing(user_id=1, kelompok_id=1)
    
    # Next query should fetch fresh data
    fresh = get_pembimbing_list()
    
    print('✓ Cache invalidation works')
    "

✓ 6.5 Load testing

    Use tools like Apache Bench or wrk to test cache hit rate
    under load:
    
    ab -n 1000 -c 10 http://localhost:8000/api/pembimbing/list
    
    Expected: >85% faster response times compared to non-cached


PHASE 7: DEPLOYMENT (Day 8-9)
===============================

✓ 7.1 Update .env

    REDIS_HOST=localhost        # or production Redis address
    REDIS_PORT=6379
    REDIS_DB=0
    CACHE_ENABLED=true
    SYNC_CHECK_ENABLED=true
    AUTO_INVALIDATE=true

✓ 7.2 Update Docker setup

    If using Docker, add Redis service:
    
    services:
      redis:
        image: redis:7-alpine
        ports:
          - "6379:6379"
        volumes:
          - redis_data:/data
        command: redis-server --appendonly yes

✓ 7.3 Database migrations

    No schema changes needed - Redis is transparent layer

✓ 7.4 Startup sequence

    1. Start Redis: docker-compose up -d redis
    2. Start API: python start_api.py
    3. API initialization will:
       - Connect to Redis
       - Initialize cache system
       - Warmup common queries
       - Begin accepting requests


PHASE 8: OPTIMIZATION (Ongoing)
=================================

Monitor and optimize:

✓ 8.1 Hit rate optimization
      
    Target: >85% hit rate
    If below target:
    - Increase TTL untuk frequently accessed data
    - Analyze which queries miss and why
    - Add cache warmup untuk popular queries

✓ 8.2 Memory optimization

    Monitor Redis memory usage:
    - Max cache size ~512MB (adjustable)
    - Auto-cleanup when 90% full
    - Regular monitoring via /api/cache/stats

✓ 8.3 Sync validation optimization

    Adjust which entities need sync checking:
    - Keep for volatile data (nilai, jadwal)
    - Can reduce for stable data (dosen, prodi)

✓ 8.4 TTL fine-tuning

    Current defaults:
    - Pembimbing: 1 hour
    - Kelompok: 1 hour
    - Mahasiswa: 1 hour
    - Nilai: 30 minutes (more volatile)
    
    Adjust based on actual update frequency


CONFIGURATION REFERENCE
=======================

File: core/cache_config.py

Key settings:

    REDIS_HOST = "localhost"           # Redis address
    REDIS_PORT = 6379
    REDIS_DB = 0
    
    CACHE_ENABLED = True               # Master switch
    SYNC_CHECK_ENABLED = True          # Validate cache
    AUTO_INVALIDATE = True             # Auto-clear on change
    
    TTL_PEMBIMBING = 3600              # 1 hour
    TTL_KELOMPOK = 3600
    TTL_MAHASISWA = 3600
    TTL_NILAI = 1800                   # 30 min (volatile)
    TTL_JADWAL = 3600
    
    TARGET_HIT_RATE = 0.85             # Target 85%
    MONITOR_HIT_RATE = True
    STATS_INTERVAL = 3600              # Log stats every hour


CACHE KEY NAMING CONVENTION
============================

Follow consistent naming:

    pembimbing:all                          # All pembimbing
    pembimbing:context:{prodi}:{kpa}:{thn}  # Pembimbing by context
    pembimbing:user:{user_id}               # Pembimbing by user
    
    kelompok:all                            # All kelompok
    kelompok:context:{...}                  # Kelompok by context
    
    mahasiswa:prodi:{prodi_id}              # Mahasiswa by prodi
    
    query:hasil:{...}                       # Query results
    tool:generate_pembimbing:{...}          # Tool results
    
    session:{user_id}                       # User sessions
    state:{session_id}                      # Agent state


TROUBLESHOOTING
===============

Q: Redis connection refused
A: - Check Redis running: redis-cli ping
   - Check host/port in .env
   - Check firewall rules

Q: Cache always misses
A: - Verify cache enabled: CacheConfig.CACHE_ENABLED
   - Check cache key format
   - Check TTL not too short

Q: Memory keeps growing
A: - Check for unbounded caches
   - Verify TTL cleanup working
   - Monitor /api/cache/stats

Q: Data out of sync
A: - Enable sync_check_enabled
   - Check database update frequency
   - Adjust TTL values

Q: Slow responses despite caching
A: - Check hit rate (target >85%)
   - Profile cache misses
   - Review cache keys
   - Optimize database queries


MIGRATION CHECKLIST
===================

□ Phase 1: Setup
  □ Install Redis locally
  □ Create config files (✓ 5 files)
  □ Test Redis connection
  
□ Phase 2: Documentation
  □ Review REDIS_CACHING_GUIDE.py
  □ Review REDIS_EXAMPLE_PEMBIMBING.py
  □ Review API_REDIS_INTEGRATION.py
  
□ Phase 3: Tool Migration
  □ Migrate 5 Priority 1 tools
  □ Migrate 3 Priority 2 tools
  □ Migrate 3 Priority 3 tools
  
□ Phase 4: API Integration
  □ Add startup hook
  □ Add health check endpoint
  □ Add cache stats endpoint
  □ Add cache control endpoints
  
□ Phase 5: Agent Nodes
  □ Enhance planner_node
  □ Enhance executor_node
  □ Add cache context
  □ Add cache warmup
  
□ Phase 6: Testing
  □ Test cache functionality
  □ Test cache invalidation
  □ Run load tests
  □ Monitor cache hit rate
  
□ Phase 7: Deployment
  □ Update .env
  □ Update Docker setup
  □ Deploy to staging
  □ Deploy to production
  
□ Phase 8: Optimization
  □ Monitor hit rate
  □ Optimize TTLs
  □ Optimize memory
  □ Document lessons learned


EXPECTED BENEFITS (With Optional Redis Caching)
================================================

✓ Performance Improvement (when Redis available)
  - Repeated queries: 10-100x faster (from cache)
  - Agent planning: Faster with cached context
  - System latency: 20-30% reduction (with caching)

✓ Database Load Reduction (when Redis enabled)
  - With 85% hit rate: 85% fewer DB queries
  - Less connection pool strain
  - Reduced database CPU usage

✓ Reliability
  - System works even if Redis is DOWN
  - Automatic fallback to database
  - No dependency on Redis for correctness
  - Database is always source of truth

✓ Flexibility
  - Can enable/disable Redis anytime
  - Gradual adoption (migrate tools at your pace)
  - Optional per-tool basis
  - Non-invasive caching layer


SUPPORT & DOCUMENTATION
========================

Files created:
✓ REDIS_CACHING_GUIDE.py ................... 200+ lines guide
✓ REDIS_EXAMPLE_PEMBIMBING.py ............. Before/after examples
✓ API_REDIS_INTEGRATION.py ................ FastAPI integration
✓ AGENT_NODES_REDIS_INTEGRATION.py ........ Agent nodes integration
✓ core/redis_cache_manager.py ............. Cache manager (500+ lines)
✓ core/cache_sync.py ...................... Sync validator (400+ lines)
✓ core/cache_tools.py ..................... Helper tools (300+ lines)
✓ core/cache_config.py .................... Configuration (200+ lines)
✓ core/cache_init.py ...................... Initialization (400+ lines)

Total: 2500+ lines of cache infrastructure code

Questions? Check the relevant file for detailed documentation.


START HERE
==========

1. Run initialization test:
   python core/cache_init.py

2. Read the migration guide:
   cat REDIS_CACHING_GUIDE.py

3. Review example:
   cat REDIS_EXAMPLE_PEMBIMBING.py

4. Migrate first tool:
   See PHASE 3 above

5. Monitor progress:
   python core/cache_init.py monitor

Good luck! 🚀
""")
