"""
Tools untuk query kelompok PA dari database
"""

from core.database import SessionLocal
from models.kelompok import Kelompok
from models.kelompokMahasiswa import KelompokMahasiswa
from models.mahasiswa import Mahasiswa


def get_kelompok_list() -> dict:
    """Ambil daftar kelompok dari database"""
    try:
        session = SessionLocal()
        query = session.query(Kelompok)
        kelompoks = query.all()
        session.close()
        
        if not kelompoks:
            return {"status": "empty", "message": "Belum ada data kelompok"}
        
        kelompok_data = [
            {
                "id": k.id,
                "nomor_kelompok": k.nomor_kelompok,
                "prodi_id": k.prodi_id,
                "tahun_ajaran_id": k.tahun_ajaran_id,
                "status": k.status or "N/A"
            }
            for k in kelompoks
        ]
        
        return {
            "status": "success",
            "total": len(kelompok_data),
            "data": kelompok_data
        }
    except Exception as e:
        print(f"Error querying kelompok: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def search_kelompok(nomor: str = None, prodi_id: int = None, tahun_ajaran_id: int = None) -> dict:
    """Search kelompok berdasarkan nomor, prodi, atau tahun ajaran"""
    try:
        session = SessionLocal()
        query = session.query(Kelompok)
        
        if nomor:
            query = query.filter(Kelompok.nomor_kelompok.ilike(f"%{nomor}%"))
        if prodi_id:
            query = query.filter(Kelompok.prodi_id == prodi_id)
        if tahun_ajaran_id:
            query = query.filter(Kelompok.tahun_ajaran_id == tahun_ajaran_id)
        
        kelompoks = query.all()
        session.close()
        
        if not kelompoks:
            return {"status": "empty", "message": "Tidak ada kelompok yang sesuai dengan kriteria pencarian"}
        
        kelompok_data = [
            {
                "id": k.id,
                "nomor_kelompok": k.nomor_kelompok,
                "prodi_id": k.prodi_id,
                "tahun_ajaran_id": k.tahun_ajaran_id,
                "status": k.status or "N/A"
            }
            for k in kelompoks
        ]
        
        return {
            "status": "success",
            "total": len(kelompok_data),
            "data": kelompok_data
        }
    except Exception as e:
        print(f"Error searching kelompok: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_kelompok_by_prodi(prodi_id: int) -> dict:
    """Ambil daftar kelompok berdasarkan prodi"""
    try:
        session = SessionLocal()
        kelompoks = session.query(Kelompok).filter(Kelompok.prodi_id == prodi_id).all()
        session.close()
        
        if not kelompoks:
            return {"status": "empty", "message": f"Tidak ada kelompok di prodi {prodi_id}"}
        
        kelompok_data = [
            {
                "id": k.id,
                "nomor_kelompok": k.nomor_kelompok,
                "prodi_id": k.prodi_id,
                "tahun_ajaran_id": k.tahun_ajaran_id,
                "status": k.status or "N/A"
            }
            for k in kelompoks
        ]
        
        return {
            "status": "success",
            "total": len(kelompok_data),
            "prodi_id": prodi_id,
            "data": kelompok_data
        }
    except Exception as e:
        print(f"Error querying kelompok by prodi: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_kelompok_by_pembimbing(pembimbing_id: int) -> dict:
    """Ambil daftar kelompok berdasarkan pembimbing (by pembimbing id)"""
    try:
        session = SessionLocal()
        from models.pembimbing import Pembimbing
        
        # Get kelompok ids dari pembimbing dengan id tertentu
        pembimbings = session.query(Pembimbing).filter(
            Pembimbing.id == pembimbing_id
        ).all()
        
        if not pembimbings:
            session.close()
            return {"status": "empty", "message": f"Tidak ada kelompok dengan pembimbing {pembimbing_id}"}
        
        kelompok_ids = [p.kelompok_id for p in pembimbings]
        kelompoks = session.query(Kelompok).filter(Kelompok.id.in_(kelompok_ids)).all()
        session.close()
        
        if not kelompoks:
            return {"status": "empty", "message": f"Tidak ada kelompok dengan pembimbing {pembimbing_id}"}
        
        kelompok_data = [
            {
                "id": k.id,
                "nomor_kelompok": k.nomor_kelompok,
                "prodi_id": k.prodi_id,
                "tahun_ajaran_id": k.tahun_ajaran_id,
                "status": k.status or "N/A"
            }
            for k in kelompoks
        ]
        
        return {
            "status": "success",
            "total": len(kelompok_data),
            "pembimbing_id": pembimbing_id,
            "data": kelompok_data
        }
    except Exception as e:
        print(f"Error querying kelompok by pembimbing: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_kelompok_by_dosen_context(prodi_id: int = None, kategori_pa_id: int = None) -> dict:
    """
    Ambil daftar kelompok berdasarkan prodi_id dan kategori_pa_id dari payload dosen
    
    Args:
        prodi_id: ID prodi dari dosenRole.prodi_id
        kategori_pa_id: ID kategori PA dari dosenRole.kategoriPA->id
    
    Returns:
        Dict dengan status, total, dan data kelompok yang sesuai
    """
    try:
        session = SessionLocal()
        query = session.query(Kelompok)
        
        # Filter berdasarkan prodi_id jika diberikan
        if prodi_id:
            query = query.filter(Kelompok.prodi_id == prodi_id)
        
        # TODO: Filter berdasarkan kategori_pa_id jika ada relasi ke kategori_pa di model Kelompok
        # if kategori_pa_id:
        #     query = query.filter(Kelompok.kategori_pa_id == kategori_pa_id)
        
        kelompoks = query.all()
        session.close()
        
        if not kelompoks:
            filters = []
            if prodi_id:
                filters.append(f"prodi_id={prodi_id}")
            if kategori_pa_id:
                filters.append(f"kategori_pa_id={kategori_pa_id}")
            
            return {
                "status": "empty",
                "message": f"Tidak ada kelompok dengan filter: {', '.join(filters) if filters else 'default'}"
            }
        
        kelompok_data = [
            {
                "id": k.id,
                "nomor_kelompok": k.nomor_kelompok,
                "prodi_id": k.prodi_id,
                "tahun_ajaran_id": k.tahun_ajaran_id,
                "status": k.status or "N/A"
            }
            for k in kelompoks
        ]
        
        return {
            "status": "success",
            "total": len(kelompok_data),
            "filters": {
                "prodi_id": prodi_id,
                "kategori_pa_id": kategori_pa_id
            },
            "data": kelompok_data
        }
    except Exception as e:
        print(f"Error querying kelompok by dosen context: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_kelompok_with_anggota_by_context(prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> dict:
    """Ambil kelompok pada konteks dosen beserta daftar anggotanya."""
    session = SessionLocal()
    try:
        query = (
            session.query(Kelompok, Mahasiswa)
            .outerjoin(KelompokMahasiswa, KelompokMahasiswa.kelompok_id == Kelompok.id)
            .outerjoin(Mahasiswa, Mahasiswa.user_id == KelompokMahasiswa.user_id)
        )

        if prodi_id:
            query = query.filter(Kelompok.prodi_id == prodi_id)
        if kategori_pa_id:
            query = query.filter(Kelompok.KPA_id == kategori_pa_id)
        if angkatan_id:
            query = query.filter(Kelompok.TM_id == angkatan_id)

        rows = query.order_by(Kelompok.id.asc()).all()

        grouped = {}
        for kelompok, mahasiswa in rows:
            if kelompok.id not in grouped:
                grouped[kelompok.id] = {
                    "id": kelompok.id,
                    "nomor_kelompok": kelompok.nomor_kelompok,
                    "prodi_id": kelompok.prodi_id,
                    "kategori_pa_id": kelompok.KPA_id,
                    "angkatan_id": kelompok.TM_id,
                    "tahun_ajaran_id": kelompok.tahun_ajaran_id,
                    "status": kelompok.status or "N/A",
                    "anggota": [],
                }

            if mahasiswa:
                grouped[kelompok.id]["anggota"].append({
                    "user_id": mahasiswa.user_id,
                    "nim": mahasiswa.nim,
                    "nama": mahasiswa.nama,
                    "email": mahasiswa.email,
                    "angkatan": mahasiswa.angkatan,
                    "status": mahasiswa.status,
                })

        data = list(grouped.values())
        for item in data:
            item["anggota"] = sorted(item["anggota"], key=lambda m: ((m.get("nim") or ""), (m.get("nama") or "")))

        if not data:
            return {
                "status": "empty",
                "message": "Tidak ada kelompok pada konteks ini.",
                "filters": {
                    "prodi_id": prodi_id,
                    "kategori_pa_id": kategori_pa_id,
                    "angkatan_id": angkatan_id,
                },
            }

        total_members = sum(len(item.get("anggota", [])) for item in data)
        return {
            "status": "success",
            "total": len(data),
            "total_members": total_members,
            "filters": {
                "prodi_id": prodi_id,
                "kategori_pa_id": kategori_pa_id,
                "angkatan_id": angkatan_id,
            },
            "data": data,
        }
    except Exception as e:
        print(f"Error querying kelompok+anggota by context: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}
    finally:
        session.close()


def check_existing_kelompok_by_context(prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> dict:
    """Cek apakah kelompok sudah ada pada context dosen saat ini."""
    try:
        session = SessionLocal()
        query = session.query(Kelompok)

        if prodi_id:
            query = query.filter(Kelompok.prodi_id == prodi_id)
        if kategori_pa_id:
            query = query.filter(Kelompok.KPA_id == kategori_pa_id)
        if angkatan_id:
            query = query.filter(Kelompok.TM_id == angkatan_id)

        kelompoks = query.order_by(Kelompok.id.asc()).all()
        session.close()

        data = [
            {
                "id": k.id,
                "nomor_kelompok": k.nomor_kelompok,
                "prodi_id": k.prodi_id,
                "kategori_pa_id": k.KPA_id,
                "angkatan_id": k.TM_id,
                "tahun_ajaran_id": k.tahun_ajaran_id,
                "status": k.status or "N/A",
            }
            for k in kelompoks
        ]

        return {
            "status": "success",
            "exists": len(data) > 0,
            "total": len(data),
            "filters": {
                "prodi_id": prodi_id,
                "kategori_pa_id": kategori_pa_id,
                "angkatan_id": angkatan_id,
            },
            "data": data,
        }
    except Exception as e:
        print(f"Error checking existing kelompok by context: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def delete_kelompok_by_context(prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> dict:
    """Hapus kelompok lama dan anggotanya berdasarkan context dosen."""
    session = SessionLocal()
    try:
        query = session.query(Kelompok)

        if prodi_id:
            query = query.filter(Kelompok.prodi_id == prodi_id)
        if kategori_pa_id:
            query = query.filter(Kelompok.KPA_id == kategori_pa_id)
        if angkatan_id:
            query = query.filter(Kelompok.TM_id == angkatan_id)

        kelompoks = query.all()
        if not kelompoks:
            session.close()
            return {
                "status": "empty",
                "deleted_kelompok": 0,
                "deleted_members": 0,
                "message": "Tidak ada kelompok yang perlu dihapus.",
            }

        kelompok_ids = [k.id for k in kelompoks]
        deleted_members = session.query(KelompokMahasiswa).filter(
            KelompokMahasiswa.kelompok_id.in_(kelompok_ids)
        ).delete(synchronize_session=False)
        deleted_kelompok = session.query(Kelompok).filter(
            Kelompok.id.in_(kelompok_ids)
        ).delete(synchronize_session=False)

        session.commit()
        session.close()

        return {
            "status": "success",
            "deleted_kelompok": deleted_kelompok,
            "deleted_members": deleted_members,
            "message": f"Berhasil menghapus {deleted_kelompok} kelompok dan {deleted_members} anggota.",
        }
    except Exception as e:
        session.rollback()
        session.close()
        print(f"Error deleting kelompok by context: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_anggota_kelompok_by_nomor(nomor_kelompok: str, prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> dict:
    """Ambil anggota kelompok berdasarkan nomor kelompok dan context dosen."""
    session = SessionLocal()
    try:
        query = session.query(Kelompok).filter(Kelompok.nomor_kelompok == str(nomor_kelompok))

        if prodi_id:
            query = query.filter(Kelompok.prodi_id == prodi_id)
        if kategori_pa_id:
            query = query.filter(Kelompok.KPA_id == kategori_pa_id)
        if angkatan_id:
            query = query.filter(Kelompok.TM_id == angkatan_id)

        kelompok = query.first()
        if not kelompok:
            session.close()
            return {
                "status": "empty",
                "message": f"Kelompok nomor {nomor_kelompok} tidak ditemukan pada konteks ini.",
            }

        anggota_rows = (
            session.query(KelompokMahasiswa, Mahasiswa)
            .join(Mahasiswa, Mahasiswa.user_id == KelompokMahasiswa.user_id)
            .filter(KelompokMahasiswa.kelompok_id == kelompok.id)
            .all()
        )

        anggota = []
        for kelompok_mahasiswa, mahasiswa in anggota_rows:
            anggota.append({
                "user_id": mahasiswa.user_id,
                "nim": mahasiswa.nim,
                "nama": mahasiswa.nama,
                "email": mahasiswa.email,
                "prodi_id": mahasiswa.prodi_id,
                "angkatan": mahasiswa.angkatan,
                "status": mahasiswa.status,
            })

        session.close()

        return {
            "status": "success",
            "total": len(anggota),
            "group": {
                "id": kelompok.id,
                "nomor_kelompok": kelompok.nomor_kelompok,
                "prodi_id": kelompok.prodi_id,
                "kategori_pa_id": kelompok.KPA_id,
                "angkatan_id": kelompok.TM_id,
                "tahun_ajaran_id": kelompok.tahun_ajaran_id,
                "status": kelompok.status or "N/A",
            },
            "data": anggota,
        }
    except Exception as e:
        session.rollback()
        session.close()
        print(f"Error getting anggota kelompok by nomor: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}
