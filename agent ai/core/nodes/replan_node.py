"""
Replan Node - Automatic Recovery on Failure
Ketika execution gagal atau quality rendah, node ini generate alternate plan.
"""

from core.state import AgentState
from openai import OpenAI
from app.config import OPENAI_API_KEY
import json


def node_replan(state: AgentState) -> AgentState:
    """
    Replan node - Generate alternate execution strategy.
    
    Scenarios:
    1. Step execution failed → Try alternative approach
    2. Quality score < 0.6 → Different strategy for same request
    3. Timeout or service error → Simplified approach
    
    Returns: Modified state dengan new steps atau fallback path
    """
    
    print(f"[REPLAN] Generating alternate strategy...")
    
    prompt = state.get("prompt", "")
    detected_type = state.get("detected_type", "unknown")
    evaluation = state.get("evaluation", {})
    execution_log = state.get("execution_log", [])
    current_steps = state.get("steps", [])
    dosen_context = state.get("dosen_context", {})
    
    replan_info = {
        "original_strategy": detected_type,
        "failure_reason": evaluation.get("issues", ["unknown"]),
        "retry_attempt": state.get("retry_count", 0),
        "fallback_used": False,
        "new_steps": []
    }
    
    # === Check retry limits ===
    retry_count = state.get("retry_count", 0)
    if retry_count >= 2:
        print(f"[REPLAN] Retry limit reached (2 attempts)")
        replan_info["fallback_used"] = True
        replan_info["strategy"] = "fallback_simple_response"
        state["replan_info"] = replan_info
        state["next_action"] = None  # Go to END with current response
        return state
    
    # === LLM-based replan ===
    try:
        client = OpenAI(api_key=OPENAI_API_KEY)
        
        # Build context untuk replan
        prev_steps_summary = "\n".join([
            f"- {step.get('action')}: {step.get('params', {})}"
            for step in current_steps[:5]
        ])
        
        errors_summary = "\n".join(
            execution_log[-3:] if execution_log else ["No execution history"]
        )
        
        replan_prompt = f"""
TASK: Generate alternate strategy untuk user request yang gagal.

ORIGINAL REQUEST:
{prompt}

REQUEST TYPE DETECTED:
{detected_type}

PREVIOUS STRATEGY FAILURE REASON:
{json.dumps(evaluation.get('issues', []), indent=2)}

PREVIOUS STEPS ATTEMPTED:
{prev_steps_summary}

RECENT ERRORS:
{errors_summary}

RETRY ATTEMPT: {retry_count + 1}/2

ALTERNATIVE STRATEGIES:
1. Simplified approach: Menor steps, basic data only
2. Different order: Reorder steps untuk dependency issues
3. Data reduction: Konten less data, return essential only
4. Partial completion: Kalau bisa, return partial result
5. Use cache: Gunakan any cached results dari memory

Please generate NEW PLAN dalam format JSON:
{{
    "strategy_name": "simplified" | "reordered" | "data_reduced" | "partial" | "cached",
    "reasoning": "why this should work",
    "new_steps": [
        {{"action": "...", "params": {{...}}}},
        ...
    ]
}}

Anda HARUS return valid JSON, walau minimal untuk fallback.
"""
        
        response = client.chat.completions.create(
            model="gpt-4o-mini",
            messages=[
                {
                    "role": "system",
                    "content": "Anda adalah recovery strategist. Generate alternate plans yang robust dan simpler."
                },
                {
                    "role": "user",
                    "content": replan_prompt
                }
            ],
            temperature=0.5,
            max_tokens=400
        )
        
        replan_text = response.choices[0].message.content
        
        # Parse JSON response
        try:
            json_start = replan_text.find('{')
            json_end = replan_text.rfind('}') + 1
            if json_start >= 0 and json_end > json_start:
                json_str = replan_text[json_start:json_end]
                alt_plan = json.loads(json_str)
                
                replan_info["strategy"] = alt_plan.get("strategy_name", "simplified")
                replan_info["reasoning"] = alt_plan.get("reasoning", "")
                replan_info["new_steps"] = alt_plan.get("new_steps", [])
                
                print(f"[REPLAN] Strategy: {replan_info['strategy']}")
                print(f"[REPLAN] Reasoning: {replan_info['reasoning'][:100]}...")
                print(f"[REPLAN] New steps: {len(replan_info['new_steps'])} steps")
            else:
                raise ValueError("No JSON found in response")
        
        except (json.JSONDecodeError, ValueError) as e:
            print(f"[REPLAN] JSON parse error: {e}, using fallback")
            replan_info["fallback_used"] = True
            replan_info["strategy"] = "fallback_simple"
    
    except Exception as e:
        print(f"[REPLAN] LLM replan error: {e}, using fallback")
        replan_info["fallback_used"] = True
        replan_info["strategy"] = "fallback_error"
    
    # === Fallback logic ===
    if not replan_info.get("new_steps"):
        print(f"[REPLAN] Applying fallback strategy...")
        
        if detected_type == "grouping":
            # Fallback: Simple grouping tanpa advanced filters
            replan_info["new_steps"] = [
                {
                    "action": "get_all_mahasiswa_grouped",
                    "params": {}
                },
                {
                    "action": "save_grouping",
                    "params": {"auto_save": True}
                }
            ]
        
        elif detected_type in ["view_grades", "view_progress"]:
            # Fallback: Get basic info only
            replan_info["new_steps"] = [
                {
                    "action": "get_mahasiswa",
                    "params": {"dosen_id": dosen_context.get("id")}
                }
            ]
        
        else:
            # Generic fallback: Return what we have
            replan_info["new_steps"] = [
                {
                    "action": "quick_answer",
                    "params": {"question": prompt}
                }
            ]
        
        replan_info["fallback_used"] = True
    
    # Update state dengan new plan
    state["steps"] = replan_info["new_steps"]
    state["retry_count"] = retry_count + 1
    state["replan_info"] = replan_info
    
    # Set next action kembali ke executor untuk try steps baru
    state["next_action"] = "node_executor"  # Go back to executor with new steps
    
    print(f"[REPLAN] ✓ Replanning complete - {len(replan_info['new_steps'])} steps ready")
    
    return state
