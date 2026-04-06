"""
Tool untuk mengelola nilai matakuliah dan perhitungan skor
"""
from app.db import engine
from sqlalchemy import text
from tools.grouping_tool import group_students_with_constraints

def get_student_scores_by_category(students, kategori_pa):
    """
    Ambil nilai mahasiswa berdasarkan kategori_pa
    kategori_pa = 1: semester 1 saja
    kategori_pa = 2: semester 1, 2, 3
    kategori_pa = 3: semester 1, 2, 3, 4, 5
    
    Returns: List[{student_data, scores_by_semester, average_score}]
    """
    
    # Tentukan semester berdasarkan kategori_pa
    if kategori_pa == 1:
        semesters = [1]
    elif kategori_pa == 2:
        semesters = [1, 2, 3]
    elif kategori_pa == 3:
        semesters = [1, 2, 3, 4, 5]
    else:
        semesters = [1, 2, 3, 4, 5]  # Default semua
    
    result = []
    
    for student in students:
        # Join melalui user_id: mahasiswa.user_id = nilai_matkul_mahasiswa.mahasiswa_id
        user_id = student.get("user_id")
        
        if not user_id:
            continue
        
        # Query nilai matakuliah dari database
        with engine.connect() as conn:
            query = text("""
                SELECT 
                    id,
                    mahasiswa_id,
                    kode_mk,
                    nilai_angka,
                    nilai_huruf,
                    bobot_nilai,
                    semester,
                    tahun_ajaran
                FROM nilai_matkul_mahasiswa
                WHERE mahasiswa_id = :mahasiswa_id 
                AND semester IN :semesters
                ORDER BY semester ASC
            """)
            
            scores = conn.execute(
                query,
                {
                    "mahasiswa_id": user_id,
                    "semesters": tuple(semesters)
                }
            ).fetchall()
            
            # Organize scores by semester
            scores_by_semester = {}
            all_scores = []
            
            for row in scores:
                sem = row[6]  # semester column
                nilai = float(row[3])  # nilai_angka
                all_scores.append(nilai)
                
                if sem not in scores_by_semester:
                    scores_by_semester[sem] = {
                        "kode_mk": [],
                        "nilai": [],
                        "rata_rata": 0
                    }
                
                scores_by_semester[sem]["kode_mk"].append(row[2])  # kode_mk
                scores_by_semester[sem]["nilai"].append(nilai)
            
            # Calculate average per semester
            for sem in scores_by_semester:
                values = scores_by_semester[sem]["nilai"]
                scores_by_semester[sem]["rata_rata"] = sum(values) / len(values) if values else 0
            
            # Calculate overall average
            average_score = sum(all_scores) / len(all_scores) if all_scores else 0
            
            result.append({
                "mahasiswa_id": user_id,
                "student_data": student,
                "scores_by_semester": scores_by_semester,
                "average_score": round(average_score, 2),
                "total_subjects": len(all_scores)
            })
    
    print(f"[SCORE] get_student_scores_by_category: {len(result)} students retrieved")
    return result


def get_class_average(student_scores):
    """
    Hitung rata-rata nilai keseluruhan kelas
    
    Returns: {class_average, std_dev, min_score, max_score}
    """
    if not student_scores:
        return {"class_average": 0, "std_dev": 0, "min_score": 0, "max_score": 0}
    
    scores = [s["average_score"] for s in student_scores]
    
    class_average = sum(scores) / len(scores)
    
    # Calculate standard deviation
    variance = sum((x - class_average) ** 2 for x in scores) / len(scores)
    std_dev = variance ** 0.5
    
    min_score = min(scores)
    max_score = max(scores)
    
    result = {
        "class_average": round(class_average, 2),
        "std_dev": round(std_dev, 2),
        "min_score": round(min_score, 2),
        "max_score": round(max_score, 2),
        "total_students": len(scores)
    }
    
    return result


