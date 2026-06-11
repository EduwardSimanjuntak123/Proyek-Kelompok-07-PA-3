# Tools untuk membentuk kelompok berdasarkan nilai akademik mahasiswa dengan PA category awareness.
# PERBAIKAN v3:
# - balance_group_by_grades: tambah parameter constraints (must_together, must_apart)
# - Implementasi Union-Find untuk handle chain must_together (A-B, B-C → satu kelompok)
# - must_apart diterapkan saat distribusi snake dengan penalti penempatan
# - create_group_by_grades: teruskan constraints ke balance_group_by_grades
# - create_group_by_grades_members_per_group: teruskan constraints ke balance_group_by_grades

import math
import statistics
import random
from collections import defaultdict
from typing import Dict, List, Optional
from decimal import Decimal

from core.database import SessionLocal
from models.kelompok import Kelompok
from models.kelompokMahasiswa import KelompokMahasiswa
from models.mahasiswa import Mahasiswa
from models.nilai_matkul_mahasiswa import NilaiMatkulMahasiswa
from models.tahun_masuk import TahunMasuk
from models.kategori_pa import KategoriPA
from sqlalchemy import func, and_

# ──────────────────────────────────────────────────────────────────────────────
# PA CATEGORY → SEMESTERS MAPPING
# ──────────────────────────────────────────────────────────────────────────────

def get_pa_category_semesters(kategori_pa_id: int) -> List[int]:
    """
    Mapping PA category ke semester yang diambil untuk rata-rata nilai.
    """
    session = SessionLocal()
    try:
        pa = session.query(KategoriPA).filter(KategoriPA.id == kategori_pa_id).first()
        if not pa:
            return [1]

        pa_name = (pa.kategori_pa or "").lower().strip()

        if "pa" in pa_name and "1" in pa_name and "2" not in pa_name and "3" not in pa_name:
            return [1]
        elif "pa" in pa_name and "2" in pa_name and "3" not in pa_name:
            return [1, 2, 3]
        elif "pa" in pa_name and "3" in pa_name:
            return [1, 2, 3, 4, 5]
        else:
            return [1]
    finally:
        session.close()

# ──────────────────────────────────────────────────────────────────────────────
# HITUNG NILAI RATA-RATA MAHASISWA
# ──────────────────────────────────────────────────────────────────────────────

