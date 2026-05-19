"""
FastAPI endpoint untuk integrasi dengan UI Laravel
Menghubungkan agent grouping dengan Laravel Kelompok Management System
"""

from fastapi import FastAPI, HTTPException
from fastapi.responses import JSONResponse
from pydantic import BaseModel
from typing import List, Optional, Dict, Any
import json
import logging
import sys
import traceback
import time
from datetime import datetime
from bson import ObjectId
from json import JSONEncoder


def _configure_stdio_encoding() -> None:
    """Make stdio safer for Windows terminals with legacy codepages."""
    for stream_name in ("stdout", "stderr"):
        stream = getattr(sys, stream_name, None)
        if stream and hasattr(stream, "reconfigure"):
            try:
                stream.reconfigure(encoding="utf-8", errors="replace")
            except Exception:
                # Keep default encoding if reconfigure is unsupported.
                pass


class Cp1252SafeFilter(logging.Filter):
    """Prevent logging crashes on cp1252 by replacing unsupported characters."""

    @staticmethod
    def _safe_text(value):
        if not isinstance(value, str):
            return value
        try:
            return value.encode("cp1252", errors="replace").decode("cp1252")
        except Exception:
            return value

    def filter(self, record):
        record.msg = self._safe_text(record.msg)
        if isinstance(record.args, tuple):
            record.args = tuple(self._safe_text(arg) for arg in record.args)
        elif isinstance(record.args, dict):
            record.args = {k: self._safe_text(v) for k, v in record.args.items()}
        return True


_configure_stdio_encoding()

from main import run_agent_chat
from core.memory import ConversationMemory
from core.redis_memory import get_redis_manager
from core.mongo_memory import get_mongo_memory


# Configure logging - show DEBUG level untuk detailed flow tracking
logging.basicConfig(
    level=logging.DEBUG,
    format='%(asctime)s [%(name)s] %(levelname)s: %(message)s',
    handlers=[
        logging.StreamHandler(),  # Console output
        logging.FileHandler('agent_api.log')  # File logging
    ]
)

for handler in logging.getLogger().handlers:
    handler.addFilter(Cp1252SafeFilter())

logger = logging.getLogger(__name__)
logger.setLevel(logging.DEBUG)

# Set FastAPI/Uvicorn loggers ke level yang lebih rendah for less noise
logging.getLogger("uvicorn").setLevel(logging.INFO)
logging.getLogger("uvicorn.access").setLevel(logging.WARNING)
logging.getLogger("fastapi").setLevel(logging.INFO)

# Custom JSON encoder untuk handle MongoDB ObjectId dan datetime
class MongoJSONEncoder(JSONEncoder):
    def default(self, o):
        if isinstance(o, ObjectId):
            return str(o)
        if isinstance(o, datetime):
            return o.isoformat()
        return super().default(o)

app = FastAPI(title="Agent Grouping API", version="1.0.0")

# Helper function to convert MongoDB objects for JSON serialization
def convert_mongo_objects(obj):
    """Recursively convert MongoDB objects to JSON-serializable types"""
    if isinstance(obj, dict):
        return {k: convert_mongo_objects(v) for k, v in obj.items()}
    elif isinstance(obj, list):
        return [convert_mongo_objects(item) for item in obj]
    elif isinstance(obj, ObjectId):
        return str(obj)
    elif isinstance(obj, datetime):
        return obj.isoformat()
    return obj


class DosenContext(BaseModel):
    """Context dosen untuk grouping"""
    user_id: int
    angkatan: Optional[int]
    prodi: Optional[str]
    prodi_id: Optional[int]
    role: Optional[str]
    kategori_pa: Optional[int]


class GenerateGroupingRequest(BaseModel):
    """Request untuk generate grouping dari Laravel"""
    prompt: str
    dosen_context: List[DosenContext]
    user_id: int  # User ID dari Laravel session untuk conversation tracking


class GroupMember(BaseModel):
    """Anggota dalam kelompok"""
    nim: str
    nama: str
    email: Optional[str]


class GroupData(BaseModel):
    """Data kelompok hasil generate"""
    kelompok: int
    members: List[GroupMember]


