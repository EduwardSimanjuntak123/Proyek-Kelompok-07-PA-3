# 📚 Dokumentasi NilaiMahasiswa Tools

## Overview

Tools untuk query nilai mahasiswa dari database dengan dua fitur utama:
1. **Per Matakuliah (Permatkul)** - Nilai berdasarkan mata kuliah/course
2. **Per Semester (Persemester)** - Nilai berdasarkan semester dengan GPA tracking

---

## 📋 Daftar Functions

### 1. `get_nilai_akhir_by_dosen_context(prodi_id: int = None) -> dict`

**Deskripsi**: Mengambil nilai akhir mahasiswa dengan filtering opsional by prodi.

**Parameters**:
- `prodi_id` (int, optional): Filter berdasarkan ID program studi

**Returns**:
```python
{
    "status": "success|empty|error",
    "message": "...",
    "total": int,  # Total mahasiswa
    "rata_rata": float,  # Rata-rata nilai akhir
    "data": [
        {
            "id": int,
            "nim": str,
            "nama": str,
            "nilai_akhir": float,
            "kelompok_id": int,
            "prodi_id": int
        }
    ]
}
```

**Contoh Penggunaan**:
```python
# Ambil semua nilai akhir
result = get_nilai_akhir_by_dosen_context()

# Ambil nilai akhir untuk prodi tertentu
result = get_nilai_akhir_by_dosen_context(prodi_id=1)
```

---

## 🎓 PER MATAKULIAH (PERMATKUL)

### 2. `get_nilai_permatkul_by_mahasiswa(mahasiswa_id: int = None, nim: str = None, prodi_id: int = None) -> dict`

**Deskripsi**: Mengambil nilai mahasiswa per matakuliah dengan detail lengkap.

**Parameters**:
- `mahasiswa_id` (int, optional): ID mahasiswa spesifik
- `nim` (str, optional): NIM mahasiswa spesifik
- `prodi_id` (int, optional): Filter berdasarkan prodi

**Returns**:
```python
{
    "status": "success|empty|error",
    "mahasiswa": {
        "mahasiswa_id": int,
        "nim": str,
        "nama": str,
        "prodi_id": int,
        "prodi_name": str
    },
    "total_matakuliah": int,
    "rata_rata_nilai": float,  # Average across all courses
    "nilai_permatkul": [
        {
            "kode_mk": str,  # Course code
            "nama_matkul": str,  # Course name
            "sks": int,  # Credit points
            "semester": int,
            "tahun_ajaran": int,  # Academic year
            "nilai_angka": float,  # Numeric grade
            "nilai_huruf": str,  # Letter grade (A, B, C, etc)
            "bobot_nilai": float  # Weighted value
        }
    ]
}
```

**Contoh Penggunaan**:
```python
# Ambil nilai permatkul untuk mahasiswa spesifik (by ID)
result = get_nilai_permatkul_by_mahasiswa(mahasiswa_id=5)

# Ambil nilai permatkul berdasarkan NIM
result = get_nilai_permatkul_by_mahasiswa(nim="2401010001")

# Ambil nilai permatkul dengan filter prodi
result = get_nilai_permatkul_by_mahasiswa(prodi_id=1)
```

**Use Cases**:
- Melihat performa mahasiswa per mata kuliah
- Analisis kelemahan di mata kuliah tertentu
- Tracking progress akademik per course
- GPA per course calculation

---

### 3. `get_nilai_permatkul_group_by_dosen_context(prodi_id: int = None, semester: int = None) -> dict`

**Deskripsi**: Mengambil semua nilai matakuliah mahasiswa dikelompokkan per matakuliah (untuk analisis cross-students).

**Parameters**:
- `prodi_id` (int, optional): Filter berdasarkan prodi
- `semester` (int, optional): Filter berdasarkan semester tertentu

