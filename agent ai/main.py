"""
PA Agent - FastAPI Application Entry Point

Provides REST API endpoints for:
- Generating student groups (kelompok)
- Managing pembimbing assignments
- Querying student data
- Manipulating groups
"""

from fastapi import FastAPI, HTTPException
from fastapi.responses import StreamingResponse, JSONResponse
from fastapi.middleware.cors import CORSMiddleware
import json
import traceback
from typing import Optional, List, Dict, Any
from datetime import datetime

from agents.planner import plan
from utils.logger import log_step
from schemas.request import GenerateRequest
from memory.memory import save_conversation_memory

def log(msg):
    """Simple logging wrapper"""
    print(msg)

# ========== INITIALIZE FASTAPI APP ==========
app = FastAPI(
    title="PA Agent API",
    description="API untuk sistem pengelompokan mahasiswa PA (Proyek Akhir)",
    version="2.0"
)

# ========== CORS MIDDLEWARE ==========
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ========== HEALTH CHECK ENDPOINT ==========
@app.get("/health")
def health_check():
    """Health check endpoint"""
    return {"status": "healthy", "version": "2.0"}


# ========== HELPER FUNCTIONS ==========
def _extract_result_text(result: dict) -> str:
    """
    Extract human-readable result text from various response structures.
    
    Handles different node response formats:
    - Greeting: has 'message' field
    - Capability: has 'response' or 'capabilities'
    - Data queries: has 'action', 'strategy', 'description' in metadata
    - Errors: has 'message' or 'error' field
    - Grouping: has 'group_count' or 'data' with groups
    
    Returns meaningful text for display to user.
    """
    
    # Try direct text fields first
    if result.get("message"):
        return result.get("message")
    
    if result.get("response"):
        return result.get("response")
    
    if result.get("answer"):
        return result.get("answer")
    
    # Try structured responses with descriptions
    if result.get("metadata", {}).get("description"):
        description = result["metadata"]["description"]
        # Build message from metadata
        action = result.get("action", "").replace("_", " ").upper()
        if action:
            return f"{action}: {description}"
        return description
    
    # Handle grouping results
    response_type = result.get("type", "").replace("_", " ")
    
    if response_type in ["grouping_result", "dynamic_grouping"] and result.get("group_count"):
        group_count = result.get("group_count", 0)
        return f"✅ Berhasil membuat {group_count} kelompok"
    
    # Try to build message from action/type
    action = result.get("action", "").replace("_", " ")
    
    if action:
        return f"{response_type}: Processing {action}"
    
    if response_type:
        return f"Processed: {response_type}"
    
    # Fallback for empty responses
    if "dynamic grouping" in response_type:
        return "Kelompok sedang diproses. Silakan tunggu hasil..."
    
    if "grouping command" in response_type:
        strategy = result.get("strategy", "").replace("_", " ")
        if strategy:
            return f"Membuat kelompok dengan strategi: {strategy}"
        return "Membuat kelompok..."
    
    # Last resort - return JSON representation for debugging
    return f"Processed: {response_type}" if response_type else "Request processed"


def _format_dosen_data(result: dict) -> str:
    """Format dosen data for display in chat as HTML table (hide sensitive fields)"""
    dosen_data = result.get("dosen_data", {})
    data_list = dosen_data.get("data", [])
    
    if not data_list:
        return "<p>Tidak ada dosen ditemukan</p>"
    
    # Build HTML table (show nama, jabatan, nip - hide email, nidn, dll)
    total = len(data_list)
    html = f'<p><strong>Total: {total} dosen</strong></p>'
    html += '<table class="agent-data-table"><thead><tr><th>No</th><th>Nama Dosen</th><th>Jabatan</th><th>NIP</th></tr></thead><tbody>'
    
    for idx, dosen in enumerate(data_list, 1):  # Show all dosen
        # Extract nama, jabatan, nip (hide email, nidn, user_id, dll)
        nama = dosen.get("nama", "-")
        jabatan = dosen.get("jabatan_akademik_desc", "-")
        nip = dosen.get("nip", "-")
        
        html += f"<tr><td>{idx}</td><td>{nama}</td><td>{jabatan}</td><td>{nip}</td></tr>"
    
    html += "</tbody></table>"
    
    return html