def calculate_student_average_grades(
    prodi_id: int,
    kategori_pa_id: int,
    angkatan_id: int = None,
    exclude_existing: bool = True,
) -> Dict:
    """
    Hitung rata-rata nilai mahasiswa berdasarkan semester untuk PA category.
    """
    session = SessionLocal()
    try:
        semesters = get_pa_category_semesters(kategori_pa_id)

        query = session.query(Mahasiswa)
        if prodi_id:
            query = query.filter(Mahasiswa.prodi_id == prodi_id)
        if angkatan_id:
            tahun_masuk = session.query(TahunMasuk).filter(TahunMasuk.id == angkatan_id).first()
            if tahun_masuk:
                query = query.filter(Mahasiswa.angkatan == tahun_masuk.Tahun_Masuk)

        mahasiswas = query.all()
        if not mahasiswas:
            return {
                "status": "empty",
                "message": "Tidak ada mahasiswa pada konteks yang dipilih",
            }

        occupied_user_ids: set = set()
        if exclude_existing:
            prodi_user_ids = {mhs.user_id for mhs in mahasiswas if mhs.user_id is not None}
            if prodi_user_ids:
                occupied_rows = (
                    session.query(KelompokMahasiswa.user_id)
                    .filter(KelompokMahasiswa.user_id.in_(prodi_user_ids))
                    .all()
                )
                occupied_user_ids = {row[0] for row in occupied_rows if row and row[0] is not None}

        student_grades: List[Dict] = []
        students_without_grades = 0
        students_with_grades = 0

        for mhs in mahasiswas:
            if exclude_existing and mhs.user_id in occupied_user_ids:
                continue

            grade_rows = session.query(NilaiMatkulMahasiswa).filter(
                and_(
                    NilaiMatkulMahasiswa.mahasiswa_id == mhs.user_id,
                    NilaiMatkulMahasiswa.semester.in_(semesters),
                )
            ).all()

            if grade_rows:
                nilai_list = [
                    float(g.nilai_angka)
                    for g in grade_rows
                    if g.nilai_angka is not None
                ]
                if nilai_list:
                    avg_grade = sum(nilai_list) / len(nilai_list)
                    unique_semesters = sorted({g.semester for g in grade_rows})
                    course_count = len(nilai_list)
                    students_with_grades += 1
                else:
                    avg_grade = 0.0
                    unique_semesters = []
                    course_count = 0
                    students_without_grades += 1
            else:
                avg_grade = 0.0
                unique_semesters = []
                course_count = 0
                students_without_grades += 1

            student_grades.append({
                "mahasiswa_id" : mhs.id,
                "user_id"      : mhs.user_id,
                "nim"          : mhs.nim,
                "nama"         : mhs.nama,
                "angkatan"     : mhs.angkatan,
                "average_grade": round(avg_grade, 2),
                "course_count" : course_count,
                "semesters"    : unique_semesters,
                "has_grades"   : course_count > 0,
            })

        if not student_grades:
            return {
                "status" : "empty",
                "message": (
                    f"Tidak ada mahasiswa pada konteks yang dipilih. "
                    f"Total dalam prodi: {len(mahasiswas)}, "
                    f"Sudah dalam kelompok: {len(occupied_user_ids)}"
                ),
            }

        grades_with_data = [
            sg["average_grade"] for sg in student_grades if sg["has_grades"]
        ]
        if grades_with_data:
            mean_grade = statistics.mean(grades_with_data)
            std_dev    = statistics.stdev(grades_with_data) if len(grades_with_data) > 1 else 0.0
        else:
            mean_grade = 0.0
            std_dev    = 0.0

        total_in_prodi   = len(mahasiswas)
        already_in_group = len(occupied_user_ids)
        candidates_count = total_in_prodi - already_in_group

        return {
            "status" : "success",
            "message": (
                f"Berhasil menghitung rata-rata nilai untuk {len(student_grades)} mahasiswa "
                f"(termasuk {students_without_grades} mahasiswa tanpa data nilai)"
            ),
            "semesters_used": semesters,
            "breakdown": {
                "total_mahasiswa_dalam_prodi"  : total_in_prodi,
                "sudah_dalam_kelompok_excluded": already_in_group,
                "kandidat_untuk_grouping"      : candidates_count,
                "dengan_data_nilai_semesters"  : students_with_grades,
                "tanpa_data_nilai_semesters"   : students_without_grades,
                "catatan": (
                    f"Dari {candidates_count} mahasiswa kandidat: "
                    f"{students_with_grades} dengan data nilai di semester "
                    f"{', '.join(map(str, semesters))}, "
                    f"{students_without_grades} tanpa data nilai "
                    f"(akan digunakan dengan nilai default 0)"
                ),
            },
            "student_grades"  : student_grades,
            "class_statistics": {
                "total_students": len(student_grades),
                "mean"          : round(mean_grade, 2),
                "std_dev"       : round(std_dev, 2),
                "min_grade"     : round(min(grades_with_data) if grades_with_data else 0.0, 2),
                "max_grade"     : round(max(grades_with_data) if grades_with_data else 0.0, 2),
            },
        }
    except Exception as e:
        return {"status": "error", "message": f"Error menghitung rata-rata nilai: {str(e)}"}
    finally:
        session.close()

# ──────────────────────────────────────────────────────────────────────────────
# UNION-FIND HELPER
# ──────────────────────────────────────────────────────────────────────────────

