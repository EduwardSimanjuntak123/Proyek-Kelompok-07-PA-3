"""
Tools untuk query roles dan keterkaitannya dengan dosen_roles.
"""

from core.database import SessionLocal
from models.role import Role
from models.dosen_role import DosenRole


def get_roles_list() -> dict:
    """
    Ambil daftar role beserta jumlah assignment pada dosen_roles.
    """
    try:
        session = SessionLocal()
        roles = session.query(Role).all()

        if not roles:
            session.close()
            return {
                "status": "empty",
                "message": "Belum ada data role"
            }

        data = []
        for role in roles:
            total_dosen_roles = session.query(DosenRole).filter(
                DosenRole.role_id == role.id
            ).count()

            active_dosen_roles = session.query(DosenRole).filter(
                DosenRole.role_id == role.id,
                DosenRole.status == "Aktif"
            ).count()

            data.append({
                "id": role.id,
                "role_name": role.role_name,
                "total_dosen_roles": total_dosen_roles,
                "active_dosen_roles": active_dosen_roles
            })

        session.close()
        return {
            "status": "success",
            "total": len(data),
            "data": data
        }
    except Exception as e:
        print(f"Error querying roles: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_role_detail_with_assignments(role_id: int) -> dict:
    """
    Ambil detail satu role dan semua data assignment di dosen_roles.
    """
    try:
        session = SessionLocal()
        role = session.query(Role).filter(Role.id == role_id).first()

        if not role:
            session.close()
            return {
                "status": "empty",
                "message": f"Role dengan id={role_id} tidak ditemukan"
            }

        assignments = session.query(DosenRole).filter(
            DosenRole.role_id == role_id
        ).all()

        assignment_data = [
            {
                "dosen_role_id": item.id,
                "user_id": item.user_id,
                "kpa_id": item.KPA_id,
                "prodi_id": item.prodi_id,
                "tm_id": item.TM_id,
                "tahun_ajaran_id": item.tahun_ajaran_id,
                "status": item.status,
            }
            for item in assignments
        ]

        session.close()
        return {
            "status": "success",
            "role": {
                "id": role.id,
                "role_name": role.role_name,
            },
            "total_assignments": len(assignment_data),
            "assignments": assignment_data,
        }
    except Exception as e:
        print(f"Error querying role detail: {e}")
        return {"status": "error", "message": f"Error: {str(e)}"}
