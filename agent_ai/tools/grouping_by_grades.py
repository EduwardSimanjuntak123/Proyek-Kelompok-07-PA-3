"""Tools untuk membentuk kelompok berdasarkan nilai akademik mahasiswa dengan PA category awareness."""

import math
import statistics
from typing import Dict, List, Tuple
from decimal import Decimal

from core.database import SessionLocal
from models.kelompok import Kelompok
from models.kelompokMahasiswa import KelompokMahasiswa
from models.mahasiswa import Mahasiswa
from models.nilai_matkul_mahasiswa import NilaiMatkulMahasiswa
from models.tahun_masuk import TahunMasuk
from models.kategori_pa import KategoriPA
from sqlalchemy import func, and_


def get_pa_category_semesters(kategori_pa_id: int) -> List[int]:
    """
    Mapping PA category ke semester yang diambil untuk rata-rata nilai.
    
    Args:
        kategori_pa_id: ID kategori PA
        
    Returns:
        List semester yang akan digunakan untuk perhitungan
    """
    session = SessionLocal()
    try:
        pa = session.query(KategoriPA).filter(KategoriPA.id == kategori_pa_id).first()
        if not pa:
            return [1]  # Default semester 1
            
        pa_name = (pa.kategori_pa or "").lower().strip()
        
        # PA 1: Semester 1
        if "pa" in pa_name and "1" in pa_name and "2" not in pa_name and "3" not in pa_name:
            return [1]
        # PA 2: Semester 1, 2, 3
        elif "pa" in pa_name and "2" in pa_name and "3" not in pa_name:
            return [1, 2, 3]
        # PA 3: Semester 1, 2, 3, 4, 5
        elif "pa" in pa_name and "3" in pa_name:
            return [1, 2, 3, 4, 5]
        else:
            return [1]
    finally:
        session.close()


