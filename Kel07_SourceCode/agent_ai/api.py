"""
FastAPI endpoint untuk integrasi dengan UI Laravel
Menghubungkan agent grouping dengan Laravel Kelompok Management System
FIXED VERSION - Dengan error handling yang robust
"""

from fastapi import FastAPI, HTTPException, Request
from fastapi.responses import JSONResponse
from fastapi.middleware.cors import CORSMiddleware
from starlette.middleware.base import BaseHTTPMiddleware
from pydantic import BaseModel
from typing import List, Optional, Dict, Any
import json
import logging
import sys
import traceback
import time
from datetime import datetime
from json import JSONEncoder

from core.database import is_database_connection_error


def _configure_stdio_encoding() -> None:
    """Make stdio safer for Windows terminals with legacy codepages."""
    for stream_name in ("stdout", "stderr"):
        stream = getattr(sys, stream_name, None)
        if stream and hasattr(stream, "reconfigure"):
            try:
                stream.reconfigure(encoding="utf-8", errors="replace")
            except Exception:
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

from logging_setup import configure_concise_logging
configure_concise_logging(level=logging.INFO)

logger = logging.getLogger(__name__)
logger.setLevel(logging.INFO)


# ─────────────────────────────────────────────────────────────────────────────
# UTILITY: Sanitasi surrogate characters agar aman di-encode UTF-8
# ─────────────────────────────────────────────────────────────────────────────

def sanitize_surrogates(text: str) -> str:
    """
    Hapus lone surrogate characters yang tidak bisa di-encode UTF-8.
    Penyebab umum: emoji dari f-string Python di Windows (cp1252 environment).
    """
    if not isinstance(text, str):
        return text
    return text.encode("utf-8", errors="surrogatepass").decode("utf-8", errors="ignore")


def sanitize_dict(obj):
    """
    Rekursif sanitasi semua string dalam dict/list agar bebas surrogate.
    Dipanggil sebelum JSONResponse agar tidak ada UnicodeEncodeError.
    """
    if isinstance(obj, dict):
        return {k: sanitize_dict(v) for k, v in obj.items()}
    elif isinstance(obj, list):
        return [sanitize_dict(item) for item in obj]
    elif isinstance(obj, str):
        return sanitize_surrogates(obj)
    return obj


def safe_json_response(content: dict, status_code: int = 200) -> JSONResponse:
    """
    JSONResponse wrapper yang otomatis sanitasi surrogate sebelum encode.
    Gunakan ini di seluruh endpoint sebagai pengganti JSONResponse biasa.
    """
    safe_content = sanitize_dict(convert_mongo_objects(content))
    return JSONResponse(status_code=status_code, content=safe_content)


# ─────────────────────────────────────────────────────────────────────────────

app = FastAPI(title="Agent Grouping API", version="2.0.0")


# ─────────────────────────────────────────────────────────────────────────────
# MIDDLEWARE
# ─────────────────────────────────────────────────────────────────────────────

# Add CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Ganti dengan domain Laravel Anda di production
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


# Custom middleware untuk error handling
class ErrorHandlingMiddleware(BaseHTTPMiddleware):
    async def dispatch(self, request: Request, call_next):
        try:
            response = await call_next(request)
            return response
        except Exception as e:
            logger.error(f"Unhandled exception: {str(e)}")
            logger.error(traceback.format_exc())
            return safe_json_response({
                "success": False,
                "result": f"Server error: {str(e)}",
                "error_type": "internal_server_error"
            }, status_code=500)


app.add_middleware(ErrorHandlingMiddleware)


# ─────────────────────────────────────────────────────────────────────────────
# UTILITY FUNCTIONS
# ─────────────────────────────────────────────────────────────────────────────

def convert_mongo_objects(obj):
    """Recursively convert MongoDB objects to JSON-serializable types"""
    if isinstance(obj, dict):
        return {k: convert_mongo_objects(v) for k, v in obj.items()}
    elif isinstance(obj, list):
        return [convert_mongo_objects(item) for item in obj]
    elif type(obj).__name__ == 'ObjectId':
        return str(obj)
    elif isinstance(obj, datetime):
        return obj.isoformat()
    return obj


