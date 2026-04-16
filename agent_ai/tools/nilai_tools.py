"""
Tools untuk query nilai matakuliah mahasiswa dari database
"""

from core.database import SessionLocal
from models.nilai_matkul_mahasiswa import NilaiMatkulMahasiswa
from models.mahasiswa import Mahasiswa


def get_nilai_mahasiswa(mahasiswa_id: int = None, nama: str = None) -> dict:
    """Ambil nilai matakuliah mahasiswa dari database"""
    try:
        session = SessionLocal()
        
        if mahasiswa_id:
            nilais = session.query(NilaiMatkulMahasiswa).filter(
                NilaiMatkulMahasiswa.mahasiswa_id == mahasiswa_id
            ).all()
        elif nama:
            mahasiswa = session.query(Mahasiswa).filter(
                Mahasiswa.nama.ilike(f"%{nama}%")
            ).first()
            if not mahasiswa:
                session.close()
                return {"status": "empty", "message": f"Mahasiswa {nama} tidak ditemukan"}
            nilais = session.query(NilaiMatkulMahasiswa).filter(
                NilaiMatkulMahasiswa.mahasiswa_id == mahasiswa.id
            ).all()
        else:
            session.close()
            return {"status": "error", "message": "Mohon sediakan mahasiswa_id atau nama"}
        
        session.close()
        
        if not nilais:
            return {"status": "empty", "message": "Tidak ada data nilai untuk mahasiswa tersebut"}
        
        nilai_data = [
            {
                "id": n.id,
                "mahasiswa_id": n.mahasiswa_id,
                "kode_mk": n.kode_mk,
                "nilai_angka": float(n.nilai_angka) if n.nilai_angka else 0,
                "nilai_huruf": n.nilai_huruf or "N/A",
                "bobot_nilai": float(n.bobot_nilai) if n.bobot_nilai else 0,
                "semester": n.semester or "N/A"
            }
            for n in nilais
        ]
        
        # Calculate average grade
        total_nilai = sum(n['nilai_angka'] for n in nilai_data)
        rata_rata = total_nilai / len(nilai_data) if nilai_data else 0
        
        return {
            "status": "success",
            "total": len(nilai_data),
            "rata_rata": round(rata_rata, 2),
            "data": nilai_data
        }
    except Exception as e:
        print(f"Error querying nilai mahasiswa: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_nilai_by_matakuliah(kode_mk: str) -> dict:
    """Ambil nilai dari semua mahasiswa untuk suatu matakuliah"""
    try:
        session = SessionLocal()
        nilais = session.query(NilaiMatkulMahasiswa).filter(
            NilaiMatkulMahasiswa.kode_mk.like(f"%{kode_mk}%")
        ).all()
        session.close()
        
        if not nilais:
            return {"status": "empty", "message": f"Tidak ada data nilai untuk matakuliah {kode_mk}"}
        
        nilai_data = [
            {
                "id": n.id,
                "mahasiswa_id": n.mahasiswa_id,
                "kode_mk": n.kode_mk,
                "nilai_angka": float(n.nilai_angka) if n.nilai_angka else 0,
                "nilai_huruf": n.nilai_huruf or "N/A",
                "bobot_nilai": float(n.bobot_nilai) if n.bobot_nilai else 0,
                "semester": n.semester or "N/A"
            }
            for n in nilais
        ]
        
        # Calculate average grade
        total_nilai = sum(n['nilai_angka'] for n in nilai_data)
        rata_rata = total_nilai / len(nilai_data) if nilai_data else 0
        
        return {
            "status": "success",
            "total": len(nilai_data),
            "matakuliah": kode_mk,
            "rata_rata_kelas": round(rata_rata, 2),
            "data": nilai_data
        }
    except Exception as e:
        print(f"Error querying nilai by matakuliah: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_nilai_by_semester(mahasiswa_id: int, semester: int) -> dict:
    """Ambil nilai mahasiswa untuk semester tertentu"""
    try:
        session = SessionLocal()
        nilais = session.query(NilaiMatkulMahasiswa).filter(
            NilaiMatkulMahasiswa.mahasiswa_id == mahasiswa_id,
            NilaiMatkulMahasiswa.semester == semester
        ).all()
        session.close()
        
        if not nilais:
            return {"status": "empty", "message": f"Tidak ada nilai untuk semester {semester}"}
        
        nilai_data = [
            {
                "id": n.id,
                "mahasiswa_id": n.mahasiswa_id,
                "kode_mk": n.kode_mk,
                "nilai_angka": float(n.nilai_angka) if n.nilai_angka else 0,
                "nilai_huruf": n.nilai_huruf or "N/A",
                "bobot_nilai": float(n.bobot_nilai) if n.bobot_nilai else 0,
                "semester": n.semester or "N/A"
            }
            for n in nilais
        ]
        
        total_nilai = sum(n['nilai_angka'] for n in nilai_data)
        rata_rata = total_nilai / len(nilai_data) if nilai_data else 0
        
        return {
            "status": "success",
            "total": len(nilai_data),
            "semester": semester,
            "rata_rata": round(rata_rata, 2),
            "data": nilai_data
        }
    except Exception as e:
        print(f"Error querying nilai by semester: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}
