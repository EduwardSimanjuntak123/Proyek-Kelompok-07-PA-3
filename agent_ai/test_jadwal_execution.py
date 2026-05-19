#!/usr/bin/env python3
"""
Test to reproduce the jadwal seminar error
"""
import sys
sys.path.insert(0, '.')

from datetime import datetime
from tools.jadwal_seminar import JadwalSeminarTools
from models.kelompok import Kelompok
from core.database import SessionLocal

def test_jadwal_steps():
    """Test jadwal seminar step by step"""
    print("\n" + "=" * 80)
    print("🧪 TEST: Testing jadwal seminar generation step by step")
    print("=" * 80 + "\n")
    
    session = SessionLocal()
    try:
        # Step 1: Test parse_tanggal_input
        print("✓ Step 1: Testing parse_tanggal_input")
        tanggal_str = "15 mei 2026"
        tanggal = JadwalSeminarTools.parse_tanggal_input(tanggal_str)
        print(f"  ✅ Parsed '{tanggal_str}' → {tanggal}\n")
        
        # Step 2: Get kelompok for jadwal
        print("✓ Step 2: Testing get_kelompok_for_jadwal")
        dosen_context = {
            "prodi_id": 4,
            "angkatan": 2,
            "kategori_pa": 3
        }
        kelompok_list = JadwalSeminarTools.get_kelompok_for_jadwal(1, [dosen_context])
        print(f"  ✅ Found {len(kelompok_list)} kelompok\n")
        
        if not kelompok_list:
            print("  ⚠️  No kelompok found, creating test data...")
            # Try with different filters
            from models.kelompok import Kelompok
            all_kelompok = session.query(Kelompok).limit(3).all()
            if all_kelompok:
                print(f"  ℹ️  Found {len(all_kelompok)} kelompok in database")
                kelompok_list = all_kelompok
        
        # Step 3: Test generate_jadwal_seminar
        if kelompok_list:
            print("✓ Step 3: Testing generate_jadwal_seminar")
            result = JadwalSeminarTools.generate_jadwal_seminar(
                user_id=1,
                tanggal_mulai=tanggal,
                durasi_menit=110,
                ruangan_list=[1, 2],
                kelompok_list=kelompok_list,
                dosen_context=[dosen_context],
                persist=False
            )
            
            if result.get("success"):
                print(f"  ✅ Generated {result.get('total')} jadwal entries")
                print(f"  ✅ Message length: {len(result.get('message', ''))}")
                print(f"  ✅ Has grouped data: {'grouped' in result}")
                print(f"  ✅ Result keys: {list(result.keys())}\n")
                print("✅ ALL TESTS PASSED!")
            else:
                print(f"  ❌ Error: {result.get('message')}\n")
        else:
            print("  ⚠️  No kelompok to test generate_jadwal_seminar\n")
        
    except Exception as e:
        print(f"❌ ERROR: {type(e).__name__}: {e}")
        import traceback
        traceback.print_exc()
    finally:
        session.close()

if __name__ == "__main__":
    test_jadwal_steps()
