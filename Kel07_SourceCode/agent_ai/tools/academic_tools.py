"""
Tools untuk data akademik referensi: kategori_pa, tahun_ajaran, ruangan
"""

from core.database import SessionLocal
from models.kategori_pa import KategoriPA
from models.tahun_ajaran import TahunAjaran
from models.ruangan import Ruangan


def get_kategori_pa_list() -> dict:
    try:
        session = SessionLocal()
        rows = session.query(KategoriPA).all()
        session.close()

        if not rows:
            return {"status": "empty", "message": "Belum ada data kategori PA"}

        data = [
            {
                "id": row.id,
                "kategori_pa": row.kategori_pa
            }
            for row in rows
        ]

        return {"status": "success", "total": len(data), "data": data}
    except Exception as e:
        print(f"Error querying kategori_pa: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_tahun_ajaran_list() -> dict:
    try:
        session = SessionLocal()
        rows = session.query(TahunAjaran).all()
        session.close()

        if not rows:
            return {"status": "empty", "message": "Belum ada data tahun ajaran"}

        data = [
            {
                "id": row.id,
                "tahun_mulai": int(row.tahun_mulai) if row.tahun_mulai is not None else None,
                "tahun_selesai": int(row.tahun_selesai) if row.tahun_selesai is not None else None,
                "status": row.status
            }
            for row in rows
        ]

        return {"status": "success", "total": len(data), "data": data}
    except Exception as e:
        print(f"Error querying tahun_ajaran: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_ruangan_list() -> dict:
    try:
        session = SessionLocal()
        rows = session.query(Ruangan).all()
        session.close()

        if not rows:
            return {"status": "empty", "message": "Belum ada data ruangan"}

        data = [
            {
                "id": row.id,
                "ruangan": row.ruangan
            }
            for row in rows
        ]

        return {"status": "success", "total": len(data), "data": data}
    except Exception as e:
        print(f"Error querying ruangan: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}