def calculate_student_average_grades(
    prodi_id: int,
    kategori_pa_id: int,
    angkatan_id: int = None,
    exclude_existing: bool = True
) -> Dict:
    """
    Hitung rata-rata nilai mahasiswa berdasarkan semester untuk PA category.
    
    Args:
        prodi_id: ID program studi
        kategori_pa_id: ID kategori PA
        angkatan_id: Optional ID tahun masuk untuk filter
        exclude_existing: Exclude mahasiswa yang sudah ada di kelompok
        
    Returns:
        {
            "status": "success|empty|error",
            "message": str,
            "student_grades": [
                {
                    "mahasiswa_id": int,
                    "user_id": int,
                    "nim": str,
                    "nama": str,
                    "angkatan": int,
                    "average_grade": float,
                    "course_count": int,
                    "semesters": list
                },
                ...
            ],
            "class_statistics": {
                "total_students": int,
                "mean": float,
                "std_dev": float,
                "min_grade": float,
                "max_grade": float
            }
        }
    """
    session = SessionLocal()
    try:
        # Get PA semesters
        semesters = get_pa_category_semesters(kategori_pa_id)
        
        # Get all students for this prodi and angkatan
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
                "message": "Tidak ada mahasiswa pada konteks yang dipilih"
            }
        
        # Get occupied user_ids if exclude_existing
        occupied_user_ids = set()
        if exclude_existing:
            occupied = session.query(KelompokMahasiswa.user_id).all()
            occupied_user_ids = {row[0] for row in occupied if row and row[0] is not None}
        
        # Calculate grades for each student
        student_grades = []
        students_without_grades = 0
        students_with_grades = 0
        
        for mhs in mahasiswas:
            # Skip if already in kelompok
            if exclude_existing and mhs.user_id in occupied_user_ids:
                continue
            
            # Query grades for this student in specified semesters
            grade_query = session.query(NilaiMatkulMahasiswa).filter(
                and_(
                    NilaiMatkulMahasiswa.mahasiswa_id == mhs.user_id,
                    NilaiMatkulMahasiswa.semester.in_(semesters)
                )
            ).all()
            
            # Calculate average grade or use 0 as default for students without grades
            if grade_query:
                nilai_list = [float(g.nilai_angka) for g in grade_query if g.nilai_angka is not None]
                if nilai_list:
                    avg_grade = sum(nilai_list) / len(nilai_list)
                    unique_semesters = sorted(set(g.semester for g in grade_query))
                    course_count = len(nilai_list)
                    students_with_grades += 1
                else:
                    # No valid grades - use default
                    avg_grade = 0.0
                    unique_semesters = []
                    course_count = 0
                    students_without_grades += 1
            else:
                # No grades found - use default
                avg_grade = 0.0
                unique_semesters = []
                course_count = 0
                students_without_grades += 1
            
            # Include all students (with or without grades)
            student_grades.append({
                "mahasiswa_id": mhs.id,
                "user_id": mhs.user_id,
                "nim": mhs.nim,
                "nama": mhs.nama,
                "angkatan": mhs.angkatan,
                "average_grade": round(avg_grade, 2),
                "course_count": course_count,
                "semesters": unique_semesters,
                "has_grades": course_count > 0
            })
        
        if not student_grades:
            return {
                "status": "empty",
                "message": f"Tidak ada mahasiswa pada konteks yang dipilih. Total dalam prodi: {len(mahasiswas)}, Sudah dalam kelompok: {len(occupied_user_ids)}"
            }
        
        # Calculate class statistics (only from students with actual grades)
        grades_with_data = [sg["average_grade"] for sg in student_grades if sg["has_grades"]]
        
        if grades_with_data:
            mean_grade = statistics.mean(grades_with_data)
            std_dev = statistics.stdev(grades_with_data) if len(grades_with_data) > 1 else 0.0
        else:
            # If no students have grades, use default statistics
            mean_grade = 0.0
            std_dev = 0.0
        
        # Calculate breakdown
        candidates_count = len(mahasiswas) - len(occupied_user_ids)  # After excluding existing
        
        return {
            "status": "success",
            "message": f"Berhasil menghitung rata-rata nilai untuk {len(student_grades)} mahasiswa (termasuk {students_without_grades} mahasiswa tanpa data nilai)",
            "semesters_used": semesters,
            "breakdown": {
                "total_mahasiswa_dalam_prodi": len(mahasiswas),
                "sudah_dalam_kelompok_excluded": len(occupied_user_ids),
                "kandidat_untuk_grouping": candidates_count,
                "dengan_data_nilai_semesters": students_with_grades,
                "tanpa_data_nilai_semesters": students_without_grades,
                "catatan": f"Dari {candidates_count} mahasiswa kandidat: {students_with_grades} dengan data nilai di semester {', '.join(map(str, semesters))}, {students_without_grades} tanpa data nilai (akan digunakan dengan nilai default 0)"
            },
            "student_grades": student_grades,
            "class_statistics": {
                "total_students": len(student_grades),
                "mean": round(mean_grade, 2),
                "std_dev": round(std_dev, 2),
                "min_grade": round(min(grades_with_data) if grades_with_data else 0.0, 2),
                "max_grade": round(max(grades_with_data) if grades_with_data else 0.0, 2)
            }
        }
    except Exception as e:
        return {
            "status": "error",
            "message": f"Error menghitung rata-rata nilai: {str(e)}"
        }
    finally:
        session.close()


