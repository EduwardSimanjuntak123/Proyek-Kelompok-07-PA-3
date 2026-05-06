#!/usr/bin/env python3
"""
Debug test for create_group_by_grades error
"""
import json
import logging
import sys

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

from core.database import SessionLocal
from models.dosen import Dosen
from nodes.planner_node import planner_node
from nodes.executor_node import executor_node

def test_create_group_grades():
    session = SessionLocal()
    
    # Get a dosen
    dosen = session.query(Dosen).first()
    if not dosen:
        print("No dosen found")
        session.close()
        return False
    
    user_id = dosen.user_id
    
    # Initialize state
    state = {
        "user_id": user_id,
        "messages": [
            {
                "role": "user",
                "content": "Buatkan kelompok 5 orang per kelompok berdasarkan nilai akademik",
            }
        ],
        "context": {
            "dosen_context": [
                {
                    "user_id": user_id,
                    "prodi_id": 1,
                    "kategori_pa": 1,
                    "angkatan": 1,
                }
            ]
        },
    }
    
    print(f"\n{'='*80}")
    print(f"Testing create_group_by_grades for dosen: {dosen.nama} (user_id={user_id})")
    print(f"{'='*80}\n")
    
    # Run planner
    print("Step 1: Running planner...")
    state = planner_node(state)
    action = state.get("plan", {}).get("action", "unknown")
    print(f"Action detected: {action}\n")
    
    # Ensure action is in state for executor
    state["action"] = action
    
    # Run executor
    print("Step 2: Running executor...")
    try:
        state = executor_node(state)
        result = state.get("result", "")
        print(f"Result: {result[:200]}...")
    except Exception as e:
        print(f"ERROR: {e}")
        import traceback
        traceback.print_exc()
        return False
    
    session.close()
    return True

if __name__ == "__main__":
    try:
        success = test_create_group_grades()
        sys.exit(0 if success else 1)
    except Exception as e:
        print(f"Test failed: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)
