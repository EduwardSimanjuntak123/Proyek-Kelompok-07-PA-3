"""
Course/Matakuliah Tools - Get and display courses
"""
from tools.db_tool import engine
from sqlalchemy import text

def get_courses_by_context(context, semester_filter=None):
    """
    Get list of all matakuliah (courses) for the given context, optionally filtered by semester
    
    Args:
        context: dict with prodi_id, angkatan, kpa_id, etc.
        semester_filter: optional int (1-8) to filter courses by semester
    
    Returns:
        list of courses with info and semester
    """
    if not context:
        return []
    
    prodi_id = context.get('prodi_id')
    
    if not prodi_id:
        return []
    
    try:
        query = """
        SELECT DISTINCT 
            mk.kode_mk,
            mk.nama_matkul,
            mk.semester,
            COUNT(DISTINCT nmk.mahasiswa_id) as jumlah_mahasiswa,
            AVG(CAST(nmk.nilai_angka AS DECIMAL(5,2))) as rata_rata_nilai
        FROM nilai_matkul_mahasiswa nmk
        JOIN mata_kuliah mk ON nmk.kode_mk = mk.kode_mk
        JOIN mahasiswa m ON nmk.mahasiswa_id = m.id
        WHERE m.prodi_id = :prodi_id
        """
        
        params = {'prodi_id': prodi_id}
        
        if semester_filter:
            query += " AND mk.semester = :semester"
            params['semester'] = semester_filter
        
        query += """
        GROUP BY mk.kode_mk, mk.nama_matkul, mk.semester
        ORDER BY mk.semester, mk.nama_matkul
        """
        
        with engine.connect() as conn:
            result = conn.execute(text(query), params)
            rows = result.fetchall()
            
            courses = []
            for row in rows:
                courses.append({
                    'kode_mk': row[0],
                    'nama_mk': row[1],
                    'semester': row[2],
                    'jumlah_mahasiswa': row[3],
                    'rata_rata_nilai': float(row[4]) if row[4] else 0
                })
            
            return courses
    
    except Exception as e:
        print(f"[DB ERROR] get_courses_by_context: {str(e)}")
        return []


def format_courses_for_display(courses, show_rata_rata=False, group_by_semester=True):
    """
    Format courses into HTML table for display, optionally grouped by semester
    
    Args:
        courses: list of course dicts
        show_rata_rata: if True, show average scores; if False, show only basic info
        group_by_semester: if True, organize by semester
    
    Returns:
        HTML string
    """
    if not courses:
        return "<p>Tidak ada matakuliah ditemukan</p>"
    
    if not group_by_semester:
        # Simple flat list format
        html = '<div class="courses-result" style="padding: 15px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">'
        html += f'<h4>📚 Daftar Matakuliah ({len(courses)} total)</h4>'
        html += '<table style="width: 100%; border-collapse: collapse;">'
        html += '<thead style="background-color: #f0f0f0;"><tr>'
        html += '<th style="border: 1px solid #ddd; padding: 10px; text-align: left;">Kode MK</th>'
        html += '<th style="border: 1px solid #ddd; padding: 10px; text-align: left;">Nama Matakuliah</th>'
        if show_rata_rata:
            html += '<th style="border: 1px solid #ddd; padding: 10px; text-align: center;">Rata-rata Nilai</th>'
        html += '</tr></thead><tbody>'
        
        for course in courses:
            html += '<tr>'
            html += f'<td style="border: 1px solid #ddd; padding: 8px; font-weight: bold;">{course.get("kode_mk", "?")}</td>'
            html += f'<td style="border: 1px solid #ddd; padding: 8px;">{course.get("nama_mk", "?")}</td>'
            
            if show_rata_rata:
                avg_score = course.get("rata_rata_nilai", 0)
                avg_str = f"{avg_score:.2f}" if avg_score > 0 else "N/A"
                html += f'<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{avg_str}</td>'
            
            html += '</tr>'
        
        html += '</tbody></table>'
        html += '</div>'
        return html
    
    else:
        # Group by semester
        html = '<div class="courses-result" style="padding: 15px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">'
        
        html += f'<h4>📚 Daftar Matakuliah per Semester ({len(courses)} total)</h4>'
        
        # Group courses by semester
        semesters = {}
        for course in courses:
            sem = course.get('semester', 0)
            if sem not in semesters:
                semesters[sem] = []
            semesters[sem].append(course)
        
        # Display each semester
        for sem in sorted(semesters.keys()):
            sem_courses = semesters[sem]
            html += f'<div style="margin-top: 20px; margin-bottom: 15px;">'
            html += f'<h5 style="margin-bottom: 10px; color: #333; border-bottom: 2px solid #007bff; padding-bottom: 5px;">Semester {sem} ({len(sem_courses)} matakuliah)</h5>'
            html += '<table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">'
            html += '<thead style="background-color: #e8f4f8;"><tr>'
            html += '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Kode MK</th>'
            html += '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Nama Matakuliah</th>'
            if show_rata_rata:
                html += '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Rata-rata</th>'
            html += '</tr></thead><tbody>'
            
            for course in sem_courses:
                html += '<tr>'
                html += f'<td style="border: 1px solid #ddd; padding: 8px; font-weight: bold;">{course.get("kode_mk", "?")}</td>'
                html += f'<td style="border: 1px solid #ddd; padding: 8px;">{course.get("nama_mk", "?")}</td>'
                
                if show_rata_rata:
                    avg_score = course.get("rata_rata_nilai", 0)
                    avg_str = f"{avg_score:.2f}" if avg_score > 0 else "N/A"
                    html += f'<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{avg_str}</td>'
                
                html += '</tr>'
            
            html += '</tbody></table>'
            html += '</div>'
        
        html += '</div>'
        return html


# ========== NEW: Dynamic Course Query Functions ==========

def get_courses_by_semester(semester, prodi_id=None, context=None):
    """
    Get all courses for a specific semester
    
    Args:
        semester: Semester number (1-8)
        prodi_id: Study program ID
        context: Dosen context to extract prodi_id if not provided
        
    Returns:
        List of courses in that semester
    """
    if not prodi_id and context:
        prodi_id = context.get("prodi_id") if isinstance(context, dict) else getattr(context, "prodi_id", None)
    
    if not prodi_id:
        return []
    
    try:
        query = """
            SELECT id, kode_mk, nama_matkul, sks, semester, prodi_id
            FROM mata_kuliah
            WHERE semester = :semester AND prodi_id = :prodi_id
            ORDER BY kode_mk
        """
        
        with engine.connect() as conn:
            result = conn.execute(text(query), {"semester": semester, "prodi_id": prodi_id})
            rows = result.fetchall()
            
            courses = []
            for row in rows:
                courses.append({
                    "id": row[0],
                    "kode_mk": row[1],
                    "nama_matkul": row[2],
                    "sks": row[3],
                    "semester": row[4],
                })
            
            return courses
    except Exception as e:
        print(f"[COURSE_TOOL] Error getting courses for semester {semester}: {e}")
        return []


def get_courses_by_semesters(semesters, prodi_id=None, context=None):
    """
    Get courses for multiple semesters
    
    Args:
        semesters: List of semester numbers (e.g., [4, 5])
        prodi_id: Study program ID
        context: Dosen context
        
    Returns:
        Dict with semester as key, list of courses as value
    """
    result = {}
    for semester in semesters:
        result[semester] = get_courses_by_semester(semester, prodi_id, context)
    return result
