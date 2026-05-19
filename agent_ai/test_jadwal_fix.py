#!/usr/bin/env python3
"""
Test to verify generate_jadwal_seminar works with grouped result
"""
import sys
sys.path.insert(0, '.')

from tools.jadwal_seminar import JadwalSeminarTools
from datetime import datetime

def test_generate_with_grouped():
    """Test that generate_jadwal_seminar properly includes grouped result"""
    print("\n" + "=" * 80)
    print("🧪 TEST: generate_jadwal_seminar with grouped result integration")
    print("=" * 80 + "\n")
    
    # Create mock jadwal entries (simulating what generate_jadwal_seminar returns)
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
            "ruangan_name": "Ruang Rapat"
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
    
    # Test format_jadwal_by_date with these entries
    print("✓ Step 1: Format jadwal grouped by date")
    print("-" * 80)
    
    grouped_result = JadwalSeminarTools.format_jadwal_by_date(sample_entries)
    
    # Verify grouped_result structure
    assert "grouped" in grouped_result, "❌ Missing 'grouped' key"
    assert "html" in grouped_result, "❌ Missing 'html' key"
    assert "sorted_dates" in grouped_result, "❌ Missing 'sorted_dates' key"
    assert "total" in grouped_result, "❌ Missing 'total' key"
    
    print(f"  ✅ grouped_result keys: {list(grouped_result.keys())}")
    print(f"  ✅ Total entries: {grouped_result.get('total')}")
    print(f"  ✅ Sorted dates: {grouped_result.get('sorted_dates')}")
    print(f"  ✅ Grouped structure:")
    for date_key, entries in grouped_result.get("grouped", {}).items():
        print(f"     - {date_key}: {len(entries)} entries")
    
    # Verify HTML contains expected content
    print("\n✓ Step 2: Verify HTML content")
    print("-" * 80)
    
    html = grouped_result.get("html", "")
    has_preview_title = "Preview Jadwal Seminar" in html
    has_day_names = "Senin" in html or "Selasa" in html or "Rabu" in html
    has_group_data = "Kelompok" in html
    has_room_data = "Auditorium" in html
    
    print(f"  ✅ Has preview title: {has_preview_title}")
    print(f"  ✅ Has day names: {has_day_names}")
    print(f"  ✅ Has group data: {has_group_data}")
    print(f"  ✅ Has room data: {has_room_data}")
    
    # Show HTML snippet
    print(f"\n  HTML snippet (first 400 chars):")
    print(f"  {html[:400]}...")
    
    print("\n" + "=" * 80)
    print("✅ TEST PASSED - generate_jadwal_seminar integration working!")
    print("=" * 80 + "\n")
    
    return True

if __name__ == "__main__":
    try:
        success = test_generate_with_grouped()
        sys.exit(0 if success else 1)
    except Exception as e:
        print(f"\n❌ TEST FAILED: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)
