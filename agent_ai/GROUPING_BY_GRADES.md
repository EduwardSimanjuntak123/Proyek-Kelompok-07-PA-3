# Generate Kelompok Berdasarkan Nilai (Grade-Based Grouping)

## Deskripsi Fitur

Fitur ini memungkinkan pembentukan kelompok otomatis berdasarkan nilai akademik mahasiswa dengan mempertimbangkan kategori PA (Proyek Akhir). Sistem akan:

1. **Menghitung rata-rata nilai per mahasiswa** berdasarkan semester yang sesuai kategori PA
2. **Menghitung statistik kelas** (mean, standar deviasi)
3. **Membentuk kelompok seimbang** dimana rata-rata nilai kelompok tidak menyimpang jauh dari rata-rata kelas
4. **Memverifikasi keseimbangan** kelompok dengan range yang acceptable (±1 standar deviasi dari mean)

---

## PA Category Semester Mapping

Sistem secara otomatis menentukan semester mana yang digunakan berdasarkan kategori PA:

| Kategori PA | Semester yang Digunakan |
|------------|------------------------|
| **PA 1** | Semester 1 |
| **PA 2** | Semester 1, 2, 3 |
| **PA 3** | Semester 1, 2, 3, 4, 5 |

Contoh:
- PA 1 mahasiswa: Nilai rata-rata = (semua nilai di semester 1) / jumlah matakuliah
- PA 2 mahasiswa: Nilai rata-rata = (semua nilai di semester 1,2,3) / total jumlah matakuliah
- PA 3 mahasiswa: Nilai rata-rata = (semua nilai di semester 1,2,3,4,5) / total jumlah matakuliah

---

## Algoritma Pembentukan Kelompok

### Tahap 1: Kalkulasi Nilai Mahasiswa
```
Untuk setiap mahasiswa:
  1. Ambil semua nilai dari semester yang sesuai kategori PA
  2. Hitung rata-rata nilai angka
  3. Simpan sebagai student_average_grade
```

### Tahap 2: Statistik Kelas
```
class_mean = rata-rata dari semua student_average_grade
class_std_dev = standar deviasi dari semua student_average_grade
```

### Tahap 3: Distribusi Seimbang (Snake/Zigzag Pattern)
```
1. Urutkan mahasiswa berdasarkan nilai (tertinggi ke terendah)
2. Distribusikan ke kelompok secara round-robin:
   - Mahasiswa 1 → Kelompok 1
   - Mahasiswa 2 → Kelompok 2
   - Mahasiswa 3 → Kelompok 3
   - ...
   - Mahasiswa (n+1) → Kelompok 1 (kembali ke awal)

Pola ini memastikan:
  - Kelompok dengan nilai tinggi seimbang dengan kelompok nilai rendah
  - Distribusi yang adil dan merata
```

### Tahap 4: Verifikasi Keseimbangan
```
Untuk setiap kelompok:
  group_average = rata-rata nilai dari semua anggota kelompok
  
Cek apakah dalam range acceptable:
  acceptable_min = class_mean - class_std_dev
  acceptable_max = class_mean + class_std_dev
  
  Status: ✅ dalam range ATAU ⚠️ menyimpang dari range
```

---

## Cara Menggunakan

### Dari User (Natural Language)

User dapat mengatakan salah satu dari:
- "buat kelompok berdasarkan nilai" (akan membuat 5 kelompok default)
- "buat 8 kelompok berdasarkan nilai"
- "kelompokkan 6 kelompok dengan nilai"
- "generate kelompok nilai"

### Contoh Dialog

```
User: "buat 4 kelompok berdasarkan nilai untuk PA 2"
System:
  1. Deteksi: create_group_by_grades action
  2. Ambil semester untuk PA 2: [1, 2, 3]
  3. Hitung nilai setiap mahasiswa dari semester 1, 2, 3
  4. Hitung class statistics
  5. Bentuk 4 kelompok dengan algoritma seimbang
  6. Tampilkan hasil dengan verifikasi keseimbangan
```

---

## Output / Hasil

### Struktur Response

