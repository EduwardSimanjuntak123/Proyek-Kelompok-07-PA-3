from __future__ import annotations

"""
Tools penguji kelompok.

Aturan utama:
- Setiap kelompok wajib mendapat 2 penguji.
- Dosen pembimbing pada kelompok yang sama TIDAK BOLEH menjadi penguji kelompok tersebut.
"""

from datetime import datetime
from math import ceil
from typing import Dict, List, Optional, Set

from sqlalchemy import func, or_

from core.database import SessionLocal
from models.dosen import Dosen
from models.dosen_role import DosenRole
from models.kelompok import Kelompok
from models.pembimbing import Pembimbing
from models.penguji import Penguji
from models.role import Role


DOSEN_ID_FALLBACK_OFFSET = 1000000000


def _jabatan_rank(jabatan_desc: Optional[str]) -> int:
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


def _get_candidate_penguji_by_context(session, prodi_id: int = None):
    query = session.query(Dosen)
    if prodi_id:
        query = query.filter(Dosen.prodi_id == prodi_id)

    dosen_rows = query.order_by(Dosen.nama.asc(), Dosen.id.asc()).all()
    if not dosen_rows:
        return []

    candidates = []
    for dosen in dosen_rows:
        assignment_user_id = int(dosen.user_id) if dosen.user_id is not None else DOSEN_ID_FALLBACK_OFFSET + int(dosen.id)
        candidates.append({
            "user_id": assignment_user_id,
            "source_dosen_id": dosen.id,
            "source_user_id": dosen.user_id,
            "nama": dosen.nama,
            "email": dosen.email,
            "prodi": dosen.prodi,
            "jabatan_akademik_desc": dosen.jabatan_akademik_desc,
            "weight": _jabatan_weight(dosen.jabatan_akademik_desc),
            "rank": _jabatan_rank(dosen.jabatan_akademik_desc),
        })

    return candidates


def _get_pembimbing_map_for_groups(session, group_ids: List[int]) -> Dict[int, Set[int]]:
    rows = session.query(Pembimbing.kelompok_id, Pembimbing.user_id).filter(Pembimbing.kelompok_id.in_(group_ids)).all()
    result: Dict[int, Set[int]] = {gid: set() for gid in group_ids}
    for kelompok_id, user_id in rows:
        if kelompok_id not in result:
            result[kelompok_id] = set()
        result[kelompok_id].add(int(user_id))
    return result


def _get_assignment_rows_by_context(session, prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None):
    query = (
        session.query(Penguji, Kelompok, Dosen)
        .join(Kelompok, Kelompok.id == Penguji.kelompok_id)
        .outerjoin(
            Dosen,
            or_(
                Dosen.user_id == Penguji.user_id,
                (Dosen.id + DOSEN_ID_FALLBACK_OFFSET) == Penguji.user_id,
            ),
        )
    )

    if prodi_id:
        query = query.filter(Kelompok.prodi_id == prodi_id)
    if kategori_pa_id:
        query = query.filter(Kelompok.KPA_id == kategori_pa_id)
    if angkatan_id:
        query = query.filter(Kelompok.TM_id == angkatan_id)

    rows = query.order_by(Kelompok.id.asc(), Penguji.id.asc()).all()

    data = []
    for penguji, kelompok, dosen in rows:
        data.append({
            "id": penguji.id,
            "kelompok_id": kelompok.id,
            "nomor_kelompok": kelompok.nomor_kelompok,
            "user_id": penguji.user_id,
            "dosen_nama": dosen.nama if dosen else "N/A",
            "dosen_email": dosen.email if dosen else "N/A",
            "jabatan_akademik_desc": dosen.jabatan_akademik_desc if dosen else "N/A",
            "prodi_id": kelompok.prodi_id,
            "kategori_pa_id": kelompok.KPA_id,
            "angkatan_id": kelompok.TM_id,
        })
    return data