def _to_grouping_students(student_scores):
    """Convert score payload to student dicts for constrained grouping."""
    converted = []
    for item in student_scores:
        student_data = item.get("student_data", {})
        converted.append({
            "nama": student_data.get("nama"),
            "nim": student_data.get("nim"),
            "user_id": student_data.get("user_id"),
            "nilai_rata_rata": item.get("average_score", 0),
            "semesters": list(item.get("scores_by_semester", {}).keys())
        })
    return converted


def group_by_score_balance_with_constraints(
    student_scores,
    group_size=6,
    allow_deviation=0.5,
    must_pairs=None,
    avoid_pairs=None,
    shuffle=False
):
    """
    Group students by score while honoring must/avoid constraints.
    """
    must_pairs = must_pairs or []
    avoid_pairs = avoid_pairs or []

    class_stats = get_class_average(student_scores)
    class_average = class_stats["class_average"]

    students = _to_grouping_students(student_scores)
    if not students:
        return [], class_stats

    # Build high-low ordering to keep score distribution relatively balanced.
    sorted_students = sorted(students, key=lambda x: x.get("nilai_rata_rata", 0), reverse=True)
    ordered_students = []
    left = 0
    right = len(sorted_students) - 1
    while left <= right:
        ordered_students.append(sorted_students[left])
        left += 1
        if left <= right:
            ordered_students.append(sorted_students[right])
            right -= 1

    grouped_raw = group_students_with_constraints(
        ordered_students,
        group_size=group_size,
        avoid_pairs=avoid_pairs,
        must_pairs=must_pairs,
        shuffle=shuffle
    )

    result = []
    for group in grouped_raw:
        members = group.get("members", [])
        group_scores = [m.get("nilai_rata_rata", 0) for m in members if isinstance(m, dict)]
        group_average = sum(group_scores) / len(group_scores) if group_scores else 0

        result.append({
            "kelompok": group.get("kelompok"),
            "members": [
                {
                    "user_id": m.get("user_id"),
                    "nama": m.get("nama"),
                    "nim": m.get("nim"),
                    "nilai_rata_rata": m.get("nilai_rata_rata", 0),
                    "semesters": m.get("semesters", [])
                }
                for m in members
            ],
            "group_average": round(group_average, 2),
            "deviation_from_class": round(group_average - class_average, 2),
            "member_count": len(members)
        })

    print(
        f"[SCORE] group_by_score_balance_with_constraints: {len(result)} groups created "
        f"(must={len(must_pairs)}, avoid={len(avoid_pairs)})"
    )
    return result, class_stats


def group_by_score_balance(student_scores, group_size=6, allow_deviation=0.5):
    """
    Group students sehingga nilai rata-rata per kelompok seimbang
    dan tidak jauh dari rata-rata kelas
    
    allow_deviation: toleransi deviasi dari rata-rata kelas (dalam poin, misal 0.5 = ±0.5)
    
    Returns: [{kelompok, members, group_average}]
    """
    
    # Get class average
    class_stats = get_class_average(student_scores)
    class_average = class_stats["class_average"]
    
    # Sort students by score (descending)
    sorted_students = sorted(student_scores, key=lambda x: x["average_score"], reverse=True)
    total_students = len(sorted_students)
    num_groups = (total_students + group_size - 1) // group_size  # Ceiling division
    
    # Calculate target members per group and extra members
    base_size = total_students // num_groups
    extra_members = total_students % num_groups
    

    
    # Create groups with balanced member count using snake pattern
    groups = []
    student_idx = 0
    
    # Use snake/zigzag pattern: high-low-high-low for better balance
    for group_idx in range(num_groups):
        # Determine size for this group
        group_target_size = base_size + (1 if group_idx < extra_members else 0)
        
        group_members = []
        group_scores = []
        
        # Alternate between taking from top and bottom
        if group_idx % 2 == 0:  # Take from top (high scores)
            for _ in range(group_target_size):
                if student_idx < total_students:
                    student = sorted_students[student_idx]
                    group_members.append(student)
                    group_scores.append(student["average_score"])
                    student_idx += 1
        else:  # Take from bottom (low scores) in reverse
            temp_members = []
            for _ in range(group_target_size):
                if student_idx < total_students:
                    student = sorted_students[student_idx]
                    temp_members.append(student)
                    group_scores.append(student["average_score"])
                    student_idx += 1
            # Reverse order for this group to balance
            group_members = list(reversed(temp_members))
        
        if group_members:
            groups.append({
                "members": group_members,
                "scores": group_scores,
                "group_average": sum(group_scores) / len(group_scores) if group_scores else 0
            })
    
    # Format output
    result = []
    for idx, group in enumerate(groups, 1):
        members_data = []
        for student in group["members"]:
            # Debug: check student structure
            student_info = student.get("student_data", {})
            print(f"[SCORE_DEBUG] Student structure keys: {student_info.keys() if isinstance(student_info, dict) else 'NOT_DICT'}")
            print(f"[SCORE_DEBUG] Student info: {student_info}")
            
            members_data.append({
                "user_id": student_info.get("user_id"),
                "nama": student_info.get("nama"),
                "nim": student_info.get("nim"),
                "nilai_rata_rata": student["average_score"],
                "semesters": list(student["scores_by_semester"].keys())
            })
        
        result.append({
            "kelompok": idx,
            "members": members_data,
            "group_average": round(group["group_average"], 2),
            "deviation_from_class": round(group["group_average"] - class_average, 2),
            "member_count": len(members_data)
        })
    
    print(f"[SCORE] group_by_score_balance: {len(result)} groups created (avg members: {sum(g['member_count'] for g in result) // len(result)})")
    
    return result, class_stats


