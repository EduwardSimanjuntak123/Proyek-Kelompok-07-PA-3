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

from main import run_agent_chat
from core.memory import ConversationMemory

import logging
import traceback

# Configure logging - show DEBUG level untuk detailed flow tracking
logging.basicConfig(
    level=logging.DEBUG,
    format='%(asctime)s [%(name)s] %(levelname)s: %(message)s',
    handlers=[
        logging.StreamHandler(),  # Console output
        logging.FileHandler('agent_api.log')  # File logging
    ]
)
logger = logging.getLogger(__name__)
logger.setLevel(logging.DEBUG)

# Set FastAPI/Uvicorn loggers ke level yang lebih rendah for less noise
logging.getLogger("uvicorn").setLevel(logging.INFO)
logging.getLogger("uvicorn.access").setLevel(logging.WARNING)
logging.getLogger("fastapi").setLevel(logging.INFO)

app = FastAPI(title="Agent Grouping API", version="1.0.0")


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
    """Health check endpoint"""
    return {"status": "ok", "service": "agent-grouping"}


@app.post("/agent")
async def agent_endpoint(request: GenerateGroupingRequest):
    """
    Unified endpoint untuk semua AI Agent chat
    Model-aware: bisa menjawab tentang models/schema sesuai role dosen
    
    User bebas input apapun - agent akan:
    - Jika tanya tentang model/schema/table → jawab dengan model awareness
    - Jika tanya hal lain → generik chat response
    
    Args:
        request: GenerateGroupingRequest dengan prompt, dosen_context, user_id
    
    Returns:
        Simple response dengan result dari LLM (model-aware)
    """
    
    trace_id = f"user_{request.user_id}"
    
    try:
        logger.info(f"[{trace_id}] 📨 API Request: '{request.prompt[:50]}...'")
        
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
        
        # Load only recent conversation history to avoid context explosion
        memory = ConversationMemory(str(request.user_id))
        conversation_history = memory.load_conversation()[-8:]
        
        # Call agent - dengan dosen_context untuk model awareness
        agent_result = run_agent_chat(
            prompt=request.prompt,
            user_id=request.user_id,
            dosen_context=dosen_context,
            conversation_history=conversation_history
        )
        
        logger.info(f"[{trace_id}] ✓ Respons dikirim ke Laravel")
        
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
                "trace_id": trace_id
            }
        )
    
    except Exception as e:
        logger.error(f"[{trace_id}] ❌ Error: {str(e)}")
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


@app.delete("/conversation-history/{user_id}")
async def clear_conversation_history(user_id: int):
    """
    Clear conversation history untuk user tertentu
    
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
        logger.debug(f"[{trace_id}] History cleared")
        
        return JSONResponse(
            status_code=200,
            content={
                "success": True,
                "message": f"Conversation history for user {user_id} cleared"
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
    
    print("Starting FastAPI server for Agent Grouping...")
    uvicorn.run(
        "api:app",
        host="127.0.0.1",
        port=8001,
        reload=True,
        log_level="info"
    )
