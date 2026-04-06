"""
Node Grouping - Menangani pembuatan dan modifikasi kelompok mahasiswa.

Ini adalah node paling kompleks yang menangani:
- Parsing constraint (must_pairs, avoid_pairs)
- Menentukan strategi grouping (random, score-based, career-based)
- Building step-by-step execution plan
- Smart dependency resolution untuk combined requests (grouping + save, dll)
"""

import json
from core.state import AgentState
from agents.parser import parse_grouping_request
from agents.step_builder import build_grouping_steps
from agents.combined_request_builder import detect_combined_request, build_combined_steps


def node_grouping(state: AgentState) -> AgentState:
    """
    Node Grouping - Memproses request untuk membuat atau modify kelompok.
    
    Handling:
    1. Detect jika ada combined request (grouping + save, grouping + dosen, dll)
    2. Jika combined: gunakan smart dependency resolver
    3. Jika single: Parse grouping request dan build steps normal
    4. Handle constraints dan strategi
    5. Handle existing groups (modify)
    
    Args:
        state: Current agent state
        
    Returns:
        state: Updated state dengan grouping plan
    """
    
    print(f"[GROUPING NODE] Memproses grouping request...")
    
    prompt = state.get("prompt", "")
    dosen_context = state.get("dosen_context")
    existing_groups = state.get("existing_groups")
    
    # Extract context
    context = None
    if dosen_context and len(dosen_context) > 0:
        context = dosen_context[0]
    
    # === STEP 1: Detect combined requests ===
    combined_request = detect_combined_request(prompt)
    
    # If this is a combined request and grouping is one of the intents, use smart resolver
    if combined_request.get("type") in ["combined", "single"] and "create_group" in combined_request.get("actions", []):
        print(f"[GROUPING NODE] ✓ Detected combined request with create_group")
        print(f"[GROUPING NODE] Actions: {combined_request.get('actions')}")
        
        # Use smart dependency resolver
        combined_steps = build_combined_steps(combined_request, dosen_context)
        
        plan_response = {
            "type": "combined_grouping",
            "message": f"Executing {len(combined_steps.get('actions', []))} actions dengan resolution order",
            "combined_request": combined_request,
            "combined_steps": combined_steps,
            "steps": combined_steps.get("steps", []),
            "actions": combined_steps.get("actions", [])
        }
        
        state["response"] = plan_response
        state["steps"] = combined_steps.get("steps", [])
        state["combined_request"] = combined_request
        
        print(f"[GROUPING NODE] ✓ Combined plan ready dengan {len(state['steps'])} steps (dependencies resolved)")
        
        return state
    
    # === STEP 2: Single grouping request (normal path) ===
    print(f"[GROUPING NODE] Parsing request dengan LLM...")
    parsed_request = parse_grouping_request(prompt)
    
    print(f"[GROUPING NODE] Parsed result: action={parsed_request.get('action')}")
    
    # Check apakah parsing gagal
    if not parsed_request.get("action") or parsed_request.get("action") in ["unknown", None, ""]:
        print(f"[GROUPING NODE] ⚠ Parsing gagal, fallback ke LLM...")
        
        from agents.responder import respond_to_unrecognized_query
        
        # Generate natural response sebagai fallback
        llm_response = respond_to_unrecognized_query(prompt)
        
        state["response"] = {
            "type": "natural_response",
            "message": llm_response
        }
        
        print(f"[GROUPING NODE] ⚠ Fallback response generated")
        
        return state
    
    # Build grouping steps
    print(f"[GROUPING NODE] Building grouping steps...")
    steps_result = build_grouping_steps(parsed_request, context)
    
    if "error" in steps_result:
        print(f"[GROUPING NODE] ✗ Error: {steps_result['error']}")
        
        state["response"] = {
            "type": "error",
            "message": steps_result["error"]
        }
        
        state["error"] = steps_result["error"]
        
        return state
    
    # Prepare response dengan grouping plan
    plan_response = {
        "type": "dynamic_grouping",
        "message": f"Membuat kelompok dengan strategi {parsed_request.get('strategy', 'random')}",
        "parsed": parsed_request,
        "steps": steps_result.get("steps", []),
        "metadata": steps_result.get("metadata", {})
    }
    
    # Jika ada existing_groups, store untuk potential modify operations
    if existing_groups:
        plan_response["existing_groups_context"] = {
            "total_groups": len(existing_groups),
            "total_members": sum(len(g.get("members", [])) for g in existing_groups)
        }
    
    state["response"] = plan_response
    state["parsed_request"] = parsed_request
    state["steps"] = steps_result.get("steps", [])
    
    print(f"[GROUPING NODE] ✓ Grouping plan ready dengan {len(state['steps'])} steps")
    
    return state