def _find_student_by_name_fuzzy(students, search_name):
    """
    Fuzzy matching untuk cari mahasiswa by name
    Levels:
    1. Exact match (case-insensitive)
    2. Partial match (substring both ways)
    3. First name match with tolerance
    4. Last name matching
    """
    search_lower = search_name.lower().strip()
    
    # Level 1: Exact match
    for student in students:
        if student.get("nama", "").lower() == search_lower:
            return student
    
    # Level 2: Partial match
    for student in students:
        student_name = student.get("nama", "").lower()
        if search_lower in student_name or student_name in search_lower:
            return student
    
    # Level 3: First name partial with tolerance
    search_parts = search_lower.split()
    for student in students:
        student_parts = student.get("nama", "").lower().split()
        for search_part in search_parts:
            for student_part in student_parts:
                # Fuzzy: check if starts with same letter and similar
                if len(search_part) >= 3 and len(student_part) >= 3:
                    if student_part.startswith(search_part[0]) and \
                       abs(len(search_part) - len(student_part)) <= 2:
                        return student
    
    # Level 4: Last name matching for surnames
    if len(search_parts) > 0:
        last_search = search_parts[-1]
        for student in students:
            student_parts = student.get("nama", "").lower().split()
            if len(student_parts) > 0:
                if student_parts[-1] == last_search or \
                   student_parts[-1].startswith(last_search):
                    return student
    
    return None