**Returns**:
```python
{
    "status": "success|empty|error",
    "total_courses": int,
    "total_records": int,
    "filter": {
        "prodi_id": int|None,
        "semester": int|None
    },
    "courses": [
        {
            "kode_mk": str,
            "nama_matkul": str,
            "sks": int,
            "semester": int,
            "tahun_ajaran": int,
            "total_mahasiswa": int,  # Number of students taking this course
            "rata_rata": float,  # Average grade for this course
            "nilai_tertinggi": float,  # Highest grade
            "nilai_terendah": float,  # Lowest grade
            "mahasiswa_list": [
                {
                    "mahasiswa_id": int,
                    "nim": str,
                    "nama": str,
                    "nilai_angka": float,
                    "nilai_huruf": str,
                    "bobot_nilai": float
                }
            ]
        }
    ]
}
```

**Contoh Penggunaan**:
```python
# Ambil statistik nilai per matakuliah untuk seluruh prodi
result = get_nilai_permatkul_group_by_dosen_context(prodi_id=1)

# Ambil statistik nilai per matakuliah untuk semester tertentu
result = get_nilai_permatkul_group_by_dosen_context(prodi_id=1, semester=3)
```

**Use Cases**:
- Analisis kesulitan mata kuliah (average grade per course)
- Identifikasi courses dengan performance rendah
- Bandingkan performa mahasiswa dalam course yang sama
- Rekomendasi remedial untuk courses tertentu

---

## 📅 PER SEMESTER (PERSEMESTER)

### 4. `get_nilai_persemester_by_mahasiswa(mahasiswa_id: int = None, nim: str = None, prodi_id: int = None) -> dict`

**Deskripsi**: Mengambil nilai mahasiswa per semester dengan GPA per semester.

**Parameters**:
- `mahasiswa_id` (int, optional): ID mahasiswa spesifik
- `nim` (str, optional): NIM mahasiswa spesifik
- `prodi_id` (int, optional): Filter berdasarkan prodi

**Returns**:
```python
{
    "status": "success|empty|error",
    "mahasiswa": {
        "mahasiswa_id": int,
        "nim": str,
        "nama": str,
        "prodi_id": int,
        "prodi_name": str,
        "angkatan": int  # Year of entry
    },
    "cumulative_gpa": float,  # Overall GPA across all semesters
    "total_semesters": int,
    "nilai_persemester": [
        {
            "semester_label": str,  # e.g., "Semester 1 (TA 2021)"
            "semester": int,
            "tahun_ajaran": int,
            "total_courses": int,  # Courses taken in this semester
            "gpa_semester": float,  # GPA for this semester only
            "courses": [
                {
                    "kode_mk": str,
                    "nilai_angka": float,
                    "nilai_huruf": str,
                    "bobot_nilai": float
                }
            ]
        }
    ]
}
```

**Contoh Penggunaan**:
```python
# Ambil nilai persemester untuk mahasiswa spesifik (by ID)
result = get_nilai_persemester_by_mahasiswa(mahasiswa_id=5)

# Ambil nilai persemester berdasarkan NIM
result = get_nilai_persemester_by_mahasiswa(nim="2401010001")
```

**Use Cases**:
- Tracking progress akademik per semester
- Monitoring GPA trend (apakah naik/turun tiap semester)
- Identifikasi semester dengan performance terbaik/terburuk
- Academic probation assessment (semester dengan GPA rendah)
- Graduation eligibility check

---

### 5. `get_nilai_persemester_group_by_dosen_context(prodi_id: int = None, semester: int = None) -> dict`

**Deskripsi**: Mengambil semua nilai persemester mahasiswa dikelompokkan per semester (untuk analisis cross-students).

**Parameters**:
- `prodi_id` (int, optional): Filter berdasarkan prodi
- `semester` (int, optional): Filter berdasarkan semester tertentu

**Returns**:
```python
{
    "status": "success|empty|error",
    "total_semesters": int,
    "total_records": int,
    "filter": {
        "prodi_id": int|None,
        "semester": int|None
    },
    "semesters": [
        {
            "semester_label": str,
            "semester": int,
            "tahun_ajaran": int,
            "total_mahasiswa": int,
            "rata_rata_gpa": float,  # Average GPA in this semester
            "gpa_tertinggi": float,  # Highest GPA
            "gpa_terendah": float,  # Lowest GPA
            "mahasiswa_list": [
                {
                    "mahasiswa_id": int,
                    "nim": str,
                    "nama": str,
                    "gpa": float,  # GPA for this semester
                    "total_courses": int
                }
            ]
        }
    ]
}
```

