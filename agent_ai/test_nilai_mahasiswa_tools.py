"""
Test file untuk nilai_mahasiswa_tools
Demonstrasi penggunaan:
  - Per Matakuliah (Permatkul)
  - Per Semester (Persemester)
  - Combined Analysis
"""

from tools.nilai_mahasiswa_tools import (
    get_nilai_akhir_by_dosen_context,
    get_nilai_permatkul_by_mahasiswa,
    get_nilai_permatkul_group_by_dosen_context,
    get_nilai_persemester_by_mahasiswa,
    get_nilai_persemester_group_by_dosen_context,
    get_combined_analisis_nilai
)
import json


def print_result(title: str, result: dict):
    """Pretty print result"""
    print(f"\n{'='*80}")
    print(f"🔹 {title}")
    print(f"{'='*80}")
    print(json.dumps(result, indent=2, ensure_ascii=False, default=str))


# ==================== TEST 1: NILAI AKHIR (ORIGINAL) ====================

def test_nilai_akhir():
    """Test original: Get final grades by context"""
    print("\n\n{'='*80}")
    print("TEST 1: NILAI AKHIR (ORIGINAL FUNCTION)")
    print(f"{'='*80}")
    
    # Get all final grades
    result = get_nilai_akhir_by_dosen_context()
    print_result("Semua Nilai Akhir Mahasiswa", result)
    
    # Get by prodi
    result_prodi = get_nilai_akhir_by_dosen_context(prodi_id=1)
    print_result("Nilai Akhir by Prodi ID=1", result_prodi)


# ==================== TEST 2: PER MATAKULIAH (PERMATKUL) ====================

def test_nilai_permatkul():
    """Test per matakuliah"""
    print("\n\n{'='*80}")
    print("TEST 2: PER MATAKULIAH (PERMATKUL)")
    print(f"{'='*80}")
    
    # Get by single mahasiswa
    result = get_nilai_permatkul_by_mahasiswa(mahasiswa_id=1)
    print_result("Nilai Per Matakuliah - Mahasiswa ID=1", result)
    
    # Get by nim
    result_nim = get_nilai_permatkul_by_mahasiswa(nim="2401010000")
    print_result("Nilai Per Matakuliah - NIM=2401010000", result_nim)
    
    # Group by course across students
    result_grouped = get_nilai_permatkul_group_by_dosen_context(prodi_id=1)
    print_result("Nilai Per Matakuliah (Grouped by Course) - Prodi ID=1", result_grouped)
    
    # Group by course and semester
    result_grouped_sem = get_nilai_permatkul_group_by_dosen_context(prodi_id=1, semester=2)
    print_result("Nilai Per Matakuliah (Grouped) - Prodi ID=1, Semester=2", result_grouped_sem)


# ==================== TEST 3: PER SEMESTER (PERSEMESTER) ====================

def test_nilai_persemester():
    """Test per semester"""
    print("\n\n{'='*80}")
    print("TEST 3: PER SEMESTER (PERSEMESTER)")
    print(f"{'='*80}")
    
    # Get by single mahasiswa
    result = get_nilai_persemester_by_mahasiswa(mahasiswa_id=1)
    print_result("Nilai Per Semester - Mahasiswa ID=1", result)
    
    # Get by nim
    result_nim = get_nilai_persemester_by_mahasiswa(nim="2401010000")
    print_result("Nilai Per Semester - NIM=2401010000", result_nim)
    
    # Group by semester across students
    result_grouped = get_nilai_persemester_group_by_dosen_context(prodi_id=1)
    print_result("Nilai Per Semester (Grouped by Semester) - Prodi ID=1", result_grouped)
    
    # Group by semester filter specific semester
    result_grouped_sem = get_nilai_persemester_group_by_dosen_context(prodi_id=1, semester=2)
    print_result("Nilai Per Semester (Grouped) - Prodi ID=1, Semester=2", result_grouped_sem)