def get_student_grades_detail(students, search_name=None, semester_filter=None):
    """
    Ambil detail nilai mahasiswa (satu atau semua)
    
    Parameters:
    - students: list of mahasiswa dengan user_id
    - search_name: nama mahasiswa (jika None, ambil semua)
    - semester_filter: list semester atau None untuk semua
    
    Returns: {
        "students": [{
            "nama": str,
            "nim": str,
            "nilai_rata_rata": float,
            "by_semester": {
                1: {"kode_mk": [...], "nilai": [...], "rata_rata": float},
                2: {...}
            }
        }]
    }
    """
    result_students = []
    
    # Filter students
    if search_name:
        found_student = _find_student_by_name_fuzzy(students, search_name)
        if not found_student:
            return {"students": [], "message": f"Mahasiswa '{search_name}' tidak ditemukan"}
        students_to_process = [found_student]
    else:
        students_to_process = students
    
    # Ambil nilai untuk setiap student
    for student in students_to_process:
        user_id = student.get("user_id")
        
        if not user_id:
            continue
        
        # Query nilai dari database
        with engine.connect() as conn:
            query_str = """
                SELECT 
                    kode_mk,
                    nilai_angka,
                    nilai_huruf,
                    semester,
                    bobot_nilai
                FROM nilai_matkul_mahasiswa
                WHERE mahasiswa_id = :mahasiswa_id
                ORDER BY semester ASC
            """
            
            scores = conn.execute(
                text(query_str),
                {"mahasiswa_id": user_id}
            ).fetchall()
            
            # Organize by semester
            by_semester = {}
            all_scores = []
            
            for row in scores:
                kode_mk = row[0]
                nilai = float(row[1])
                semester = int(row[3])
                
                # Filter by semester jika ada
                if semester_filter and semester not in semester_filter:
                    continue
                
                all_scores.append(nilai)
                
                if semester not in by_semester:
                    by_semester[semester] = {
                        "kode_mk": [],
                        "nilai": [],
                        "rata_rata": 0
                    }
                
                by_semester[semester]["kode_mk"].append(kode_mk)
                by_semester[semester]["nilai"].append(nilai)
            
            # Calculate semester averages
            for sem in by_semester:
                nilai_list = by_semester[sem]["nilai"]
                by_semester[sem]["rata_rata"] = round(sum(nilai_list) / len(nilai_list), 2) if nilai_list else 0
            
            # Calculate overall average
            overall_avg = round(sum(all_scores) / len(all_scores), 2) if all_scores else 0
            
            result_students.append({
                "nama": student.get("nama"),
                "nim": student.get("nim"),
                "nilai_rata_rata": overall_avg,
                "by_semester": by_semester
            })
    
    return {
        "students": result_students,
        "total_students": len(result_students)
    }


def get_class_grades_summary(students):
    """
    Ambil ringkasan nilai seluruh kelas (untuk display)
    
    Returns: [{
        "nama": str,
        "nim": str,
        "nilai_rata_rata": float,
        "status": "tinggi/sedang/rendah"
    }]
    """
    result = []
    all_averages = []
    
    # Collect all grades first
    for student in students:
        user_id = student.get("user_id")
        
        if not user_id:
            continue
        
        with engine.connect() as conn:
            query = text("""
                SELECT nilai_angka
                FROM nilai_matkul_mahasiswa
                WHERE mahasiswa_id = :mahasiswa_id
            """)
            
            scores = conn.execute(query, {"mahasiswa_id": user_id}).fetchall()
            
            if scores:
                values = [float(row[0]) for row in scores]
                avg = round(sum(values) / len(values), 2)
                all_averages.append(avg)
                
                result.append({
                    "nama": student.get("nama"),
                    "nim": student.get("nim"),
                    "nilai_rata_rata": avg
                })
    
    # Calculate class statistics for status
    if all_averages:
        class_avg = sum(all_averages) / len(all_averages)
        std_dev = (sum((x - class_avg) ** 2 for x in all_averages) / len(all_averages)) ** 0.5
        
        # Determine status for each student
        for student in result:
            nilai = student["nilai_rata_rata"]
            if nilai >= class_avg + std_dev:
                student["status"] = "Tinggi"
            elif nilai <= class_avg - std_dev:
                student["status"] = "Rendah"
            else:
                student["status"] = "Sedang"
    
    return result


