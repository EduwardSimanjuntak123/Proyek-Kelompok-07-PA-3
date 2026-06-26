"""
Tools untuk query dosen_roles dari database
"""

from core.database import SessionLocal
from models.dosen_role import DosenRole
from models.role import Role
from models.kategori_pa import KategoriPA
from models.prodi import Prodi
from models.tahun_masuk import TahunMasuk
from models.tahun_ajaran import TahunAjaran


def get_dosen_roles_by_dosen_context(user_id: int = None, prodi_id: int = None, kategori_pa_id: int = None) -> dict:
    """
    Ambil role dosen berdasarkan context login.
    """
    try:
        session = SessionLocal()
        query = session.query(DosenRole)

        if user_id:
            query = query.filter(DosenRole.user_id == user_id)
        if prodi_id:
            query = query.filter(DosenRole.prodi_id == prodi_id)
        if kategori_pa_id:
            query = query.filter(DosenRole.KPA_id == kategori_pa_id)

        roles = query.all()

        if not roles:
            session.close()
            return {
                "status": "empty",
                "message": "Tidak ada role dosen untuk context ini"
            }

        data = []
        for item in roles:
            role_name = session.query(Role.role_name).filter(Role.id == item.role_id).scalar()
            kategori_pa = session.query(KategoriPA.kategori_pa).filter(KategoriPA.id == item.KPA_id).scalar()
            prodi_name = session.query(Prodi.nama_prodi).filter(Prodi.id == item.prodi_id).scalar()
            tahun_masuk = session.query(TahunMasuk.Tahun_Masuk).filter(TahunMasuk.id == item.TM_id).scalar()
            tahun_ajaran = session.query(TahunAjaran).filter(TahunAjaran.id == item.tahun_ajaran_id).first()

            data.append({
                "id": item.id,
                "user_id": item.user_id,
                "role": role_name or "N/A",
                "kategori_pa": kategori_pa or "N/A",
                "prodi": prodi_name or "N/A",
                "tahun_masuk": int(tahun_masuk) if tahun_masuk is not None else "N/A",
                "tahun_ajaran": f"{tahun_ajaran.tahun_mulai}/{tahun_ajaran.tahun_selesai}" if tahun_ajaran else "N/A",
                "status": item.status or "N/A"
            })

        session.close()
        return {
            "status": "success",
            "total": len(data),
            "data": data
        }
    except Exception as e:
        print(f"Error querying dosen_roles: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}