def check_existing_penguji_by_context(prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> dict:
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
        kelompok_with_penguji = len(set(item["kelompok_id"] for item in assignment_data))

        session.close()

        return {
            "status": "success",
            "exists": len(assignment_data) > 0,
            "total_assignments": len(assignment_data),
            "total_kelompok": total_kelompok,
            "kelompok_with_penguji": kelompok_with_penguji,
            "kelompok_without_penguji": max(0, total_kelompok - kelompok_with_penguji),
            "message": "Sudah ada data penguji" if assignment_data else "Belum ada data penguji",
        }
    except Exception as e:
        return {"status": "error", "message": f"Error: {str(e)}"}


def get_penguji_assignments_by_context(prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> dict:
    try:
        session = SessionLocal()
        data = _get_assignment_rows_by_context(session, prodi_id, kategori_pa_id, angkatan_id)
        session.close()

        if not data:
            return {
                "status": "empty",
                "message": "Belum ada assignment penguji pada konteks ini",
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


def get_penguji_of_kelompok(nomor_kelompok: str, prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> dict:
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
            session.query(Penguji, Dosen)
            .outerjoin(
                Dosen,
                or_(
                    Dosen.user_id == Penguji.user_id,
                    (Dosen.id + DOSEN_ID_FALLBACK_OFFSET) == Penguji.user_id,
                ),
            )
            .filter(Penguji.kelompok_id == kelompok.id)
            .order_by(Penguji.id.asc())
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
                "message": f"Kelompok {nomor_kelompok} belum memiliki penguji",
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


def generate_penguji_assignments_by_context(
    prodi_id: int = None,
    kategori_pa_id: int = None,
    angkatan_id: int = None,
    min_per_group: int = 2,
    max_per_group: int = 2,
    replace_existing: bool = True,
    persist: bool = True,
) -> dict:
    session = SessionLocal()
    try:
        # Ketentuan: penguji wajib 2 per kelompok.
        min_per_group = 2
        max_per_group = 2
        if max_per_group < min_per_group:
            max_per_group = min_per_group
        max_per_group = min(max_per_group, 2)

        kelompoks = _get_kelompok_by_context(session, prodi_id, kategori_pa_id, angkatan_id)
        if not kelompoks:
            session.close()
            return {"status": "empty", "message": "Tidak ada kelompok pada konteks ini"}

        candidates = _get_candidate_penguji_by_context(session, prodi_id)
        if not candidates:
            session.close()
            return {"status": "empty", "message": "Tidak ada kandidat dosen penguji pada konteks ini"}

        group_ids = [k.id for k in kelompoks]
        pembimbing_map = _get_pembimbing_map_for_groups(session, group_ids)

        existing_assignments = session.query(Penguji).filter(Penguji.kelompok_id.in_(group_ids)).all()
        if existing_assignments and not replace_existing:
            session.close()
            return {
                "status": "conflict",
                "message": "Penguji sudah ada. Aktifkan replace_existing untuk generate ulang.",
                "existing_assignments": len(existing_assignments),
            }

        total_groups = len(kelompoks)
        total_candidates = len(candidates)

        avg_weight = sum(c["weight"] for c in candidates) / max(1, total_candidates)
        base_capacity = max(1, ceil((total_groups * min_per_group) / max(1, total_candidates)))

        capacities: Dict[int, int] = {}
        loads: Dict[int, int] = {}
        candidate_map = {c["user_id"]: c for c in candidates}

        for c in candidates:
            scaled = round(base_capacity * (c["weight"] / max(avg_weight, 1e-9)))
            capacities[c["user_id"]] = max(1, int(scaled))
            loads[c["user_id"]] = 0

        min_total_needed = total_groups * min_per_group
        while sum(capacities.values()) < min_total_needed:
            for c in candidates:
                capacities[c["user_id"]] += 1
                if sum(capacities.values()) >= min_total_needed:
                    break

        group_assignments: Dict[int, List[int]] = {k.id: [] for k in kelompoks}

        def pick_next_candidate(exclude_user_ids: Set[int]):
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

        # Pass 1: minimal penguji per kelompok
        for kelompok in kelompoks:
            excluded = set(group_assignments[kelompok.id]) | set(pembimbing_map.get(kelompok.id, set()))
            cand = pick_next_candidate(excluded)
            if not cand:
                session.close()
                return {
                    "status": "error",
                    "message": "Kandidat penguji tidak cukup setelah mengecualikan pembimbing kelompok.",
                }

            uid = cand["user_id"]
            group_assignments[kelompok.id].append(uid)
            loads[uid] += 1

        # Pass 2: penguji kedua WAJIB per kelompok.
        for kelompok in kelompoks:
            if len(group_assignments[kelompok.id]) >= 2:
                continue

            excluded = set(group_assignments[kelompok.id]) | set(pembimbing_map.get(kelompok.id, set()))
            cand = pick_next_candidate(excluded)
            if not cand:
                session.close()
                return {
                    "status": "error",
                    "message": "Tidak dapat memenuhi ketentuan wajib 2 penguji per kelompok setelah mengecualikan pembimbing kelompok.",
                }

            uid = cand["user_id"]
            group_assignments[kelompok.id].append(uid)
            loads[uid] += 1

        inserts = []
        dosen_role_inserts = []
        if persist:
            if existing_assignments and replace_existing:
                session.query(Penguji).filter(Penguji.kelompok_id.in_(group_ids)).delete(synchronize_session=False)

            now = datetime.now()
            for kelompok in kelompoks:
                for idx, user_id in enumerate(group_assignments[kelompok.id]):
                    inserts.append(
                        Penguji(
                            user_id=user_id,
                            kelompok_id=kelompok.id,
                            created_at=now,
                            updated_at=now,
                        )
                    )
                    
                    # Tentukan role_id berdasarkan posisi penguji (1st atau 2nd)
                    # role_id 2 = Penguji 1, role_id 4 = Penguji 2
                    role_id = 2 if idx == 0 else 4
                    
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
            penguji_rows = []
            for user_id in group_assignments[kelompok.id]:
                c = candidate_map.get(user_id, {})
                penguji_rows.append(
                    {
                        "user_id": user_id,
                        "dosen_nama": c.get("nama", "N/A"),
                        "jabatan_akademik_desc": c.get("jabatan_akademik_desc", "N/A"),
                    }
                )

            grouped_output.append(
                {
                    "kelompok_id": kelompok.id,
                    "nomor_kelompok": kelompok.nomor_kelompok,
                    "penguji_count": len(penguji_rows),
                    "penguji": penguji_rows,
                    "pembimbing_user_ids": sorted(list(pembimbing_map.get(kelompok.id, set()))),
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
            "message": "Generate penguji kelompok berhasil",
            "summary": {
                "total_kelompok": total_groups,
                "total_kandidat_penguji": total_candidates,
                "total_assignments": sum(len(v) for v in group_assignments.values()),
                "min_per_group": min_per_group,
                "max_per_group": max_per_group,
                "replace_existing": replace_existing,
                "persisted": persist,
                "rule": "pembimbing tidak boleh menjadi penguji pada kelompok yang sama",
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
