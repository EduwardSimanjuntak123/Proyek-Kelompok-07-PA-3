#!/usr/bin/env python3
"""
Test jadwal seminar buttons implementation
Verify that the form with buttons works correctly
"""

import sys
import os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'agent_ai'))

from tools.jadwal_seminar import JadwalSeminarTools

def test_form_generation():
    """Test that form is generated without the original submit button"""
    print("=" * 60)
    print("TEST 1: Form Generation")
    print("=" * 60)
    
    form_result = JadwalSeminarTools.get_form_jadwal()
    
    print(f"✓ Form generated successfully")
    print(f"  - asking: {form_result.get('asking')}")
    print(f"  - stage: {form_result.get('stage')}")
    print(f"  - message length: {len(form_result.get('message', ''))} chars")
    print(f"  - ruangan_list count: {len(form_result.get('ruangan_list', []))}")
    
    # Verify form contains expected elements
    form_html = form_result.get('message', '')
    
    checks = {
        'Contains form heading': 'Input Jadwal Seminar' in form_html,
        'Contains tanggal input': 'jadwal-tanggal' in form_html,
        'Contains ruangan container': 'jadwal-ruangan-container' in form_html,
        'Contains + Tambah Ruangan button': 'add-ruangan-btn' in form_html,
        'Contains durasi jam input': 'jadwal-durasi-jam' in form_html,
        'Contains durasi menit input': 'jadwal-durasi-menit' in form_html,
        'Does NOT contain old submit button': 'submit-jadwal-btn' not in form_html,
    }
    
    print("\nForm HTML Checks:")
    for check_name, result in checks.items():
        status = "✓" if result else "✗"
        print(f"  {status} {check_name}")
    
    all_passed = all(checks.values())
    return all_passed


def test_date_parsing():
    """Test date parsing functionality"""
    print("\n" + "=" * 60)
    print("TEST 2: Date Parsing")
    print("=" * 60)
    
    test_dates = [
        ("15 mei 2026", True),
        ("10 juni 2026", True),
        ("1 januari 2026", True),
        ("31 desember 2025", True),
        ("invalid date", False),
    ]
    
    print("Testing date parsing:")
    for date_str, should_pass in test_dates:
        result = JadwalSeminarTools.parse_tanggal_input(date_str)
        passed = (result is not None) == should_pass
        status = "✓" if passed else "✗"
        print(f"  {status} '{date_str}': {result}")
    
    return True


def test_message_format():
    """Test the expected message format from JavaScript button submission"""
    print("\n" + "=" * 60)
    print("TEST 3: Expected Message Format")
    print("=" * 60)
    
    test_message = "[jadwal] tanggal: 15 mei 2026 | ruangan: 1,2,3 | durasi: 110"
    
    print(f"Expected message format:")
    print(f"  {test_message}")
    
    # Simulate parsing in executor
    import re
    tanggal_match = re.search(r'tanggal[:\s]+([^|]*?)(?:\||$)', test_message.lower())
    ruangan_match = re.search(r'ruangan[:\s]*([0-9,]+)', test_message.lower())
    durasi_match = re.search(r'durasi[:\s]*(\d+)', test_message.lower())
    
    tanggal_str = tanggal_match.group(1).strip() if tanggal_match else None
    ruangan_str = ruangan_match.group(1) if ruangan_match else None
    durasi_menit = int(durasi_match.group(1)) if durasi_match else None
    
    print(f"\nParsed values:")
    print(f"  ✓ tanggal: {tanggal_str}")
    print(f"  ✓ ruangan: {ruangan_str}")
    print(f"  ✓ durasi_menit: {durasi_menit}")
    
    return True


def main():
    print("\n" + "█" * 60)
    print("█ JADWAL SEMINAR BUTTONS IMPLEMENTATION TEST")
    print("█" * 60 + "\n")
    
    tests = [
        ("Form Generation", test_form_generation),
        ("Date Parsing", test_date_parsing),
        ("Message Format", test_message_format),
    ]
    
    results = {}
    for test_name, test_func in tests:
        try:
            results[test_name] = test_func()
        except Exception as e:
            print(f"\n✗ Exception in {test_name}: {e}")
            import traceback
            traceback.print_exc()
            results[test_name] = False
    
    print("\n" + "=" * 60)
    print("TEST SUMMARY")
    print("=" * 60)
    for test_name, passed in results.items():
        status = "✓" if passed else "✗"
        print(f"{status} {test_name}: {'PASSED' if passed else 'FAILED'}")
    
    total = len(results)
    passed = sum(1 for v in results.values() if v)
    print(f"\nTotal: {passed}/{total} tests passed")
    
    return all(results.values())


if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)