def _format_grouping_result(result: dict, show_scores: bool = False) -> str:
    """Format grouping result as HTML for display in chat
    
    Args:
        result: Result dict from executor with groups
        show_scores: Whether to show nilai_rata_rata for each member
    """
    # Get groups from the correct location in the response
    # Groups can be in various formats after executor processes them
    groups_list = None
    
    # Check 1: result["data"] is a list of groups directly
    if result.get("data") and isinstance(result["data"], list):
        # Check if it looks like groups data (has 'kelompok' or 'members' keys)
        if result["data"] and isinstance(result["data"][0], dict):
            if "kelompok" in result["data"][0] or "members" in result["data"][0]:
                groups_list = result["data"]
    
    # Check 2: result["data"] is a dict with 'groups' key (from executor.group_by_score_balance_with_constraints)
    if not groups_list and result.get("data") and isinstance(result["data"], dict):
        if "groups" in result["data"] and isinstance(result["data"]["groups"], list):
            groups_list = result["data"]["groups"]
    
    # Check 3: result["groups"] format (legacy)
    if not groups_list:
        if result.get("groups") and isinstance(result["groups"], dict) and "groups" in result["groups"]:
            groups_list = result["groups"]["groups"]
        elif result.get("groups") and isinstance(result["groups"], list):
            groups_list = result["groups"]
    
    if not groups_list:
        return "<p>Tidak ada kelompok ditemukan</p>"
    
    # Extract class stats if available
    class_stats = None
    if result.get("data") and isinstance(result["data"], dict):
        class_stats = result["data"].get("class_stats", {})
    
    class_avg = class_stats.get("class_average") if class_stats else None
    
    # Build HTML for groups
    html = f'<div style="background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin: 10px 0;">'
    html += f'<p style="margin-top: 0; font-weight: bold; color: #333;">✅ Berhasil membuat {len(groups_list)} kelompok</p>'
    
    # Add class statistics if available
    if class_stats:
        html += f'''<div style="background-color: #e8f4f8; padding: 10px; border-radius: 4px; margin: 10px 0; font-size: 0.9em;">
            <strong>📊 Statistik Nilai Kelas:</strong><br/>
            • Rata-rata kelas: <span style="font-weight: bold; color: #007bff;">{class_avg:.2f}</span><br/>
            • Range: {class_stats.get("min_score", 0):.2f} - {class_stats.get("max_score", 0):.2f}<br/>
            • Standar Deviasi: {class_stats.get("std_dev", 0):.2f}
        </div>'''
    
    # Format each group
    html += '<div style="display: grid; gap: 12px; margin-top: 15px;">'
    
    for idx, group in enumerate(groups_list, 1):
        group_size = 0
        members_html = ""
        group_num = idx
        group_average = None
        deviation = None
        
        if isinstance(group, dict):
            members = group.get("members", [])
            group_num = group.get("kelompok", idx)
            group_size = len(members)
            group_average = group.get("group_average")
            deviation = group.get("deviation_from_class")
            
            for member in members:
                if isinstance(member, dict):
                    # Handle nested student_data structure
                    if "student_data" in member and member["student_data"]:
                        member_name = member["student_data"].get("nama", "Unknown")
                        member_nim = member["student_data"].get("nim", "")
                        member_score = member["student_data"].get("nilai_rata_rata")
                    else:
                        member_name = member.get("nama", member.get("name", "Unknown"))
                        member_nim = member.get("nim", "")
                        member_score = member.get("nilai_rata_rata")
                    
                    # Only add if member name exists
                    if member_name and member_name != "None" and member_name.lower() != "unknown":
                        # Format dengan atau tanpa score
                        if show_scores and member_score:
                            members_html += f'<div style="padding: 4px 0; font-size: 0.9em;">• {member_name} ({member_nim}) - Nilai: {member_score:.2f}</div>'
                        else:
                            members_html += f'<div style="padding: 4px 0; font-size: 0.9em;">• {member_name} ({member_nim})</div>'
                else:
                    members_html += f'<div style="padding: 4px 0; font-size: 0.9em;">• {member}</div>'
        elif isinstance(group, list):
            group_size = len(group)
            for member in group:
                if isinstance(member, dict):
                    member_name = member.get("nama", member.get("name", "Unknown"))
                    member_nim = member.get("nim", "")
                    member_score = member.get("nilai_rata_rata")
                    if member_name and member_name != "None":
                        if show_scores and member_score:
                            members_html += f'<div style="padding: 4px 0; font-size: 0.9em;">• {member_name} ({member_nim}) - Nilai: {member_score:.2f}</div>'
                        else:
                            members_html += f'<div style="padding: 4px 0; font-size: 0.9em;">• {member_name} ({member_nim})</div>'
                else:
                    members_html += f'<div style="padding: 4px 0; font-size: 0.9em;">• {member}</div>'
        
        # Build group header with average score
        header_html = f'<div style="font-weight: bold; color: #007bff; margin-bottom: 8px;">'
        header_html += f'Kelompok {group_num} ({group_size} anggota)'
        
        # Add group average if available
        if group_average is not None:
            # Determine color based on deviation from class average
            balance_indicator = "✓"
            balance_color = "#28a745"  # Green
            
            if deviation is not None:
                if abs(deviation) > 1.5:
                    balance_indicator = "⚠"
                    balance_color = "#ffc107"  # Yellow
                elif abs(deviation) > 2:
                    balance_indicator = "✗"
                    balance_color = "#dc3545"  # Red
            
            header_html += f'<br/><span style="color: {balance_color}; font-size: 0.9em;">{balance_indicator} Rata-rata: <strong>{group_average:.2f}</strong>'
            if class_avg:
                diff = group_average - class_avg
                header_html += f' ({"+" if diff > 0 else ""}{diff:.2f} dari kelas)'
            header_html += '</span>'
        
        header_html += '</div>'
        
        # Group card
        html += f'''<div style="background-color: white; padding: 12px; border-left: 4px solid #007bff; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            {header_html}
            {members_html}
        </div>'''
    
    html += '</div></div>'
    
    return html