class _UnionFind:
    """
    Union-Find sederhana untuk mengelompokkan mahasiswa yang harus satu kelompok.
    Mendukung chain: A-B + B-C → A, B, C satu kelompok.
    """

    def __init__(self, keys: List[str]):
        self.parent = {k: k for k in keys}

    def find(self, x: str) -> str:
        while self.parent[x] != x:
            self.parent[x] = self.parent[self.parent[x]]  # path compression
            x = self.parent[x]
        return x

    def union(self, x: str, y: str) -> None:
        px, py = self.find(x), self.find(y)
        if px != py:
            self.parent[px] = py

    def groups(self) -> Dict[str, List[str]]:
        """Return dict root → list of members."""
        result: Dict[str, List[str]] = defaultdict(list)
        for k in self.parent:
            result[self.find(k)].append(k)
        return result

# ──────────────────────────────────────────────────────────────────────────────
# ALGORITMA PENYEIMBANGAN KELOMPOK
# ──────────────────────────────────────────────────────────────────────────────

def balance_group_by_grades(
    student_grades: List[Dict],
    group_count: int,
    class_mean: float,
    class_std_dev: float,
    randomize_ties: bool = False,
    constraints: List[Dict] = None,
) -> Dict:
    """
    Bentuk kelompok dengan menyeimbangkan nilai rata-rata kelompok.

    Algoritma:
    1. Pre-assign: mahasiswa dengan constraint must_together
    digabung dulu menggunakan Union-Find (support chain A-B-C).
    2. Sisa mahasiswa (singles) didistribusikan dengan snake/zigzag.
    3. must_apart diterapkan sebagai swap post-processing jika masih
    satu kelompok setelah distribusi.

    Args:
        student_grades : list dict mahasiswa (wajib ada field "nim", "average_grade")
        group_count    : jumlah kelompok target
        class_mean     : rata-rata nilai kelas
        class_std_dev  : standar deviasi nilai kelas
        randomize_ties : acak urutan mahasiswa dengan nilai sama
        constraints    : list dict constraint dari _parse_constraints
                            [{"type": "must_together", "student1": nim, "student2": nim}, ...]
                            [{"type": "must_apart",    "student1": nim, "student2": nim}, ...]
                            [{"type": "must_together_group", "students": [nim, ...]}, ...]
    """
    try:
        if not student_grades:
            return {"status": "error", "message": "Tidak ada mahasiswa untuk dikelompokkan"}
        if group_count <= 0:
            return {"status": "error", "message": "Jumlah kelompok harus lebih dari 0"}

        constraints    = constraints or []
        group_count    = min(group_count, len(student_grades))
        constraint_warnings: List[str] = []

        # Lookup NIM → student dict  (nim disimpan sebagai string untuk konsistensi)
        nim_to_student: Dict[str, Dict] = {str(s["nim"]): s for s in student_grades}
        all_nims = list(nim_to_student.keys())

        # ── 1. Union-Find untuk must_together ──────────────────────────────
        uf = _UnionFind(all_nims)

        for c in constraints:
            ctype = c.get("type")

            if ctype == "must_together":
                n1, n2 = str(c["student1"]), str(c["student2"])
                if n1 not in nim_to_student:
                    constraint_warnings.append(f"NIM {n1} tidak ditemukan, constraint diabaikan")
                    continue
                if n2 not in nim_to_student:
                    constraint_warnings.append(f"NIM {n2} tidak ditemukan, constraint diabaikan")
                    continue
                uf.union(n1, n2)

            elif ctype == "must_together_group":
                nims = [str(n) for n in c.get("students", [])]
                valid = []
                for n in nims:
                    if n not in nim_to_student:
                        constraint_warnings.append(f"NIM {n} tidak ditemukan, constraint diabaikan")
                    else:
                        valid.append(n)
                for i in range(1, len(valid)):
                    uf.union(valid[0], valid[i])

        # ── 2. Pisahkan constrained clusters vs singles ────────────────────
        uf_groups = uf.groups()  # root → [nim, ...]

        # Cluster = lebih dari 1 orang (hasil must_together)
        clusters: List[List[Dict]] = []
        singles:  List[Dict]       = []

        for root, nims in uf_groups.items():
            members = [nim_to_student[n] for n in nims]
            if len(members) > 1:
                clusters.append(members)
            else:
                singles.append(members[0])

        # Validasi: cluster tidak melebihi ukuran kelompok maksimal
        max_group_size = math.ceil(len(student_grades) / group_count) + 1
        for cluster in clusters:
            if len(cluster) > max_group_size:
                constraint_warnings.append(
                    f"Constraint must_together menghasilkan cluster {len(cluster)} orang, "
                    f"melebihi ukuran kelompok maksimal ~{max_group_size}. "
                    f"Constraint tetap diterapkan."
                )

        # ── 3. Sort singles untuk distribusi snake ─────────────────────────
        if randomize_ties:
            random.shuffle(singles)
        singles = sorted(singles, key=lambda x: x["average_grade"], reverse=True)
        if randomize_ties:
            tier_size = group_count
            shuffled: List[Dict] = []
            for i in range(0, len(singles), tier_size):
                tier = singles[i: i + tier_size]
                random.shuffle(tier)
                shuffled.extend(tier)
            singles = shuffled

        # ── 4. Inisialisasi groups & tempatkan clusters ────────────────────
        groups: List[List[Dict]] = [[] for _ in range(group_count)]

        # Tempatkan setiap cluster ke kelompok yang paling kosong
        # Sort cluster descending by avg nilai agar kelompok seimbang
        clusters_sorted = sorted(
            clusters,
            key=lambda c: sum(m["average_grade"] for m in c) / len(c),
            reverse=True,
        )
        for cluster in clusters_sorted:
            target_idx = min(range(group_count), key=lambda i: len(groups[i]))
            groups[target_idx].extend(cluster)

        # ── 5. Distribusi snake untuk singles ─────────────────────────────
        # Hitung target size per kelompok
        target_total = len(student_grades)
        base_size    = target_total // group_count
        extras       = target_total % group_count  # kelompok pertama 'extras' dapat +1

        def _capacity(idx: int) -> int:
            return base_size + (1 if idx < extras else 0)

        for i, student in enumerate(singles):
            # Snake: baris genap → kiri ke kanan, baris ganjil → kanan ke kiri
            row = i // group_count
            col = i % group_count
            snake_col = (group_count - 1 - col) if (row % 2 == 1) else col

            available_groups = []

            if len(groups[snake_col]) >= _capacity(snake_col):

                available_groups = [
                    idx for idx in range(group_count)
                    if len(groups[idx]) < _capacity(idx)
                ]

                if available_groups:
                    snake_col = min(
                        available_groups,
                        key=lambda idx: len(groups[idx])
                    )
                else:
                    continue

            groups[snake_col].append(student)

        for grp in groups:
            if len(grp) > 6:
                    # redistribusi anggota ke kelompok lain yang lebih kosong
                groups[snake_col].append(student)

        # ── 6. Post-processing: must_apart ─────────────────────────────────
        # Jika dua mahasiswa must_apart masih satu kelompok, coba swap
        for c in constraints:
            if c.get("type") != "must_apart":
                continue

            n1, n2 = str(c["student1"]), str(c["student2"])
            if n1 not in nim_to_student or n2 not in nim_to_student:
                constraint_warnings.append(
                    f"NIM {n1} atau {n2} tidak ditemukan, constraint must_apart diabaikan"
                )
                continue

            # Cari lokasi n1 dan n2
            loc1 = loc2 = None
            for gi, grp in enumerate(groups):
                for mi, m in enumerate(grp):
                    if str(m["nim"]) == n1:
                        loc1 = (gi, mi)
                    if str(m["nim"]) == n2:
                        loc2 = (gi, mi)

            if loc1 is None or loc2 is None:
                continue

            gi1, mi1 = loc1
            gi2, mi2 = loc2

            if gi1 == gi2:
                # Mereka satu kelompok → cari kandidat swap dari kelompok berbeda
                swapped = False
                for gi_other in range(group_count):
                    if gi_other == gi1:
                        continue
                    for mi_other, candidate in enumerate(groups[gi_other]):
                        # Pastikan candidate bukan bagian dari must_together dengan n1 atau n2
                        candidate_nim = str(candidate["nim"])
                        in_cluster_with_n1 = (uf.find(candidate_nim) == uf.find(n1))
                        in_cluster_with_n2 = (uf.find(candidate_nim) == uf.find(n2))
                        if in_cluster_with_n1 or in_cluster_with_n2:
                            continue

                        # Swap n2 dengan candidate
                        groups[gi1][mi2], groups[gi_other][mi_other] = (
                            groups[gi_other][mi_other],
                            groups[gi1][mi2],
                        )
                        swapped = True
                        break
                    if swapped:
                        break
                if not swapped:
                    constraint_warnings.append(
                        f"Tidak dapat memisahkan NIM {n1} dan NIM {n2} "
                        f"(tidak ada kandidat swap yang memenuhi semua constraint)"
                    )

        # ── 7. Hitung statistik tiap kelompok ─────────────────────────────
        acceptable_min = class_mean - class_std_dev
        acceptable_max = class_mean + class_std_dev
        all_within_range = True
        group_results: List[Dict] = []

        for group_num, members in enumerate(groups, start=1):
            if not members:
                continue
            group_avg = round(sum(m["average_grade"] for m in members) / len(members), 2)
            deviation = round(group_avg - class_mean, 2)
            within_rng = acceptable_min <= group_avg <= acceptable_max
            if not within_rng:
                all_within_range = False

            group_results.append({
                "group_number": group_num,
                "members": members,
                "member_count": len(members),
                "group_average": group_avg,
                "deviation_from_mean": deviation,
                "within_acceptable_range": within_rng,
            })

        result = {
            "status": "success",
            "message": f"Berhasil membentuk {len(group_results)} kelompok dengan algoritma balanced grades",
            "groups": group_results,
            "group_statistics": {
                "total_groups": len(group_results),
                "target_size": round(len(student_grades) / group_count, 1),
                "group_averages": [g["group_average"] for g in group_results],
                "group_deviations": [g["deviation_from_mean"] for g in group_results],
                "all_within_range": all_within_range,
                "acceptable_range": {
                    "min": round(acceptable_min, 2),
                    "max": round(acceptable_max, 2),
                    "center": round(class_mean, 2),
                    "std_dev": round(class_std_dev, 2),
                },
            },
        }

        if constraint_warnings:
            result["constraint_warnings"] = constraint_warnings

        return result
    except Exception as e:
        return {"status": "error", "message": f"Error membentuk kelompok: {str(e)}"}