# ─────────────────────────────────────────────────────────────────────────────
# PYDANTIC MODELS
# ─────────────────────────────────────────────────────────────────────────────

class DosenContext(BaseModel):
    user_id: int
    angkatan: Optional[int]
    prodi: Optional[str]
    prodi_id: Optional[int]
    role: Optional[str]
    kategori_pa: Optional[int]


class GenerateGroupingRequest(BaseModel):
    prompt: str
    dosen_context: List[DosenContext]
    user_id: int
    request_data: Optional[Dict[str, Any]] = None

# ─────────────────────────────────────────────────────────────────────────────
# HEALTH & STATUS ENDPOINTS
# ─────────────────────────────────────────────────────────────────────────────

@app.get("/health")
async def health_check():
    try:
        redis_mem = get_redis_manager()
        redis_stats = redis_mem.get_stats()
        return safe_json_response({
            "status": "ok",
            "service": "agent-grouping",
            "redis": {"connected": True, "stats": redis_stats}
        })
    except Exception as e:
        logger.warning(f"Redis health check failed: {e}")
        return safe_json_response({
            "status": "ok",
            "service": "agent-grouping",
            "redis": {"connected": False, "error": str(e)}
        })


@app.get("/health/detailed")
async def detailed_health_check():
    """
    Detailed health check for debugging
    """
    status = {
        "status": "ok",
        "service": "agent-grouping",
        "version": "2.0.0",
        "timestamp": datetime.now().isoformat(),
        "checks": {}
    }
    
    # Check Redis
    try:
        redis_mem = get_redis_manager()
        redis_stats = redis_mem.get_stats()
        status["checks"]["redis"] = {"connected": True, "stats": redis_stats}
    except Exception as e:
        status["checks"]["redis"] = {"connected": False, "error": str(e)}
        status["status"] = "degraded"
    
    # Check MongoDB
    try:
        mongo_mem = get_mongo_memory()
        is_connected = mongo_mem.is_connected()
        status["checks"]["mongodb"] = {"connected": is_connected}
        if not is_connected:
            status["status"] = "degraded"
    except Exception as e:
        status["checks"]["mongodb"] = {"connected": False, "error": str(e)}
        status["status"] = "degraded"
    
    # Check database connection
    try:
        from core.database import SessionLocal
        db = SessionLocal()
        db.execute("SELECT 1")
        db.close()
        status["checks"]["database"] = {"connected": True}
    except Exception as e:
        status["checks"]["database"] = {"connected": False, "error": str(e)}
        status["status"] = "degraded"
    
    return safe_json_response(status)


@app.get("/mongodb-status")
async def mongodb_status():
    try:
        mongo_mem = get_mongo_memory()
        return safe_json_response({
            "status": "ok",
            "mongodb_connected": mongo_mem.is_connected(),
            "service": "mongodb-memory",
            "database": "VokasiTeraDB",
            "collections": [
                "sessions", "planner_logs", "executor_logs",
                "metrics", "memory_store", "messages"
            ]
        })
    except Exception as e:
        logger.error(f"MongoDB status check error: {str(e)}")
        return safe_json_response({"status": "error", "error": str(e)}, status_code=500)


# ─────────────────────────────────────────────────────────────────────────────
# SESSION MANAGEMENT ENDPOINTS
# ─────────────────────────────────────────────────────────────────────────────

