#!/usr/bin/env python3
"""
Integration test for jadwal seminar feature - complete flow
"""
import sys
sys.path.insert(0, 'd:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\agent_ai')

from tools.jadwal_seminar import JadwalSeminarTools
from datetime import datetime

print("=" * 70)
print("INTEGRATION TEST: Jadwal Seminar Complete Flow")
print("=" * 70)

# Step 1: Get form
print("\n✅ STEP 1: Get form")
print("-" * 70)
form_result = JadwalSeminarTools.get_form_jadwal()
print(f"Form generated: {form_result.get('asking')}")
print(f"Stage: {form_result.get('stage')}")
print(f"Ruangan count: {len(form_result.get('ruangan_list', []))}")

# Step 2: Parse date
print("\n✅ STEP 2: Parse Indonesian date")
print("-" * 70)
test_date = "15 juni 2026"
parsed_date = JadwalSeminarTools.parse_tanggal_input(test_date)
print(f"Input: '{test_date}'")
print(f"Parsed: {parsed_date}")
if parsed_date:
    print(f"✓ Date parsed successfully: {parsed_date.strftime('%d %B %Y')}")
else:
    print(f"✗ Failed to parse date")
    sys.exit(1)

# Step 3: Get kelompok with correct dosen_context
print("\n✅ STEP 3: Get kelompok with correct dosen_context mapping")
print("-" * 70)
dosen_context = {
    "prodi_id": 4,
    "angkatan": 2,      # This maps to Kelompok.TM_id
    "kategori_pa": 3,   # This maps to Kelompok.KPA_id
}
print(f"Dosen context: {dosen_context}")

kelompok_list = JadwalSeminarTools.get_kelompok_for_jadwal(user_id=123, dosen_context=[dosen_context])
print(f"Found {len(kelompok_list)} kelompok")
if not kelompok_list:
    print("✗ ERROR: No kelompok found!")
    sys.exit(1)
print(f"✓ Found kelompok: {[k.nomor_kelompok for k in kelompok_list[:3]]}...")

# Step 4: Generate jadwal
print("\n✅ STEP 4: Generate jadwal (10 minutes timeout)")
print("-" * 70)
print("Generating jadwal for 18 kelompok (need 5 days for 4 slots/day)...")

try:
    result = JadwalSeminarTools.generate_jadwal_seminar(
        user_id=123,
        tanggal_mulai=parsed_date,
        durasi_menit=110,
        ruangan_id=1,
        kelompok_list=kelompok_list,
        dosen_context=[dosen_context]
    )
    
    print(f"Success: {result.get('success')}")
    print(f"Total created: {result.get('total', 0)}")
    print(f"Message: {result.get('message', '')[:100]}...")
    
    if result.get('success') and result.get('total', 0) > 0:
        print(f"✅ SUCCESS! Created {result.get('total')} jadwal entries")
    else:
        print(f"❌ FAILED! {result.get('message')}")
        sys.exit(1)
        
except Exception as e:
    print(f"❌ Exception: {e}")
    import traceback
    traceback.print_exc()
    sys.exit(1)

print("\n" + "=" * 70)
print("✅ ALL INTEGRATION TESTS PASSED!")
print("=" * 70)
print("\nNow try in browser:")
print("1. Go to http://localhost:8000/ai-agent/kelompok")
print("2. Type: 'buatkan jadwal seminar'")
print("3. Form should appear (if agent correctly routes action)")
print("4. Fill in: tanggal='15 juni 2026', ruangan='1', durasi='110'")
print("5. Click submit and verify jadwal created")