def create_groups_by_score_with_details(students, kategori_pa, group_size=6):
    """
    Membuat kelompok berdasarkan nilai dengan detail informasi lengkap.
    
    Menampilkan:
    - Nama mahasiswa
    - NIM
    - Semester yang digunakan untuk penilaian
    - Rata-rata nilai
    - Informasi per grup dengan rata-rata group
    
    Args:
        students: List of student data with {user_id, nama, nim, ...}
        kategori_pa: Category for semesters (1=sem1, 2=sem1-3, 3=sem1-5)
        group_size: Target size per group (default 6)
    
    Returns: {
        "groups": [
            {
                "group_number": int,
                "members": [
                    {
                        "nama": str,
                        "nim": str,
                        "semesters": list,
                        "nilai_rata_rata": float
                    }
                ],
                "group_average": float,
                "member_count": int
            }
        ],
        "class_stats": {
            "class_average": float,
            "std_dev": float,
            "total_students": int,
            "total_groups": int
        },
        "summary_table": str  # Formatted table for display
    }
    """
    
    print(f"[SCORE_GROUPING] Starting score-based grouping for {len(students)} students")
    
    # Step 1: Get student scores by category
    print(f"[SCORE_GROUPING] Step 1: Fetching scores for kategori_pa={kategori_pa}")
    student_scores = get_student_scores_by_category(students, kategori_pa)
    
    if not student_scores:
        print("[SCORE_GROUPING] No student scores found!")
        return {
            "groups": [],
            "class_stats": {},
            "summary_table": "❌ Tidak ada data nilai untuk mahasiswa",
            "error": "No scores found"
        }
    
    print(f"[SCORE_GROUPING] Step 2: Retrieved scores for {len(student_scores)} students")
    
    # Step 2: Get class statistics
    class_stats = get_class_average(student_scores)
    print(f"[SCORE_GROUPING] Class average: {class_stats['class_average']}, StdDev: {class_stats['std_dev']}")
    
    # Step 3: Group students by score balance
    print(f"[SCORE_GROUPING] Step 3: Grouping {len(student_scores)} students into groups of {group_size}")
    groups_data, class_stats_calc = group_by_score_balance(student_scores, group_size=group_size)
    
    if not groups_data:
        print("[SCORE_GROUPING] Grouping failed!")
        return {
            "groups": [],
            "class_stats": class_stats,
            "summary_table": "❌ Gagal membuat kelompok",
            "error": "Grouping failed"
        }
    
    print(f"[SCORE_GROUPING] Step 4: Created {len(groups_data)} groups")
    
    # Step 4: Format groups with detailed information
    formatted_groups = []
    
    for group_idx, group in enumerate(groups_data, 1):
        members_detail = []
        
        for member in group.get("members", []):
            # Member already has the flattened structure from group_by_score_balance
            members_detail.append({
                "nama": member.get("nama", "N/A"),
                "nim": member.get("nim", "N/A"),
                "semesters": member.get("semesters", []),
                "nilai_rata_rata": member.get("nilai_rata_rata", 0),
                "semesters_str": ", ".join(str(s) for s in member.get("semesters", []))
            })
        
        formatted_groups.append({
            "group_number": group_idx,
            "members": members_detail,
            "group_average": round(group.get("group_average", 0), 2),
            "member_count": len(members_detail),
            "deviation": round(group.get("group_average", 0) - class_stats["class_average"], 2)
        })
    
    # Step 5: Build summary table for display
    summary_parts = []
    summary_parts.append(f"📊 **Pengelompokan Berbasis Nilai**\n")
    summary_parts.append(f"⏱️ **Statistik Kelas**")
    summary_parts.append(f"- Total Mahasiswa: {class_stats['total_students']}")
    summary_parts.append(f"- Rata-rata Kelas: {class_stats['class_average']}")
    summary_parts.append(f"- Std Dev: {class_stats['std_dev']}")
    summary_parts.append(f"- Total Kelompok: {len(formatted_groups)}")
    summary_parts.append("")
    
    # Add detailed group info
    for group in formatted_groups:
        summary_parts.append(f"**Kelompok {group['group_number']}** ({group['member_count']} anggota)")
        summary_parts.append(f"Rata-rata: {group['group_average']} (Deviasi: {group['deviation']:+.2f})")
        
        # Create table for this group
        summary_parts.append("| No | Nama | NIM | Semester | Nilai Rata-rata |")
        summary_parts.append("|---|---|---|---|---|")
        
        for idx, member in enumerate(group['members'], 1):
            summary_parts.append(
                f"| {idx} | {member['nama']} | {member['nim']} | "
                f"{member['semesters_str']} | {member['nilai_rata_rata']} |"
            )
        
        summary_parts.append("")
    
    summary_table = "\n".join(summary_parts)
    
    result = {
        "groups": formatted_groups,
        "class_stats": class_stats,
        "summary_table": summary_table,
        "total_groups": len(formatted_groups),
        "total_students": len(student_scores)
    }
    
    print(f"[SCORE_GROUPING] ✓ Successfully created {len(formatted_groups)} groups with score-based balancing")
    
    return result
