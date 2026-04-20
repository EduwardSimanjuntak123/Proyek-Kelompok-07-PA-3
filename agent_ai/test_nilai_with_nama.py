"""
Test file untuk demonstrasi penggunaan nilai mahasiswa tools dengan parameter NAMA
"""

from tools.nilai_mahasiswa_tools import (
    get_nilai_permatkul_by_mahasiswa,
    get_nilai_persemester_by_mahasiswa,
    get_combined_analisis_nilai
)


print("=" * 80)
print("🔍 TEST: Get Nilai Permatkul By Mahasiswa - Using NAMA Parameter")
print("=" * 80)

# Test 1: Get by nama (partial match)
result = get_nilai_permatkul_by_mahasiswa(nama="John")
print(f"\n📌 Query: get_nilai_permatkul_by_mahasiswa(nama='John')")
print(f"Status: {result['status']}")
if result['status'] == 'success':
    print(f"Mahasiswa: {result['mahasiswa']['nama']} ({result['mahasiswa']['nim']})")
    print(f"Total Matakuliah: {result['total_matakuliah']}")
    print(f"Rata-rata Nilai: {result['rata_rata_nilai']}")
elif result['status'] == 'empty':
    print(f"Message: {result['message']}")

print("\n" + "=" * 80)
print("🔍 TEST: Get Nilai Persemester By Mahasiswa - Using NAMA Parameter")
print("=" * 80)

# Test 2: Get semester by nama
result = get_nilai_persemester_by_mahasiswa(nama="John")
print(f"\n📌 Query: get_nilai_persemester_by_mahasiswa(nama='John')")
print(f"Status: {result['status']}")
if result['status'] == 'success':
    print(f"Mahasiswa: {result['mahasiswa']['nama']} ({result['mahasiswa']['nim']})")
    print(f"Total Semesters: {result['total_semester']}")
    print(f"Cumulative GPA: {result['cumulative_gpa']}")
elif result['status'] == 'empty':
    print(f"Message: {result['message']}")

print("\n" + "=" * 80)
print("🔍 TEST: Get Combined Analysis - Using NAMA Parameter")
print("=" * 80)

# Test 3: Combined analysis with nama
result = get_combined_analisis_nilai(nama="John")
print(f"\n📌 Query: get_combined_analisis_nilai(nama='John')")
print(f"Status: {result['status']}")
if result['status'] == 'success':
    print(f"Mahasiswa: {result['mahasiswa']['nama']} ({result['mahasiswa']['nim']})")
    print(f"Total Matakuliah: {result['total_matakuliah']}")
    print(f"Rata-rata Nilai: {result['rata_rata_nilai']}")
    print(f"Total Semester: {result['total_semester']}")
    print(f"Cumulative GPA: {result['cumulative_gpa']}")
elif result['status'] == 'empty':
    print(f"Message: {result['message']}")

print("\n" + "=" * 80)
print("🔍 TEST: Multiple Query Options (All Parameters)")
print("=" * 80)

# Test 4: Query dengan multiple parameters
print("\n📌 Option 1: Query by mahasiswa_id")
result = get_nilai_permatkul_by_mahasiswa(mahasiswa_id=1)
print(f"Status: {result['status']}")

print("\n📌 Option 2: Query by NIM")
result = get_nilai_permatkul_by_mahasiswa(nim="2401010001")
print(f"Status: {result['status']}")

print("\n📌 Option 3: Query by NAMA (case-insensitive, partial match)")
result = get_nilai_permatkul_by_mahasiswa(nama="John")
print(f"Status: {result['status']}")

print("\n📌 Option 4: Query by prodi")
result = get_nilai_permatkul_by_mahasiswa(prodi_id=1)
print(f"Status: {result['status']}")

print("\n📌 Option 5: Query by combination (nama + prodi)")
result = get_nilai_permatkul_by_mahasiswa(nama="John", prodi_id=1)
print(f"Status: {result['status']}")

print("\n" + "=" * 80)
print("✅ TEST COMPLETE")
print("=" * 80)
print("""
SUMMARY OF NEW FEATURES:
========================

✅ Function: get_nilai_permatkul_by_mahasiswa()
   Parameters: mahasiswa_id, nim, nama, prodi_id
   New: Can now filter by NAMA (partial match, case-insensitive)
   
✅ Function: get_nilai_persemester_by_mahasiswa()
   Parameters: mahasiswa_id, nim, nama, prodi_id
   New: Can now filter by NAMA (partial match, case-insensitive)
   
✅ Function: get_combined_analisis_nilai()
   Parameters: mahasiswa_id, nim, nama
   New: Can now filter by NAMA (partial match, case-insensitive)

USAGE EXAMPLES:
===============
1. By ID:     get_nilai_permatkul_by_mahasiswa(mahasiswa_id=5)
2. By NIM:    get_nilai_permatkul_by_mahasiswa(nim="2401010001")
3. By NAMA:   get_nilai_permatkul_by_mahasiswa(nama="John")
4. Combined:  get_nilai_permatkul_by_mahasiswa(nama="John", prodi_id=1)

NAMA MATCHING:
==============
- Partial match: "John" will match "John Doe", "Johnny", "John Smith", etc.
- Case-insensitive: "john" matches "John" or "JOHN"
- Uses SQL ILIKE operator for flexibility
""")
