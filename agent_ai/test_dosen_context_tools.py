"""
Test file untuk demonstrasi dosen context tools
Menunjukkan bagaimana dosen mengakses nilai mahasiswa mereka (berdasarkan prodi_id)
"""

from tools.nilai_mahasiswa_tools import (
    get_nilai_permatkul_by_dosen_context,
    get_nilai_persemester_by_dosen_context,
    get_combined_analisis_by_dosen_context
)


print("=" * 80)
print("🎓 DOSEN CONTEXT TOOLS TEST")
print("=" * 80)

print("\n" + "=" * 80)
print("📚 TEST 1: Get Nilai Permatkul By Dosen Context")
print("=" * 80)

# Test 1: Get all nilai permatkul for prodi 1 (Teknik Informatika)
result = get_nilai_permatkul_by_dosen_context(prodi_id=1)
print(f"\n📌 Query: get_nilai_permatkul_by_dosen_context(prodi_id=1)")
print(f"Status: {result['status']}")
if result['status'] == 'success':
    print(f"Prodi: {result['prodi']['prodi_name']}")
    print(f"Total Courses: {result['total_courses']}")
    print(f"Total Records: {result['total_records']}")
    if result.get('courses'):
        print(f"\nFirst Course:")
        course = result['courses'][0]
        print(f"  - Kode: {course['kode_mk']}")
        print(f"  - Nama: {course['nama_matkul']}")
        print(f"  - Total Mahasiswa: {course['total_mahasiswa']}")
        print(f"  - Rata-rata Nilai: {course['rata_rata_nilai']}")
elif result['status'] == 'empty':
    print(f"Message: {result['message']}")

print("\n" + "=" * 80)
print("📚 TEST 2: Get Nilai Permatkul By Dosen Context - With Semester Filter")
print("=" * 80)

# Test 2: Get nilai permatkul for prodi 1, semester 3 only
result = get_nilai_permatkul_by_dosen_context(prodi_id=1, semester=3)
print(f"\n📌 Query: get_nilai_permatkul_by_dosen_context(prodi_id=1, semester=3)")
print(f"Status: {result['status']}")
if result['status'] == 'success':
    print(f"Prodi: {result['prodi']['prodi_name']}")
    print(f"Filter: Prodi {result['filter']['prodi_id']}, Semester {result['filter']['semester']}")
    print(f"Total Courses: {result['total_courses']}")
elif result['status'] == 'empty':
    print(f"Message: {result['message']}")

print("\n" + "=" * 80)
print("📊 TEST 3: Get Nilai Persemester By Dosen Context")
print("=" * 80)

# Test 3: Get all nilai persemester for prodi 1
result = get_nilai_persemester_by_dosen_context(prodi_id=1)
print(f"\n📌 Query: get_nilai_persemester_by_dosen_context(prodi_id=1)")
print(f"Status: {result['status']}")
if result['status'] == 'success':
    print(f"Prodi: {result['prodi']['prodi_name']}")
    print(f"Total Semesters: {result['total_semesters']}")
    print(f"Total Records: {result['total_records']}")
    if result.get('semesters'):
        print(f"\nFirst Semester:")
        semester = result['semesters'][0]
        print(f"  - Semester: {semester['semester_label']}")
        print(f"  - Total Mahasiswa: {semester['total_mahasiswa']}")
        print(f"  - Rata-rata GPA: {semester['rata_rata_gpa']}")
        print(f"  - GPA Tertinggi: {semester['gpa_tertinggi']}")
        print(f"  - GPA Terendah: {semester['gpa_terendah']}")
elif result['status'] == 'empty':
    print(f"Message: {result['message']}")

print("\n" + "=" * 80)
print("🎯 TEST 4: Get Nilai Persemester By Dosen Context - With Semester Filter")
print("=" * 80)

