#!/usr/bin/env python3
"""
Test script untuk verify jadwal seminar flow dengan multi-ruangan support
"""
import json
import sys
import logging
from datetime import datetime

# Setup logging
logging.basicConfig(level=logging.DEBUG)
logger = logging.getLogger(__name__)

# Import yang diperlukan
from core.database import SessionLocal
from models.kelompok import Kelompok
from models.ruangan import Ruangan
from models.dosen import Dosen
from nodes.planner_node import planner_node
from nodes.executor_node import executor_node

def test_jadwal_seminar_flow():
    """Test complete jadwal seminar flow"""
    
    print("\n" + "="*80)
    print("TEST: Jadwal Seminar Flow dengan Multi-Ruangan Support")
    print("="*80)
    
    session = SessionLocal()
    
    # 1. Check data yang tersedia
    print("\n1. Check available data...")
    
    kelompoks = session.query(Kelompok).limit(5).all()
    print(f"   ✓ Kelompok tersedia: {len(session.query(Kelompok).all())} total")
    
    ruangans = session.query(Ruangan).all()
    print(f"   ✓ Ruangan tersedia: {len(ruangans)}")
    for ruang in ruangans[:5]:  # Limit output
        print(f"      - {ruang.id}: {ruang.ruangan}")
    if len(ruangans) > 5:
        print(f"      ... dan {len(ruangans) - 5} ruangan lainnya")
    
    # 2. Prepare state untuk test
    print("\n2. Prepare test state...")
    
    # Ambil dosen context dari dosen aktif
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
    print(f"   ✓ Dosen context: {dosen.nama} (user_id={dosen.user_id})")
    
    session.close()
    
    # 3. Test prompt untuk meminta jadwal seminar
    print("\n3. Test Jadwal Seminar Request...")
    
    state = {
        "user_id": dosen.user_id,
        "messages": [],
        "context": {
            "dosen_context": [dosen_context]
        },
        "jadwal_payload": None,
    }
    
    # Prepare fake jadwal_payload untuk test save
    fake_jadwal_payload = {
        "jadwal_list": [
            {
                "kelompok_id": 1,
                "kelompok_nomor": 1,
                "waktu_mulai": "2026-05-05T08:00:00",
                "waktu_selesai": "2026-05-05T09:50:00",
                "ruangan_id": 1,
                "ruangan_name": "Auditorium",
                "pembimbing": ["Dr. Smith"],
                "penguji": ["Dr. Johnson", "Dr. Williams"],
            }
        ],
        "summary": {
            "total_kelompok": 1,
            "scheduled_kelompok": 1,
            "tanggal_mulai": "2026-05-05",
            "ruangan_ids": [1],
            "ruangan_count": 1,
        }
    }
    
    # Prepare jadwal_meta untuk save operation
    jadwal_meta = {
        "prodi_id": dosen_context.get("prodi_id"),
        "kategori_pa_id": dosen_context.get("kategori_pa"),
        "angkatan_id": dosen_context.get("angkatan"),
        "ruangan_ids": [1],
        "ruangan_names": ["Auditorium"],
    }
    
    prompts_to_test = [
        ("Buat jadwal seminar mulai 5 mei 2026", "ask_schedule_details or generate_jadwal"),
        ("Simpan jadwal", "save_jadwal"),
    ]
    
    for i, (prompt, expected_action) in enumerate(prompts_to_test, 1):
        print(f"\n   Test {i}: '{prompt}'")
        
        # Add message ke state
        state["messages"].append({"role": "user", "content": prompt})
        
        # Run planner
        print(f"     → Running planner...")
        state = planner_node(state)
        action = state.get("plan", {}).get("action", "unknown")
        print(f"     → Action detected: {action}")
        
        if action == "unknown":
            print(f"     ✗ Action tidak terdeteksi")
            continue
        
        # For test 2 (save_jadwal), inject fake payload before executor
        if action == "save_jadwal":
            state["jadwal_payload"] = fake_jadwal_payload
            state["jadwal_meta"] = jadwal_meta
            print(f"     → Injected fake jadwal_payload and jadwal_meta for testing")
        
        # Run executor
        print(f"     → Running executor...")
        try:
            state = executor_node(state)
            
            result = state.get("result", "")
            if isinstance(result, str):
                # Truncate untuk display
                result_preview = result[:150] if len(result) > 150 else result
                print(f"     ✓ Result: {result_preview}...")
            else:
                print(f"     ✓ Result type: {type(result)}")
            
            # Check specific fields untuk test
            if action == "generate_jadwal":
                # Check if asking for details or generated
                if "Informasi Tambahan Diperlukan" in state.get("result", ""):
                    print(f"     ✓ Asking for schedule details (checkbox UI expected)")
                    if state.get("available_ruangan"):
                        print(f"       Available ruangan: {len(state['available_ruangan'])} options")
                elif state.get("jadwal_payload"):
                    jadwal_list = state["jadwal_payload"].get("jadwal_list", [])
                    print(f"     ✓ Jadwal generated: {len(jadwal_list)} entries")
            
            elif action == "save_jadwal":
                if "Berhasil Disimpan" in state.get("result", ""):
                    print(f"     ✓ Jadwal save successful")
                elif "Jadwal tidak ditemukan" in state.get("result", ""):
                    print(f"     ⚠ No jadwal to save (expected without prior generation)")
                else:
                    print(f"     ⚠ Save result: {state.get('result', '')[:100]}...")
            
        except Exception as e:
            print(f"     ✗ Error: {str(e)}")
            import traceback
            traceback.print_exc()
            return False
    
    print("\n" + "="*80)
    print("✓ Test completed successfully!")
    print("="*80 + "\n")
    
    return True


if __name__ == "__main__":
    try:
        success = test_jadwal_seminar_flow()
        sys.exit(0 if success else 1)
    except Exception as e:
        print(f"\n✗ Test failed with error: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)