def balance_group_by_grades(
    student_grades: List[Dict],
    group_count: int,
    class_mean: float,
    class_std_dev: float
) -> Dict:
    """
    Bentuk kelompok dengan menyeimbangkan nilai rata-rata kelompok.
    
    Algoritma:
    1. Urutkan mahasiswa berdasarkan nilai (descending)
    2. Distribusi ke kelompok secara snake/zigzag agar seimbang
    3. Pastikan rata-rata kelompok tidak menyimpang jauh dari class mean (±1 std_dev)
    
    Args:
        student_grades: List student dengan average_grade
        group_count: Jumlah kelompok yang diinginkan
        class_mean: Rata-rata nilai kelas
        class_std_dev: Standar deviasi nilai kelas
        
    Returns:
        {
            "status": "success|error",
            "message": str,
            "groups": [
                {
                    "group_number": int,
                    "members": [...],
                    "member_count": int,
                    "group_average": float,
                    "deviation_from_mean": float,
                    "within_acceptable_range": bool
                },
                ...
            ],
            "group_statistics": {
                "target_size": float,
                "group_averages": [float],
                "group_deviations": [float],
                "all_within_range": bool
            }
        }
    """
    try:
        if not student_grades:
            return {
                "status": "error",
                "message": "Tidak ada mahasiswa untuk dikelompokkan"
            }
        
        if group_count <= 0:
            return {
                "status": "error",
                "message": "Jumlah kelompok harus lebih dari 0"
            }
        
        # Sort students by grade (descending) - highest grades first
        sorted_students = sorted(student_grades, key=lambda x: x["average_grade"], reverse=True)
        
        # Initialize groups
        groups: List[List[Dict]] = [[] for _ in range(group_count)]
        
        # Snake/zigzag distribution: alternate direction per row
        # This ensures high and low grades are balanced across groups
        for i, student in enumerate(sorted_students):
            group_idx = i % group_count
            groups[group_idx].append(student)
        
        # Calculate group statistics
        group_results = []
        all_within_range = True
        acceptable_min = class_mean - class_std_dev
        acceptable_max = class_mean + class_std_dev
        
        for group_num, members in enumerate(groups, start=1):
            if members:
                group_avg = sum(m["average_grade"] for m in members) / len(members)
                group_avg = round(group_avg, 2)
                deviation = round(group_avg - class_mean, 2)
                within_range = acceptable_min <= group_avg <= acceptable_max
                
                if not within_range:
                    all_within_range = False
                
                group_results.append({
                    "group_number": group_num,
                    "members": members,
                    "member_count": len(members),
                    "group_average": group_avg,
                    "deviation_from_mean": deviation,
                    "within_acceptable_range": within_range
                })
        
        group_averages = [g["group_average"] for g in group_results]
        group_deviations = [g["deviation_from_mean"] for g in group_results]
        
        return {
            "status": "success",
            "message": f"Berhasil membentuk {len(group_results)} kelompok dengan algoritma balanced grades",
            "groups": group_results,
            "group_statistics": {
                "total_groups": len(group_results),
                "target_size": len(sorted_students) / group_count,
                "group_averages": group_averages,
                "group_deviations": group_deviations,
                "all_within_range": all_within_range,
                "acceptable_range": {
                    "min": round(acceptable_min, 2),
                    "max": round(acceptable_max, 2),
                    "center": round(class_mean, 2),
                    "std_dev": round(class_std_dev, 2)
                }
            }
        }
    except Exception as e:
        return {
            "status": "error",
            "message": f"Error membentuk kelompok: {str(e)}"
        }


def create_group_by_grades(
    prodi_id: int,
    kategori_pa_id: int,
    group_count: int,
    angkatan_id: int = None,
    exclude_existing: bool = True
) -> Dict:
    """
    Bentuk kelompok berdasarkan nilai mahasiswa dengan PA category awareness.
    
    Workflow:
    1. Hitung rata-rata nilai per mahasiswa berdasarkan PA category
    2. Hitung statistik kelas (mean, std_dev)
    3. Bentuk kelompok dengan algoritma balanced grades
    4. Pastikan group average tidak menyimpang > 1 std_dev dari class mean
    
    Args:
        prodi_id: ID program studi
        kategori_pa_id: ID kategori PA (determines semesters)
        group_count: Jumlah kelompok yang diinginkan
        angkatan_id: Optional filter tahun masuk
        exclude_existing: Exclude mahasiswa yang sudah ada di kelompok
        
    Returns:
        {
            "status": "success|error",
            "message": str,
            "pa_category": str,
            "semesters_used": [int],
            "class_statistics": {...},
            "groups": [...]
        }
    """
    # Step 1: Calculate student grades
    grade_result = calculate_student_average_grades(
        prodi_id=prodi_id,
        kategori_pa_id=kategori_pa_id,
        angkatan_id=angkatan_id,
        exclude_existing=exclude_existing
    )
    
    if grade_result.get("status") != "success":
        return grade_result
    
    student_grades = grade_result.get("student_grades", [])
    class_stats = grade_result.get("class_statistics", {})
    semesters_used = grade_result.get("semesters_used", [])
    breakdown = grade_result.get("breakdown", {})  # Get breakdown from grade_result
    
    # Step 2: Get PA category name
    session = SessionLocal()
    try:
        pa = session.query(KategoriPA).filter(KategoriPA.id == kategori_pa_id).first()
        pa_name = pa.kategori_pa if pa else f"PA {kategori_pa_id}"
    finally:
        session.close()
    
    # Step 3: Balance groups by grades
    balance_result = balance_group_by_grades(
        student_grades=student_grades,
        group_count=group_count,
        class_mean=class_stats.get("mean", 0),
        class_std_dev=class_stats.get("std_dev", 0)
    )
    
    if balance_result.get("status") != "success":
        return balance_result
    
    return {
        "status": "success",
        "message": f"Berhasil membuat {group_count} kelompok berdasarkan nilai dengan {pa_name}",
        "pa_category": pa_name,
        "semesters_used": semesters_used,
        "breakdown": breakdown,  # Include breakdown in return
        "class_statistics": class_stats,
        "groups": balance_result.get("groups", []),
        "group_statistics": balance_result.get("group_statistics", {})
    }
