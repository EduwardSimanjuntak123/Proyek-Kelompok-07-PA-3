"""
AGENT NODES REDIS INTEGRATION
==============================

How to integrate Redis caching into agent nodes (planner, executor, etc)
"""

import logging
from typing import Dict, Any
from core.cache_tools import get_cached_tool_helper
from core.cache_config import CacheConfig, CachePolicies

logger = logging.getLogger(__name__)
helper = get_cached_tool_helper()


# ==================== PLANNER NODE ENHANCEMENT ====================

def enhanced_planner_node(state):
    """
    Enhanced planner_node dengan Redis caching awarenessu 
    
    Instead of just routing to tools, also:
    1. Check if we have cached results for common queries
    2. Determine cache invalidation needs
    3. Suggest caching strategies for tool outputs
    """
    
    try:
        user_id = state.get("user_id", "default")
        prompt = state["messages"][-1]["content"]
        
        # Extract intent (existing logic)
        # ... (existing planner logic)
        
        plan = state.get("plan", {})
        
        # ▼ NEW: Add cache hints to plan
        if plan.get("action") == "query_pembimbing":
            # This is a query - add cache strategy
            plan["cache_strategy"] = {
                "cache_key": f"pembimbing:query:{user_id}",
                "use_sync_check": True,
                "ttl": CacheConfig.TTL_PEMBIMBING
            }
        
        elif plan.get("action") == "generate_pembimbing":
            # This is a mutation - will need cache invalidation
            plan["invalidation_needed"] = True
            plan["invalidation_types"] = ["pembimbing", "kelompok"]
        
        state["plan"] = plan
        return state
    
    except Exception as e:
        logger.error(f"Error in enhanced planner: {e}")
        return state


# ==================== EXECUTOR NODE ENHANCEMENT ====================

def enhanced_executor_node(state):
    """
    Enhanced executor_node dengan Redis cache management
    
    Before executing tool:
    1. Check if result is already cached
    2. Check if cache is still valid (sync check)
    3. If valid cache exists, skip tool execution
    
    After executing tool:
    4. Cache the result
    5. If mutation, invalidate dependent caches
    """
    
    try:
        user_id = state.get("user_id", "default")
        plan = state.get("plan", {})
        action = plan.get("action")
        
        logger.info(f"[EXECUTOR] Executing action: {action}")
        
        # ▼ NEW: Check for cached results BEFORE tool execution
        cache_strategy = plan.get("cache_strategy", {})
        if cache_strategy:
            cache_key = cache_strategy.get("cache_key")
            cached_result = helper.cache_manager.get_cached_query(cache_key)
            
            if cached_result:
                logger.info(f"[EXECUTOR] ✓ Using cached result for {action}")
                state["execution_result"] = cached_result
                state["cache_used"] = True
                return state
        
        # No cache hit - execute tool
        logger.info(f"[EXECUTOR] Executing tool for {action} (cache miss/no strategy)")
        state["cache_used"] = False
        
        # Existing tool execution logic...
        # (call appropriate tool based on action)
        execution_result = execute_tool(action, plan)
        
        # ▼ NEW: Cache the result if successful
        if execution_result.get("status") == "success":
            if cache_strategy:
                cache_key = cache_strategy.get("cache_key")
                ttl = cache_strategy.get("ttl", CacheConfig.DEFAULT_TTL)
                
                helper.cache_tool_result(
                    action,
                    plan,
                    execution_result,
                    ttl=ttl
                )
                logger.info(f"[EXECUTOR] ✓ Cached result for {action}")
        
        # ▼ NEW: Invalidate caches if this was a mutation
        if plan.get("invalidation_needed"):
            invalidation_types = plan.get("invalidation_types", [])
            invalidation_map = {et: [] for et in invalidation_types}
            
            helper.invalidate_on_change(invalidation_map)
            logger.info(f"[EXECUTOR] ✓ Invalidated caches: {invalidation_types}")
        
        state["execution_result"] = execution_result
        return state
    
    except Exception as e:
        logger.error(f"Error in enhanced executor: {e}")
        state["error"] = str(e)
        return state


def execute_tool(action: str, plan: Dict) -> Dict:
    """Execute tool based on action type"""
    
    from tools.pembimbing_tools import (
        generate_pembimbing_assignments_by_context,
        get_pembimbing_list,
        get_pembimbing_by_dosen_name,
    )
    from tools.kelompok_tools import (
        get_kelompok_by_dosen_context,
        get_anggota_kelompok_by_nomor,
    )
    
    try:
        if action == "query_pembimbing":
            # Use cached query
            def fetch():
                return get_pembimbing_list()
            
            result = helper.cached_query(
                cache_key="pembimbing:all",
                db_fetch_fn=fetch,
                ttl=CacheConfig.TTL_PEMBIMBING
            )
            return {"status": "success", "data": result}
        
        elif action == "generate_pembimbing":
            prodi_id = plan.get("prodi_id")
            kategori_pa_id = plan.get("kategori_pa_id")
            angkatan_id = plan.get("angkatan_id")
            prompt = plan.get("prompt")
            
            return generate_pembimbing_assignments_by_context(
                prodi_id=prodi_id,
                kategori_pa_id=kategori_pa_id,
                angkatan_id=angkatan_id,
                persist=plan.get("persist", False),
                prompt=prompt
            )
        
        elif action == "query_kelompok":
            return get_kelompok_by_dosen_context()
        
        elif action == "query_anggota_kelompok":
            nomor = plan.get("nomor_kelompok")
            return get_anggota_kelompok_by_nomor(nomor)
        
        else:
            return {"status": "error", "message": f"Unknown action: {action}"}
    
    except Exception as e:
        logger.error(f"Error executing tool: {e}")
        return {"status": "error", "message": str(e)}


