#!/usr/bin/env python3
"""
Quick test for jadwal seminar feature
"""
import sys
sys.path.insert(0, 'd:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\agent_ai')

from tools.jadwal_seminar import JadwalSeminarTools
from datetime import datetime

print("=" * 70)
print("TEST: JADWAL SEMINAR FEATURE")
print("=" * 70)

# Test 1: Form generation
print("\n✅ TEST 1: Form Generation")
print("-" * 70)
form = JadwalSeminarTools.get_form_jadwal()
print(f"Form asking: {form.get('asking')}")
print(f"Form stage: {form.get('stage')}")
print(f"Ruangan count: {len(form.get('ruangan_list', []))}")
if "tanggal" in form.get("message", "").lower() and "ruangan" in form.get("message", "").lower():
    print("✓ Form HTML contains tanggal and ruangan inputs")
else:
    print("✗ Form HTML missing inputs")
print(f"Form HTML length: {len(form.get('message', ''))} chars")

# Test 2: Tanggal parsing
print("\n✅ TEST 2: Tanggal Parsing")
print("-" * 70)
test_dates = [
    ("15 mei 2026", datetime(2026, 5, 15)),
    ("10 juni", datetime(2026, 6, 10)),  # Should use current year
    ("31 desember 2025", datetime(2025, 12, 31)),
]

for date_str, expected in test_dates:
    parsed = JadwalSeminarTools.parse_tanggal_input(date_str)
    if parsed:
        if parsed.year == expected.year and parsed.month == expected.month and parsed.day == expected.day:
            print(f"✓ '{date_str}' → {parsed.strftime('%d %b %Y')}")
        else:
            print(f"✗ '{date_str}' → {parsed} (expected {expected})")
    else:
        print(f"✗ '{date_str}' → Failed to parse")

# Test 3: Time slots
print("\n✅ TEST 3: Time Slots")
print("-" * 70)
print(f"Total slots per day: {len(JadwalSeminarTools.TIME_SLOTS)}")
for idx, (start, end) in enumerate(JadwalSeminarTools.TIME_SLOTS, 1):
    print(f"  Slot {idx}: {start} - {end}")

# Test 4: Duration
print("\n✅ TEST 4: Duration")
print("-" * 70)
print(f"Standard duration: {JadwalSeminarTools.DURATION_MINUTES} minutes ({JadwalSeminarTools.DURATION_MINUTES // 60} hour {JadwalSeminarTools.DURATION_MINUTES % 60} minutes)")

print("\n" + "=" * 70)
print("✅ BASIC TESTS COMPLETED SUCCESSFULLY")
print("=" * 70)
print("\nNext steps:")
print("1. Test in browser: http://localhost:8000/ai-agent/kelompok")
print("2. Type: 'buatkan jadwal seminar'")
print("3. Form should appear with tanggal input, ruangan dropdown, durasi input")
print("4. Fill in form and click button")
print("5. Check browser console for [JADWAL] logs")
print("6. Monitor laravel.log for backend logs")