```json
{
  "status": "success",
  "message": "Berhasil membuat 4 kelompok berdasarkan nilai dengan PA 2",
  "pa_category": "PA 2",
  "semesters_used": [1, 2, 3],
  "class_statistics": {
    "total_students": 28,
    "mean": 77.5,
    "std_dev": 5.2,
    "min_grade": 65.3,
    "max_grade": 88.5
  },
  "groups": [
    {
      "group_number": 1,
      "member_count": 7,
      "members": [
        {
          "mahasiswa_id": 5,
          "user_id": 105,
          "nim": "21001",
          "nama": "Andi Wijaya",
          "average_grade": 85.5
        },
        ...
      ],
      "group_average": 79.2,
      "deviation_from_mean": 1.7,
      "within_acceptable_range": true
    },
    ...
  ],
  "group_statistics": {
    "total_groups": 4,
    "target_size": 7.0,
    "group_averages": [79.2, 78.1, 76.9, 77.5],
    "group_deviations": [1.7, 0.6, -0.6, 0.0],
    "all_within_range": true,
    "acceptable_range": {
      "min": 72.3,
      "max": 82.7,
      "center": 77.5,
      "std_dev": 5.2
    }
  }
}
```

### Penjelasan Field

- **pa_category**: Kategori PA yang digunakan
- **semesters_used**: Semester mana yang diambil nilainya
- **class_statistics**: Statistik dari semua mahasiswa
  - `mean`: Rata-rata nilai kelas
  - `std_dev`: Standar deviasi (sebaran data)
  - Digunakan untuk menentukan acceptable range
- **groups**: Array kelompok yang dibentuk
  - `group_average`: Rata-rata nilai anggota kelompok
  - `deviation_from_mean`: Berapa besar penyimpangan dari class mean
  - `within_acceptable_range`: Apakah dalam range acceptable (±1 std_dev)
- **group_statistics**: Ringkasan statistik semua kelompok
  - `all_within_range`: Apakah SEMUA kelompok within range

---

## Interpretasi Hasil

### ✅ Ideal (Semua kelompok within range)

```
Acceptable Range: 72.3 - 82.7
Group 1: 79.2 ✅ (Deviation: +1.7)
Group 2: 78.1 ✅ (Deviation: +0.6)
Group 3: 76.9 ✅ (Deviation: -0.6)
Group 4: 77.5 ✅ (Deviation: 0.0)

Status: ✅ Semua kelompok seimbang
```

Interpretasi: Semua kelompok memiliki keseimbangan nilai yang baik. Tidak ada kelompok yang terlalu tinggi atau terlalu rendah dibanding rata-rata kelas.

### ⚠️ Warning (Beberapa kelompok out of range)

```
Acceptable Range: 72.3 - 82.7
Group 1: 85.1 ⚠️ (Deviation: +7.6) ABOVE RANGE
Group 2: 76.8 ✅ (Deviation: -0.7)
Group 3: 78.2 ✅ (Deviation: +0.7)
Group 4: 70.5 ⚠️ (Deviation: -7.0) BELOW RANGE

Status: ⚠️ Beberapa kelompok menyimpang dari range
```

Interpretasi: Ada kelompok yang "terlalu bagus" dan ada yang "terlalu lemah". Mungkin perlu dipertimbangkan untuk:
- Lakukan shuffle manual anggota
- Gunakan algoritma dengan constraint yang lebih ketat
- Terima penyimpangan ini jika distribusi sudah optimal

---

## Fitur & Perilaku

### 1. Context-Aware Filtering

Sistem otomatis memfilter mahasiswa berdasarkan:
- **prodi_id**: Dari dosen yang login
- **kategori_pa_id**: Dari dosen yang login (menentukan semester)
- **angkatan_id**: Optional, dari dosen yang login

Hanya mahasiswa yang match context ini yang akan dikelompokkan.

### 2. Exclude Existing Members

Secara default, mahasiswa yang sudah ada dalam kelompok akan diabaikan. Parameter `exclude_existing=True` dapat diatur.

### 3. Flexible Group Count

User dapat menentukan jumlah kelompok, contoh:
- "buat 5 kelompok" → 5 kelompok
- "buat kelompok" (tanpa angka) → default 5 kelompok

---

## Teknis / Implementation

### File-file yang Terlibat

1. **tools/grouping_by_grades.py** (NEW)
   - `get_pa_category_semesters()`: Mapping PA category ke semester
   - `calculate_student_average_grades()`: Hitung rata-rata nilai per mahasiswa
   - `balance_group_by_grades()`: Algoritma pembentukan kelompok seimbang
   - `create_group_by_grades()`: Orchestrator utama

