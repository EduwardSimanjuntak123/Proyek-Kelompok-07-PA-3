"""
Tools untuk query nilai akhir mahasiswa dari database
"""

from core.database import SessionLocal
from models.nilai_mahasiswa import NilaiMahasiswa
from models.mahasiswa import Mahasiswa


def get_nilai_akhir_by_dosen_context(prodi_id: int = None) -> dict:
    """
    Ambil nilai akhir mahasiswa dan filter by prodi jika tersedia.
    """
    try:
        session = SessionLocal()
        query = session.query(NilaiMahasiswa, Mahasiswa).join(
            Mahasiswa,
            NilaiMahasiswa.user_id == Mahasiswa.user_id
        )

        if prodi_id:
            query = query.filter(Mahasiswa.prodi_id == prodi_id)

        rows = query.all()

        if not rows:
            session.close()
            return {
                "status": "empty",
                "message": "Tidak ada data nilai akhir"
            }

        data = []
        for nilai, mahasiswa in rows:
            data.append({
                "id": nilai.id,
                "nim": mahasiswa.nim,
                "nama": mahasiswa.nama,
                "nilai_akhir": float(nilai.nilai_akhir) if nilai.nilai_akhir is not None else 0.0,
                "kelompok_id": nilai.kelompok_id,
                "prodi_id": mahasiswa.prodi_id
            })

        avg = sum(item["nilai_akhir"] for item in data) / len(data)

        session.close()
        return {
            "status": "success",
            "total": len(data),
            "rata_rata": round(avg, 2),
            "data": data
        }
    except Exception as e:
        print(f"Error querying nilai_mahasiswa: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}