@app.post("/clear-session/{user_id}")
async def clear_user_session(user_id: int):
    """
    Clear all session data for a user when switching features
    """
    trace_id = f"user_{user_id}"
    try:
        logger.info(f"[{trace_id}] Clearing user session data")
        
        # Clear Redis
        redis_mem = get_redis_manager()
        redis_mem.delete_context(user_id)
        
        # Clear session state
        if hasattr(redis_mem, 'set_session_state'):
            redis_mem.set_session_state(user_id, {})
        
        # Optional: Clear MongoDB messages for this session only
        mongo_mem = get_mongo_memory()
        # Don't delete all history, just mark session as ended
        try:
            mongo_mem.update_session(user_id, {
                "session_ended": True,
                "ended_at": datetime.now().isoformat()
            })
        except Exception as e:
            logger.warning(f"[{trace_id}] Could not update MongoDB session: {e}")
        
        logger.info(f"[{trace_id}] Session cleared successfully")
        
        return safe_json_response({
            "success": True,
            "message": f"Session for user {user_id} cleared",
            "trace_id": trace_id
        })
        
    except Exception as e:
        logger.error(f"[{trace_id}] Error clearing session: {str(e)}")
        logger.error(traceback.format_exc())
        return safe_json_response({
            "success": False, 
            "error": str(e),
            "trace_id": trace_id
        }, status_code=500)


@app.get("/debug/session/{user_id}")
async def debug_user_session(user_id: int):
    """
    Debug endpoint to check session state
    """
    trace_id = f"user_{user_id}"
    try:
        redis_mem = get_redis_manager()
        mongo_mem = get_mongo_memory()
        
        # Get Redis context
        redis_context = redis_mem.load_context(user_id)
        
        # Get session state
        session_state = redis_context.get("session_state", {})
        
        # Get recent messages from MongoDB
        try:
            recent_messages = mongo_mem.get_conversation_history(user_id, days=1)[:10]
            messages_count = len(recent_messages)
        except Exception as e:
            messages_count = 0
            logger.warning(f"[{trace_id}] Could not fetch MongoDB messages: {e}")
        
        return safe_json_response({
            "success": True,
            "user_id": user_id,
            "redis_context": {
                "messages_count": len(redis_context.get("messages", [])),
                "session_state": session_state,
                "preferences": redis_context.get("preferences", {})
            },
            "mongodb_recent_messages": messages_count,
            "trace_id": trace_id
        })
        
    except Exception as e:
        logger.error(f"[{trace_id}] Debug error: {str(e)}")
        return safe_json_response({
            "success": False,
            "error": str(e),
            "trace_id": trace_id
        }, status_code=500)


# ─────────────────────────────────────────────────────────────────────────────
# MAIN AGENT ENDPOINT
# ─────────────────────────────────────────────────────────────────────────────