def _get_mahasiswa_data(result: dict, dosen_context: Optional[List[Dict[str, Any]]]) -> str:
    """Get mahasiswa data from database"""
    # For now, just return formatted message
    # The executor should handle actual data retrieval
    try:
        if result.get("dosen_data"):
            data_list = result["dosen_data"].get("data", [])
            if data_list:
                html = f'<p><strong>Total: {len(data_list)} orang</strong></p>'
                html += '<table class="agent-data-table"><tr><th>No</th><th>Nama</th><th>NIM</th></tr>'
                for idx, item in enumerate(data_list[:25], 1):
                    nama = item.get("nama", "-")
                    nim = item.get("nim", "-")
                    html += f"<tr><td>{idx}</td><td>{nama}</td><td>{nim}</td></tr>"
                html += "</table>"
                return html
        return ""
    except:
        return ""


def _get_mahasiswa_count(result: dict, dosen_context: Optional[List[Dict[str, Any]]]) -> str:
    """Get mahasiswa count"""
    from tools.db_tool import count_mahasiswa_by_context
    
    if not dosen_context:
        return "Konteks dosen tidak tersedia"
    
    try:
        ctx = dosen_context[0]
        count = count_mahasiswa_by_context(
            prodi_id=ctx.get("prodi_id"),
            tahun_masuk_id=ctx.get("angkatan"),
            kategori_pa=ctx.get("kategori_pa")
        )
        
        return f"<strong>Total mahasiswa: {count}</strong>"
    except Exception as e:
        return f"Error menghitung mahasiswa: {str(e)}"