**Contoh Penggunaan**:
```python
# Ambil statistik semester untuk seluruh prodi
result = get_nilai_persemester_group_by_dosen_context(prodi_id=1)

# Ambil statistik semester tertentu
result = get_nilai_persemester_group_by_dosen_context(prodi_id=1, semester=2)
```

**Use Cases**:
- Analisis program studi trend (apakah mahasiswa semakin baik/buruk)
- Identifikasi semester dengan difficulty tinggi
- Bandingkan performance mahasiswa di semester yang sama
- Rekomendasi intervention untuk semester dengan GPA rendah

---

## 🔄 COMBINED ANALYSIS

### 6. `get_combined_analisis_nilai(mahasiswa_id: int = None, nim: str = None) -> dict`

**Deskripsi**: Mengambil analisis kombinasi nilai permatkul dan persemester dalam satu response.

**Parameters**:
- `mahasiswa_id` (int, optional): ID mahasiswa spesifik
- `nim` (str, optional): NIM mahasiswa spesifik

**Returns**:
```python
{
    "status": "success|error",
    "mahasiswa": {
        "mahasiswa_id": int,
        "nim": str,
        "nama": str,
        "prodi_id": int,
        "prodi_name": str,
        "angkatan": int
    },
    "per_matakuliah": {
        "total": int,
        "rata_rata": float,
        "data": [ ... ]  # Same as get_nilai_permatkul_by_mahasiswa
    },
    "per_semester": {
        "total": int,
        "cumulative_gpa": float,
        "data": [ ... ]  # Same as get_nilai_persemester_by_mahasiswa
    }
}
```

**Contoh Penggunaan**:
```python
# Ambil analisis lengkap (permatkul + persemester) untuk 1 mahasiswa
result = get_combined_analisis_nilai(mahasiswa_id=5)

# Ambil analisis lengkap berdasarkan NIM
result = get_combined_analisis_nilai(nim="2401010001")
```

**Use Cases**:
- Comprehensive academic profile view
- Dashboard untuk monitoring akademik mahasiswa
- Identifikasi weakness pattern (mata kuliah tertentu consistently rendah)
- Semester performance vs course performance comparison
- Holistic assessment untuk academic advising

---

## 🎯 Common Use Cases

### Use Case 1: Academic Advising
```python
# Advisor ingin tahu performa lengkap mahasiswa
result = get_combined_analisis_nilai(nim="2401010001")

# Dari hasil ini, advisor bisa:
# - Lihat mata kuliah mana yang problematik (permatkul)
# - Lihat semester mana yang terburuk (persemester)
# - Buat action plan untuk improvement
```

### Use Case 2: Course Performance Analysis
```python
# Dosen ingin tahu performance mahasiswa di mata kuliahnya
result = get_nilai_permatkul_group_by_dosen_context(prodi_id=1, semester=3)

# Dari hasil ini, dosen bisa:
# - Identifikasi mahasiswa dengan nilai rendah
# - Lihat rata-rata nilai untuk mata kuliah tersebut
# - Tentukan apakah ada remedial class needed
```

### Use Case 3: Semester Analysis
```python
# Program coordinator ingin evaluasi semester 2
result = get_nilai_persemester_group_by_dosen_context(prodi_id=1, semester=2)

# Dari hasil ini, coordinator bisa:
# - Lihat GPA trend per mahasiswa
# - Identifikasi mahasiswa yang perlu intervention
# - Analisis kesulitan semester tersebut
```

### Use Case 4: Student Academic Profile
```python
# Mahasiswa ingin lihat performa akademik mereka
result = get_combined_analisis_nilai(nim="2401010001")

# Mahasiswa bisa:
# - Lihat semua nilai per matakuliah
# - Lihat GPA trend per semester
# - Identifikasi area untuk improvement
```

---

## 📊 Response Status Codes

