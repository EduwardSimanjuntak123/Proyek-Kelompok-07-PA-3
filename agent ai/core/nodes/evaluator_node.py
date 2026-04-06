"""
Evaluator Node - Quality Check dan Reflection
Melakukan evaluation pada hasil execution sebelum dikirim ke user.
Bisa trigger retry/replan jika quality rendah.
"""

from core.state import AgentState
from openai import OpenAI
from app.config import OPENAI_API_KEY
import json


def node_evaluator(state: AgentState) -> AgentState:
    """
    Quality check node - evaluate response sebelum final output.
    
    Checks:
    1. Response completeness (ada data? error free?)
    2. Relevance to original prompt
    3. Consistency dengan prior responses
    4. Confidence score
    
    Output:
    - quality_score (0-1)
    - needs_retry (boolean)
    - suggested_retry_strategy (string optional)
    - feedback_for_memory (dict)
    """
    
    print(f"[EVALUATOR] Evaluating response quality...")
    
    prompt = state.get("prompt", "")
    response = state.get("response", {})
    detected_type = state.get("detected_type", "unknown")
    steps = state.get("steps", [])
    execution_log = state.get("execution_log", [])
    
    # Initialize evaluation result
    evaluation = {
        "quality_score": 0.5,  # Default middle
        "completeness": 0,
        "relevance": 0,
        "confidence": 0,
        "needs_retry": False,
        "issues": [],
        "feedback_for_memory": {}
    }
    
    # === QUICK CHECKS (non-LLM) ===
    
    # Check 1: Response exists
    if not response:
        evaluation["issues"].append("Response kosong")
        evaluation["completeness"] = 0
        evaluation["needs_retry"] = True
        evaluation["suggested_retry_strategy"] = "replan_full"
        state["evaluation"] = evaluation
        print(f"[EVALUATOR] ✗ Empty response - marking for retry")
        return state
    
    # Check 2: No critical errors
    if response.get("type") in ["error", "parsing_failed"]:
        evaluation["issues"].append(f"Response tipe error: {response.get('type')}")
        evaluation["completeness"] = 0
        evaluation["confidence"] = 0.1
        evaluation["needs_retry"] = True
        evaluation["suggested_retry_strategy"] = "replan_with_fallback"
        state["evaluation"] = evaluation
        print(f"[EVALUATOR] ✗ Error response - marking for retry")
        return state
    
    # Check 3: Steps executed (untuk action-based responses)
    if detected_type == "grouping" and "steps" in response:
        step_count = len(response.get("steps", []))
        execution_count = len(execution_log)
        
        if step_count > 0 and execution_count == 0:
            evaluation["issues"].append("Steps planned tapi tidak di-execute")
            evaluation["completeness"] = 0.3
            evaluation["needs_retry"] = True
            evaluation["suggested_retry_strategy"] = "execute_steps"
    
    # === LLM-BASED EVALUATION ===
    try:
        client = OpenAI(api_key=OPENAI_API_KEY)
        
        eval_prompt = f"""
TASK: Evaluate response quality untuk user request.

USER REQUEST:
{prompt[:200]}...

DETECTED TYPE:
{detected_type}

RESPONSE:
{json.dumps(response, indent=2, default=str)[:500]}...

EVALUATION CRITERIA:
1. Completeness (0-1): Apakah response menjawab semua aspek request?
2. Relevance (0-1): Apakah response relevant dengan request?
3. Confidence (0-1): Seberapa confident Anda dengan response ini?
4. Issues (list): Masalah apakah yang Anda lihat?
5. Needs Retry (bool): Apakah response ini perlu di-retry?

Format respons HARUS JSON dengan keys: completeness, relevance, confidence, issues, needs_retry, suggested_strategy
"""
        
        response = client.chat.completions.create(
            model="gpt-4o-mini",
            messages=[
                {
                    "role": "system",
                    "content": "Anda adalah evaluator untuk response quality. Berikan evaluation objektif dan terukur."
                },
                {
                    "role": "user",
                    "content": eval_prompt
                }
            ],
            temperature=0.3,
            max_tokens=300
        )
        
        eval_text = response.choices[0].message.content
        
        # Try to parse JSON dari response
        try:
            # Extract JSON dari response (bisa ada text sebelum/sesudahnya)
            json_start = eval_text.find('{')
            json_end = eval_text.rfind('}') + 1
            if json_start >= 0 and json_end > json_start:
                json_str = eval_text[json_start:json_end]
                eval_result = json.loads(json_str)
                
                evaluation["completeness"] = float(eval_result.get("completeness", 0.5))
                evaluation["relevance"] = float(eval_result.get("relevance", 0.5))
                evaluation["confidence"] = float(eval_result.get("confidence", 0.5))
                evaluation["issues"].extend(eval_result.get("issues", []))
                evaluation["needs_retry"] = eval_result.get("needs_retry", False)
                evaluation["suggested_retry_strategy"] = eval_result.get("suggested_strategy")
        
        except json.JSONDecodeError:
            print(f"[EVALUATOR] Warning: Could not parse evaluation JSON")
            evaluation["issues"].append("Evaluation parsing error")
    
    except Exception as e:
        print(f"[EVALUATOR] LLM evaluation error: {e}")
        evaluation["issues"].append(f"LLM evaluation failed: {str(e)}")
    
    # Calculate overall quality score
    evaluation["quality_score"] = (
        evaluation["completeness"] * 0.4 +
        evaluation["relevance"] * 0.3 +
        evaluation["confidence"] * 0.3
    )
    
    # Determine if retry needed
    if evaluation["quality_score"] < 0.5:
        evaluation["needs_retry"] = True
        if not evaluation.get("suggested_retry_strategy"):
            evaluation["suggested_retry_strategy"] = "replan_with_context"
    
    # Build feedback untuk memory (untuk future interactions)
    evaluation["feedback_for_memory"] = {
        "request_type": detected_type,
        "quality_score": evaluation["quality_score"],
        "success": evaluation["quality_score"] >= 0.6,
        "issues": evaluation["issues"][:3],  # Top 3 issues
        "strategy_worked": evaluation["confidence"] > 0.7
    }
    
    # Print summary
    print(f"[EVALUATOR] Quality score: {evaluation['quality_score']:.2f}")
    print(f"[EVALUATOR]   - Completeness: {evaluation['completeness']:.2f}")
    print(f"[EVALUATOR]   - Relevance: {evaluation['relevance']:.2f}")
    print(f"[EVALUATOR]   - Confidence: {evaluation['confidence']:.2f}")
    
    if evaluation["needs_retry"]:
        print(f"[EVALUATOR] ⚠ Needs retry: {evaluation.get('suggested_retry_strategy')}")
    else:
        print(f"[EVALUATOR] ✓ Quality OK - Ready for output")
    
    # Store evaluation di state
    state["evaluation"] = evaluation
    
    # Update next_action based on evaluation
    if evaluation["needs_retry"]:
        state["next_action"] = "node_replan"  # Will trigger replanningnode
    else:
        state["next_action"] = None  # Go to END
    
    return state
