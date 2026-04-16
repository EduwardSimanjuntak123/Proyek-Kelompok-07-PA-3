"""
Tools untuk query prodi dari database
"""

from core.database import SessionLocal
from models.prodi import Prodi


def get_prodi_list() -> dict:
    """Ambil daftar semua prodi dari database"""
    try:
        session = SessionLocal()
        prodis = session.query(Prodi).all()
        session.close()
        
        if not prodis:
            return {"status": "empty", "message": "Belum ada data prodi"}
        
        prodi_data = [
            {
                "id": p.id,
                "nama": p.nama_prodi,
                "maks_project": p.maks_project or "N/A"
            }
            for p in prodis
        ]
        
        return {
            "status": "success",
            "total": len(prodi_data),
            "data": prodi_data
        }
    except Exception as e:
        print(f"Error querying prodi: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def search_prodi(nama: str = None, jenjang: str = None) -> dict:
    """Search prodi berdasarkan nama"""
    try:
        session = SessionLocal()
        query = session.query(Prodi)
        
        if nama:
            query = query.filter(Prodi.nama_prodi.ilike(f"%{nama}%"))
        
        prodis = query.all()
        session.close()
        
        if not prodis:
            return {"status": "empty", "message": "Tidak ada prodi yang sesuai"}
        
        prodi_data = [
            {
                "id": p.id,
                "nama": p.nama_prodi,
                "maks_project": p.maks_project or "N/A"
            }
            for p in prodis
        ]
        
        return {
            "status": "success",
            "total": len(prodi_data),
            "data": prodi_data
        }
    except Exception as e:
        print(f"Error searching prodi: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_prodi_by_jenjang(jenjang: str) -> dict:
    """Ambil daftar prodi berdasarkan jenjang"""
    try:
        session = SessionLocal()
        # Since jenjang is not in model, just return all for now
        prodis = session.query(Prodi).all()
        session.close()
        
        if not prodis:
            return {"status": "empty", "message": f"Tidak ada prodi dengan jenjang {jenjang}"}
        
        prodi_data = [
            {
                "id": p.id,
                "nama": p.nama_prodi,
                "maks_project": p.maks_project or "N/A"
            }
            for p in prodis
        ]
        
        return {
            "status": "success",
            "total": len(prodi_data),
            "jenjang": jenjang,
            "data": prodi_data
        }
    except Exception as e:
        print(f"Error querying prodi by jenjang: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}