@app.post("/agent")
async def agent_endpoint(request: GenerateGroupingRequest):
    """
    Unified endpoint untuk semua AI Agent chat.
    Menggunakan Redis untuk fast chat context loading (1-5ms).
    """

    trace_id = f"user_{request.user_id}"
    start_time = time.time()

    try:
        logger.info(f"[{trace_id}] API Request: '{request.prompt[:50]}...'")

        # Validate input
        if not request.prompt or not request.prompt.strip():
            return safe_json_response({
                "success": False,
                "result": "Prompt tidak boleh kosong",
                "error_type": "invalid_input",
                "trace_id": trace_id
            }, status_code=400)

        redis_mem = get_redis_manager()
        mongo_mem = get_mongo_memory()

        # Load chat context with error handling
        try:
            chat_context = redis_mem.load_context(request.user_id)
            conversation_history = chat_context.get("messages", [])[-20:]
            user_prefs = chat_context.get("preferences", {})
            session_state = chat_context.get("session_state", {})
            logger.debug(f"[{trace_id}] Chat context loaded: {len(conversation_history)} messages from Redis")
        except Exception as e:
            logger.warning(f"[{trace_id}] Could not load Redis context: {e}")
            conversation_history = []
            user_prefs = {}
            session_state = {}

        # Initialize MongoDB session if needed
        try:
            if not session_state.get("mongo_session_id"):
                session_data = {
                    "user_id": request.user_id,
                    "dosen_context": [d.dict() for d in request.dosen_context] if request.dosen_context else [],
                    "start_time": datetime.now()
                }
                session_id = mongo_mem.create_session(request.user_id, session_data)
                session_state["mongo_session_id"] = session_id
                redis_mem.set_session_state(request.user_id, session_state)
            else:
                mongo_mem.update_session(request.user_id, {
                    "user_id": request.user_id,
                    "dosen_context": [d.dict() for d in request.dosen_context] if request.dosen_context else []
                })
        except Exception as e:
            logger.warning(f"[{trace_id}] MongoDB session error: {e}")
            # Continue without MongoDB - not critical

        # Prepare dosen context
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

        if dosen_context:
            dosen = dosen_context[0]
            session_state.update({
                "prodi_id": dosen.get("prodi_id"),
                "kategori_pa": dosen.get("kategori_pa"),
                "angkatan": dosen.get("angkatan"),
                "user_id": dosen.get("user_id")
            })
            try:
                redis_mem.set_session_state(request.user_id, session_state)
            except Exception as e:
                logger.warning(f"[{trace_id}] Could not save session state: {e}")

        # Store user message in MongoDB (non-critical)
        try:
            mongo_mem.store_message(
                request.user_id, "user", request.prompt,
                metadata={"source": "api", "timestamp": datetime.now().isoformat()}
            )
        except Exception as e:
            logger.warning(f"[{trace_id}] Could not store user message: {e}")

        # Call agent with error handling
        try:
            agent_result = run_agent_chat(
                prompt=request.prompt,
                user_id=request.user_id,
                dosen_context=dosen_context,
                conversation_history=conversation_history,
                request_data=request.request_data or {}
            )
        except Exception as e:
            logger.error(f"[{trace_id}] Agent execution error: {str(e)}")
            logger.error(traceback.format_exc())
            return safe_json_response({
                "success": False,
                "result": f"Gagal memproses permintaan: {str(e)}",
                "error_type": "agent_execution_error",
                "trace_id": trace_id
            }, status_code=500)

        # Check for database connection error
        if is_database_connection_error(agent_result.get("result", "")) or \
           is_database_connection_error(agent_result.get("error", "")):
            logger.error(f"[{trace_id}] Database connection issue detected in agent response")
            return safe_json_response({
                "success": False,
                "result": "Koneksi ke database sedang bermasalah. Silakan coba lagi beberapa saat.",
                "error_type": "database_connection_error",
                "trace_id": trace_id,
            }, status_code=503)

        # Store assistant response
        try:
            redis_mem.add_message(request.user_id, "user", request.prompt)
            assistant_response = agent_result.get("result", "")
            redis_mem.add_message(request.user_id, "assistant", assistant_response)
        except Exception as e:
            logger.warning(f"[{trace_id}] Could not store messages in Redis: {e}")

        try:
            mongo_mem.store_message(
                request.user_id, "assistant", assistant_response,
                metadata={
                    "action": agent_result.get("action"),
                    "model": "gpt-4",
                    "timestamp": datetime.now().isoformat()
                }
            )
        except Exception as e:
            logger.warning(f"[{trace_id}] Could not store assistant message: {e}")

        # Log actions
        action = agent_result.get("action")
        if action:
            try:
                redis_mem.set_last_action(request.user_id, action)
                mongo_mem.log_executor_action(
                    request.user_id, action,
                    {
                        "prompt": request.prompt,
                        "action_result": agent_result.get("grouping_payload") or
                                         agent_result.get("pembimbing_payload") or
                                         agent_result.get("jadwal_meta")
                    },
                    status="success"
                )
            except Exception as e:
                logger.warning(f"[{trace_id}] Could not log action: {e}")

        # Record metrics
        try:
            response_time_ms = (time.time() - start_time) * 1000
            mongo_mem.record_metric(
                request.user_id, "response_time_ms", response_time_ms,
                tags={"action": action, "timestamp": datetime.now().isoformat()}
            )
            mongo_mem.record_metric(
                request.user_id, "response_quality",
                1 if agent_result.get("success") else 0,
                tags={"action": action, "timestamp": datetime.now().isoformat()}
            )
            if action:
                mongo_mem.record_metric(
                    request.user_id, "action_count", 1,
                    tags={"action_type": action, "timestamp": datetime.now().isoformat()}
                )
        except Exception as e:
            logger.warning(f"[{trace_id}] Could not record metrics: {e}")

        # JSON backup (non-critical)
        try:
            memory = ConversationMemory(str(request.user_id))
            full_context = redis_mem.load_context(request.user_id)
            memory.save_conversation(full_context.get("messages", []))
        except Exception as e:
            logger.warning(f"[{trace_id}] JSON backup failed: {e}")

        logger.info(f"[{trace_id}] OK: Respons dikirim ke Laravel (Redis + MongoDB)")

        return safe_json_response({
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
        })

    except Exception as e:
        logger.error(f"[{trace_id}] Error: {str(e)}")
        logger.error(f"[{trace_id}] Traceback:\n{traceback.format_exc()}")
        return safe_json_response({
            "success": False,
            "result": f"Terjadi kesalahan: {str(e)}",
            "trace_id": trace_id,
            "error": str(e)
        }, status_code=500)


