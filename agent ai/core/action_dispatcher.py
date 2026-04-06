"""
Dynamic Action Dispatcher - Level 4 Upgrade
Replaces hardcoded if/elif with registry-based tool dispatch.
Enables dynamic action execution dengan automatic error recovery.
"""

from typing import Dict, Any, Callable, Optional, Tuple
from core.registry import registry
import json
import traceback


class ActionDispatcher:
    """Dynamic dispatcher untuk execute actions berdasarkan registry"""
    
    def __init__(self, max_retries: int = 2, fallback_strategy: str = "llm_recovery"):
        self.max_retries = max_retries
        self.fallback_strategy = fallback_strategy  # "llm_recovery" atau "skip"
        self.execution_history = []  # Track all executions untuk learning
    
    def dispatch(self, action_name: str, params: Dict[str, Any], context: Any = None) -> Tuple[Any, Dict]:
        """
        Execute action dinamis berdasarkan registry.
        
        Returns: (result, metadata) dimana metadata berisi status, retries, timing, dll
        """
        metadata = {
            "action": action_name,
            "status": "pending",
            "retries": 0,
            "errors": [],
            "timing_ms": 0,
            "fallback_used": False
        }
        
        # Check if action exists di registry
        if not registry.is_registered(action_name):
            metadata["status"] = "not_found"
            metadata["error"] = f"Action '{action_name}' not registered"
            return None, metadata
        
        # Get action metadata
        action_meta = registry.get_metadata(action_name)
        action_func = registry.get_function(action_name)
        
        if not action_func:
            metadata["status"] = "not_callable"
            metadata["error"] = f"Action '{action_name}' tidak dapat dipanggil"
            return None, metadata
        
        # Retry loop dengan exponential backoff
        import time
        last_error = None
        
        for attempt in range(self.max_retries + 1):
            try:
                start_time = time.time()
                
                # Execute action dengan params
                result = action_func(**params) if isinstance(params, dict) else action_func(params)
                
                timing = (time.time() - start_time) * 1000
                
                metadata["status"] = "success"
                metadata["timing_ms"] = timing
                self.execution_history.append({
                    "action": action_name,
                    "attempt": attempt + 1,
                    "status": "success",
                    "timing_ms": timing
                })
                
                return result, metadata
                
            except Exception as e:
                last_error = str(e)
                metadata["retries"] = attempt + 1
                metadata["errors"].append({
                    "attempt": attempt + 1,
                    "error": str(e),
                    "traceback": traceback.format_exc()
                })
                
                if attempt < self.max_retries:
                    # Exponential backoff sebelum retry
                    wait_time = (2 ** attempt) * 0.1  # 0.1s, 0.2s, 0.4s
                    import time
                    time.sleep(wait_time)
                    
                    print(f"[DISPATCHER] Retry {attempt + 1}/{self.max_retries} untuk {action_name} dalam {wait_time}s...")
                else:
                    # Semua retry gagal
                    metadata["status"] = "failed"
                    
                    # Try fallback strategy
                    if self.fallback_strategy == "llm_recovery":
                        metadata["fallback_used"] = True
                        recovery_result = self._try_llm_recovery(action_name, params, last_error)
                        if recovery_result:
                            metadata["status"] = "recovered_via_llm"
                            return recovery_result, metadata
        
        return None, metadata
    
    def _try_llm_recovery(self, action_name: str, params: Dict, error: str) -> Optional[Any]:
        """Try LLM-based recovery untuk failed action"""
        try:
            from openai import OpenAI
            from app.config import OPENAI_API_KEY
            
            client = OpenAI(api_key=OPENAI_API_KEY)
            
            recovery_prompt = f"""
Action '{action_name}' failed dengan error:
{error}

Params yang dikirim:
{json.dumps(params, default=str, indent=2)}

Buat recovery strategy - apakah perlu:
1. Retry dengan params berbeda?
2. Skip action ini dan lanjut ke yang lain?
3. Return dummy/cached data?

Berikan saran singkat.
"""
            
            response = client.chat.completions.create(
                model="gpt-4o-mini",
                messages=[
                    {"role": "system", "content": "Anda adalah error recovery specialist. Berikan strategi cepat."},
                    {"role": "user", "content": recovery_prompt}
                ],
                temperature=0.3,
                max_tokens=200
            )
            
            recovery_text = response.choices[0].message.content
            print(f"[LLM RECOVERY] Suggestion: {recovery_text}")
            
            # Return recovery suggestion sebagai fallback response
            return {
                "type": "recovery",
                "original_action": action_name,
                "original_error": error,
                "recovery_suggestion": recovery_text
            }
            
        except Exception as e:
            print(f"[LLM RECOVERY] Failed: {e}")
            return None
    
    def dispatch_batch(self, actions_list: list, stop_on_error: bool = False) -> Dict[str, Any]:
        """
        Execute multiple actions secara sequential atau parallel.
        
        actions_list: [{"action": "name", "params": {}}, ...]
        """
        results = {}
        
        for action_item in actions_list:
            action_name = action_item.get("action")
            params = action_item.get("params", {})
            
            result, metadata = self.dispatch(action_name, params)
            results[action_name] = {
                "result": result,
                "metadata": metadata
            }
            
            if stop_on_error and metadata["status"] != "success":
                print(f"[DISPATCHER] Stopping batch execution karena error di {action_name}")
                results[action_name]["batch_stopped"] = True
                break
        
        return results


# Global dispatcher instance
_dispatcher = None

def get_dispatcher() -> ActionDispatcher:
    """Get atau create global dispatcher"""
    global _dispatcher
    if _dispatcher is None:
        _dispatcher = ActionDispatcher()
    return _dispatcher
