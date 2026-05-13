#!/usr/bin/env python3
"""
Test to verify the dosen_context field mapping is correct
"""
import sys
sys.path.insert(0, 'd:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\agent_ai')

from tools.jadwal_seminar import JadwalSeminarTools
from core.database import SessionLocal
from models.kelompok import Kelompok

print("=" * 70)
print("TEST: Dosen Context Field Mapping")
print("=" * 70)

# First, check what kelompok exist in the database
print("\n✅ STEP 1: Check existing kelompok in database")
print("-" * 70)
session = SessionLocal()
all_kelompok = session.query(Kelompok).all()
print(f"Total kelompok in database: {len(all_kelompok)}")
if all_kelompok:
    for i, k in enumerate(all_kelompok[:5], 1):
        print(f"  {i}. Kelompok {k.nomor_kelompok}: prodi_id={k.prodi_id}, KPA_id={k.KPA_id}, TM_id={k.TM_id}")
    if len(all_kelompok) > 5:
        print(f"  ... and {len(all_kelompok) - 5} more")
else:
    print("  ⚠️  No kelompok found in database!")
session.close()

# Now test with dosen_context that uses the correct field names
print("\n✅ STEP 2: Test with correct dosen_context field names")
print("-" * 70)

# Pick first kelompok as reference
if all_kelompok:
    ref_kelompok = all_kelompok[0]
    
    # Create dosen_context with correct field names (angkatan, kategori_pa)
    dosen_context = {
        "prodi_id": ref_kelompok.prodi_id,
        "angkatan": ref_kelompok.TM_id,  # Maps to TM_id
        "kategori_pa": ref_kelompok.KPA_id,  # Maps to KPA_id
    }
    
    print(f"Test dosen_context: {dosen_context}")
    
    # Call the function
    result = JadwalSeminarTools.get_kelompok_for_jadwal(user_id=1, dosen_context=[dosen_context])
    
    print(f"\nResult: Found {len(result)} kelompok")
    if result:
        print(f"✅ SUCCESS! Found matching kelompok:")
        for i, k in enumerate(result[:3], 1):
            print(f"  {i}. Kelompok {k.nomor_kelompok} (id={k.id})")
        if len(result) > 3:
            print(f"  ... and {len(result) - 3} more")
    else:
        print(f"❌ FAILED! No kelompok found even though criteria should match reference kelompok")
        print(f"   Reference: prodi_id={ref_kelompok.prodi_id}, TM_id={ref_kelompok.TM_id}, KPA_id={ref_kelompok.KPA_id}")
        print(f"   This might indicate a database issue or missing data")
else:
    print("Skipped - no kelompok in database")

print("\n" + "=" * 70)
print("TEST COMPLETE")
print("=" * 70)
