"""
Tools untuk query dosen dari database
"""

from core.database import SessionLocal
from models.dosen import Dosen


def get_dosen_list() -> dict:
    """
    Ambil daftar dosen dari database
    
    Returns:
        Dict dengan status, total, dan data dosen
    """
    try:
        session = SessionLocal()
        dosens = session.query(Dosen).all()
        session.close()
        
        if not dosens:
            return {"status": "empty", "message": "Belum ada data dosen"}
        
        dosens_data = [
            {
                "id": d.id,
                "nama": d.nama,
                "email": d.email or "N/A",
                "prodi": d.prodi or "N/A",
                "jabatan_akademik": d.jabatan_akademik or "N/A",
                "nidn": d.nidn or "N/A"
            }
            for d in dosens
        ]
        
        return {
            "status": "success",
            "total": len(dosens_data),
            "data": dosens_data
        }
    except Exception as e:
        print(f"Error querying dosen: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def search_dosen(nama: str = None, prodi: str = None) -> dict:
    """
    Search dosen berdasarkan nama atau prodi
    """
    try:
        session = SessionLocal()
        query = session.query(Dosen)
        
        if nama:
            query = query.filter(Dosen.nama.ilike(f"%{nama}%"))
        if prodi:
            query = query.filter(Dosen.prodi.ilike(f"%{prodi}%"))
        
        dosens = query.all()
        session.close()
        
        if not dosens:
            return {"status": "empty", "message": "Tidak ada dosen yang sesuai dengan kriteria pencarian"}
        
        dosens_data = [
            {
                "id": d.id,
                "nama": d.nama,
                "email": d.email or "N/A",
                "prodi": d.prodi or "N/A",
                "jabatan_akademik": d.jabatan_akademik or "N/A",
                "nidn": d.nidn or "N/A"
            }
            for d in dosens
        ]
        
        return {
            "status": "success",
            "total": len(dosens_data),
            "data": dosens_data
        }
    except Exception as e:
        print(f"Error searching dosen: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_dosen_by_prodi(prodi: str) -> dict:
    """
    Ambil daftar dosen berdasarkan prodi
    """
    try:
        session = SessionLocal()
        dosens = session.query(Dosen).filter(Dosen.prodi.ilike(f"%{prodi}%")).all()
        session.close()
        
        if not dosens:
            return {"status": "empty", "message": f"Tidak ada dosen di prodi {prodi}"}
        
        dosens_data = [
            {
                "id": d.id,
                "nama": d.nama,
                "email": d.email or "N/A",
                "prodi": d.prodi or "N/A",
                "jabatan_akademik": d.jabatan_akademik or "N/A",
                "nidn": d.nidn or "N/A"
            }
            for d in dosens
        ]
        
        return {
            "status": "success",
            "total": len(dosens_data),
            "prodi": prodi,
            "data": dosens_data
        }
    except Exception as e:
        print(f"Error querying dosen by prodi: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_dosen_by_dosen_context(prodi_id: int = None) -> dict:
    """
    Ambil daftar dosen berdasarkan prodi_id dari payload dosen
    
    Args:
        prodi_id: ID prodi dari dosenRole.prodi_id
    
    Returns:
        Dict dengan status, total, dan data dosen yang sesuai
    """
    try:
        session = SessionLocal()
        query = session.query(Dosen)
        
        # Filter berdasarkan prodi_id - kita perlu join dengan Prodi model
        if prodi_id:
            from models.prodi import Prodi
            query = query.join(Prodi, Dosen.prodi == Prodi.nama_prodi).filter(
                Prodi.id == prodi_id
            )
        
        dosens = query.all()
        session.close()
        
        if not dosens:
            return {
                "status": "empty",
                "message": f"Tidak ada dosen dengan prodi_id={prodi_id}" if prodi_id else "Tidak ada dosen"
            }
        
        dosens_data = [
            {
                "id": d.id,
                "nama": d.nama,
                "email": d.email or "N/A",
                "prodi": d.prodi or "N/A",
                "jabatan_akademik": d.jabatan_akademik or "N/A",
                "nidn": d.nidn or "N/A"
            }
            for d in dosens
        ]
        
        return {
            "status": "success",
            "total": len(dosens_data),
            "filters": {
                "prodi_id": prodi_id
            },
            "data": dosens_data
        }
    except Exception as e:
        print(f"Error querying dosen by dosen context: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}