# ========== MAIN ENDPOINT: /generate-kelompok ==========
@app.post("/generate-kelompok")
async def generate_kelompok(request: GenerateRequest):
    """
    Main endpoint untuk PA Agent
    
    Parameters:
    - prompt: User instruction/query
    - dosen_context: Dosen information context
    
    Returns:
    - JSON response dengan hasil dan data
    """
    
    try:
        # Log request
        log(f"[API] Received request: {request.prompt[:50]}...")
        
        # Convert dosen_context from Pydantic models to dicts
        dosen_context_dicts = [ctx.dict() for ctx in request.dosen_context] if request.dosen_context else None
        
        # Extract user_id (prefer explicit user_id in request, fallback to dosen_context)
        user_id = request.user_id or (dosen_context_dicts[0].get("user_id") if dosen_context_dicts else None)
        print(f"[DEBUG] Extracted user_id: {user_id} (from request.user_id={request.user_id}, dosen_context[0]={dosen_context_dicts[0].get('user_id') if dosen_context_dicts else None})")
        
        # Call planner
        result_json = plan(
            prompt=request.prompt,
            dosen_context=dosen_context_dicts,
            user_id=user_id,
            existing_groups=None,
        )
        
        # Parse result
        try:
            result = json.loads(result_json)
        except:
            result = {"type": "error", "message": result_json}
        
        # Extract show_scores flag from parsed BEFORE executor runs
        # This preserves the parsed instruction info
        show_scores_flag = False
        if result.get("parsed") and result["parsed"].get("show_scores"):
            show_scores_flag = True
        elif result.get("metadata") and result["metadata"].get("show_scores"):
            show_scores_flag = True
        
        # Execute steps if result has them (for data retrieval operations)
        if result.get("steps"):
            print(f"[API] Executing steps for response type: {result.get('type')}")
            try:
                from agents.executor import execute
                executed_result, messages = execute(
                    json.dumps(result),
                    dosen_context_dicts,
                    existing_groups=None
                )
                result = executed_result
                
                # Add execution messages to result (ensure result is dict)
                if messages and isinstance(result, dict):
                    result["execution_messages"] = messages
                elif messages and not isinstance(result, dict):
                    # Wrap non-dict result in dict structure
                    result = {
                        "type": "execution_result",
                        "data": result,
                        "execution_messages": messages
                    }
            except Exception as e:
                print(f"[API] Executor error: {str(e)}")
                import traceback
                traceback.print_exc()
                # Ensure result is dict before adding error
                if not isinstance(result, dict):
                    result = {"type": result.get("type") if hasattr(result, "get") else "error"}
                result["execution_error"] = str(e)
        
        # Extract result text
        result_text = _extract_result_text(result)
        
        # Save conversation memory if user_id is available
        if user_id:
            try:
                query_type = result.get("type", "general")
                save_conversation_memory(
                    user_id=user_id,
                    prompt=request.prompt,
                    response=result_text,
                    query_type=query_type,
                    metadata={
                        "response_type": query_type,
                        "has_data": bool(result.get("formatted_response") or result.get("data")),
                        "timestamp": datetime.utcnow().isoformat()
                    },
                    status="success"
                )
                log(f"[API] Conversation memory saved for user {user_id}")
            except Exception as e:
                log(f"[API WARNING] Failed to save conversation memory: {str(e)}")
        
        # Format data berdasarkan response type
        data_to_display = result.get("formatted_response", "")
        
        # Format dosen data jika ada
        if result.get("type") == "dosen_query" and result.get("dosen_data"):
            dosen_html = _format_dosen_data(result)
            if dosen_html:
                data_to_display = dosen_html
        
        # Format student search result
        if result.get("type") == "student_query_result":
            if result.get("formatted_response"):
                data_to_display = result.get("formatted_response")
        
        # Format course list result
        if result.get("type") == "course_list_result":
            if result.get("formatted_response"):
                data_to_display = result.get("formatted_response")
        
        # Format grouping result
        if result.get("type") in ["grouping_result", "dynamic_grouping"]:
            # If we have groups data in result["data"], format it
            if result.get("data"):
                # Case 1: data is a list of groups directly
                if isinstance(result.get("data"), list):
                    grouping_html = _format_grouping_result(result, show_scores=show_scores_flag)
                    if grouping_html and grouping_html != "<p>Tidak ada kelompok ditemukan</p>":
                        data_to_display = grouping_html
                # Case 2: data is a dict with 'groups' key (from executor)
                elif isinstance(result.get("data"), dict) and "groups" in result["data"]:
                    grouping_html = _format_grouping_result(result, show_scores=show_scores_flag)
                    if grouping_html and grouping_html != "<p>Tidak ada kelompok ditemukan</p>":
                        data_to_display = grouping_html
            # Case 3: Try result["groups"] for legacy format
            elif result.get("groups"):
                grouping_html = _format_grouping_result(result, show_scores=show_scores_flag)
                if grouping_html and grouping_html != "<p>Tidak ada kelompok ditemukan</p>":
                    data_to_display = grouping_html
        
        # Extract groups data dari result jika ada
        groups_data = result.get("groups")
        
        # Return response with Laravel-compatible structure
        response_data = {
            "result": result_text,
            "type": result.get("type"),
            "data": data_to_display,  # Formatted data from executor
            "groups": groups_data,
            "recommendations": result.get("recommendations"),
            "plan": result.get("plan"),
            "execution_steps": result.get("execution_steps"),
            "full_response": result
        }
        
        return JSONResponse(content=response_data)
        
    except Exception as e:
        error_msg = f"Error: {str(e)}"
        log(f"[API ERROR] {error_msg}")
        log(f"[TRACEBACK] {traceback.format_exc()}")
        
        return JSONResponse(
            status_code=500,
            content={
                "error": error_msg,
                "type": "execution_error"
            }
        )