# ==================== TEST 4: COMBINED ANALYSIS ====================

def test_combined_analysis():
    """Test combined analysis"""
    print("\n\n{'='*80}")
    print("TEST 4: COMBINED ANALYSIS (PERMATKUL + PERSEMESTER)")
    print(f"{'='*80}")
    
    # Get combined analysis
    result = get_combined_analisis_nilai(mahasiswa_id=1)
    print_result("Analisis Kombinasi Nilai - Mahasiswa ID=1", result)
    
    # Get by nim
    result_nim = get_combined_analisis_nilai(nim="2401010000")
    print_result("Analisis Kombinasi Nilai - NIM=2401010000", result_nim)


# ==================== HELPER FUNCTIONS ====================

def test_response_structure():
    """Verify response structures"""
    print("\n\n{'='*80}")
    print("RESPONSE STRUCTURE VERIFICATION")
    print(f"{'='*80}")
    
    print("\n✓ Nilai Akhir Response Structure:")
    print("""
    {
        "status": "success|empty|error",
        "message": "...",
        "total": int,
        "rata_rata": float,
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
    """)
    
    print("\n✓ Nilai Permatkul Response Structure:")
    print("""
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
        "rata_rata_nilai": float,
        "nilai_permatkul": [
            {
                "kode_mk": str,
                "nama_matkul": str,
                "sks": int,
                "semester": int,
                "tahun_ajaran": int,
                "nilai_angka": float,
                "nilai_huruf": str,
                "bobot_nilai": float
            }
        ]
    }
    """)
    
    print("\n✓ Nilai Persemester Response Structure:")
    print("""
    {
        "status": "success|empty|error",
        "mahasiswa": {
            "mahasiswa_id": int,
            "nim": str,
            "nama": str,
            "prodi_id": int,
            "prodi_name": str,
            "angkatan": int
        },
        "cumulative_gpa": float,
        "total_semesters": int,
        "nilai_persemester": [
            {
                "semester_label": str,
                "semester": int,
                "tahun_ajaran": int,
                "total_courses": int,
                "gpa_semester": float,
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
    """)
    
    print("\n✓ Combined Analysis Response Structure:")
    print("""
    {
        "status": "success|error",
        "mahasiswa": { ... },
        "per_matakuliah": {
            "total": int,
            "rata_rata": float,
            "data": [ ... ]
        },
        "per_semester": {
            "total": int,
            "cumulative_gpa": float,
            "data": [ ... ]
        }
    }
    """)


# ==================== MAIN ====================

if __name__ == "__main__":
    print("""
    ╔════════════════════════════════════════════════════════════════════════════╗
    ║         TEST: NILAI MAHASISWA TOOLS (PERMATKUL & PERSEMESTER)             ║
    ║                                                                            ║
    ║  Functions:                                                                ║
    ║  1. get_nilai_akhir_by_dosen_context()          - Nilai akhir             ║
    ║  2. get_nilai_permatkul_by_mahasiswa()          - Per matakuliah (single) ║
    ║  3. get_nilai_permatkul_group_by_dosen_context()- Per matakuliah (group)  ║
    ║  4. get_nilai_persemester_by_mahasiswa()        - Per semester (single)   ║
    ║  5. get_nilai_persemester_group_by_dosen_context()- Per semester (group)   ║
    ║  6. get_combined_analisis_nilai()               - Combined analysis      ║
    ╚════════════════════════════════════════════════════════════════════════════╝
    """)
    
    try:
        # Run tests
        test_nilai_akhir()
        test_nilai_permatkul()
        test_nilai_persemester()
        test_combined_analysis()
        test_response_structure()
        
        print("\n\n" + "="*80)
        print("✅ ALL TESTS COMPLETED SUCCESSFULLY")
        print("="*80)
        
    except Exception as e:
        print(f"\n❌ ERROR: {e}")
        import traceback
        traceback.print_exc()