@app.get("/health")
async def health_check():
    """Health check endpoint dengan Redis status"""
    try:
        redis_mem = get_redis_manager()
        redis_stats = redis_mem.get_stats()
        
        return {
            "status": "ok",
            "service": "agent-grouping",
            "redis": {
                "connected": True,
                "stats": redis_stats
            }
        }
    except Exception as e:
        logger.warning(f"Redis health check failed: {e}")
        return {
            "status": "ok",
            "service": "agent-grouping",
            "redis": {
                "connected": False,
                "error": str(e)
            }
        }


@app.post("/agent")
async def agent_endpoint(request: GenerateGroupingRequest):
    """
    Unified endpoint untuk semua AI Agent chat
    Model-aware: bisa menjawab tentang models/schema sesuai role dosen
    
    Menggunakan Redis untuk fast chat context loading (1-5ms)
    
    User bebas input apapun - agent akan:
    - Jika tanya tentang model/schema/table → jawab dengan model awareness
    - Jika tanya hal lain → generik chat response
    
    Args:
        request: GenerateGroupingRequest dengan prompt, dosen_context, user_id
    
    Returns:
        Simple response dengan result dari LLM (model-aware)
    """
    
    trace_id = f"user_{request.user_id}"
    start_time = time.time()  # Track response time
    
    try:
        logger.info(f"[{trace_id}] 📨 API Request: '{request.prompt[:50]}...'")
        
        # Get Redis memory manager (short-term)
        redis_mem = get_redis_manager()
        
        # Get MongoDB memory manager (long-term)
        mongo_mem = get_mongo_memory()
        
        # Load chat context dari Redis (fast) ⚡
        chat_context = redis_mem.load_context(request.user_id)
        conversation_history = chat_context.get("messages", [])[-20:]  # Last 20 messages
        user_prefs = chat_context.get("preferences", {})
        session_state = chat_context.get("session_state", {})
        
        logger.debug(f"[{trace_id}] Chat context loaded: {len(conversation_history)} messages from Redis")
        
        # Log to MongoDB: Session creation atau update
        if not session_state.get("mongo_session_id"):
            session_data = {
                "user_id": request.user_id,
                "dosen_context": [d.dict() for d in request.dosen_context] if request.dosen_context else [],
                "start_time": __import__('datetime').datetime.now()
            }
            session_id = mongo_mem.create_session(request.user_id, session_data)
            session_state["mongo_session_id"] = session_id
            redis_mem.set_session_state(request.user_id, session_state)
        else:
            mongo_mem.update_session(request.user_id, {
                "user_id": request.user_id,
                "dosen_context": [d.dict() for d in request.dosen_context] if request.dosen_context else []
            })
        
        # Build dosen context untuk agent
        dosen_context = []
        for dosen in request.dosen_context:
            dosen_context.append({
                "user_id": dosen.user_id,
                "angkatan": dosen.angkatan,
                "prodi": dosen.prodi,
                "prodi_id": dosen.prodi_id,
                "role": dosen.role,
                "kategori_pa": dosen.kategori_pa
            })
        
        # Update session state jika ada dari dosen_context
        if dosen_context and len(dosen_context) > 0:
            dosen = dosen_context[0]
            session_state.update({
                "prodi_id": dosen.get("prodi_id"),
                "kategori_pa": dosen.get("kategori_pa"),
                "angkatan": dosen.get("angkatan"),
                "user_id": dosen.get("user_id")
            })
            redis_mem.set_session_state(request.user_id, session_state)
        
        # Log user input to MongoDB
        mongo_mem.store_message(
            request.user_id,
            "user",
            request.prompt,
            metadata={"source": "api", "timestamp": __import__('datetime').datetime.now().isoformat()}
        )
        logger.debug(f"[{trace_id}] User message logged to MongoDB")
        
        # Call agent - dengan dosen_context untuk model awareness
        agent_result = run_agent_chat(
            prompt=request.prompt,
            user_id=request.user_id,
            dosen_context=dosen_context,
            conversation_history=conversation_history
        )
        
        # Add user message ke Redis context (short-term)
        redis_mem.add_message(request.user_id, "user", request.prompt)
        logger.debug(f"[{trace_id}] User message saved to Redis")
        
        # Add assistant response ke Redis context (short-term)
        assistant_response = agent_result.get("result", "")
        redis_mem.add_message(request.user_id, "assistant", assistant_response)
        logger.debug(f"[{trace_id}] Assistant response saved to Redis")
        
        # Add assistant response to MongoDB (long-term)
        mongo_mem.store_message(
            request.user_id,
            "assistant",
            assistant_response,
            metadata={
                "action": agent_result.get("action"),
                "model": "gpt-4",
                "timestamp": __import__('datetime').datetime.now().isoformat()
            }
        )
        logger.debug(f"[{trace_id}] Assistant response logged to MongoDB")
        
        # Log action to MongoDB
        action = agent_result.get("action")
        if action:
            redis_mem.set_last_action(request.user_id, action)
            
            # Log to MongoDB executor logs
            mongo_mem.log_executor_action(
                request.user_id,
                action,
                {
                    "prompt": request.prompt,
                    "action_result": agent_result.get("grouping_payload") or 
                                    agent_result.get("pembimbing_payload") or
                                    agent_result.get("jadwal_meta")
                },
                status="success"
            )
            logger.debug(f"[{trace_id}] Action '{action}' logged to MongoDB")
        
        # Record metrics to MongoDB - Response Quality
        mongo_mem.record_metric(
            request.user_id,
            "response_quality",
            1 if agent_result.get("success") else 0,
            tags={"action": action, "timestamp": datetime.now().isoformat()}
        )
        
        # Record metrics to MongoDB - Response Time (in milliseconds)
        response_time_ms = (time.time() - start_time) * 1000
        mongo_mem.record_metric(
            request.user_id,
            "response_time_ms",
            response_time_ms,
            tags={"action": action, "timestamp": datetime.now().isoformat()}
        )
        logger.debug(f"[{trace_id}] Recorded response_time_ms: {response_time_ms:.2f}ms")
        
        # Record action count metric for analytics
        if action:
            mongo_mem.record_metric(
                request.user_id,
                "action_count",
                1,
                tags={"action_type": action, "timestamp": datetime.now().isoformat()}
            )
        
        # Also save to JSON file untuk backup (async process)
        try:
            memory = ConversationMemory(str(request.user_id))
            # Get full history dari Redis
            full_context = redis_mem.load_context(request.user_id)
            memory.save_conversation(full_context.get("messages", []))
            logger.debug(f"[{trace_id}] Conversation backed up to JSON")
        except Exception as e:
            logger.warning(f"[{trace_id}] JSON backup failed: {e}")
        
        logger.info(f"[{trace_id}] OK: Respons dikirim ke Laravel (Redis + MongoDB)")
        
        return JSONResponse(
            status_code=200,
            content={
                "success": agent_result.get("success", True),
                "result": agent_result.get("result", ""),
                "action": agent_result.get("action"),
                "grouping_payload": agent_result.get("grouping_payload"),
                "grouping_meta": agent_result.get("grouping_meta"),
                "pembimbing_payload": agent_result.get("pembimbing_payload"),
                "pembimbing_meta": agent_result.get("pembimbing_meta"),
                "penguji_payload": agent_result.get("penguji_payload"),
                "penguji_meta": agent_result.get("penguji_meta"),
                "excel_file_path": agent_result.get("excel_file_path"),
                "excel_filename": agent_result.get("excel_filename"),
                "jadwal_stage": agent_result.get("jadwal_stage"),
                "jadwal_entries": agent_result.get("jadwal_entries"),
                "jadwal_meta": agent_result.get("jadwal_meta"),
                "jadwal_actions": agent_result.get("jadwal_actions"),
                "trace_id": trace_id
            }
        )
    
    except Exception as e:
        logger.error(f"[{trace_id}] Error: {str(e)}")
        logger.error(f"[{trace_id}] Traceback:\n{traceback.format_exc()}")
        
        return JSONResponse(
            status_code=500,
            content={
                "success": False,
                "result": f"Error: {str(e)}",
                "trace_id": trace_id,
                "error": str(e)
            }
        )