# ========== ALTERNATIVE ENDPOINTS FOR SPECIFIC OPERATIONS ==========

@app.post("/create-groups")
async def create_groups(
    group_size: int = 6,
    dosen_context: Optional[List[Dict[str, Any]]] = None,
):
    """Quick endpoint to create groups with specified size"""
    return await generate_kelompok(
        prompt="buatkan kelompok",
        dosen_context=dosen_context,
        group_size=group_size
    )


@app.post("/save-groups")
async def save_groups(dosen_context: Optional[List[Dict[str, Any]]] = None):
    """Quick endpoint to save groups to database"""
    return await generate_kelompok(
        prompt="simpan hasil kelompok ke database",
        dosen_context=dosen_context
    )


@app.post("/view-groups")
async def view_groups(dosen_context: Optional[List[Dict[str, Any]]] = None):
    """Quick endpoint to view existing groups"""
    return await generate_kelompok(
        prompt="tampilkan kelompok",
        dosen_context=dosen_context
    )


@app.post("/query")
async def query(
    prompt: str,
    dosen_context: Optional[List[Dict[str, Any]]] = None,
):
    """Generic query endpoint for advanced queries"""
    return await generate_kelompok(
        prompt=prompt,
        dosen_context=dosen_context
    )


# ========== INFO ENDPOINTS ==========

@app.get("/info")
def get_info():
    """API information"""
    return {
        "name": "PA Agent API",
        "version": "2.0",
        "description": "Sistem pengelompokan mahasiswa PA (Proyek Akhir)",
        "main_endpoint": "/generate-kelompok",
        "features": [
            "Create student groups",
            "Save groups to database",
            "View existing groups",
            "Query student data",
            "Manipulate group assignments",
            "Assign pembimbing (advisors)"
        ]
    }


@app.get("/endpoints")
def list_endpoints():
    """List all available endpoints"""
    return {
        "endpoints": [
            {
                "method": "POST",
                "path": "/generate-kelompok",
                "description": "Main endpoint - generate or manipulate groups"
            },
            {
                "method": "POST",
                "path": "/create-groups",
                "description": "Quick: Create groups"
            },
            {
                "method": "POST",
                "path": "/save-groups",
                "description": "Quick: Save groups to database"
            },
            {
                "method": "POST",
                "path": "/view-groups",
                "description": "Quick: View existing groups"
            },
            {
                "method": "POST",
                "path": "/query",
                "description": "Generic query endpoint"
            },
            {
                "method": "GET",
                "path": "/health",
                "description": "Health check"
            },
            {
                "method": "GET",
                "path": "/info",
                "description": "API information"
            },
            {
                "method": "GET",
                "path": "/endpoints",
                "description": "List endpoints"
            }
        ]
    }


# ========== ROOT ENDPOINT ==========

@app.get("/")
def root():
    """Root endpoint"""
    return {
        "message": "PA Agent API v2.0",
        "docs": "/docs",
        "info": "/info",
        "endpoints": "/endpoints"
    }


# ========== ERROR HANDLERS ==========

@app.exception_handler(HTTPException)
async def http_exception_handler(request, exc):
    """Handle HTTP exceptions"""
    return JSONResponse(
        status_code=exc.status_code,
        content={"detail": exc.detail}
    )


@app.exception_handler(Exception)
async def general_exception_handler(request, exc):
    """Handle general exceptions"""
    log(f"[UNHANDLED ERROR] {str(exc)}")
    return JSONResponse(
        status_code=500,
        content={"detail": "Internal server error"}
    )


# ========== RUN SERVER ==========
if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        app,
        host="127.0.0.1",
        port=8001,
        reload=True
    )
