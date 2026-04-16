"""
Tools untuk query jadwal dari database
"""

from core.database import SessionLocal
from models.jadwal import Jadwal
from models.kelompok import Kelompok
from models.ruangan import Ruangan


def get_jadwal_by_dosen_context(prodi_id: int = None, kategori_pa_id: int = None) -> dict:
    """
    Ambil jadwal berdasarkan context dosen.
    """
    try:
        session = SessionLocal()
        query = session.query(Jadwal)

        if prodi_id:
            query = query.filter(Jadwal.prodi_id == prodi_id)
        if kategori_pa_id:
            query = query.filter(Jadwal.KPA_id == kategori_pa_id)

        jadwal_list = query.order_by(Jadwal.waktu_mulai.asc()).all()

        if not jadwal_list:
            session.close()
            return {
                "status": "empty",
                "message": "Tidak ada jadwal untuk context ini"
            }

        data = []
        for item in jadwal_list:
            kelompok = session.query(Kelompok).filter(Kelompok.id == item.kelompok_id).first()
            ruang = session.query(Ruangan.ruangan).filter(Ruangan.id == item.ruangan_id).scalar()

            data.append({
                "id": item.id,
                "nomor_kelompok": kelompok.nomor_kelompok if kelompok else item.kelompok_id,
                "waktu_mulai": item.waktu_mulai,
                "waktu_selesai": item.waktu_selesai,
                "ruangan": ruang or "N/A",
                "prodi_id": item.prodi_id,
                "kategori_pa_id": item.KPA_id,
                "tm_id": item.TM_id
            })

        session.close()
        return {
            "status": "success",
            "total": len(data),
            "data": data
        }
    except Exception as e:
        print(f"Error querying jadwal: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}
