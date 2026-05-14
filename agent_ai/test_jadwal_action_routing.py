#!/usr/bin/env python3
"""
Test untuk memastikan action routing jadwal seminar bekerja dengan benar
"""
import sys
import logging
from nodes.planner_node import planner_node, _score_action_candidates

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

def test_planner_action_routing():
    """Test planner outputs generate_jadwal_seminar for jadwal seminar inputs"""
    
    test_cases = [
        ("buatkan jadwal seminar", "generate_jadwal_seminar"),
        ("buat jadwal seminar", "generate_jadwal_seminar"),
        ("buatkan jadwal", "generate_jadwal_seminar"),
        ("generate jadwal seminar", "generate_jadwal_seminar"),
        ("atur jadwal presentasi", "generate_jadwal_seminar"),
    ]
    
    print("\n" + "="*60)
    print("🧪 Testing Planner Action Routing for Jadwal Seminar")
    print("="*60 + "\n")
    
    passed = 0
    failed = 0
    
    for prompt, expected_action in test_cases:
        state = {
            "messages": [{"role": "user", "content": prompt}],
            "user_id": "test_user"
        }
        
        result = planner_node(state)
        actual_action = result.get("plan", {}).get("action")
        
        status = "✅" if actual_action == expected_action else "❌"
        
        print(f'{status} Input: "{prompt}"')
        print(f'   Expected: {expected_action}')
        print(f'   Actual:   {actual_action}')
        
        if actual_action == expected_action:
            passed += 1
        else:
            failed += 1
        
        print()
    
    # Test scoring
    print("\n" + "-"*60)
    print("📊 Scoring Rules for 'buatkan jadwal seminar':")
    print("-"*60 + "\n")
    
    candidates = _score_action_candidates("buatkan jadwal seminar")
    for action, score in candidates[:5]:
        print(f"  {score:2d} points - {action}")
    
    print("\n" + "="*60)
    print(f"Results: ✅ {passed} passed, ❌ {failed} failed")
    print("="*60 + "\n")
    
    return failed == 0

if __name__ == "__main__":
    success = test_planner_action_routing()
    sys.exit(0 if success else 1)
