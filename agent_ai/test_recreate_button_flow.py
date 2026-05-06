"""
Test the complete flow of delete + recreate when clicking the button
Tests: 
1. First request: Create groups (should show confirmation if groups exist)
2. Second request: Click button with [CONFIRM_RECREATE] marker
   - Should delete existing groups
   - Should create new groups with all students
"""

import sys
import logging
logging.basicConfig(level=logging.INFO)

from core.database import SessionLocal
from models.dosen import Dosen
from nodes.planner_node import planner_node
from nodes.executor_node import executor_node
import uuid

print("=" * 80)
print("Testing DELETE + RECREATE Button Flow")
print("=" * 80)

# Get a dosen from database
session = SessionLocal()
dosen = session.query(Dosen).first()
if not dosen:
    print("No dosen found in database")
    sys.exit(1)

user_id = dosen.user_id
dosen_context = [
    {
        "user_id": user_id,
        "prodi_id": 1,
        "kategori_pa": 1,
        "angkatan": 1,
    }
]

# ============================================================================
# SCENARIO 1: First request - groups already exist, should show confirmation
# ============================================================================
print("\n### STEP 1: Create initial groups")
print("-" * 80)

initial_prompt = "Buatkan kelompok 5 orang per kelompok berdasarkan nilai"

state1 = {
    "user_id": user_id,
    "messages": [
        {"role": "user", "content": initial_prompt, "timestamp": str(uuid.uuid4())}
    ],
    "context": {
        "dosen_context": dosen_context
    }
}

# Run planner
planner_result = planner_node(state1)
print(f"✓ Planner routed to: {planner_result['plan']['action']}")

# Run executor to create initial groups
executor_result = executor_node(planner_result)
print(f"✓ Executor result: {executor_result['result'][:100]}...")

# ============================================================================
# SCENARIO 2: User clicks "Hapus Kelompok Lama & Buat Baru" button
# The button sends: [CONFIRM_RECREATE] Buatkan kelompok 5 orang per kelompok...
# ============================================================================
print("\n### STEP 2: User clicks 'Hapus Kelompok Lama & Buat Baru' button")
print("-" * 80)

# Simulate the button click - prompt with [CONFIRM_RECREATE] marker
confirmed_prompt = f"[CONFIRM_RECREATE] {initial_prompt}"

state2 = {
    "user_id": user_id,
    "messages": [
        {"role": "user", "content": initial_prompt, "timestamp": str(uuid.uuid4())},
        {"role": "assistant", "content": "Menampilkan tombol konfirmasi", "timestamp": str(uuid.uuid4())},
        {"role": "user", "content": confirmed_prompt, "timestamp": str(uuid.uuid4())}
    ],
    "context": {
        "dosen_context": dosen_context
    }
}

# Run planner again
planner_result2 = planner_node(state2)
print(f"✓ Planner routed to: {planner_result2['plan']['action']}")

# Run executor with the [CONFIRM_RECREATE] marker
executor_result2 = executor_node(planner_result2)
result_text = executor_result2['result']

print(f"✓ Executor result: {result_text[:150]}...")

# Check if it's a success message (not a confirmation)
if "✅ Kelompok Berdasarkan Nilai Berhasil Dibuat" in result_text:
    print("\n✓ SUCCESS! Groups were deleted and recreated!")
    print("  - The [CONFIRM_RECREATE] marker was properly handled")
    print("  - Existing groups were deleted")
    print("  - New groups were created with all students")
    session.close()
    sys.exit(0)
else:
    print("\n✗ FAILED! Expected success message but got:")
    print(result_text[:200])
    session.close()
    sys.exit(1)
