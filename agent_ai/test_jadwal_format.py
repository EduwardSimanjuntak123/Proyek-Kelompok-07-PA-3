#!/usr/bin/env python3
"""
Test to show the new jadwal format without emojis and with prodi+KPA column
"""
import sys
sys.path.insert(0, '.')

from datetime import datetime
from tools.jadwal_seminar import JadwalSeminarTools
from core.database import SessionLocal

def test_jadwal_output():
    """Test jadwal output format"""
    print("\n" + "=" * 80)
    print("TEST: Jadwal Output Format (No Emojis, With Prodi+KPA Column)")
    print("=" * 80 + "\n")
    
    session = SessionLocal()
    try:
        # Test data
        tanggal_str = "15 mei 2026"
        tanggal = JadwalSeminarTools.parse_tanggal_input(tanggal_str)
        
        dosen_context = {
            "prodi_id": 4,
            "angkatan": 2,
            "kategori_pa": 3
        }
        
        # Get kelompok
        from models.kelompok import Kelompok
        kelompok_list = session.query(Kelompok).limit(3).all()
        
        if kelompok_list:
            # Generate jadwal
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
                html = result.get("message", "")
                
                print("✅ HTML OUTPUT (First 2000 chars):\n")
                print(html[:2000])
                print("\n...")
                
                print("\n✅ JADWAL ENTRIES:")
                for entry in result.get("jadwal_entries", []):
                    print(f"\n  - Kelompok {entry.get('kelompok_nomor')}")
                    print(f"    Prodi & KPA: {entry.get('prodi_kpa')}")
                    print(f"    Ruangan: {entry.get('ruangan_name')}")
                    print(f"    Tanggal: {entry.get('tanggal')}")
                    print(f"    Waktu: {entry.get('waktu')}")
                
                print("\n✅ CHECKS:")
                print(f"  - Has emoji check mark: {'✅' in html}")
                print(f"  - Has emoji info: {'ℹ️' in html}")
                print(f"  - Has prodi_kpa data: {'prodi_kpa' in str(result.get('jadwal_entries', []))}")
                print(f"  - Has meta data: {bool(result.get('meta'))}")
                print(f"  - Meta keys: {list(result.get('meta', {}).keys())}")
                
            else:
                print(f"❌ Error: {result.get('message')}")
        else:
            print("⚠️  No kelompok found")
    
    except Exception as e:
        print(f"❌ ERROR: {e}")
        import traceback
        traceback.print_exc()
    finally:
        session.close()

if __name__ == "__main__":
    test_jadwal_output()
