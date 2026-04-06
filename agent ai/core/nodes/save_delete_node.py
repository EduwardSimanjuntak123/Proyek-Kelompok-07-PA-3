"""
Nodes untuk save dan delete operations pada kelompok.

Menangani:
- Save/confirm kelompok yang sudah dibuat
- Delete/reset kelompok
"""

import json
from core.state import AgentState


def node_save_delete(state: AgentState) -> AgentState:
    """
    Node Save Delete - Menangani save dan delete operations.
    
    Menentukan action berdasarkan detected_type:
    - save_groups: Menyimpan kelompok
    - delete_groups: Menghapus/reset kelompok
    
    Args:
        state: Current agent state
        
    Returns:
        state: Updated state dengan save/delete response
    """
    
    print(f"[SAVE DELETE NODE] Memproses save/delete request...")
    
    detected_type = state.get("detected_type")
    
    if detected_type == "save_groups":
        print(f"[SAVE DELETE NODE] Action: SAVE kelompok")
        
        state["response"] = {
            "type": "save_groups",
            "message": "Menyimpan kelompok ke database",
            "action": "save_group",
            "strategy": "save",
            "steps": [
                {
                    "action": "save_group",
                    "params": {}
                }
            ],
            "metadata": {
                "type": "save"
            }
        }
        
        print(f"[SAVE DELETE NODE] ✓ Save operation ready")
        
    elif detected_type == "delete_groups":
        print(f"[SAVE DELETE NODE] Action: DELETE kelompok")
        
        state["response"] = {
            "type": "delete_groups",
            "message": "Menghapus kelompok dari database",
            "action": "delete_group",
            "strategy": "delete",
            "steps": [
                {
                    "action": "delete_group",
                    "params": {}
                }
            ],
            "metadata": {
                "type": "delete"
            }
        }
        
        print(f"[SAVE DELETE NODE] ✓ Delete operation ready")
    
    return state