@app.get("/conversation-history/{user_id}")
@app.get("/conversation-history/{user_id}")
async def get_conversation_history(user_id: int):
    """
    Endpoint untuk mendapatkan conversation history user
    
    Args:
        user_id: User ID dari session
    
    Returns:
        History percakapan dengan agent
    """
    
    trace_id = f"user_{user_id}"
    
    try:
        logger.debug(f"[{trace_id}] === GET CONVERSATION HISTORY ===")
        
        memory = ConversationMemory(str(user_id))
        history = memory.load_conversation()
        logger.debug(f"[{trace_id}] Loaded {len(history)} messages")
        
        return JSONResponse(
            status_code=200,
            content={
                "success": True,
                "user_id": user_id,
                "message_count": len(history),
                "history": history
            }
        )
    
    except Exception as e:
        logger.debug(f"[{trace_id}] Get history error: {str(e)}")
        
        return JSONResponse(
            status_code=500,
            content={
                "success": False,
                "error": str(e)
            }
        )


@app.get("/analytics/{user_id}")
async def get_user_analytics(user_id: int):
    """
    Get comprehensive analytics untuk user (long-term insights)
    
    Includes:
    - Total messages, actions, metrics
    - Last activity timestamp
    - Session info
    - Average metrics (response time, quality)
    
    Args:
        user_id: User ID
    
    Returns:
        Analytics summary
    """
    trace_id = f"user_{user_id}"
    
    try:
        logger.info(f"[{trace_id}] 📊 Fetching analytics")
        
        mongo_mem = get_mongo_memory()
        analytics = mongo_mem.get_user_analytics(user_id)
        
        # Add more detailed metrics
        response_time_metrics = mongo_mem.get_metrics(user_id, "response_time_ms", days=30)
        quality_metrics = mongo_mem.get_metrics(user_id, "response_quality", days=30)
        action_metrics = mongo_mem.get_metrics(user_id, "action_count", days=30)
        
        # Calculate averages
        avg_response_time = 0
        if response_time_metrics:
            avg_response_time = sum(m.get("value", 0) for m in response_time_metrics) / len(response_time_metrics)
        
        avg_quality = 0
        if quality_metrics:
            avg_quality = sum(m.get("value", 0) for m in quality_metrics) / len(quality_metrics)
        
        total_actions = len(action_metrics) if action_metrics else 0
        
        # Convert datetime to string for JSON serialization
        last_activity = analytics.get("last_activity")
        if last_activity and hasattr(last_activity, 'isoformat'):
            last_activity = last_activity.isoformat()
        
        return JSONResponse(
            status_code=200,
            content={
                "success": True,
                "user_id": user_id,
                "total_messages": analytics.get("total_messages", 0),
                "total_planner_actions": analytics.get("total_planner_actions", 0),
                "total_executor_actions": analytics.get("total_executor_actions", 0),
                "total_actions": total_actions,
                "last_activity": last_activity,
                "avg_response_time_ms": round(avg_response_time, 2),
                "avg_quality_score": round(avg_quality, 2),
                "metrics_count": {
                    "response_time_ms": len(response_time_metrics),
                    "response_quality": len(quality_metrics),
                    "action_count": total_actions
                },
                "trace_id": trace_id
            }
        )
    
    except Exception as e:
        logger.error(f"[{trace_id}] Analytics error: {str(e)}")
        return JSONResponse(
            status_code=500,
            content={"success": False, "error": str(e)}
        )


