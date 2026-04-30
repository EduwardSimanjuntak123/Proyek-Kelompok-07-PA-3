from __future__ import annotations

"""
Tools pembimbing kelompok.

Fitur utama:
- Generate assignment pembimbing per kelompok (1-2 pembimbing per kelompok).
- Pembagian mempertimbangkan jabatan akademik dosen:
  semakin tinggi jabatan, semakin kecil beban bimbingan.
- Query assignment pembimbing berdasarkan konteks dosen.
"""

from datetime import datetime
from math import ceil
from typing import Dict, List, Optional

from sqlalchemy import func, or_

from core.database import SessionLocal
from models.dosen import Dosen
from models.dosen_role import DosenRole
from models.kelompok import Kelompok
from models.pembimbing import Pembimbing
from models.role import Role


DOSEN_ID_FALLBACK_OFFSET = 1000000000


def _jabatan_rank(jabatan_desc: Optional[str]) -> int:
    """Semakin besar rank, semakin tinggi jabatan akademik."""
    value = (jabatan_desc or "").lower().strip()
    if "profesor" in value or "guru besar" in value:
        return 5
    if "lektor kepala" in value:
        return 4
    if "lektor" in value:
        return 3
    if "asisten ahli" in value:
        return 2
    if "tenaga pengajar" in value or "pengajar" in value:
        return 1
    return 2


def _jabatan_weight(jabatan_desc: Optional[str]) -> int:
    """Semakin tinggi jabatan, bobot makin kecil (beban makin sedikit)."""
    # Rank 5 (Profesor) -> weight 1; Rank 1 -> weight 5
    return max(1, 6 - _jabatan_rank(jabatan_desc))


def _get_kelompok_by_context(session, prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None):
    query = session.query(Kelompok)
    if prodi_id:
        query = query.filter(Kelompok.prodi_id == prodi_id)
    if kategori_pa_id:
        query = query.filter(Kelompok.KPA_id == kategori_pa_id)
    if angkatan_id:
        query = query.filter(Kelompok.TM_id == angkatan_id)
    return query.order_by(Kelompok.id.asc()).all()


def _get_candidate_pembimbing_by_context(session, prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None):
    # Sumber utama kandidat diambil dari tabel dosen sesuai prodi_id
    # agar tidak terbatasi oleh kombinasi KPA/TM pada dosen_roles.
    dosen_query = session.query(Dosen)
    if prodi_id:
        dosen_query = dosen_query.filter(Dosen.prodi_id == prodi_id)

    dosen_rows = dosen_query.order_by(Dosen.nama.asc(), Dosen.user_id.asc()).all()
    if not dosen_rows:
        return []

    user_ids = [d.user_id for d in dosen_rows if d.user_id is not None]
    role_rows = (
        session.query(DosenRole.user_id, Role.role_name)
        .join(Role, Role.id == DosenRole.role_id)
        .filter(DosenRole.user_id.in_(user_ids))
        .filter(or_(DosenRole.status == None, func.lower(DosenRole.status) == "aktif"))
    )
    if prodi_id:
        role_rows = role_rows.filter(DosenRole.prodi_id == prodi_id)

    roles_by_user: Dict[int, List[str]] = {}
    for uid, role_name in role_rows.all():
        roles_by_user.setdefault(uid, []).append(role_name or "")

    candidates = []
    for dosen in dosen_rows:
        if dosen.user_id is not None:
            assignment_user_id = int(dosen.user_id)
        else:
            assignment_user_id = DOSEN_ID_FALLBACK_OFFSET + int(dosen.id)

        role_names = roles_by_user.get(dosen.user_id, [])
        pembimbing_role = next((r for r in role_names if "pembimbing" in (r or "").lower()), None)
        role_name = pembimbing_role or (role_names[0] if role_names else "Dosen")

        candidates.append({
            "user_id": assignment_user_id,
            "source_dosen_id": dosen.id,
            "source_user_id": dosen.user_id,
            "nama": dosen.nama,
            "email": dosen.email,
            "prodi": dosen.prodi,
            "jabatan_akademik_desc": dosen.jabatan_akademik_desc,
            "role_name": role_name,
            "dosen_role_id": None,
            "weight": _jabatan_weight(dosen.jabatan_akademik_desc),
            "rank": _jabatan_rank(dosen.jabatan_akademik_desc),
        })

    return candidates


