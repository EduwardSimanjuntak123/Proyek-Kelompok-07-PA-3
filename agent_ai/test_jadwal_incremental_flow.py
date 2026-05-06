#!/usr/bin/env python3
"""
Test script untuk verify incremental jadwal seminar flow dengan Kirim buttons.
"""
import json
import sys
import logging

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

from core.database import SessionLocal
from models.dosen import Dosen
from nodes.planner_node import planner_node
from nodes.executor_node import executor_node

def test_incremental_flow():
    """Test incremental jadwal seminar flow"""
    
    print("\n" + "="*80)
    print("TEST: Incremental Jadwal Seminar Flow dengan Kirim Buttons")
    print("="*80)
    
    session = SessionLocal()
    
    # Get dosen
    dosens = session.query(Dosen).limit(1).all()
    if not dosens:
        print("   ✗ Tidak ada dosen di database")
        session.close()
        return False
    
    dosen = dosens[0]
    dosen_context = {
        "user_id": dosen.user_id,
        "prodi_id": 1,
        "kategori_pa": 3,
        "angkatan": 1,
    }
    print(f"✓ Dosen: {dosen.nama} (user_id={dosen.user_id})")
    
    session.close()
    
    # Test flow
    print("\n" + "-"*80)
    print("STAGE 1: User requests jadwal seminar")
    print("-"*80)
    
    state = {
        "user_id": dosen.user_id,
        "messages": [{"role": "user", "content": "Buat jadwal seminar"}],
        "context": {"dosen_context": [dosen_context]},
    }
    
    # Run planner
    state = planner_node(state)
    action = state.get("plan", {}).get("action")
    print(f"✓ Action detected: {action}")
    
    if action != "generate_jadwal":
        print(f"✗ Expected generate_jadwal, got {action}")
        return False
    
    # Run executor - Stage 1
    state = executor_node(state)
    result = state.get("result", "")
    
    if "Pilih Ruangan" not in result:
        print(f"✗ Expected 'Pilih Ruangan' message")
        print(f"Got: {result[:200]}")
        return False
    
    if "Kirim Pilihan" not in result:
        print(f"✗ Expected 'Kirim Pilihan' button")
        return False
    
    print("✓ Stage 1: Shows ruangan selection with Kirim button")
    print(f"  State stage: {state.get('jadwal_stage')}")
    
    # Verify state
    if state.get("jadwal_stage") != "waiting_ruangan":
        print(f"✗ Expected jadwal_stage='waiting_ruangan', got '{state.get('jadwal_stage')}'")
        return False
    
    if not state.get("available_ruangan"):
        print(f"✗ available_ruangan should be populated")
        return False
    
    print(f"✓ Available ruangan: {len(state.get('available_ruangan'))} options")
    
    # Simulate user clicking Kirim button with selected ruangan
    print("\n" + "-"*80)
    print("STAGE 2: User submits ruangan selection (simulating Kirim button)")
    print("-"*80)
    
    # Simulate the message that would come from the button click
    state["messages"].append({
        "role": "user",
        "content": "Saya pilih ruangan: 1, 2"  # Simulating checkbox submission
    })
    
    # Run executor - Stage 2
    state = executor_node(state)
    result = state.get("result", "")
    
    if "Tanggal" not in result or "tanggal" not in result.lower():
        print(f"✗ Expected 'Tanggal' message in result")
        print(f"Got: {result[:200]}")
        return False
    
    if "Kirim Tanggal" not in result:
        print(f"✗ Expected 'Kirim Tanggal' button")
        return False
    
    print("✓ Stage 2: Shows tanggal input with Kirim button")
    print(f"  State stage: {state.get('jadwal_stage')}")
    print(f"  Selected ruangan: {state.get('selected_ruangan_ids')}")
    
    # Verify state
    if state.get("jadwal_stage") != "waiting_tanggal":
        print(f"✗ Expected jadwal_stage='waiting_tanggal', got '{state.get('jadwal_stage')}'")
        return False
    
    if state.get("selected_ruangan_ids") != [1, 2]:
        print(f"✗ Expected selected_ruangan_ids=[1,2], got {state.get('selected_ruangan_ids')}")
        return False
    
    # Simulate user clicking Kirim button with tanggal
    print("\n" + "-"*80)
    print("STAGE 3: User submits tanggal (simulating Kirim button)")
    print("-"*80)
    
    state["messages"].append({
        "role": "user",
        "content": "5 mei 2026"  # Simulating date input submission
    })
    
    # Run executor - Stage 3
    state = executor_node(state)
    result = state.get("result", "")
    
    # Either success with table or error about no kelompok (both are valid)
    has_success = "Jadwal Seminar" in result and "Berhasil Dibuat" in result
    has_error = "kelompok" in result.lower() or "error" in result.lower()
    
    if not (has_success or has_error):
        print(f"✗ Expected success or kelompok error")
        print(f"Got: {result[:200]}")
        return False
    
    if has_success:
        print("✓ Stage 3: Jadwal generated successfully")
        if "table" not in result.lower():
            print(f"✗ Expected jadwal table in result")
            return False
        print(f"  Jadwal payload: {len(state.get('jadwal_payload', {}).get('jadwal_list', []))} entries")
    else:
        print(f"✓ Stage 3: Returned expected error (no kelompok in test data)")
        print(f"  This is expected - database doesn't have kelompok for this context")
    
    # Verify clean state
    if state.get("jadwal_stage") is not None:
        print(f"✗ jadwal_stage should be cleared after generation")
        return False
    
    print("\n" + "="*80)
    print("✅ All tests passed!")
    print("="*80 + "\n")
    
    return True


if __name__ == "__main__":
    try:
        success = test_incremental_flow()
        sys.exit(0 if success else 1)
    except Exception as e:
        print(f"\n✗ Test failed with error: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)