2. **nodes/planner_node.py** (UPDATED)
   - Added: Detection untuk "create_group_by_grades" action
   - Keywords: "buat kelompok berdasarkan nilai", "kelompok nilai", dll

3. **nodes/executor_node.py** (UPDATED)
   - Added: Handler untuk "create_group_by_grades" action
   - Imported: `create_group_by_grades`, `calculate_student_average_grades`
   - Formatting: HTML table untuk menampilkan hasil

### Database Models yang Digunakan

- **KategoriPA**: Untuk mendapat mapping semester
- **Mahasiswa**: Untuk get list mahasiswa dan filter
- **NilaiMatkulMahasiswa**: Untuk get nilai per semester
- **TahunMasuk**: Untuk filter angkatan
- **KelompokMahasiswa**: Untuk check existing members

### SQL Queries Pattern

```python
# Ambil nilai mahasiswa untuk semester tertentu
NilaiMatkulMahasiswa.query.filter(
    and_(
        NilaiMatkulMahasiswa.mahasiswa_id == id,
        NilaiMatkulMahasiswa.semester.in_([1, 2, 3])  # PA 2 semesters
    )
).all()
```

---

## Statistik & Mathematics

### Rata-rata (Mean)
```
mean = sum(values) / count(values)
```

### Standar Deviasi (Standard Deviation)
```
variance = sum((x - mean)² for x in values) / (count - 1)
std_dev = sqrt(variance)
```

### Acceptable Range
```
acceptable_min = mean - std_dev
acceptable_max = mean + std_dev

Mahasiswa dengan nilai dalam range ini dianggap "normal"
Di luar range ini adalah "outlier" (terlalu tinggi atau terlalu rendah)
```

### Contoh Kalkulasi

Data nilai: [75, 78, 80, 82, 85, 88, 90]

```
mean = (75+78+80+82+85+88+90) / 7 = 83.71
variance = sum of squared differences from mean / 6
std_dev ≈ 5.5

acceptable_range = [83.71-5.5, 83.71+5.5] = [78.21, 89.21]

Nilai 75 di bawah range → menyimpang
Nilai 88 dalam range → normal
Nilai 92 di atas range → menyimpang
```

---

## Troubleshooting

### Error: "Tidak ada mahasiswa dengan data nilai"

**Penyebab**: Mahasiswa tidak memiliki nilai untuk semester yang sesuai kategori PA

**Solusi**:
1. Cek apakah ada data nilai di semester tersebut
2. Verifikasi kategori PA yang digunakan
3. Run seeder: `php artisan db:seed --class=NilaiMahasiswaSeeder`

### Error: "Tidak ada mahasiswa pada konteks"

**Penyebab**: Tidak ada mahasiswa di prodi/kategori PA yang dipilih

**Solusi**:
1. Cek konteks dosen (login ulang)
2. Pastikan ada mahasiswa di prodi tersebut
3. Cek filter angkatan jika digunakan

### Warning: "Beberapa kelompok menyimpang dari range"

**Penyebab**: Distribusi mahasiswa tidak sempurna, ada kelompok terlalu tinggi/rendah

**Solusi**:
1. Ini adalah kondisi normal jika data berdistribusi tidak merata
2. Abaikan jika penyimpangan masih dapat diterima
3. Manual shuffle anggota kelompok jika diperlukan

---

## Future Improvements

Fitur-fitur yang bisa ditambahkan di masa depan:

1. **Advanced Balancing Algorithm**
   - Simulated annealing untuk optimasi lebih baik
   - Constraint programming untuk hard constraints

2. **Skill-based Grouping**
   - Kombinasi nilai + skills/preference
   - Diversity dalam technical skill

3. **Historical Pairing Avoidance**
   - Hindari mahasiswa yang sudah kelompok bersama sebelumnya
   - Ensure fresh combinations

4. **Performance Metrics**
   - Track group performance over time
   - Predictive grouping based on past data

5. **Export & Import**
   - Export hasil grouping ke Excel
   - Import custom grouping dari file

---

## Changelog

### Version 1.0 (2026-04-19)

- ✅ Initial implementation
- ✅ PA 1, PA 2, PA 3 category support
- ✅ Snake/zigzag distribution algorithm
- ✅ Balance verification dengan standard deviation
- ✅ Integration dengan planner & executor nodes
- ✅ HTML formatted output dengan statistics