# ──────────────────────────────────────────────────────────────────────────────
# ENTRY POINT: CREATE GROUP BY GRADES
# ──────────────────────────────────────────────────────────────────────────────

def create_group_by_grades(
    prodi_id: int,
    kategori_pa_id: int,
    group_count: int,
    angkatan_id: int = None,
    exclude_existing: bool = True,
    randomize_ties: bool = False,
    constraints: List[Dict] = None,
) -> Dict:
    """
    Bentuk kelompok berdasarkan nilai mahasiswa dengan PA category awareness.

    Args:
        prodi_id        : ID program studi
        kategori_pa_id  : ID kategori PA (determines semesters)
        group_count     : Jumlah kelompok yang diinginkan
        angkatan_id     : Optional filter tahun masuk
        exclude_existing: Exclude mahasiswa yang sudah ada di kelompok
        randomize_ties  : Acak antar mahasiswa dengan nilai sama
        constraints     : List constraint dari _parse_constraints
    """
    grade_result = calculate_student_average_grades(
        prodi_id=prodi_id,
        kategori_pa_id=kategori_pa_id,
        angkatan_id=angkatan_id,
        exclude_existing=exclude_existing,
    )
    if grade_result.get("status") != "success":
        return grade_result

    student_grades = grade_result.get("student_grades", [])
    class_stats    = grade_result.get("class_statistics", {})
    semesters_used = grade_result.get("semesters_used", [])
    breakdown      = grade_result.get("breakdown", {})

    n_students = len(student_grades)
    if group_count > n_students:
        return {
            "status": "error",
            "message": (
                f"Jumlah kelompok ({group_count}) melebihi jumlah mahasiswa "
                f"yang tersedia ({n_students}). "
                f"Kurangi jumlah kelompok atau tambah mahasiswa."
            ),
        }

    session = SessionLocal()
    try:
        pa      = session.query(KategoriPA).filter(KategoriPA.id == kategori_pa_id).first()
        pa_name = pa.kategori_pa if pa else f"PA {kategori_pa_id}"
    finally:
        session.close()

    balance_result = balance_group_by_grades(
        student_grades=student_grades,
        group_count=group_count,
        class_mean=class_stats.get("mean", 0),
        class_std_dev=class_stats.get("std_dev", 0),
        randomize_ties=randomize_ties,
        constraints=constraints,
    )
    if balance_result.get("status") != "success":
        return balance_result

    result = {
        "status": "success",
        "message": f"Berhasil membuat {group_count} kelompok berdasarkan nilai dengan {pa_name}",
        "pa_category": pa_name,
        "semesters_used": semesters_used,
        "breakdown": breakdown,
        "class_statistics": class_stats,
        "groups": balance_result.get("groups", []),
        "group_statistics": balance_result.get("group_statistics", {}),
    }
    if balance_result.get("constraint_warnings"):
        result["constraint_warnings"] = balance_result["constraint_warnings"]

    return result