# ─────────────────────────────────────────────────────────────────────────────
# HISTORY & ANALYTICS ENDPOINTS
# ─────────────────────────────────────────────────────────────────────────────

@app.get("/conversation-history/{user_id}")
async def get_conversation_history(user_id: int):
    trace_id = f"user_{user_id}"
    try:
        memory = ConversationMemory(str(user_id))
        history = memory.load_conversation()
        return safe_json_response({
            "success": True,
            "user_id": user_id,
            "message_count": len(history),
            "history": history
        })
    except Exception as e:
        logger.error(f"[{trace_id}] Error: {str(e)}")
        return safe_json_response({"success": False, "error": str(e)}, status_code=500)


@app.get("/long-term-history/{user_id}")
async def get_long_term_history(user_id: int, days: int = 30, limit: int = 100):
    trace_id = f"user_{user_id}"
    try:
        logger.info(f"[{trace_id}] Fetching long-term history ({days} days, {limit} messages)")
        mongo_mem = get_mongo_memory()
        history = mongo_mem.get_conversation_history(user_id, days=days)
        history = history[:limit]
        return safe_json_response({
            "success": True,
            "user_id": user_id,
            "message_count": len(history),
            "period_days": days,
            "history": history,
            "trace_id": trace_id
        })
    except Exception as e:
        logger.error(f"[{trace_id}] Long-term history error: {str(e)}")
        return safe_json_response({"success": False, "error": str(e)}, status_code=500)


@app.get("/analytics/{user_id}")
async def get_user_analytics(user_id: int):
    trace_id = f"user_{user_id}"
    try:
        logger.info(f"[{trace_id}] Fetching analytics")
        mongo_mem = get_mongo_memory()
        analytics = mongo_mem.get_user_analytics(user_id)

        response_time_metrics = mongo_mem.get_metrics(user_id, "response_time_ms", days=30)
        quality_metrics = mongo_mem.get_metrics(user_id, "response_quality", days=30)
        action_metrics = mongo_mem.get_metrics(user_id, "action_count", days=30)

        avg_response_time = (
            sum(m.get("value", 0) for m in response_time_metrics) / len(response_time_metrics)
            if response_time_metrics else 0
        )
        avg_quality = (
            sum(m.get("value", 0) for m in quality_metrics) / len(quality_metrics)
            if quality_metrics else 0
        )
        total_actions = len(action_metrics) if action_metrics else 0

        last_activity = analytics.get("last_activity")
        if last_activity and hasattr(last_activity, 'isoformat'):
            last_activity = last_activity.isoformat()

        return safe_json_response({
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
        })
    except Exception as e:
        logger.error(f"[{trace_id}] Analytics error: {str(e)}")
        return safe_json_response({"success": False, "error": str(e)}, status_code=500)