# ==================== ANSWER NODE ENHANCEMENT ====================

def enhanced_answer_node(state):
    """
    Enhanced answer_node dengan cache performance info
    
    Include cache hit info dalam response untuk user awareness
    """
    
    try:
        execution_result = state.get("execution_result", {})
        cache_used = state.get("cache_used", False)
        
        # Format answer with cache info
        answer = "Berikut hasil query:\n\n"
        
        if cache_used:
            answer += "📦 (Dari cache - Response cepat)\n\n"
        else:
            answer += "🔄 (Fresh dari database)\n\n"
        
        # Existing answer formatting logic...
        # ... (format execution_result into human-readable answer)
        
        state["answer"] = answer
        state["messages"].append({
            "role": "assistant",
            "content": answer,
            "metadata": {
                "cache_used": cache_used,
                "execution_result": execution_result
            }
        })
        
        return state
    
    except Exception as e:
        logger.error(f"Error in enhanced answer: {e}")
        return state


# ==================== SESSION MEMORY WITH CACHE ====================

def load_session_with_cache(session_id: str) -> Dict:
    """
    Load user session dengan cache awareness
    
    Track:
    - User's previous queries (for potential cache reuse)
    - User's preferences untuk caching
    - User's access patterns
    """
    
    try:
        # Check if session cached
        session_cache_key = f"session:{session_id}"
        cached_session = helper.cache_manager.get_state(session_cache_key)
        
        if cached_session:
            logger.info(f"[SESSION] ✓ Session loaded from cache: {session_id}")
            return cached_session
        
        # Load from database if not cached
        # ... (existing session load logic)
        session_data = {
            "user_id": session_id,
            "queries": [],
            "cache_preference": "enabled"
        }
        
        # Cache session
        helper.cache_manager.save_state(
            session_cache_key,
            session_data,
            ttl=CacheConfig.SESSION_TTL
        )
        
        logger.info(f"[SESSION] ✓ Session cached: {session_id}")
        return session_data
    
    except Exception as e:
        logger.error(f"Error loading session: {e}")
        return {}


# ==================== CACHE WARMUP ====================

def warmup_cache_on_startup():
    """
    Warmup cache dengan common queries saat agent startup
    
    Preload frequently accessed data untuk better performance
    """
    
    try:
        logger.info("[CACHE] Starting cache warmup...")
        
        from tools.pembimbing_tools import get_pembimbing_list
        from tools.kelompok_tools import get_kelompok_list
        from tools.mahasiswa_tools import get_mahasiswa_list
        
        # Preload pembimbing list
        pembimbings = get_pembimbing_list()
        if pembimbings.get("status") == "success":
            helper.cache_tool_result(
                "get_pembimbing_list",
                {},
                pembimbings,
                ttl=CacheConfig.TTL_PEMBIMBING
            )
            logger.info(f"[CACHE] ✓ Preloaded {len(pembimbings.get('data', []))} pembimbing")
        
        # Preload kelompok list
        kelompoks = get_kelompok_list()
        if kelompoks.get("status") == "success":
            helper.cache_tool_result(
                "get_kelompok_list",
                {},
                kelompoks,
                ttl=CacheConfig.TTL_KELOMPOK
            )
            logger.info(f"[CACHE] ✓ Preloaded {len(kelompoks.get('data', []))} kelompok")
        
        logger.info("[CACHE] ✓ Cache warmup completed")
        return True
    
    except Exception as e:
        logger.error(f"[CACHE] Error during warmup: {e}")
        return False


# ==================== CONTEXT ENRICHMENT WITH CACHE STATUS ====================

def add_cache_context_to_state(state: Dict) -> Dict:
    """
    Add cache information to agent state untuk better decision making
    """
    
    try:
        if not CacheConfig.CACHE_ENABLED:
            state["cache_context"] = {"enabled": False}
            return state
        
        stats = helper.cache_manager.get_cache_stats()
        hit_rate = stats.get('hit_rate', 0)
        
        state["cache_context"] = {
            "enabled": True,
            "hit_rate": hit_rate,
            "healthy": hit_rate >= CacheConfig.TARGET_HIT_RATE * 100,
            "total_keys": stats.get('total_keys', 0),
            "memory": stats.get('used_memory', 'N/A')
        }
        
        # Add cache recommendations
        if hit_rate < CacheConfig.TARGET_HIT_RATE * 50:
            state["cache_context"]["recommendation"] = "Cache underutilized - consider longer TTLs"
        elif hit_rate > CacheConfig.TARGET_HIT_RATE * 100:
            state["cache_context"]["recommendation"] = "Cache performing well ✓"
        else:
            state["cache_context"]["recommendation"] = "Cache performing fairly well"
        
        return state
    
    except Exception as e:
        logger.error(f"Error enriching cache context: {e}")
        return state


# ==================== USAGE IN LANGGRAPH ====================

"""
Update main.py (or agents configuration) to use enhanced nodes:

from langgraph.graph import StateGraph
from nodes.enhanced_nodes import (
    enhanced_question_node,
    enhanced_planner_node,
    enhanced_executor_node,
    enhanced_answer_node
)
from core.cache_init import initialize_redis_cache

# Initialize cache on app startup
initialize_redis_cache()

# Build graph with cache-aware nodes
builder = StateGraph(AgentState)

builder.add_node("question", enhanced_question_node)
builder.add_node("planner", enhanced_planner_node)
builder.add_node("executor", enhanced_executor_node)
builder.add_node("answer", enhanced_answer_node)

# ... rest of graph configuration
"""
