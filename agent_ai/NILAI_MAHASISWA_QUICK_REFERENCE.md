# 🚀 Quick Reference - NilaiMahasiswa Tools

## Functions Summary

```
NILAI AKHIR:
├─ get_nilai_akhir_by_dosen_context(prodi_id)
│  └─ Final grades for all students, filterable by prodi

PER MATAKULIAH (PERMATKUL):
├─ get_nilai_permatkul_by_mahasiswa(mahasiswa_id|nim)
│  └─ Grades per course for single student
├─ get_nilai_permatkul_group_by_dosen_context(prodi_id, semester)
│  └─ Grades grouped by course across students

PER SEMESTER (PERSEMESTER):
├─ get_nilai_persemester_by_mahasiswa(mahasiswa_id|nim)
│  └─ Grades per semester with GPA for single student
├─ get_nilai_persemester_group_by_dosen_context(prodi_id, semester)
│  └─ Grades grouped by semester across students

COMBINED:
└─ get_combined_analisis_nilai(mahasiswa_id|nim)
   └─ Both permatkul + persemester in one response
```

---

## Function Signatures

```python
# Original function
get_nilai_akhir_by_dosen_context(
    prodi_id: int = None
) -> dict

# Per Matakuliah
get_nilai_permatkul_by_mahasiswa(
    mahasiswa_id: int = None,
    nim: str = None,
    prodi_id: int = None
) -> dict

get_nilai_permatkul_group_by_dosen_context(
    prodi_id: int = None,
    semester: int = None
) -> dict

# Per Semester
get_nilai_persemester_by_mahasiswa(
    mahasiswa_id: int = None,
    nim: str = None,
    prodi_id: int = None
) -> dict

get_nilai_persemester_group_by_dosen_context(
    prodi_id: int = None,
    semester: int = None
) -> dict

# Combined
get_combined_analisis_nilai(
    mahasiswa_id: int = None,
    nim: str = None
) -> dict
```

---

## Usage Examples

### 📍 Single Student - All Data
```python
# Get everything for one student (both permatkul & persemester)
result = get_combined_analisis_nilai(nim="2401010001")
# Returns: mahasiswa info + per_matakuliah + per_semester
```

### 📍 Single Student - Per Matakuliah Only
```python
result = get_nilai_permatkul_by_mahasiswa(mahasiswa_id=5)
# Returns: grades grouped by course for student
```

### 📍 Single Student - Per Semester Only
```python
result = get_nilai_persemester_by_mahasiswa(nim="2401010001")
# Returns: grades grouped by semester + GPA for student
```

### 🏢 Multiple Students - By Course
```python
# All students' grades for each course in prodi 1
result = get_nilai_permatkul_group_by_dosen_context(prodi_id=1)
# Returns: avg grade, max/min, list of students per course
```

### 🏢 Multiple Students - By Semester
```python
# All students' grades for each semester in prodi 1
result = get_nilai_persemester_group_by_dosen_context(prodi_id=1)
# Returns: avg GPA, max/min GPA, list of students per semester
```

### 🏢 Multiple Students - Specific Semester Only
```python
# All students' grades for semester 3 in prodi 1
result = get_nilai_persemester_group_by_dosen_context(prodi_id=1, semester=3)
# Returns: data for semester 3 only
```

---

## Response Quick Look

### ✅ Success Response
```python
{
    "status": "success",
    "total": 10,
    "rata_rata": 3.45,
    "data": [...]
}
```

### ❌ Empty Response
```python
{
    "status": "empty",
    "message": "Tidak ada data nilai matakuliah"
}
```

### ⚠️ Error Response
```python
{
    "status": "error",
    "message": "Error: [error details]"
}
```

---

## Response Structure Cheat Sheet

### Nilai Akhir Response
```
{
    status, message, total, rata_rata,
    data: [{id, nim, nama, nilai_akhir, kelompok_id, prodi_id}]
}
```

### Permatkul (Single Student) Response
```
{
    status, mahasiswa: {id, nim, nama, prodi_id, prodi_name},
    total_matakuliah, rata_rata_nilai,
    nilai_permatkul: [{kode_mk, nama_matkul, sks, semester, tahun_ajaran,
                       nilai_angka, nilai_huruf, bobot_nilai}]
}
```

### Permatkul (Grouped) Response
```
{
    status, total_courses, total_records, filter: {prodi_id, semester},
    courses: [{kode_mk, nama_matkul, sks, semester, tahun_ajaran,
               total_mahasiswa, rata_rata, nilai_tertinggi, nilai_terendah,
               mahasiswa_list: [{mahasiswa_id, nim, nama, nilai_angka, nilai_huruf, bobot_nilai}]}]
}
```

### Persemester (Single Student) Response
```
{
    status, mahasiswa: {id, nim, nama, prodi_id, prodi_name, angkatan},
    cumulative_gpa, total_semesters,
    nilai_persemester: [{semester_label, semester, tahun_ajaran, total_courses,
                         gpa_semester, courses: [{kode_mk, nilai_angka, nilai_huruf, bobot_nilai}]}]
}
```