@app.get("/metrics/{user_id}/{metric_type}")
async def get_user_metrics(user_id: int, metric_type: str, days: int = 7):
    trace_id = f"user_{user_id}"
    try:
        logger.info(f"[{trace_id}] Fetching metrics: {metric_type} ({days} days)")
        mongo_mem = get_mongo_memory()
        metrics = mongo_mem.get_metrics(user_id, metric_type, days=days, limit=100)
        stats = mongo_mem.get_metric_stats(user_id, metric_type, days=days)
        return safe_json_response({
            "success": True,
            "user_id": user_id,
            "metric_type": metric_type,
            "period_days": days,
            "metric_count": len(metrics),
            "metrics": metrics,
            "statistics": stats,
            "trace_id": trace_id
        })
    except Exception as e:
        logger.error(f"[{trace_id}] Metrics error: {str(e)}")
        return safe_json_response({"success": False, "error": str(e)}, status_code=500)


@app.get("/execution-logs/{user_id}")
async def get_execution_logs(user_id: int, limit: int = 50):
    trace_id = f"user_{user_id}"
    try:
        logger.info(f"[{trace_id}] Fetching execution logs")
        mongo_mem = get_mongo_memory()
        limit = min(limit, 200)
        logs = mongo_mem.get_executor_logs(user_id, limit=limit)
        return safe_json_response({
            "success": True,
            "user_id": user_id,
            "log_count": len(logs),
            "logs": logs,
            "trace_id": trace_id
        })
    except Exception as e:
        logger.error(f"[{trace_id}] Execution logs error: {str(e)}")
        return safe_json_response({"success": False, "error": str(e)}, status_code=500)


# ─────────────────────────────────────────────────────────────────────────────
# ENHANCED CONVERSATION HISTORY ENDPOINTS
# ─────────────────────────────────────────────────────────────────────────────

@app.get("/conversation/{user_id}/full")
async def get_full_conversation(user_id: int, limit: int = 100, days: int = 30):
    """
    Get full conversation history dengan metadata lengkap dari MongoDB
    Endpoint ini berguna untuk:
    - Menampilkan history percakapan di UI
    - Melakukan analisis percakapan
    - Export conversation
    
    Query params:
    - limit: Max messages (default 100, max 1000)
    - days: Retrieve from last n days (default 30)
    """
    trace_id = f"user_{user_id}"
    try:
        logger.info(f"[{trace_id}] Fetching full conversation")
        mongo_mem = get_mongo_memory()
        
        result = mongo_mem.get_conversation_with_metadata(
            user_id, limit=limit, days=days
        )
        
        if result.get("success"):
            result["trace_id"] = trace_id
            return safe_json_response(result)
        else:
            return safe_json_response(result, status_code=500)
            
    except Exception as e:
        logger.error(f"[{trace_id}] Error: {str(e)}")
        return safe_json_response({
            "success": False,
            "error": str(e),
            "trace_id": trace_id
        }, status_code=500)


@app.get("/conversation/{user_id}/summary")
async def get_conversation_summary(user_id: int, days: int = 30):
    """
    Get quick conversation summary tanpa full message content
    Berguna untuk:
    - Quick overview percakapan
    - Checking last activity
    - Statistics tentang chat patterns
    
    Returns:
    - total_messages: Total pesan dalam period
    - role_breakdown: Breakdown user vs assistant messages
    - last_activity: Kapan percakapan terakhir
    """
    trace_id = f"user_{user_id}"
    try:
        logger.info(f"[{trace_id}] Fetching conversation summary")
        mongo_mem = get_mongo_memory()
        
        summary = mongo_mem.get_conversation_summary(user_id, days=days)
        summary["trace_id"] = trace_id
        summary["success"] = True
        
        return safe_json_response(summary)
        
    except Exception as e:
        logger.error(f"[{trace_id}] Error: {str(e)}")
        return safe_json_response({
            "success": False,
            "error": str(e),
            "trace_id": trace_id
        }, status_code=500)