@app.get("/long-term-history/{user_id}")
async def get_long_term_history(user_id: int, days: int = 30, limit: int = 100):
    """
    Get full conversation history dari MongoDB (long-term)
    
    Args:
        user_id: User ID
        days: Number of days to retrieve (default 30)
        limit: Max number of messages (default 100, max 1000)
    
    Returns:
        Full conversation history
    """
    trace_id = f"user_{user_id}"
    
    try:
        logger.info(f"[{trace_id}] 📜 Fetching long-term history ({days} days, {limit} messages)")
        
        mongo_mem = get_mongo_memory()
        
        # Get conversation history dari MongoDB
        history = mongo_mem.get_conversation_history(user_id, days=days)
        
        # Limit results
        history = history[:limit]
        
        # Convert MongoDB objects for JSON serialization
        history = convert_mongo_objects(history)
        
        return JSONResponse(
            status_code=200,
            content={
                "success": True,
                "user_id": user_id,
                "message_count": len(history),
                "period_days": days,
                "history": history,
                "trace_id": trace_id
            }
        )
    
    except Exception as e:
        logger.error(f"[{trace_id}] Long-term history error: {str(e)}")
        return JSONResponse(
            status_code=500,
            content={"success": False, "error": str(e)}
        )


@app.get("/metrics/{user_id}/{metric_type}")
async def get_user_metrics(user_id: int, metric_type: str, days: int = 7):
    """
    Get metrics untuk user (response time, quality, token count, etc)
    
    Args:
        user_id: User ID
        metric_type: Type of metric (response_time, quality_score, token_count, etc)
        days: Retrieve metrics from last n days (default 7)
    
    Returns:
        Metrics dan statistics
    """
    trace_id = f"user_{user_id}"
    
    try:
        logger.info(f"[{trace_id}] 📈 Fetching metrics: {metric_type} ({days} days)")
        
        mongo_mem = get_mongo_memory()
        
        # Get metrics
        metrics = mongo_mem.get_metrics(user_id, metric_type, days=days, limit=100)
        
        # Get statistics
        stats = mongo_mem.get_metric_stats(user_id, metric_type, days=days)
        
        # Convert MongoDB objects (ObjectId, datetime) for JSON serialization
        metrics = convert_mongo_objects(metrics)
        stats = convert_mongo_objects(stats)
        
        return JSONResponse(
            status_code=200,
            content={
                "success": True,
                "user_id": user_id,
                "metric_type": metric_type,
                "period_days": days,
                "metric_count": len(metrics),
                "metrics": metrics,
                "statistics": stats,
                "trace_id": trace_id
            }
        )
    
    except Exception as e:
        logger.error(f"[{trace_id}] Metrics error: {str(e)}")
        return JSONResponse(
            status_code=500,
            content={"success": False, "error": str(e)}
        )