def _get_assignment_rows_by_context(session, prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None):
    query = (
        session.query(Pembimbing, Kelompok, Dosen)
        .join(Kelompok, Kelompok.id == Pembimbing.kelompok_id)
        .outerjoin(
            Dosen,
            or_(
                Dosen.user_id == Pembimbing.user_id,
                (Dosen.id + DOSEN_ID_FALLBACK_OFFSET) == Pembimbing.user_id,
            ),
        )
    )

    if prodi_id:
        query = query.filter(Kelompok.prodi_id == prodi_id)
    if kategori_pa_id:
        query = query.filter(Kelompok.KPA_id == kategori_pa_id)
    if angkatan_id:
        query = query.filter(Kelompok.TM_id == angkatan_id)

    rows = query.order_by(Kelompok.id.asc(), Pembimbing.id.asc()).all()

    data = []
    for pembimbing, kelompok, dosen in rows:
        data.append({
            "id": pembimbing.id,
            "kelompok_id": kelompok.id,
            "nomor_kelompok": kelompok.nomor_kelompok,
            "user_id": pembimbing.user_id,
            "dosen_nama": dosen.nama if dosen else "N/A",
            "dosen_email": dosen.email if dosen else "N/A",
            "dosen_prodi": dosen.prodi if dosen else "N/A",
            "jabatan_akademik_desc": dosen.jabatan_akademik_desc if dosen else "N/A",
            "status_kelompok": kelompok.status or "N/A",
            "prodi_id": kelompok.prodi_id,
            "kategori_pa_id": kelompok.KPA_id,
            "angkatan_id": kelompok.TM_id,
        })
    return data


