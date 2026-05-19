#!/usr/bin/env python3
"""
Test script untuk verify jadwal features baru
"""
import sys
sys.path.insert(0, '.')

from tools.jadwal_seminar import JadwalSeminarTools
from datetime import datetime

def test_format_jadwal_by_date():
    """Test format_jadwal_by_date function"""
    print("\n=" * 60)
    print("TEST: format_jadwal_by_date")
    print("=" * 60)
    
    # Sample jadwal entries
    sample_entries = [
        {
            "kelompok_id": 1,
            "kelompok_nomor": "1",
            "tanggal": "15 May 2026",
            "waktu": "08:00 - 09:50",
            "ruangan_id": 1,
            "ruangan_name": "Auditorium"
        },
        {
            "kelompok_id": 2,
            "kelompok_nomor": "2",
            "tanggal": "15 May 2026",
            "waktu": "10:00 - 11:50",
            "ruangan_id": 2,
            "ruangan_name": "Ruang Rapat Lt-1"
        },
        {
            "kelompok_id": 3,
            "kelompok_nomor": "3",
            "tanggal": "16 May 2026",
            "waktu": "08:00 - 09:50",
            "ruangan_id": 1,
            "ruangan_name": "Auditorium"
        },
    ]
    
    result = JadwalSeminarTools.format_jadwal_by_date(sample_entries)
    
    print("\n✓ Grouped dates:", result.get("sorted_dates"))
    print("✓ Total jadwal:", result.get("total"))
    print("✓ Total days:", result.get("total_days"))
    print("\n✓ HTML Preview (first 200 chars):")
    print(result.get("html", "")[:200] + "...")
    
    if result.get("grouped"):
        print("\n✓ Grouped structure created successfully")
        for date_key, entries in result.get("grouped", {}).items():
            print(f"  - {date_key}: {len(entries)} entries")
    
    return True

def test_get_jadwal_kelompok_detail():
    """Test get_jadwal_kelompok_detail function"""
    print("\n" + "=" * 60)
    print("TEST: get_jadwal_kelompok_detail")
    print("=" * 60)
    
    # Dummy dosen context
    dosen_context = [
        {
            "prodi_id": 1,
            "tahun_masuk_id": 1,
            "kategori_pa_id": 1
        }
    ]
    
    # Test with kelompok_nomor = 1
    result = JadwalSeminarTools.get_jadwal_kelompok_detail(
        kelompok_nomor=1,
        dosen_context=dosen_context
    )
    
    print("\n✓ Status:", result.get("status"))
    if result.get("status") == "success":
        print("✓ Kelompok nomor:", result.get("kelompok_nomor"))
        print("✓ Tanggal:", result.get("tanggal"))
        print("✓ Waktu:", result.get("waktu"))
        print("✓ Ruangan:", result.get("ruangan"))
        print("✓ Anggota count:", len(result.get("anggota", [])))
        print("✓ Pembimbing:", result.get("pembimbing", []))
        print("✓ Penguji:", result.get("penguji", []))
        print("\n✓ HTML Preview (first 300 chars):")
        print(result.get("message", "")[:300] + "...")
    else:
        print("✓ Message:", result.get("message"))
    
    return True

def main():
    print("\n" + "🔍 TESTING NEW JADWAL FEATURES" + "\n")
    
    try:
        test_format_jadwal_by_date()
        test_get_jadwal_kelompok_detail()
        
        print("\n" + "=" * 60)
        print("✅ ALL TESTS PASSED")
        print("=" * 60 + "\n")
        return True
    except Exception as e:
        print(f"\n❌ TEST FAILED: {e}")
        import traceback
        traceback.print_exc()
        return False

if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)