# ──────────────────────────────────────────────────────────────────────────────
# HELPER: CREATE GROUP BY MEMBERS PER GROUP
# ──────────────────────────────────────────────────────────────────────────────

def create_group_by_grades_members_per_group(
    prodi_id: int,
    kategori_pa_id: int,
    members_per_group: int,
    angkatan_id: int = None,
    exclude_existing: bool = True,
    randomize_ties: bool = False,
    constraints: List[Dict] = None,
) -> Dict:
    """
    Bentuk kelompok berdasarkan JUMLAH ANGGOTA per kelompok (bukan jumlah kelompok).

    Menghitung group_count = ceil(total_mahasiswa / members_per_group) secara internal.

    Args:
        members_per_group: Target anggota per kelompok (mis. 5)
        constraints      : List constraint dari _parse_constraints
    """
    if members_per_group < 2:
        return {"status": "error", "message": "Anggota per kelompok minimal 2"}

    grade_result = calculate_student_average_grades(
        prodi_id=prodi_id,
        kategori_pa_id=kategori_pa_id,
        angkatan_id=angkatan_id,
        exclude_existing=exclude_existing,
    )
    if grade_result.get("status") != "success":
        return grade_result

    student_grades = grade_result.get("student_grades", [])
    n_students     = len(student_grades)

    if n_students == 0:
        return {"status": "empty", "message": "Tidak ada mahasiswa tersedia untuk dikelompokkan"}

    group_count    = math.ceil(n_students / members_per_group)
    class_stats    = grade_result.get("class_statistics", {})
    semesters_used = grade_result.get("semesters_used", [])
    breakdown      = grade_result.get("breakdown", {})

    session = SessionLocal()
    try:
        pa      = session.query(KategoriPA).filter(KategoriPA.id == kategori_pa_id).first()
        pa_name = pa.kategori_pa if pa else f"PA {kategori_pa_id}"
    finally:
        session.close()

    balance_result = balance_group_by_grades(
        student_grades=student_grades,
        group_count=group_count,
        class_mean=class_stats.get("mean", 0),
        class_std_dev=class_stats.get("std_dev", 0),
        randomize_ties=randomize_ties,
        constraints=constraints,
    )
    if balance_result.get("status") != "success":
        return balance_result

    result = {
        "status": "success",
        "message": (
            f"Berhasil membuat {group_count} kelompok "
            f"(target {members_per_group} orang/kelompok) dengan {pa_name}"
        ),
        "pa_category": pa_name,
        "semesters_used": semesters_used,
        "members_per_group_target": members_per_group,
        "breakdown": breakdown,
        "class_statistics": class_stats,
        "groups": balance_result.get("groups", []),
        "group_statistics": balance_result.get("group_statistics", {}),
    }
    if balance_result.get("constraint_warnings"):
        result["constraint_warnings"] = balance_result["constraint_warnings"]

    return result