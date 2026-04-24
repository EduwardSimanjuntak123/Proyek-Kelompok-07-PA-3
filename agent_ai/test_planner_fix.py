#!/usr/bin/env python3
"""
Test untuk memverifikasi planner logic perbaikan
"""
import sys
sys.path.insert(0, '/home/user/project')

from nodes.planner_node import planner_node

# Test cases
test_cases = [
    {
        "name": "Buat Dosen Pembimbing",
        "input": "Buat Dosen Pembimbing untuk setiap kelompok",
        "expected_action": "generate_pembimbing"
    },
    {
        "name": "Buat Dosen Penguji",
        "input": "Buat Dosen Penguji untuk setiap kelompok",
        "expected_action": "generate_penguji"
    },
    {
        "name": "Daftar Pembimbing",
        "input": "Daftar dosen pembimbing di konteks saya",
        "expected_action": "query_pembimbing"
    },
    {
        "name": "Daftar Penguji",
        "input": "Daftar penguji yang sudah ada",
        "expected_action": "query_penguji"
    },
    {
        "name": "Query Siapa Pembimbing Kelompok",
        "input": "Siapa pembimbing kelompok 1",
        "expected_action": "query_pembimbing"
    },
    {
        "name": "Query Siapa Penguji Kelompok",
        "input": "Siapa penguji kelompok 5",
        "expected_action": "query_penguji"
    },
]

print("=" * 80)
print("TESTING PLANNER LOGIC FIX")
print("=" * 80)

passed = 0
failed = 0

for test_case in test_cases:
    state = {
        "user_id": "test_user",
        "messages": [{"role": "user", "content": test_case["input"]}],
        "context": {"dosen_context": []}
    }
    
    result = planner_node(state)
    actual_action = result.get("plan", {}).get("action", "NONE")
    expected_action = test_case["expected_action"]
    
    status = "✓ PASS" if actual_action == expected_action else "✗ FAIL"
    
    print(f"\n{status}")
    print(f"  Test: {test_case['name']}")
    print(f"  Input: '{test_case['input']}'")
    print(f"  Expected: {expected_action}")
    print(f"  Actual: {actual_action}")
    
    if actual_action == expected_action:
        passed += 1
    else:
        failed += 1

print("\n" + "=" * 80)
print(f"RESULTS: {passed} passed, {failed} failed out of {len(test_cases)} tests")
print("=" * 80)

sys.exit(0 if failed == 0 else 1)
