"""Debug script untuk cek nilai students spesifik di database"""

from core.database import SessionLocal
from models.mahasiswa import Mahasiswa
from models.nilai_matkul_mahasiswa import NilaiMatkulMahasiswa
from sqlalchemy import and_

# Students yang muncul dengan 0.0
nims_to_check = ["11419066", "11419010", "11419049"]  # Ervina, Sahat, Zico

session = SessionLocal()
try:
    for nim in nims_to_check:
        print(f"\n{'='*80}")
        print(f"🔍 Checking NIM: {nim}")
        print('='*80)
        
        # Get mahasiswa
        mhs = session.query(Mahasiswa).filter(Mahasiswa.nim == nim).first()
        if not mhs:
            print(f"❌ Mahasiswa tidak ditemukan di database")
            continue
        
        print(f"✓ Mahasiswa: {mhs.nama} (ID: {mhs.id})")
        print(f"  Prodi ID: {mhs.prodi_id}")
        print(f"  Angkatan: {mhs.angkatan}")
        
        # Get ALL nilai for this mahasiswa
        all_nilai = session.query(NilaiMatkulMahasiswa).filter(
            NilaiMatkulMahasiswa.mahasiswa_id == mhs.user_id
        ).all()
        
        print(f"\n📚 Total nilai entries di database: {len(all_nilai)}")
        
        if all_nilai:
            print("\n  Semester breakdown:")
            semesters_dict = {}
            for nilai in all_nilai:
                if nilai.semester not in semesters_dict:
                    semesters_dict[nilai.semester] = []
                semesters_dict[nilai.semester].append({
                    'kode_mk': nilai.kode_mk,
                    'nilai_angka': nilai.nilai_angka,
                    'tahun_ajaran': nilai.tahun_ajaran
                })
            
            for sem in sorted(semesters_dict.keys()):
                courses = semesters_dict[sem]
                print(f"    Semester {sem}: {len(courses)} courses")
                for course in courses:
                    print(f"      - {course['kode_mk']}: {course['nilai_angka']} (TA {course['tahun_ajaran']})")
        else:
            print("  ❌ Tidak ada nilai di database")
        
        # Check specifically for semester 1,2,3 (PA-2 requirement)
        print(f"\n  Nilai untuk semester 1,2,3 (PA-2) dengan mahasiswa_id = {mhs.user_id}:")
        nilai_pa2 = session.query(NilaiMatkulMahasiswa).filter(
            and_(
                NilaiMatkulMahasiswa.mahasiswa_id == mhs.user_id,
                NilaiMatkulMahasiswa.semester.in_([1, 2, 3])
            )
        ).all()
        
        print(f"    Found: {len(nilai_pa2)} entries")
        if nilai_pa2:
            for nilai in nilai_pa2:
                print(f"      - Semester {nilai.semester}: {nilai.kode_mk} = {nilai.nilai_angka}")
        else:
            print(f"    ❌ TIDAK ADA NILAI untuk semester 1,2,3")
            
except Exception as e:
    print(f"❌ Error: {str(e)}")
    import traceback
    traceback.print_exc()
finally:
    session.close()

print(f"\n{'='*80}")
print("✓ Debug selesai")
print('='*80)