| Status | Meaning | Action |
|--------|---------|--------|
| `success` | Query berhasil dengan data | Proses data yang diterima |
| `empty` | Query berhasil tapi tidak ada data | Tampilkan pesan "tidak ada data" |
| `error` | Query gagal | Tampilkan error message ke user |

---

## 🔍 Data Types Reference

| Field | Type | Description |
|-------|------|-------------|
| `nilai_angka` | float | Numeric grade (0-100) |
| `nilai_huruf` | str | Letter grade (A, B, C, D, E) |
| `bobot_nilai` | float | Weighted value for GPA calculation |
| `gpa_semester` | float | GPA for one semester (0-4.0) |
| `cumulative_gpa` | float | Overall GPA across all semesters |
| `rata_rata` | float | Average value |
| `sks` | int | Credit points (Satuan Kredit Semester) |

---

## ⚙️ Filter Combinations

### Valid Filter Combinations

```python
# Per Matakuliah:
✓ get_nilai_permatkul_by_mahasiswa(mahasiswa_id=5)
✓ get_nilai_permatkul_by_mahasiswa(nim="2401010001")
✓ get_nilai_permatkul_by_mahasiswa(mahasiswa_id=5, prodi_id=1)
✓ get_nilai_permatkul_group_by_dosen_context(prodi_id=1)
✓ get_nilai_permatkul_group_by_dosen_context(prodi_id=1, semester=2)

# Per Semester:
✓ get_nilai_persemester_by_mahasiswa(mahasiswa_id=5)
✓ get_nilai_persemester_by_mahasiswa(nim="2401010001")
✓ get_nilai_persemester_group_by_dosen_context(prodi_id=1)
✓ get_nilai_persemester_group_by_dosen_context(semester=2)

# Combined:
✓ get_combined_analisis_nilai(mahasiswa_id=5)
✓ get_combined_analisis_nilai(nim="2401010001")
```

---

## 🚀 Integration Examples

### Django/Flask View
```python
def mahasiswa_akademik_profile(request, nim):
    result = get_combined_analisis_nilai(nim=nim)
    
    if result["status"] == "success":
        return render(request, 'akademik_profile.html', {
            'mahasiswa': result['mahasiswa'],
            'matakuliah': result['per_matakuliah']['data'],
            'semester': result['per_semester']['data'],
            'cumulative_gpa': result['per_semester']['cumulative_gpa']
        })
    else:
        return render(request, 'error.html', {'message': result['message']})
```

### FastAPI Endpoint
```python
@app.get("/mahasiswa/{nim}/nilai")
async def get_mahasiswa_nilai(nim: str):
    result = get_combined_analisis_nilai(nim=nim)
    return result
```

### Agent Integration
```python
def analyze_student_performance(nim: str):
    # Get combined analysis
    data = get_combined_analisis_nilai(nim=nim)
    
    if data["status"] == "success":
        # Generate insights
        insights = {
            "overall_gpa": data['per_semester']['cumulative_gpa'],
            "best_semester": max(...),
            "worst_course": min(...),
            "recommendation": "..."
        }
        return insights
```

---

## 🛡️ Error Handling

```python
result = get_nilai_permatkul_by_mahasiswa(mahasiswa_id=999)

if result["status"] == "success":
    # Process data
    for course in result['nilai_permatkul']:
        print(f"{course['kode_mk']}: {course['nilai_angka']}")
        
elif result["status"] == "empty":
    print("No data found for this student")
    
elif result["status"] == "error":
    print(f"Error: {result['message']}")
```

---

## 📝 Notes

1. **GPA Calculation**: GPA dihitung dari nilai_angka, bukan dari nilai_huruf
2. **Cumulative GPA**: Adalah weighted average dari semua semester
3. **Filter Priority**: Jika multiple filter diberikan, AND logic digunakan
4. **Null Handling**: Nilai null/0 diabaikan dalam calculation
5. **Performance**: Untuk large datasets, gunakan filtering (prodi_id, semester)

---

## 🔄 Version History

- **v1.0** (April 2026): Initial implementation with permatkul and persemester functions

---

**Last Updated**: April 19, 2026
**Status**: Production Ready ✅
