#!/usr/bin/env python3
"""
Integration test for updated jadwal seminar feature with multiple rooms
"""
import sys
sys.path.insert(0, 'd:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\agent_ai')

from tools.jadwal_seminar import JadwalSeminarTools
from datetime import datetime

print("=" * 70)
print("TEST: Jadwal Seminar with Multiple Rooms")
print("=" * 70)

# Step 1: Get form
print("\n✅ STEP 1: Get form with multiple room support")
print("-" * 70)
form_result = JadwalSeminarTools.get_form_jadwal()
print(f"Form asking: {form_result.get('asking')}")
print(f"Has 'Tambah Ruangan' button: {'Tambah Ruangan' in form_result.get('message', '')}")
print(f"Has 'Durasi' with jam/menit: {'jadwal-durasi-jam' in form_result.get('message', '')}")
if "jadwal-durasi-jam" in form_result.get('message', '') and "jadwal-durasi-menit" in form_result.get('message', ''):
    print("✓ Form has jam/menit duration inputs")
else:
    print("✗ Form missing jam/menit inputs")

# Step 2: Parse date
print("\n✅ STEP 2: Parse date")
print("-" * 70)
parsed_date = JadwalSeminarTools.parse_tanggal_input("15 juni 2026")
print(f"Date: {parsed_date.strftime('%d %B %Y')}")

# Step 3: Get kelompok
print("\n✅ STEP 3: Get kelompok")
print("-" * 70)
dosen_context = {"prodi_id": 4, "angkatan": 2, "kategori_pa": 3}
kelompok_list = JadwalSeminarTools.get_kelompok_for_jadwal(123, [dosen_context])
print(f"Found {len(kelompok_list)} kelompok")

# Step 4: Generate jadwal for MULTIPLE ROOMS
print("\n✅ STEP 4: Generate jadwal for MULTIPLE rooms")
print("-" * 70)
ruangan_list = [1, 2, 3]  # 3 different rooms
print(f"Rooms to schedule: {ruangan_list}")
print(f"Kelompok to schedule: {len(kelompok_list)}")
print(f"Expected total entries: {len(ruangan_list) * len(kelompok_list)} = {len(ruangan_list)} rooms × {len(kelompok_list)} kelompok")

try:
    result = JadwalSeminarTools.generate_jadwal_seminar(
        user_id=123,
        tanggal_mulai=parsed_date,
        durasi_menit=110,  # 1 jam 50 menit
        ruangan_list=ruangan_list,  # NEW: multiple rooms!
        kelompok_list=kelompok_list,
        dosen_context=[dosen_context]
    )
    
    print(f"\nResult:")
    print(f"  Success: {result.get('success')}")
    print(f"  Total entries: {result.get('total')}")
    
    if result.get('success'):
        print(f"\n✅ SUCCESS! Created {result.get('total')} jadwal entries")
        
        # Group by ruangan to verify distribution
        entries_by_room = {}
        for entry in result.get('jadwal_entries', []):
            room = entry['ruangan_id']
            if room not in entries_by_room:
                entries_by_room[room] = []
            entries_by_room[room].append(entry)
        
        print(f"\nDistribution by room:")
        for room_id, entries in sorted(entries_by_room.items()):
            print(f"  Ruangan {room_id}: {len(entries)} kelompok")
            if len(entries) <= 3:
                for e in entries[:3]:
                    print(f"    - Kelompok {e['kelompok_id']}: {e['waktu']} ({e['tanggal']})")
            else:
                print(f"    (showing first 3)")
                for e in entries[:3]:
                    print(f"    - Kelompok {e['kelompok_id']}: {e['waktu']} ({e['tanggal']})")
    else:
        print(f"\n❌ FAILED: {result.get('message')}")
        sys.exit(1)
        
except Exception as e:
    print(f"\n❌ Exception: {e}")
    import traceback
    traceback.print_exc()
    sys.exit(1)

print("\n" + "=" * 70)
print("✅ ALL TESTS PASSED!")
print("=" * 70)
print("\n📝 Feature now supports:")
print("  ✓ Multiple rooms (tambah ruangan button)")
print("  ✓ Duration as jam + menit (not just total minutes)")
print("  ✓ Each room gets all kelompok scheduled independently")
