"""
Tools untuk query nilai mahasiswa dari database
Supports: 
  - Per Matakuliah (permatkul)
  - Per Semester (persemester)
"""

from core.database import SessionLocal
from models.nilai_mahasiswa import NilaiMahasiswa
from models.nilai_matkul_mahasiswa import NilaiMatkulMahasiswa
from models.mahasiswa import Mahasiswa
from models.matakuliah import MataKuliah
from sqlalchemy import func, and_
from decimal import Decimal


def get_nilai_akhir_by_dosen_context(prodi_id: int = None) -> dict:
    """
    Ambil nilai akhir mahasiswa dan filter by prodi jika tersedia.
    """
    try:
        session = SessionLocal()
        query = session.query(NilaiMahasiswa, Mahasiswa).join(
            Mahasiswa,
            NilaiMahasiswa.user_id == Mahasiswa.user_id
        )

        if prodi_id:
            query = query.filter(Mahasiswa.prodi_id == prodi_id)

        rows = query.all()

        if not rows:
            session.close()
            return {
                "status": "empty",
                "message": "Tidak ada data nilai akhir"
            }

        data = []
        for nilai, mahasiswa in rows:
            data.append({
                "id": nilai.id,
                "nim": mahasiswa.nim,
                "nama": mahasiswa.nama,
                "nilai_akhir": float(nilai.nilai_akhir) if nilai.nilai_akhir is not None else 0.0,
                "kelompok_id": nilai.kelompok_id,
                "prodi_id": mahasiswa.prodi_id
            })

        avg = sum(item["nilai_akhir"] for item in data) / len(data)

        session.close()
        return {
            "status": "success",
            "total": len(data),
            "rata_rata": round(avg, 2),
            "data": data
        }
    except Exception as e:
        print(f"Error querying nilai_mahasiswa: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


# ==================== PER MATAKULIAH (PERMATKUL) ====================

def get_nilai_permatkul_by_mahasiswa(mahasiswa_id: int = None, nim: str = None, nama: str = None, prodi_id: int = None) -> dict:
    """
    Ambil nilai mahasiswa per matakuliah dengan detail lengkap.
    Jika ada multiple mahasiswa yang match, tampilkan semua.
    
    Args:
        mahasiswa_id: ID mahasiswa
        nim: NIM mahasiswa
        nama: Nama mahasiswa (partial match, case-insensitive)
        prodi_id: Filter by prodi
    
    Returns:
        Nilai per matakuliah dengan statistik untuk semua mahasiswa yang match
    """
    try:
        session = SessionLocal()
        
        # Build filter
        filters = []
        if mahasiswa_id:
            filters.append(NilaiMatkulMahasiswa.mahasiswa_id == mahasiswa_id)
        if nim:
            filters.append(Mahasiswa.nim == nim)
        if nama:
            filters.append(Mahasiswa.nama.ilike(f"%{nama}%"))
        if prodi_id:
            filters.append(Mahasiswa.prodi_id == prodi_id)
        
        query = session.query(
            NilaiMatkulMahasiswa,
            Mahasiswa,
            MataKuliah
        ).join(
            Mahasiswa,
            NilaiMatkulMahasiswa.mahasiswa_id == Mahasiswa.id
        ).outerjoin(
            MataKuliah,
            NilaiMatkulMahasiswa.kode_mk == MataKuliah.kode_mk
        )
        
        if filters:
            query = query.filter(and_(*filters))
        
        rows = query.all()
        
        if not rows:
            session.close()
            return {
                "status": "empty",
                "message": "Tidak ada data nilai matakuliah"
            }
        
        # Group by mahasiswa_id, then by course
        mahasiswa_data = {}
        
        for nilai, mahasiswa, matkul in rows:
            mhs_id = mahasiswa.id
            
            # Create mahasiswa entry if not exists
            if mhs_id not in mahasiswa_data:
                mahasiswa_data[mhs_id] = {
                    "mahasiswa_info": {
                        "mahasiswa_id": mahasiswa.id,
                        "nim": mahasiswa.nim,
                        "nama": mahasiswa.nama,
                        "prodi_id": mahasiswa.prodi_id,
                        "prodi_name": mahasiswa.prodi_name
                    },
                    "courses": {}
                }
            
            # Add course data
            kode_mk = nilai.kode_mk
            if kode_mk not in mahasiswa_data[mhs_id]["courses"]:
                mahasiswa_data[mhs_id]["courses"][kode_mk] = {
                    "kode_mk": kode_mk,
                    "nama_matkul": matkul.nama_matkul if matkul else "Unknown",
                    "sks": matkul.sks if matkul else 0,
                    "semester": nilai.semester,
                    "tahun_ajaran": nilai.tahun_ajaran,
                    "nilai_angka": float(nilai.nilai_angka) if nilai.nilai_angka else 0.0,
                    "nilai_huruf": nilai.nilai_huruf or "-",
                    "bobot_nilai": float(nilai.bobot_nilai) if nilai.bobot_nilai else 0.0
                }
        
        # Format response with all matching mahasiswa
        mahasiswa_list = []
        for mhs_id, mhs_data in mahasiswa_data.items():
            courses_list = list(mhs_data["courses"].values())
            all_values = [float(c["nilai_angka"]) for c in courses_list if c["nilai_angka"] > 0]
            avg_value = sum(all_values) / len(all_values) if all_values else 0.0
            
            mahasiswa_list.append({
                "mahasiswa": mhs_data["mahasiswa_info"],
                "total_matakuliah": len(courses_list),
                "rata_rata_nilai": round(avg_value, 2),
                "nilai_permatkul": courses_list
            })
        
        session.close()
        
        return {
            "status": "success",
            "total_mahasiswa": len(mahasiswa_list),
            "mahasiswa_list": mahasiswa_list
        }
    
    except Exception as e:
        print(f"Error querying nilai permatkul: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_nilai_permatkul_group_by_dosen_context(prodi_id: int = None, semester: int = None) -> dict:
    """
    Ambil semua nilai matakuliah mahasiswa dikelompokkan per matakuliah.
    Berguna untuk analisis per matakuliah across multiple students.
    
    Args:
        prodi_id: Filter by program studi
        semester: Filter by semester tertentu
    
    Returns:
        Nilai grouped per course dengan statistik per course
    """
    try:
        session = SessionLocal()
        
        filters = []
        if prodi_id:
            filters.append(Mahasiswa.prodi_id == prodi_id)
        if semester:
            filters.append(NilaiMatkulMahasiswa.semester == semester)
        
        query = session.query(
            NilaiMatkulMahasiswa,
            Mahasiswa,
            MataKuliah
        ).join(
            Mahasiswa,
            NilaiMatkulMahasiswa.mahasiswa_id == Mahasiswa.id
        ).outerjoin(
            MataKuliah,
            NilaiMatkulMahasiswa.kode_mk == MataKuliah.kode_mk
        )
        
        if filters:
            query = query.filter(and_(*filters))
        
        rows = query.all()
        
        if not rows:
            session.close()
            return {
                "status": "empty",
                "message": "Tidak ada data nilai matakuliah"
            }
        
        # Group by matakuliah
        courses_data = {}
        
        for nilai, mahasiswa, matkul in rows:
            kode_mk = nilai.kode_mk
            
            if kode_mk not in courses_data:
                courses_data[kode_mk] = {
                    "kode_mk": kode_mk,
                    "nama_matkul": matkul.nama_matkul if matkul else "Unknown",
                    "sks": matkul.sks if matkul else 0,
                    "semester": nilai.semester,
                    "tahun_ajaran": nilai.tahun_ajaran,
                    "mahasiswa_list": []
                }
            
            courses_data[kode_mk]["mahasiswa_list"].append({
                "mahasiswa_id": mahasiswa.id,
                "nim": mahasiswa.nim,
                "nama": mahasiswa.nama,
                "nilai_angka": float(nilai.nilai_angka) if nilai.nilai_angka else 0.0,
                "nilai_huruf": nilai.nilai_huruf or "-",
                "bobot_nilai": float(nilai.bobot_nilai) if nilai.bobot_nilai else 0.0
            })
        
        # Calculate statistics per course
        for kode_mk in courses_data:
            nilai_list = [m["nilai_angka"] for m in courses_data[kode_mk]["mahasiswa_list"] if m["nilai_angka"] > 0]
            courses_data[kode_mk]["total_mahasiswa"] = len(courses_data[kode_mk]["mahasiswa_list"])
            courses_data[kode_mk]["rata_rata"] = round(sum(nilai_list) / len(nilai_list), 2) if nilai_list else 0.0
            courses_data[kode_mk]["nilai_tertinggi"] = round(max(nilai_list), 2) if nilai_list else 0.0
            courses_data[kode_mk]["nilai_terendah"] = round(min(nilai_list), 2) if nilai_list else 0.0
        
        session.close()
        
        return {
            "status": "success",
            "total_courses": len(courses_data),
            "total_records": len(rows),
            "filter": {
                "prodi_id": prodi_id,
                "semester": semester
            },
            "courses": list(courses_data.values())
        }
    
    except Exception as e:
        print(f"Error querying nilai permatkul grouped: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


# ==================== PER SEMESTER (PERSEMESTER) ====================

def get_nilai_persemester_by_mahasiswa(mahasiswa_id: int = None, nim: str = None, nama: str = None, prodi_id: int = None) -> dict:
    """
    Ambil nilai mahasiswa per semester dengan detail lengkap.
    Menampilkan GPA per semester.
    Jika ada multiple mahasiswa yang match, tampilkan semua.
    
    Args:
        mahasiswa_id: ID mahasiswa
        nim: NIM mahasiswa
        nama: Nama mahasiswa (partial match, case-insensitive)
        prodi_id: Filter by prodi
    
    Returns:
        Nilai per semester dengan GPA untuk semua mahasiswa yang match
    """
    try:
        session = SessionLocal()
        
        # Build filter
        filters = []
        if mahasiswa_id:
            filters.append(NilaiMatkulMahasiswa.mahasiswa_id == mahasiswa_id)
        if nim:
            filters.append(Mahasiswa.nim == nim)
        if nama:
            filters.append(Mahasiswa.nama.ilike(f"%{nama}%"))
        if prodi_id:
            filters.append(Mahasiswa.prodi_id == prodi_id)
        
        query = session.query(
            NilaiMatkulMahasiswa,
            Mahasiswa,
            MataKuliah
        ).join(
            Mahasiswa,
            NilaiMatkulMahasiswa.mahasiswa_id == Mahasiswa.id
        ).outerjoin(
            MataKuliah,
            NilaiMatkulMahasiswa.kode_mk == MataKuliah.kode_mk
        )
        
        if filters:
            query = query.filter(and_(*filters))
        
        rows = query.all()
        
        if not rows:
            session.close()
            return {
                "status": "empty",
                "message": "Tidak ada data nilai persemester"
            }
        
        # Group by mahasiswa_id, then by semester
        mahasiswa_data = {}
        
        for nilai, mahasiswa, matkul in rows:
            mhs_id = mahasiswa.id
            
            # Create mahasiswa entry if not exists
            if mhs_id not in mahasiswa_data:
                mahasiswa_data[mhs_id] = {
                    "mahasiswa_info": {
                        "mahasiswa_id": mahasiswa.id,
                        "nim": mahasiswa.nim,
                        "nama": mahasiswa.nama,
                        "prodi_id": mahasiswa.prodi_id,
                        "prodi_name": mahasiswa.prodi_name,
                        "angkatan": mahasiswa.angkatan
                    },
                    "semesters": {}
                }
            
            # Add semester data
            semester_key = f"Semester {nilai.semester} (TA {nilai.tahun_ajaran})"
            
            if semester_key not in mahasiswa_data[mhs_id]["semesters"]:
                mahasiswa_data[mhs_id]["semesters"][semester_key] = {
                    "semester": nilai.semester,
                    "tahun_ajaran": nilai.tahun_ajaran,
                    "courses": []
                }
            
            mahasiswa_data[mhs_id]["semesters"][semester_key]["courses"].append({
                "kode_mk": nilai.kode_mk,
                "nama_matkul": matkul.nama_matkul if matkul else "Unknown",
                "nilai_angka": float(nilai.nilai_angka) if nilai.nilai_angka else 0.0,
                "nilai_huruf": nilai.nilai_huruf or "-",
                "bobot_nilai": float(nilai.bobot_nilai) if nilai.bobot_nilai else 0.0
            })
        
        # Format response with all matching mahasiswa
        mahasiswa_list = []
        
        for mhs_id, mhs_data in mahasiswa_data.items():
            # Calculate GPA per semester
            semester_list = []
            cumulative_total = 0
            cumulative_count = 0
            
            for semester_key in sorted(mhs_data["semesters"].keys(), key=lambda x: int(x.split()[1])):
                sem_data = mhs_data["semesters"][semester_key]
                nilai_list = [c["nilai_angka"] for c in sem_data["courses"] if c["nilai_angka"] > 0]
                
                gpa = sum(nilai_list) / len(nilai_list) if nilai_list else 0.0
                cumulative_total += gpa * len(nilai_list)
                cumulative_count += len(nilai_list)
                
                semester_list.append({
                    "semester_label": semester_key,
                    "semester": sem_data["semester"],
                    "tahun_ajaran": sem_data["tahun_ajaran"],
                    "total_courses": len(sem_data["courses"]),
                    "gpa_semester": round(gpa, 2),
                    "courses": sem_data["courses"]
                })
            
            cumulative_gpa = cumulative_total / cumulative_count if cumulative_count > 0 else 0.0
            
            mahasiswa_list.append({
                "mahasiswa": mhs_data["mahasiswa_info"],
                "cumulative_gpa": round(cumulative_gpa, 2),
                "total_semesters": len(semester_list),
                "nilai_persemester": semester_list
            })
        
        session.close()
        
        return {
            "status": "success",
            "total_mahasiswa": len(mahasiswa_list),
            "mahasiswa_list": mahasiswa_list
        }
    
    except Exception as e:
        print(f"Error querying nilai persemester: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_nilai_persemester_group_by_dosen_context(prodi_id: int = None, semester: int = None) -> dict:
    """
    Ambil semua nilai persemester mahasiswa dikelompokkan per semester.
    Berguna untuk analisis per semester across multiple students.
    
    Args:
        prodi_id: Filter by program studi
        semester: Filter by semester tertentu
    
    Returns:
        Nilai grouped per semester dengan statistik per semester
    """
    try:
        session = SessionLocal()
        
        filters = []
        if prodi_id:
            filters.append(Mahasiswa.prodi_id == prodi_id)
        if semester:
            filters.append(NilaiMatkulMahasiswa.semester == semester)
        
        query = session.query(
            NilaiMatkulMahasiswa,
            Mahasiswa
        ).join(
            Mahasiswa,
            NilaiMatkulMahasiswa.mahasiswa_id == Mahasiswa.id
        )
        
        if filters:
            query = query.filter(and_(*filters))
        
        rows = query.all()
        
        if not rows:
            session.close()
            return {
                "status": "empty",
                "message": "Tidak ada data nilai persemester"
            }
        
        # Group by semester
        semesters_data = {}
        
        for nilai, mahasiswa in rows:
            semester_key = f"Semester {nilai.semester} (TA {nilai.tahun_ajaran})"
            
            if semester_key not in semesters_data:
                semesters_data[semester_key] = {
                    "semester": nilai.semester,
                    "tahun_ajaran": nilai.tahun_ajaran,
                    "mahasiswa_list": []
                }
            
            # Find if mahasiswa already exists in this semester
            existing_mhs = None
            for m in semesters_data[semester_key]["mahasiswa_list"]:
                if m["mahasiswa_id"] == mahasiswa.id:
                    existing_mhs = m
                    break
            
            if existing_mhs is None:
                semesters_data[semester_key]["mahasiswa_list"].append({
                    "mahasiswa_id": mahasiswa.id,
                    "nim": mahasiswa.nim,
                    "nama": mahasiswa.nama,
                    "nilai_list": []
                })
                existing_mhs = semesters_data[semester_key]["mahasiswa_list"][-1]
            
            existing_mhs["nilai_list"].append({
                "kode_mk": nilai.kode_mk,
                "nilai_angka": float(nilai.nilai_angka) if nilai.nilai_angka else 0.0,
                "nilai_huruf": nilai.nilai_huruf or "-"
            })
        
        # Calculate GPA per mahasiswa per semester
        for semester_key in semesters_data:
            for mhs in semesters_data[semester_key]["mahasiswa_list"]:
                nilai_list = [v["nilai_angka"] for v in mhs["nilai_list"] if v["nilai_angka"] > 0]
                mhs["gpa"] = round(sum(nilai_list) / len(nilai_list), 2) if nilai_list else 0.0
                mhs["total_courses"] = len(mhs["nilai_list"])
                del mhs["nilai_list"]  # Remove detail, keep only summary
            
            # Calculate semester statistics
            gpa_list = [m["gpa"] for m in semesters_data[semester_key]["mahasiswa_list"]]
            semesters_data[semester_key]["total_mahasiswa"] = len(semesters_data[semester_key]["mahasiswa_list"])
            semesters_data[semester_key]["rata_rata_gpa"] = round(sum(gpa_list) / len(gpa_list), 2) if gpa_list else 0.0
            semesters_data[semester_key]["gpa_tertinggi"] = round(max(gpa_list), 2) if gpa_list else 0.0
            semesters_data[semester_key]["gpa_terendah"] = round(min(gpa_list), 2) if gpa_list else 0.0
        
        session.close()
        
        return {
            "status": "success",
            "total_semesters": len(semesters_data),
            "total_records": len(rows),
            "filter": {
                "prodi_id": prodi_id,
                "semester": semester
            },
            "semesters": list(semesters_data.values())
        }
    
    except Exception as e:
        print(f"Error querying nilai persemester grouped: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_combined_analisis_nilai(mahasiswa_id: int = None, nim: str = None, nama: str = None) -> dict:
    """
    Ambil analisis kombinasi nilai per matakuliah dan per semester.
    Memberikan gambaran menyeluruh dari performa akademik mahasiswa.
    Jika ada multiple mahasiswa yang match, tampilkan semua.
    
    Args:
        mahasiswa_id: ID mahasiswa
        nim: NIM mahasiswa
        nama: Nama mahasiswa (partial match, case-insensitive)
    
    Returns:
        Combined analysis dengan permatkul dan persemester untuk semua mahasiswa yang match
    """
    try:
        # Get data per matakuliah
        matkul_data = get_nilai_permatkul_by_mahasiswa(mahasiswa_id, nim, nama)
        
        # Get data per semester
        semester_data = get_nilai_persemester_by_mahasiswa(mahasiswa_id, nim, nama)
        
        if matkul_data["status"] != "success" or semester_data["status"] != "success":
            return {
                "status": "error",
                "message": "Gagal mengambil data kombinasi nilai"
            }
        
        # Combine data for all matching mahasiswa
        combined_list = []
        
        # Create a map of mahasiswa from semester data by ID
        semester_map = {}
        if "mahasiswa_list" in semester_data:
            for sem_mhs in semester_data["mahasiswa_list"]:
                semester_map[sem_mhs["mahasiswa"]["mahasiswa_id"]] = sem_mhs
        
        # Combine with matakuliah data
        if "mahasiswa_list" in matkul_data:
            for mat_mhs in matkul_data["mahasiswa_list"]:
                mhs_id = mat_mhs["mahasiswa"]["mahasiswa_id"]
                sem_mhs = semester_map.get(mhs_id)
                
                combined_mhs = {
                    "mahasiswa": mat_mhs["mahasiswa"],
                    "per_matakuliah": {
                        "total": mat_mhs["total_matakuliah"],
                        "rata_rata": mat_mhs["rata_rata_nilai"],
                        "data": mat_mhs["nilai_permatkul"]
                    },
                    "per_semester": {
                        "total": sem_mhs["total_semesters"] if sem_mhs else 0,
                        "cumulative_gpa": sem_mhs["cumulative_gpa"] if sem_mhs else 0.0,
                        "data": sem_mhs["nilai_persemester"] if sem_mhs else []
                    }
                }
                combined_list.append(combined_mhs)
        
        return {
            "status": "success",
            "total_mahasiswa": len(combined_list),
            "mahasiswa_list": combined_list
        }
    
    except Exception as e:
        print(f"Error in combined analysis: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


# ==================== DOSEN CONTEXT - ALL TOOLS ====================

def get_nilai_permatkul_by_dosen_context(prodi_id: int, semester: int = None) -> dict:
    """
    Ambil semua nilai matakuliah untuk mahasiswa dalam prodi tertentu (dosen context).
    
    Args:
        prodi_id: ID program studi (REQUIRED - dosen context)
        semester: Filter by semester tertentu (optional)
    
    Returns:
        Nilai grouped per course dengan statistik per course untuk dosen's students
    """
    try:
        session = SessionLocal()
        
        filters = [Mahasiswa.prodi_id == prodi_id]
        if semester:
            filters.append(NilaiMatkulMahasiswa.semester == semester)
        
        query = session.query(
            NilaiMatkulMahasiswa,
            Mahasiswa,
            MataKuliah
        ).join(
            Mahasiswa,
            NilaiMatkulMahasiswa.mahasiswa_id == Mahasiswa.id
        ).outerjoin(
            MataKuliah,
            NilaiMatkulMahasiswa.kode_mk == MataKuliah.kode_mk
        ).filter(and_(*filters))
        
        rows = query.all()
        
        if not rows:
            session.close()
            return {
                "status": "empty",
                "message": f"Tidak ada data nilai matakuliah untuk prodi {prodi_id}"
            }
        
        # Group by course
        courses = {}
        prodi_info = None
        
        for nilai, mahasiswa, matkul in rows:
            if prodi_info is None:
                prodi_info = {
                    "prodi_id": mahasiswa.prodi_id,
                    "prodi_name": mahasiswa.prodi_name
                }
            
            kode_mk = nilai.kode_mk
            if kode_mk not in courses:
                courses[kode_mk] = {
                    "kode_mk": kode_mk,
                    "nama_matkul": matkul.nama_matkul if matkul else "Unknown",
                    "sks": matkul.sks if matkul else 0,
                    "semester": nilai.semester,
                    "mahasiswa_list": []
                }
            
            # Add mahasiswa to course
            courses[kode_mk]["mahasiswa_list"].append({
                "mahasiswa_id": mahasiswa.id,
                "nim": mahasiswa.nim,
                "nama": mahasiswa.nama,
                "nilai_angka": float(nilai.nilai_angka) if nilai.nilai_angka else 0.0,
                "nilai_huruf": nilai.nilai_huruf or "-",
                "bobot_nilai": float(nilai.bobot_nilai) if nilai.bobot_nilai else 0.0
            })
        
        # Calculate statistics per course
        courses_list = []
        for kode_mk, course_data in courses.items():
            nilai_list = [m["nilai_angka"] for m in course_data["mahasiswa_list"] if m["nilai_angka"] > 0]
            
            course_data["total_mahasiswa"] = len(course_data["mahasiswa_list"])
            course_data["rata_rata_nilai"] = round(sum(nilai_list) / len(nilai_list), 2) if nilai_list else 0.0
            course_data["nilai_tertinggi"] = round(max(nilai_list), 2) if nilai_list else 0.0
            course_data["nilai_terendah"] = round(min(nilai_list), 2) if nilai_list else 0.0
            
            courses_list.append(course_data)
        
        session.close()
        
        return {
            "status": "success",
            "prodi": prodi_info,
            "total_courses": len(courses_list),
            "total_records": len(rows),
            "filter": {
                "prodi_id": prodi_id,
                "semester": semester
            },
            "courses": courses_list
        }
    
    except Exception as e:
        print(f"Error querying nilai permatkul by dosen context: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_nilai_persemester_by_dosen_context(prodi_id: int, semester: int = None) -> dict:
    """
    Ambil semua nilai per semester untuk mahasiswa dalam prodi tertentu (dosen context).
    
    Args:
        prodi_id: ID program studi (REQUIRED - dosen context)
        semester: Filter by semester tertentu (optional)
    
    Returns:
        Nilai grouped per semester dengan statistik GPA untuk dosen's students
    """
    try:
        session = SessionLocal()
        
        filters = [Mahasiswa.prodi_id == prodi_id]
        if semester:
            filters.append(NilaiMatkulMahasiswa.semester == semester)
        
        query = session.query(
            NilaiMatkulMahasiswa,
            Mahasiswa
        ).join(
            Mahasiswa,
            NilaiMatkulMahasiswa.mahasiswa_id == Mahasiswa.id
        ).filter(and_(*filters))
        
        rows = query.all()
        
        if not rows:
            session.close()
            return {
                "status": "empty",
                "message": f"Tidak ada data nilai persemester untuk prodi {prodi_id}"
            }
        
        # Group by semester and mahasiswa
        semesters_data = {}
        prodi_info = None
        
        for nilai, mahasiswa in rows:
            if prodi_info is None:
                prodi_info = {
                    "prodi_id": mahasiswa.prodi_id,
                    "prodi_name": mahasiswa.prodi_name
                }
            
            semester_key = f"Semester {nilai.semester} (TA {nilai.tahun_ajaran})"
            
            if semester_key not in semesters_data:
                semesters_data[semester_key] = {
                    "semester": nilai.semester,
                    "tahun_ajaran": nilai.tahun_ajaran,
                    "mahasiswa_list": {}
                }
            
            mhs_id = mahasiswa.id
            if mhs_id not in semesters_data[semester_key]["mahasiswa_list"]:
                semesters_data[semester_key]["mahasiswa_list"][mhs_id] = {
                    "mahasiswa_id": mahasiswa.id,
                    "nim": mahasiswa.nim,
                    "nama": mahasiswa.nama,
                    "nilai_list": []
                }
            
            semesters_data[semester_key]["mahasiswa_list"][mhs_id]["nilai_list"].append({
                "kode_mk": nilai.kode_mk,
                "nilai_angka": float(nilai.nilai_angka) if nilai.nilai_angka else 0.0,
                "nilai_huruf": nilai.nilai_huruf or "-"
            })
        
        # Calculate GPA per mahasiswa per semester
        for semester_key in semesters_data:
            for mhs in semesters_data[semester_key]["mahasiswa_list"].values():
                nilai_list = [v["nilai_angka"] for v in mhs["nilai_list"] if v["nilai_angka"] > 0]
                mhs["gpa"] = round(sum(nilai_list) / len(nilai_list), 2) if nilai_list else 0.0
                mhs["total_courses"] = len(mhs["nilai_list"])
                del mhs["nilai_list"]  # Remove detail, keep only summary
            
            # Convert dict to list and calculate semester statistics
            mhs_list = list(semesters_data[semester_key]["mahasiswa_list"].values())
            semesters_data[semester_key]["mahasiswa_list"] = mhs_list
            
            gpa_list = [m["gpa"] for m in mhs_list]
            semesters_data[semester_key]["total_mahasiswa"] = len(mhs_list)
            semesters_data[semester_key]["rata_rata_gpa"] = round(sum(gpa_list) / len(gpa_list), 2) if gpa_list else 0.0
            semesters_data[semester_key]["gpa_tertinggi"] = round(max(gpa_list), 2) if gpa_list else 0.0
            semesters_data[semester_key]["gpa_terendah"] = round(min(gpa_list), 2) if gpa_list else 0.0
        
        session.close()
        
        return {
            "status": "success",
            "prodi": prodi_info,
            "total_semesters": len(semesters_data),
            "total_records": len(rows),
            "filter": {
                "prodi_id": prodi_id,
                "semester": semester
            },
            "semesters": list(semesters_data.values())
        }
    
    except Exception as e:
        print(f"Error querying nilai persemester by dosen context: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_combined_analisis_by_dosen_context(prodi_id: int, semester: int = None) -> dict:
    """
    Ambil analisis kombinasi nilai per matakuliah dan per semester untuk dosen.
    Memberikan gambaran menyeluruh dari performa akademik mahasiswa di prodi tertentu.
    
    Args:
        prodi_id: ID program studi (REQUIRED - dosen context)
        semester: Filter by semester tertentu (optional)
    
    Returns:
        Combined analysis dengan permatkul dan persemester untuk dosen's students
    """
    try:
        # Get data per matakuliah
        matkul_data = get_nilai_permatkul_by_dosen_context(prodi_id, semester)
        
        # Get data per semester
        semester_data = get_nilai_persemester_by_dosen_context(prodi_id, semester)
        
        if matkul_data["status"] != "success" or semester_data["status"] != "success":
            return {
                "status": "error",
                "message": "Gagal mengambil data kombinasi nilai untuk dosen context"
            }
        
        return {
            "status": "success",
            "prodi": matkul_data.get("prodi"),
            "filter": {
                "prodi_id": prodi_id,
                "semester": semester
            },
            "per_matakuliah": {
                "total_courses": matkul_data.get("total_courses"),
                "courses": matkul_data.get("courses", [])
            },
            "per_semester": {
                "total_semesters": semester_data.get("total_semesters"),
                "semesters": semester_data.get("semesters", [])
            }
        }
    
    except Exception as e:
        print(f"Error in combined analysis by dosen context: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}