# Test 4: Get nilai persemester for prodi 1, semester 2 only
result = get_nilai_persemester_by_dosen_context(prodi_id=1, semester=2)
print(f"\n📌 Query: get_nilai_persemester_by_dosen_context(prodi_id=1, semester=2)")
print(f"Status: {result['status']}")
if result['status'] == 'success':
    print(f"Prodi: {result['prodi']['prodi_name']}")
    print(f"Filter: Prodi {result['filter']['prodi_id']}, Semester {result['filter']['semester']}")
    print(f"Total Semesters: {result['total_semesters']}")
elif result['status'] == 'empty':
    print(f"Message: {result['message']}")

print("\n" + "=" * 80)
print("🔄 TEST 5: Get Combined Analysis By Dosen Context")
print("=" * 80)

# Test 5: Combined analysis for prodi 1
result = get_combined_analisis_by_dosen_context(prodi_id=1)
print(f"\n📌 Query: get_combined_analisis_by_dosen_context(prodi_id=1)")
print(f"Status: {result['status']}")
if result['status'] == 'success':
    print(f"Prodi: {result['prodi']['prodi_name']}")
    print(f"Total Courses: {result['per_matakuliah']['total_courses']}")
    print(f"Total Semesters: {result['per_semester']['total_semesters']}")
elif result['status'] == 'error':
    print(f"Message: {result['message']}")

print("\n" + "=" * 80)
print("🔄 TEST 6: Combined Analysis By Dosen Context - With Semester Filter")
print("=" * 80)

# Test 6: Combined analysis for prodi 1, semester 1
result = get_combined_analisis_by_dosen_context(prodi_id=1, semester=1)
print(f"\n📌 Query: get_combined_analisis_by_dosen_context(prodi_id=1, semester=1)")
print(f"Status: {result['status']}")
if result['status'] == 'success':
    print(f"Prodi: {result['prodi']['prodi_name']}")
    print(f"Filter: Prodi {result['filter']['prodi_id']}, Semester {result['filter']['semester']}")
    print(f"Total Courses: {result['per_matakuliah']['total_courses']}")
    print(f"Total Semesters: {result['per_semester']['total_semesters']}")
elif result['status'] == 'error':
    print(f"Message: {result['message']}")

print("\n" + "=" * 80)
print("✅ DOSEN CONTEXT TOOLS TEST COMPLETE")
print("=" * 80)
print("""
SUMMARY OF DOSEN CONTEXT TOOLS:
================================

✅ Function: get_nilai_permatkul_by_dosen_context(prodi_id, semester=None)
   Purpose: Get all nilai permatkul for a dosen's students
   Required: prodi_id (dosen's program studi)
   Optional: semester filter
   Returns: All courses with student lists and statistics per course
   
✅ Function: get_nilai_persemester_by_dosen_context(prodi_id, semester=None)
   Purpose: Get all nilai persemester for a dosen's students
   Required: prodi_id (dosen's program studi)
   Optional: semester filter
   Returns: All semesters with student lists and GPA statistics
   
✅ Function: get_combined_analisis_by_dosen_context(prodi_id, semester=None)
   Purpose: Get combined analysis for dosen's students
   Required: prodi_id (dosen's program studi)
   Optional: semester filter
   Returns: Both per_matakuliah and per_semester combined views

DOSEN CONTEXT:
==============
- prodi_id: ID Program Studi (required for all dosen context functions)
- Automatically filters to only dosen's students
- semester: Optional to further filter by specific semester

USAGE EXAMPLES:
===============
1. Get all courses data for a dosen in prodi 1:
   get_nilai_permatkul_by_dosen_context(prodi_id=1)

2. Get courses data for semester 3 only:
   get_nilai_permatkul_by_dosen_context(prodi_id=1, semester=3)

3. Get semester analysis for a dosen:
   get_nilai_persemester_by_dosen_context(prodi_id=1)

4. Get complete analysis for a dosen in semester 1:
   get_combined_analisis_by_dosen_context(prodi_id=1, semester=1)

RESPONSE STRUCTURE:
===================
All dosen context functions return:
- status: success/empty/error
- prodi: {prodi_id, prodi_name}
- filter: {prodi_id, semester (if provided)}
- Detailed data grouped by course or semester with statistics
""")
