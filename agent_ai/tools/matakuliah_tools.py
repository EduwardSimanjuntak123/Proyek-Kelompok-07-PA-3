"""
Tools untuk query mata kuliah dari database
"""

from core.database import SessionLocal
from models.matakuliah import MataKuliah


def get_matakuliah_list() -> dict:
    """Ambil daftar mata kuliah dari database"""
    try:
        session = SessionLocal()
        matakuiahs = session.query(MataKuliah).all()
        session.close()
        
        if not matakuiahs:
            return {"status": "empty", "message": "Belum ada data mata kuliah"}
        
        matakuliah_data = [
            {
                "id": m.id,
                "kode_mk": m.kode_mk,
                "nama_matkul": m.nama_matkul,
                "sks": m.sks,
                "semester": m.semester,
                "prodi_id": m.prodi_id
            }
            for m in matakuiahs
        ]
        
        return {
            "status": "success",
            "total": len(matakuliah_data),
            "data": matakuliah_data
        }
    except Exception as e:
        print(f"Error querying matakuliah: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def search_matakuliah(nama: str = None, kode: str = None) -> dict:
    """Search mata kuliah berdasarkan nama atau kode"""
    try:
        session = SessionLocal()
        query = session.query(MataKuliah)
        
        if nama:
            query = query.filter(MataKuliah.nama_matkul.ilike(f"%{nama}%"))
        if kode:
            query = query.filter(MataKuliah.kode_mk.like(f"%{kode}%"))
        
        matakuiahs = query.all()
        session.close()
        
        if not matakuiahs:
            return {"status": "empty", "message": "Tidak ada mata kuliah yang sesuai dengan kriteria pencarian"}
        
        matakuliah_data = [
            {
                "id": m.id,
                "kode_mk": m.kode_mk,
                "nama_matkul": m.nama_matkul,
                "sks": m.sks,
                "semester": m.semester,
                "prodi_id": m.prodi_id
            }
            for m in matakuiahs
        ]
        
        return {
            "status": "success",
            "total": len(matakuliah_data),
            "data": matakuliah_data
        }
    except Exception as e:
        print(f"Error searching matakuliah: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_matakuliah_by_semester(semester: int) -> dict:
    """Ambil daftar mata kuliah berdasarkan semester"""
    try:
        session = SessionLocal()
        matakuiahs = session.query(MataKuliah).filter(MataKuliah.semester == semester).all()
        session.close()
        
        if not matakuiahs:
            return {"status": "empty", "message": f"Tidak ada mata kuliah di semester {semester}"}
        
        matakuliah_data = [
            {
                "id": m.id,
                "kode_mk": m.kode_mk,
                "nama_matkul": m.nama_matkul,
                "sks": m.sks,
                "semester": m.semester,
                "prodi_id": m.prodi_id
            }
            for m in matakuiahs
        ]
        
        return {
            "status": "success",
            "total": len(matakuliah_data),
            "semester": semester,
            "data": matakuliah_data
        }
    except Exception as e:
        print(f"Error querying matakuliah by semester: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_matakuliah_by_prodi(prodi_id: int) -> dict:
    """Ambil daftar mata kuliah berdasarkan prodi"""
    try:
        session = SessionLocal()
        matakuiahs = session.query(MataKuliah).filter(MataKuliah.prodi_id == prodi_id).all()
        session.close()
        
        if not matakuiahs:
            return {"status": "empty", "message": f"Tidak ada mata kuliah di prodi {prodi_id}"}
        
        matakuliah_data = [
            {
                "id": m.id,
                "kode_mk": m.kode_mk,
                "nama_matkul": m.nama_matkul,
                "sks": m.sks,
                "semester": m.semester,
                "prodi_id": m.prodi_id
            }
            for m in matakuiahs
        ]
        
        return {
            "status": "success",
            "total": len(matakuliah_data),
            "prodi_id": prodi_id,
            "data": matakuliah_data
        }
    except Exception as e:
        print(f"Error querying matakuliah by prodi: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}
