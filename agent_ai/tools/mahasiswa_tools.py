"""
Tools untuk query mahasiswa dari database
"""

from sqlalchemy import select
from core.database import SessionLocal
from models.mahasiswa import Mahasiswa
from models.kelompokMahasiswa import KelompokMahasiswa


def get_mahasiswa_list() -> dict:
    """Ambil daftar mahasiswa dari database"""
    try:
        session = SessionLocal()
        mahasiswas = session.query(Mahasiswa).all()
        session.close()
        
        if not mahasiswas:
            return {"status": "empty", "message": "Belum ada data mahasiswa"}
        
        mahasiswa_data = [
            {
                "id": m.id,
                "nim": m.nim,
                "nama": m.nama,
                "email": m.email or "N/A",
                "prodi": m.prodi_name or m.prodi_id or "N/A",
                "semester": getattr(m, 'semester', "N/A"),
                "ipk": getattr(m, 'ipk', "N/A")
            }
            for m in mahasiswas
        ]
        
        return {
            "status": "success",
            "total": len(mahasiswa_data),
            "data": mahasiswa_data
        }
    except Exception as e:
        print(f"Error querying mahasiswa: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def search_mahasiswa(nama: str = None, nim: str = None, prodi: str = None) -> dict:
    """Search mahasiswa berdasarkan nama, NIM, atau prodi"""
    try:
        session = SessionLocal()
        query = session.query(Mahasiswa)
        
        if nama:
            query = query.filter(Mahasiswa.nama.ilike(f"%{nama}%"))
        if nim:
            query = query.filter(Mahasiswa.nim.like(f"%{nim}%"))
        if prodi:
            query = query.filter(Mahasiswa.prodi_name.ilike(f"%{prodi}%"))
        
        mahasiswas = query.all()
        session.close()
        
        if not mahasiswas:
            return {"status": "empty", "message": "Tidak ada mahasiswa yang sesuai dengan kriteria pencarian"}
        
        mahasiswa_data = [
            {
                "id": m.id,
                "nim": m.nim,
                "nama": m.nama,
                "email": m.email or "N/A",
                "prodi": m.prodi_name or m.prodi_id or "N/A",
                "semester": getattr(m, 'semester', "N/A"),
                "ipk": getattr(m, 'ipk', "N/A")
            }
            for m in mahasiswas
        ]
        
        return {
            "status": "success",
            "total": len(mahasiswa_data),
            "data": mahasiswa_data
        }
    except Exception as e:
        print(f"Error searching mahasiswa: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_mahasiswa_by_prodi(prodi: str) -> dict:
    """Ambil daftar mahasiswa berdasarkan prodi"""
    try:
        session = SessionLocal()
        mahasiswas = session.query(Mahasiswa).filter(Mahasiswa.prodi_name.ilike(f"%{prodi}%")).all()
        session.close()
        
        if not mahasiswas:
            return {"status": "empty", "message": f"Tidak ada mahasiswa di prodi {prodi}"}
        
        mahasiswa_data = [
            {
                "id": m.id,
                "nim": m.nim,
                "nama": m.nama,
                "email": m.email or "N/A",
                "prodi": m.prodi_name or m.prodi_id or "N/A",
                "semester": getattr(m, 'semester', "N/A"),
                "ipk": getattr(m, 'ipk', "N/A")
            }
            for m in mahasiswas
        ]
        
        return {
            "status": "success",
            "total": len(mahasiswa_data),
            "prodi": prodi,
            "data": mahasiswa_data
        }
    except Exception as e:
        print(f"Error querying mahasiswa by prodi: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_mahasiswa_by_semester(semester: int) -> dict:
    """Ambil daftar mahasiswa berdasarkan semester"""
    try:
        session = SessionLocal()
        mahasiswas = session.query(Mahasiswa).filter(Mahasiswa.angkatan == semester).all()
        session.close()
        
        if not mahasiswas:
            return {"status": "empty", "message": f"Tidak ada mahasiswa di semester {semester}"}
        
        mahasiswa_data = [
            {
                "id": m.id,
                "nim": m.nim,
                "nama": m.nama,
                "email": m.email or "N/A",
                "prodi": m.prodi_name or m.prodi_id or "N/A",
                "semester": getattr(m, 'semester', "N/A"),
                "ipk": getattr(m, 'ipk', "N/A")
            }
            for m in mahasiswas
        ]
        
        return {
            "status": "success",
            "total": len(mahasiswa_data),
            "semester": semester,
            "data": mahasiswa_data
        }
    except Exception as e:
        print(f"Error querying mahasiswa by semester: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_mahasiswa_by_dosen_context(prodi_id: int = None, angkatan_id: int = None) -> dict:
    """
    Ambil daftar mahasiswa berdasarkan prodi_id dan angkatan_id dari payload dosen
    
    Args:
        prodi_id: ID prodi dari dosenRole.prodi_id
        angkatan_id: ID tahun masuk dari dosenRole.TahunMasuk->id
    
    Returns:
        Dict dengan status, total, dan data mahasiswa yang sesuai
    """
    try:
        from models.tahun_masuk import TahunMasuk
        
        session = SessionLocal()
        query = session.query(Mahasiswa)
        
        # Filter berdasarkan prodi_id jika diberikan
        if prodi_id:
            query = query.filter(Mahasiswa.prodi_id == prodi_id)
        
        # Convert angkatan_id ke tahun aktual jika diberikan
        if angkatan_id:
            tahun_masuk = session.query(TahunMasuk).filter(
                TahunMasuk.id == angkatan_id
            ).first()
            
            if tahun_masuk:
                tahun = tahun_masuk.Tahun_Masuk
                query = query.filter(Mahasiswa.angkatan == tahun)
            else:
                session.close()
                return {
                    "status": "error",
                    "message": f"Tahun masuk dengan ID {angkatan_id} tidak ditemukan"
                }
        
        mahasiswas = query.all()
        session.close()
        
        if not mahasiswas:
            filters = []
            if prodi_id:
                filters.append(f"prodi_id={prodi_id}")
            if angkatan_id:
                filters.append(f"angkatan_id={angkatan_id}")
            
            return {
                "status": "empty",
                "message": f"Tidak ada mahasiswa dengan filter: {', '.join(filters)}"
            }
        
        mahasiswa_data = [
            {
                "id": m.id,
                "nim": m.nim,
                "nama": m.nama,
                "email": m.email or "N/A",
                "prodi": m.prodi_name or m.prodi_id or "N/A",
                "prodi_id": m.prodi_id,
                "angkatan": m.angkatan,
                "status": m.status or "N/A"
            }
            for m in mahasiswas
        ]
        
        return {
            "status": "success",
            "total": len(mahasiswa_data),
            "filters": {
                "prodi_id": prodi_id,
                "angkatan_id": angkatan_id
            },
            "data": mahasiswa_data
        }
    except Exception as e:
        print(f"Error querying mahasiswa by dosen context: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_mahasiswa_without_kelompok_by_context(prodi_id: int = None, angkatan_id: int = None) -> dict:
    """
    Ambil mahasiswa yang belum memiliki kelompok berdasarkan konteks dosen.
    """
    try:
        from models.tahun_masuk import TahunMasuk

        session = SessionLocal()
        query = session.query(Mahasiswa)

        if prodi_id:
            query = query.filter(Mahasiswa.prodi_id == prodi_id)

        if angkatan_id:
            tahun_masuk = session.query(TahunMasuk).filter(TahunMasuk.id == angkatan_id).first()
            if tahun_masuk:
                query = query.filter(Mahasiswa.angkatan == tahun_masuk.Tahun_Masuk)
            else:
                session.close()
                return {
                    "status": "error",
                    "message": f"Tahun masuk dengan ID {angkatan_id} tidak ditemukan",
                }

        occupied_select = select(KelompokMahasiswa.user_id)
        query = query.filter(~Mahasiswa.user_id.in_(occupied_select))

        mahasiswas = query.order_by(Mahasiswa.nim.asc()).all()
        session.close()

        if not mahasiswas:
            return {
                "status": "empty",
                "message": "Tidak ada mahasiswa yang belum punya kelompok pada konteks ini.",
                "filters": {
                    "prodi_id": prodi_id,
                    "angkatan_id": angkatan_id,
                },
            }

        mahasiswa_data = [
            {
                "id": m.id,
                "nim": m.nim,
                "nama": m.nama,
                "email": m.email or "N/A",
                "prodi": m.prodi_name or m.prodi_id or "N/A",
                "prodi_id": m.prodi_id,
                "angkatan": m.angkatan,
                "status": m.status or "N/A",
            }
            for m in mahasiswas
        ]

        return {
            "status": "success",
            "total": len(mahasiswa_data),
            "filters": {
                "prodi_id": prodi_id,
                "angkatan_id": angkatan_id,
            },
            "data": mahasiswa_data,
        }
    except Exception as e:
        print(f"Error querying mahasiswa tanpa kelompok by context: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}