def get_pembimbing_list() -> dict:
    """Kompatibilitas lama: ambil semua assignment pembimbing tanpa filter konteks."""
    try:
        session = SessionLocal()
        data = _get_assignment_rows_by_context(session)
        session.close()

        if not data:
            return {"status": "empty", "message": "Belum ada data pembimbing"}
        return {"status": "success", "total": len(data), "data": data}
    except Exception as e:
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_pembimbing_assignments_by_context(prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> dict:
    try:
        session = SessionLocal()
        data = _get_assignment_rows_by_context(session, prodi_id, kategori_pa_id, angkatan_id)
        session.close()

        if not data:
            return {
                "status": "empty",
                "message": "Belum ada assignment pembimbing pada konteks ini",
                "filters": {
                    "prodi_id": prodi_id,
                    "kategori_pa_id": kategori_pa_id,
                    "angkatan_id": angkatan_id,
                },
            }

        return {
            "status": "success",
            "total": len(data),
            "data": data,
            "filters": {
                "prodi_id": prodi_id,
                "kategori_pa_id": kategori_pa_id,
                "angkatan_id": angkatan_id,
            },
        }
    except Exception as e:
        return {"status": "error", "message": f"Error: {str(e)}"}


def check_existing_pembimbing_by_context(prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> dict:
    try:
        session = SessionLocal()

        kelompok_count_query = session.query(func.count(Kelompok.id))
        if prodi_id:
            kelompok_count_query = kelompok_count_query.filter(Kelompok.prodi_id == prodi_id)
        if kategori_pa_id:
            kelompok_count_query = kelompok_count_query.filter(Kelompok.KPA_id == kategori_pa_id)
        if angkatan_id:
            kelompok_count_query = kelompok_count_query.filter(Kelompok.TM_id == angkatan_id)

        total_kelompok = kelompok_count_query.scalar() or 0

        assignment_data = _get_assignment_rows_by_context(session, prodi_id, kategori_pa_id, angkatan_id)
        kelompok_with_pembimbing = len(set(item["kelompok_id"] for item in assignment_data))

        session.close()

        return {
            "status": "success",
            "exists": len(assignment_data) > 0,
            "total_assignments": len(assignment_data),
            "total_kelompok": total_kelompok,
            "kelompok_with_pembimbing": kelompok_with_pembimbing,
            "kelompok_without_pembimbing": max(0, total_kelompok - kelompok_with_pembimbing),
            "message": "Sudah ada data pembimbing" if assignment_data else "Belum ada data pembimbing",
        }
    except Exception as e:
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_kelompok_without_pembimbing_by_context(prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> dict:
    try:
        session = SessionLocal()

        subquery = session.query(Pembimbing.kelompok_id).subquery()
        query = session.query(Kelompok).filter(~Kelompok.id.in_(subquery))

        if prodi_id:
            query = query.filter(Kelompok.prodi_id == prodi_id)
        if kategori_pa_id:
            query = query.filter(Kelompok.KPA_id == kategori_pa_id)
        if angkatan_id:
            query = query.filter(Kelompok.TM_id == angkatan_id)

        kelompoks = query.order_by(Kelompok.id.asc()).all()
        session.close()

        if not kelompoks:
            return {"status": "empty", "message": "Tidak ada kelompok tanpa pembimbing pada konteks ini"}

        data = [
            {
                "id": k.id,
                "nomor_kelompok": k.nomor_kelompok,
                "status": k.status or "N/A",
                "prodi_id": k.prodi_id,
                "kategori_pa_id": k.KPA_id,
                "angkatan_id": k.TM_id,
            }
            for k in kelompoks
        ]

        return {"status": "success", "total": len(data), "data": data}
    except Exception as e:
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_kelompok_by_pembimbing_count(prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None, exact_count: int = 1) -> dict:
    try:
        session = SessionLocal()

        query = (
            session.query(
                Kelompok.id,
                Kelompok.nomor_kelompok,
                Kelompok.status,
                func.count(Pembimbing.id).label("pembimbing_count"),
            )
            .outerjoin(Pembimbing, Pembimbing.kelompok_id == Kelompok.id)
            .group_by(Kelompok.id, Kelompok.nomor_kelompok, Kelompok.status)
            .having(func.count(Pembimbing.id) == exact_count)
        )

        if prodi_id:
            query = query.filter(Kelompok.prodi_id == prodi_id)
        if kategori_pa_id:
            query = query.filter(Kelompok.KPA_id == kategori_pa_id)
        if angkatan_id:
            query = query.filter(Kelompok.TM_id == angkatan_id)

        rows = query.order_by(Kelompok.id.asc()).all()
        session.close()

        if not rows:
            return {"status": "empty", "message": f"Tidak ada kelompok dengan {exact_count} pembimbing"}

        data = [
            {
                "id": row.id,
                "nomor_kelompok": row.nomor_kelompok,
                "status": row.status or "N/A",
                "pembimbing_count": int(row.pembimbing_count or 0),
            }
            for row in rows
        ]

        return {"status": "success", "total": len(data), "data": data, "exact_count": exact_count}
    except Exception as e:
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_pembimbing_by_dosen_name(nama_dosen: str, prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> dict:
    try:
        session = SessionLocal()
        data = _get_assignment_rows_by_context(session, prodi_id, kategori_pa_id, angkatan_id)
        session.close()

        filtered = [
            row for row in data
            if nama_dosen.lower().strip() in (row.get("dosen_nama", "").lower())
        ]

        if not filtered:
            return {"status": "empty", "message": f"Tidak ada assignment pembimbing untuk dosen: {nama_dosen}"}

        return {
            "status": "success",
            "total": len(filtered),
            "nama_dosen": nama_dosen,
            "data": filtered,
        }
    except Exception as e:
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_pembimbing_of_kelompok(nomor_kelompok: str, prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> dict:
    try:
        session = SessionLocal()

        kelompok_query = session.query(Kelompok).filter(Kelompok.nomor_kelompok == str(nomor_kelompok))
        if prodi_id:
            kelompok_query = kelompok_query.filter(Kelompok.prodi_id == prodi_id)
        if kategori_pa_id:
            kelompok_query = kelompok_query.filter(Kelompok.KPA_id == kategori_pa_id)
        if angkatan_id:
            kelompok_query = kelompok_query.filter(Kelompok.TM_id == angkatan_id)

        kelompok = kelompok_query.first()
        if not kelompok:
            session.close()
            return {"status": "empty", "message": f"Kelompok {nomor_kelompok} tidak ditemukan pada konteks ini"}

        rows = (
            session.query(Pembimbing, Dosen)
            .outerjoin(
                Dosen,
                or_(
                    Dosen.user_id == Pembimbing.user_id,
                    (Dosen.id + DOSEN_ID_FALLBACK_OFFSET) == Pembimbing.user_id,
                ),
            )
            .filter(Pembimbing.kelompok_id == kelompok.id)
            .order_by(Pembimbing.id.asc())
            .all()
        )
        session.close()

        data = [
            {
                "id": p.id,
                "user_id": p.user_id,
                "dosen_nama": d.nama if d else "N/A",
                "dosen_email": d.email if d else "N/A",
                "jabatan_akademik_desc": d.jabatan_akademik_desc if d else "N/A",
            }
            for p, d in rows
        ]

        if not data:
            return {
                "status": "empty",
                "message": f"Kelompok {nomor_kelompok} belum memiliki pembimbing",
                "group": {
                    "id": kelompok.id,
                    "nomor_kelompok": kelompok.nomor_kelompok,
                },
            }

        return {
            "status": "success",
            "total": len(data),
            "group": {
                "id": kelompok.id,
                "nomor_kelompok": kelompok.nomor_kelompok,
            },
            "data": data,
        }
    except Exception as e:
        return {"status": "error", "message": f"Error: {str(e)}"}


def generate_pembimbing_assignments_by_context(
    prodi_id: int = None,
    kategori_pa_id: int = None,
    angkatan_id: int = None,
    min_per_group: int = 1,
    max_per_group: int = 2,
    replace_existing: bool = True,
    persist: bool = True,
) -> dict:
    """
    Generate assignment pembimbing ke kelompok.

    Aturan:
    - Setiap kelompok mendapat minimal 1 pembimbing.
    - Maksimal 2 pembimbing per kelompok.
    - Dosen dengan jabatan lebih tinggi cenderung membimbing lebih sedikit kelompok.
    """
    session = SessionLocal()
    try:
        if min_per_group < 1:
            min_per_group = 1
        if max_per_group < min_per_group:
            max_per_group = min_per_group
        max_per_group = min(max_per_group, 2)

        kelompoks = _get_kelompok_by_context(session, prodi_id, kategori_pa_id, angkatan_id)
        if not kelompoks:
            session.close()
            return {
                "status": "empty",
                "message": "Tidak ada kelompok pada konteks ini",
            }

        candidates = _get_candidate_pembimbing_by_context(session, prodi_id, kategori_pa_id, angkatan_id)
        if not candidates:
            session.close()
            return {
                "status": "empty",
                "message": "Tidak ada dosen pembimbing kandidat pada konteks ini",
            }

        group_ids = [k.id for k in kelompoks]

        existing_assignments = (
            session.query(Pembimbing)
            .filter(Pembimbing.kelompok_id.in_(group_ids))
            .all()
        )

        if existing_assignments and not replace_existing:
            session.close()
            return {
                "status": "conflict",
                "message": "Pembimbing sudah ada. Aktifkan replace_existing untuk generate ulang.",
                "existing_assignments": len(existing_assignments),
            }

        total_groups = len(kelompoks)
        total_candidates = len(candidates)

        avg_weight = sum(c["weight"] for c in candidates) / max(1, total_candidates)
        base_capacity = max(1, ceil(total_groups / max(1, total_candidates)))

        capacities: Dict[int, int] = {}
        loads: Dict[int, int] = {}
        candidate_map = {c["user_id"]: c for c in candidates}

        for c in candidates:
            scaled = round(base_capacity * (c["weight"] / max(avg_weight, 1e-9)))
            capacities[c["user_id"]] = max(1, int(scaled))
            loads[c["user_id"]] = 0

        # Pastikan total kapasitas cukup untuk minimal 1 pembimbing per kelompok.
        while sum(capacities.values()) < total_groups:
            for c in candidates:
                capacities[c["user_id"]] += 1
                if sum(capacities.values()) >= total_groups:
                    break

        group_assignments: Dict[int, List[int]] = {k.id: [] for k in kelompoks}

        def pick_next_candidate(exclude_user_ids: List[int]):
            available = [
                c for c in candidates
                if c["user_id"] not in exclude_user_ids and loads[c["user_id"]] < capacities[c["user_id"]]
            ]
            if not available:
                return None

            available.sort(
                key=lambda c: (
                    loads[c["user_id"]] / max(1, capacities[c["user_id"]]),
                    -c["weight"],
                    loads[c["user_id"]],
                    c.get("nama") or "",
                )
            )
            return available[0]

        # Pass 1: minimal 1 pembimbing per kelompok.
        for kelompok in kelompoks:
            cand = pick_next_candidate(group_assignments[kelompok.id])
            if not cand:
                session.close()
                return {
                    "status": "error",
                    "message": "Kapasitas pembimbing tidak cukup untuk memenuhi minimal 1 pembimbing per kelompok",
                }

            uid = cand["user_id"]
            group_assignments[kelompok.id].append(uid)
            loads[uid] += 1

        # Pass 2: tambah pembimbing kedua jika kapasitas memungkinkan (OPTIONAL, tidak wajib).
        # Jika tidak ada pembimbing dengan kapasitas, kelompok cukup dengan 1 pembimbing saja.
        if max_per_group >= 2 and total_candidates > 1:
            for kelompok in kelompoks:
                if len(group_assignments[kelompok.id]) >= 2:
                    continue
                
                # Try to find candidate with available capacity
                cand = pick_next_candidate(group_assignments[kelompok.id])
                
                # If no candidate with capacity, skip (tidak wajib pembimbing kedua)
                if not cand:
                    continue
                
                uid = cand["user_id"]
                group_assignments[kelompok.id].append(uid)
                loads[uid] += 1

        inserts = []
        dosen_role_inserts = []
        if persist:
            if existing_assignments and replace_existing:
                session.query(Pembimbing).filter(Pembimbing.kelompok_id.in_(group_ids)).delete(synchronize_session=False)

            now = datetime.now()
            for kelompok in kelompoks:
                for idx, user_id in enumerate(group_assignments[kelompok.id]):
                    inserts.append(
                        Pembimbing(
                            user_id=user_id,
                            kelompok_id=kelompok.id,
                            created_at=now,
                            updated_at=now,
                        )
                    )
                    
                    # Tentukan role_id berdasarkan posisi pembimbing (1st atau 2nd)
                    # role_id 3 = Pembimbing 1, role_id 5 = Pembimbing 2
                    role_id = 3 if idx == 0 else 5
                    
                    # Cek apakah DosenRole sudah ada
                    existing_role = session.query(DosenRole).filter(
                        DosenRole.user_id == user_id,
                        DosenRole.role_id == role_id,
                        DosenRole.prodi_id == prodi_id,
                        DosenRole.KPA_id == kategori_pa_id,
                        DosenRole.TM_id == angkatan_id,
                    ).first()
                    
                    # Jika belum ada, buat DosenRole baru
                    if not existing_role:
                        dosen_role_inserts.append(
                            DosenRole(
                                user_id=user_id,
                                role_id=role_id,
                                prodi_id=prodi_id,
                                KPA_id=kategori_pa_id,
                                TM_id=angkatan_id,
                                tahun_ajaran_id=1,  # default tahun ajaran
                                status="Aktif",
                            )
                        )

            session.add_all(inserts)
            if dosen_role_inserts:
                session.add_all(dosen_role_inserts)
            session.commit()

        grouped_output = []
        for kelompok in kelompoks:
            pembimbing_rows = []
            for user_id in group_assignments[kelompok.id]:
                c = candidate_map.get(user_id, {})
                pembimbing_rows.append(
                    {
                        "user_id": user_id,
                        "dosen_nama": c.get("nama", "N/A"),
                        "jabatan_akademik_desc": c.get("jabatan_akademik_desc", "N/A"),
                        "role_name": c.get("role_name", "N/A"),
                    }
                )

            grouped_output.append(
                {
                    "kelompok_id": kelompok.id,
                    "nomor_kelompok": kelompok.nomor_kelompok,
                    "pembimbing_count": len(pembimbing_rows),
                    "pembimbing": pembimbing_rows,
                }
            )

        dosen_loads = []
        for c in sorted(candidates, key=lambda x: (x.get("nama") or "")):
            uid = c["user_id"]
            dosen_loads.append(
                {
                    "user_id": uid,
                    "dosen_nama": c.get("nama", "N/A"),
                    "jabatan_akademik_desc": c.get("jabatan_akademik_desc", "N/A"),
                    "capacity": capacities[uid],
                    "assigned_groups": loads[uid],
                }
            )

        session.close()

        return {
            "status": "success",
            "message": "Generate pembimbing kelompok berhasil",
            "summary": {
                "total_kelompok": total_groups,
                "total_kandidat_pembimbing": total_candidates,
                "total_assignments": sum(len(v) for v in group_assignments.values()),
                "min_per_group": min_per_group,
                "max_per_group": max_per_group,
                "replace_existing": replace_existing,
                "persisted": persist,
            },
            "groups": grouped_output,
            "dosen_loads": dosen_loads,
            "filters": {
                "prodi_id": prodi_id,
                "kategori_pa_id": kategori_pa_id,
                "angkatan_id": angkatan_id,
            },
        }
    except Exception as e:
        session.rollback()
        session.close()
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_kelompok_with_two_pembimbing_by_context(prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> dict:
    return get_kelompok_by_pembimbing_count(
        prodi_id=prodi_id,
        kategori_pa_id=kategori_pa_id,
        angkatan_id=angkatan_id,
        exact_count=2,
    )


def get_kelompok_with_one_pembimbing_by_context(prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> dict:
    return get_kelompok_by_pembimbing_count(
        prodi_id=prodi_id,
        kategori_pa_id=kategori_pa_id,
        angkatan_id=angkatan_id,
        exact_count=1,
    )


def search_pembimbing(nama: str = None, keahlian: str = None) -> dict:
    """Kompatibilitas lama: arahkan ke pencarian by nama dosen."""
    if nama:
        return get_pembimbing_by_dosen_name(nama)
    return get_pembimbing_list()


def get_pembimbing_by_keahlian(keahlian: str) -> dict:
    """Kompatibilitas lama: keahlian dipetakan ke jabatan/prodi di output existing."""
    try:
        res = get_pembimbing_list()
        if res.get("status") != "success":
            return res

        key = (keahlian or "").lower().strip()
        filtered = [
            row for row in res.get("data", [])
            if key in (row.get("dosen_prodi", "").lower())
            or key in (row.get("jabatan_akademik_desc", "").lower())
        ]

        if not filtered:
            return {"status": "empty", "message": f"Tidak ada pembimbing dengan keahlian {keahlian}"}

        return {
            "status": "success",
            "total": len(filtered),
            "keahlian": keahlian,
            "data": filtered,
        }
    except Exception as e:
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_pembimbing_tersedia() -> dict:
    """Kompatibilitas lama: kandidat dosen pembimbing aktif lintas konteks."""
    try:
        session = SessionLocal()
        candidates = _get_candidate_pembimbing_by_context(session)
        session.close()

        if not candidates:
            return {"status": "empty", "message": "Tidak ada pembimbing yang tersedia"}

        data = [
            {
                "user_id": c["user_id"],
                "dosen_nama": c["nama"],
                "dosen_email": c["email"],
                "dosen_prodi": c["prodi"],
                "jabatan_akademik_desc": c["jabatan_akademik_desc"],
                "role_name": c["role_name"],
            }
            for c in candidates
        ]

        return {"status": "success", "total": len(data), "data": data}
    except Exception as e:
        return {"status": "error", "message": f"Error: {str(e)}"}