### Persemester (Grouped) Response
```
{
    status, total_semesters, total_records, filter: {prodi_id, semester},
    semesters: [{semester_label, semester, tahun_ajaran, total_mahasiswa,
                 rata_rata_gpa, gpa_tertinggi, gpa_terendah,
                 mahasiswa_list: [{mahasiswa_id, nim, nama, gpa, total_courses}]}]
}
```

### Combined Response
```
{
    status, mahasiswa: {...},
    per_matakuliah: {total, rata_rata, data: [...]},
    per_semester: {total, cumulative_gpa, data: [...]}
}
```

---

## Key Fields Reference

| Field | Type | Meaning |
|-------|------|---------|
| `nilai_angka` | float | Numeric grade (0-100) |
| `nilai_huruf` | str | Letter grade (A, B, C, D, E, F) |
| `bobot_nilai` | float | Weight for GPA calculation |
| `gpa_semester` | float | GPA for one semester only |
| `cumulative_gpa` | float | Overall GPA across all semesters |
| `rata_rata` | float | Average value |
| `sks` | int | Credit points |
| `kode_mk` | str | Course code (e.g., "PBO01") |
| `nama_matkul` | str | Course name (e.g., "Pemrograman Berorientasi Objek") |

---

## Common Filters

```python
# By mahasiswa ID
get_nilai_permatkul_by_mahasiswa(mahasiswa_id=5)

# By NIM
get_nilai_permatkul_by_mahasiswa(nim="2401010001")

# By Prodi
get_nilai_permatkul_by_mahasiswa(prodi_id=1)
get_nilai_permatkul_group_by_dosen_context(prodi_id=1)

# By Semester
get_nilai_permatkul_group_by_dosen_context(semester=2)
get_nilai_persemester_group_by_dosen_context(semester=2)

# Combination
get_nilai_permatkul_group_by_dosen_context(prodi_id=1, semester=3)
```

---

## Error Handling Pattern

```python
result = get_nilai_permatkul_by_mahasiswa(nim="2401010001")

if result["status"] == "success":
    # Process the data
    mahasiswa = result["mahasiswa"]
    courses = result["nilai_permatkul"]
    avg = result["rata_rata_nilai"]
    
elif result["status"] == "empty":
    # No data found
    print("Data not found")
    
else:  # error
    # Handle error
    print(f"Error: {result['message']}")
```

---

## Integration Checklist

- [ ] Import functions: `from tools.nilai_mahasiswa_tools import ...`
- [ ] Check database connection works
- [ ] Handle success/empty/error responses
- [ ] Add proper error logging
- [ ] Test with real data
- [ ] Validate response structure matches documentation
- [ ] Handle null/empty values gracefully
- [ ] Add performance optimization for large datasets

---

## Import Statement

```python
from tools.nilai_mahasiswa_tools import (
    get_nilai_akhir_by_dosen_context,
    get_nilai_permatkul_by_mahasiswa,
    get_nilai_permatkul_group_by_dosen_context,
    get_nilai_persemester_by_mahasiswa,
    get_nilai_persemester_group_by_dosen_context,
    get_combined_analisis_nilai
)
```

---

## File Locations

| Item | Location |
|------|----------|
| Tools Implementation | `agent_ai/tools/nilai_mahasiswa_tools.py` |
| Full Documentation | `agent_ai/NILAI_MAHASISWA_TOOLS_DOCUMENTATION.md` |
| Test File | `agent_ai/test_nilai_mahasiswa_tools.py` |
| Models | `agent_ai/models/nilai_mahasiswa.py` |
| | `agent_ai/models/nilai_matkul_mahasiswa.py` |

---

## Running Tests

```bash
cd agent_ai
python test_nilai_mahasiswa_tools.py
```

---

## 💡 Pro Tips

1. **For single student queries**: Always use either `mahasiswa_id` or `nim`, not both
2. **For grouped queries**: Use `prodi_id` to filter and reduce data size
3. **Performance**: Add `semester` filter when querying grouped data for specific semester only
4. **GPA**: Cumulative GPA is auto-calculated, semester GPA shows per-semester only
5. **Combined query**: Use for comprehensive views, break into single/group queries for specific analysis

---

## Common Use Cases Quick Select

```python
# "Show me all courses for student X"
get_nilai_permatkul_by_mahasiswa(nim="...")

# "Show me all semesters for student X"
get_nilai_persemester_by_mahasiswa(nim="...")

# "Show me everything for student X"
get_combined_analisis_nilai(nim="...")

# "Which courses have lowest average grade?"
get_nilai_permatkul_group_by_dosen_context(prodi_id=X)

# "How's semester 3 performance?"
get_nilai_persemester_group_by_dosen_context(prodi_id=X, semester=3)

# "Overall grades for prodi X"
get_nilai_akhir_by_dosen_context(prodi_id=X)
```

---

**Last Updated**: April 19, 2026 | Status: ✅ Ready to Use