@app.get("/execution-logs/{user_id}")
async def get_execution_logs(user_id: int, limit: int = 50):
    """
    Get executor logs untuk tracking apa yang sudah dilakukan agent
    
    Args:
        user_id: User ID
        limit: Max logs (default 50, max 200)
    
    Returns:
        List of executor logs
    """
    trace_id = f"user_{user_id}"
    
    try:
        logger.info(f"[{trace_id}] 🔍 Fetching execution logs")
        
        mongo_mem = get_mongo_memory()
        limit = min(limit, 200)
        
        logs = mongo_mem.get_executor_logs(user_id, limit=limit)
        
        # Convert MongoDB objects for JSON serialization
        logs = convert_mongo_objects(logs)
        
        return JSONResponse(
            status_code=200,
            content={
                "success": True,
                "user_id": user_id,
                "log_count": len(logs),
                "logs": logs,
                "trace_id": trace_id
            }
        )
    
    except Exception as e:
        logger.error(f"[{trace_id}] Execution logs error: {str(e)}")
        return JSONResponse(
            status_code=500,
            content={"success": False, "error": str(e)}
        )


@app.get("/mongodb-status")
async def mongodb_status():
    """
    Check MongoDB connection status
    
    Returns:
        MongoDB connection info
    """
    try:
        mongo_mem = get_mongo_memory()
        
        return JSONResponse(
            status_code=200,
            content={
                "status": "ok",
                "mongodb_connected": mongo_mem.is_connected(),
                "service": "mongodb-memory",
                "database": "VokasiTeraDB",
                "collections": [
                    "sessions",
                    "planner_logs",
                    "executor_logs",
                    "metrics",
                    "memory_store",
                    "messages"
                ]
            }
        )
    
    except Exception as e:
        logger.error(f"MongoDB status check error: {str(e)}")
        return JSONResponse(
            status_code=500,
            content={
                "status": "error",
                "error": str(e)
            }
        )


@app.delete("/conversation-history/{user_id}")
async def clear_conversation_history(user_id: int):
    """
    Clear conversation history untuk user tertentu
    
    Clears both Redis (short-term) dan MongoDB (long-term) memory
    
    Args:
        user_id: User ID dari session
    
    Returns:
        Status clear history
    """
    
    trace_id = f"user_{user_id}"
    
    try:
        logger.debug(f"[{trace_id}] === CLEAR CONVERSATION HISTORY ===")
        
        memory = ConversationMemory(str(user_id))
        memory.clear_conversation()
        logger.debug(f"[{trace_id}] JSON history cleared")
        
        # Also clear Redis
        redis_mem = get_redis_manager()
        redis_mem.delete_context(user_id)
        logger.debug(f"[{trace_id}] Redis context cleared")
        
        return JSONResponse(
            status_code=200,
            content={
                "success": True,
                "message": f"Conversation history for user {user_id} cleared (Redis + JSON)"
            }
        )
    
    except Exception as e:
        logger.debug(f"[{trace_id}] Clear history error: {str(e)}")
        
        return JSONResponse(
            status_code=500,
            content={
                "success": False,
                "error": str(e)
            }
        )


if __name__ == "__main__":
    import uvicorn
    
    print("Starting FastAPI server for Agent Grouping with MongoDB...")
    uvicorn.run(
        "api:app",
        host="127.0.0.1",
        port=8002,
        reload=True,
        log_level="info"
    )