@app.get("/conversation/{user_id}/search")
async def search_conversation(user_id: int, keyword: str, days: int = 30, limit: int = 50):
    """
    Search dalam conversation history berdasarkan keyword
    
    Query params:
    - keyword: Search term (case-insensitive)
    - days: Search in last n days (default 30)
    - limit: Max results (default 50, max 500)
    """
    trace_id = f"user_{user_id}"
    try:
        if not keyword or len(keyword.strip()) == 0:
            return safe_json_response({
                "success": False,
                "error": "Keyword tidak boleh kosong",
                "trace_id": trace_id
            }, status_code=400)
        
        logger.info(f"[{trace_id}] Searching conversation for: {keyword}")
        mongo_mem = get_mongo_memory()
        
        results = mongo_mem.search_conversations(
            user_id, keyword, days=days, limit=min(limit, 500)
        )
        
        return safe_json_response({
            "success": True,
            "user_id": user_id,
            "keyword": keyword,
            "result_count": len(results),
            "results": results,
            "trace_id": trace_id
        })
        
    except Exception as e:
        logger.error(f"[{trace_id}] Search error: {str(e)}")
        return safe_json_response({
            "success": False,
            "error": str(e),
            "trace_id": trace_id
        }, status_code=500)


@app.get("/conversation/{user_id}/export")
async def export_conversation(user_id: int, days: int = 30, format: str = "json"):
    """
    Export conversation history
    
    Query params:
    - days: Export from last n days (default 30)
    - format: "json" atau "text" (default json)
    """
    trace_id = f"user_{user_id}"
    try:
        if format not in ["json", "text"]:
            return safe_json_response({
                "success": False,
                "error": "Format harus 'json' atau 'text'",
                "trace_id": trace_id
            }, status_code=400)
        
        logger.info(f"[{trace_id}] Exporting conversation as {format}")
        mongo_mem = get_mongo_memory()
        
        export_data = mongo_mem.export_conversation(user_id, days=days, format=format)
        export_data["trace_id"] = trace_id
        
        if export_data.get("success"):
            return safe_json_response(export_data)
        else:
            return safe_json_response(export_data, status_code=500)
            
    except Exception as e:
        logger.error(f"[{trace_id}] Export error: {str(e)}")
        return safe_json_response({
            "success": False,
            "error": str(e),
            "trace_id": trace_id
        }, status_code=500)


# ─────────────────────────────────────────────────────────────────────────────
# DELETE ENDPOINTS
# ─────────────────────────────────────────────────────────────────────────────

@app.delete("/conversation-history/{user_id}")
async def clear_conversation_history(user_id: int):
    trace_id = f"user_{user_id}"
    try:
        memory = ConversationMemory(str(user_id))
        memory.clear_conversation()

        redis_mem = get_redis_manager()
        redis_mem.delete_context(user_id)

        return safe_json_response({
            "success": True,
            "message": f"Conversation history for user {user_id} cleared (Redis + JSON)"
        })
    except Exception as e:
        logger.error(f"[{trace_id}] Error: {str(e)}")
        return safe_json_response({"success": False, "error": str(e)}, status_code=500)


# ─────────────────────────────────────────────────────────────────────────────
# MAIN ENTRY POINT
# ─────────────────────────────────────────────────────────────────────────────

if __name__ == "__main__":
    import uvicorn
    print("=" * 60)
    print("Starting FastAPI server for Agent Grouping with MongoDB...")
    print("Version: 2.0.0 (Fixed with robust error handling)")
    print("=" * 60)
    print("Endpoints available:")
    print("  - GET  /health")
    print("  - GET  /health/detailed")
    print("  - POST /agent")
    print("  - POST /clear-session/{user_id}")
    print("  - GET  /debug/session/{user_id}")
    print("  - GET  /conversation-history/{user_id}")
    print("  - GET  /long-term-history/{user_id}")
    print("  - GET  /analytics/{user_id}")
    print("  - GET  /metrics/{user_id}/{metric_type}")
    print("  - GET  /execution-logs/{user_id}")
    print("  - GET  /mongodb-status")
    print("  - DELETE /conversation-history/{user_id}")
    print("=" * 60)
    uvicorn.run(
        "api:app", 
        host="127.0.0.1", 
        port=8002, 
        reload=True, 
        log_level="info",
        reload_dirs=["."]
    )