from __future__ import annotations

"""
Tools pembimbing kelompok.

Fitur utama:
- Generate assignment pembimbing per kelompok (1-2 pembimbing per kelompok).
- Pembagian mempertimbangkan jabatan akademik dosen:
  semakin tinggi jabatan, semakin kecil beban bimbingan.
- Query assignment pembimbing berdasarkan konteks dosen.
- Support constraint khusus: dosen tertentu hanya pembimbing 2, dosen untuk kelompok spesifik, dll.
"""

from datetime import datetime
from math import ceil
import random
from typing import Dict, List, Optional, Set, Tuple
import re

from sqlalchemy import func, or_

from core.database import SessionLocal
from models.dosen import Dosen
from models.dosen_role import DosenRole
from models.kelompok import Kelompok
from models.pembimbing import Pembimbing
from models.role import Role
from models.tahun_ajaran import TahunAjaran


DOSEN_ID_FALLBACK_OFFSET = 1000000000

# Jabatan yang benar-benar dihindari sebagai kandidat pembimbing
# Lektor kepala dan lektor tetap boleh menjadi pembimbing, tetapi kapasitasnya dibatasi melalui bobot jabatan.
DISRECOMMENDED_POSITIONS = {"profesor", "guru besar"}


def _is_disrecommended_position(jabatan_desc: Optional[str]) -> bool:
    """Check apakah jabatan termasuk yang tidak disarankan untuk pembimbing."""
    if not jabatan_desc:
        return False
    
    jabatan_lower = jabatan_desc.lower().strip()
    for pos in DISRECOMMENDED_POSITIONS:
        if pos in jabatan_lower:
            return True
    return False


def _normalize_dosen_name(nama: str) -> str:
    """Normalize nama dosen untuk matching (case-insensitive, trim)."""
    return nama.lower().strip()


def _match_dosen_by_name(nama_search: str, candidates: List[Dict]) -> Optional[Dict]:
    """
    Find dosen candidate by nama (case-insensitive substring match).
    Returns first match atau None.
    """
    search_normalized = _normalize_dosen_name(nama_search)
    for candidate in candidates:
        candidate_normalized = _normalize_dosen_name(candidate.get("nama", ""))
        if search_normalized in candidate_normalized or candidate_normalized in search_normalized:
            return candidate
    return None


def _extract_dosen_constraints_from_prompt(prompt: str) -> Dict:
    """
    Parse prompt user untuk mengekstrak constraint khusus pada dosen pembimbing.
    
    Examples:
    - "Ana Muliyana, M.Pd. hanya bisa menjadi pembimbing 2"
    - "Dr. Arnaldo Marulitua Sinaga, ST., M.InfoTech. menjadi pembimbing 1 untuk kelompok 1 dan 2"
    
    Returns dict dengan struktur:
    {
        "only_pembimbing_2": ["Nama Dosen 1", "Nama Dosen 2"],
        "only_pembimbing_1": ["Nama Dosen 3"],
        "kelompok_specific": {
            "Nama Dosen 4": [1, 2, 3],  # dosen ini hanya untuk kelompok 1, 2, 3
        },
        "exclude_dosen": ["Nama Dosen 5"],  # dosen yang tidak ingin dipilih
    }
    """
    constraints = {
        "only_pembimbing_2": [],
        "only_pembimbing_1": [],
        "kelompok_specific": {},
        "exclude_dosen": [],
    }
    
    if not prompt:
        return constraints
    
    prompt_lower = prompt.lower()

    def _clean_name(raw_name: str) -> str:
        cleaned = raw_name.strip()
        cleaned = re.sub(r"^(nama\s+dosen\s+)", "", cleaned)
        cleaned = re.sub(r"^(dr\.?\s+|prof\.?\s+)", "", cleaned)
        if "," in cleaned:
            cleaned = cleaned.split(",", 1)[0].strip()
        cleaned = re.sub(r"\b(hanya|bisa|dapat|menjadi|jadi|untuk|kelompok|pembimbing)\b.*$", "", cleaned).strip()
        cleaned = re.sub(r"\s+,\s*.*$", "", cleaned).strip()

        noisy_prefixes = (
            "buatlah", "tampilkan", "generate", "buat", "tolong", "mohon",
            "dosen pembimbing", "pembimbing untuk", "untuk setiap", "yang dimana"
        )
        if cleaned.startswith(noisy_prefixes):
            return ""
        return cleaned

    def _append_unique(target: List[str], value: str) -> None:
        if value and value not in target:
            target.append(value)

    def _extract_name_from_prefix(prefix_text: str) -> str:
        prefix = prefix_text.strip()
        marker_pos = prefix.lower().rfind("nama dosen")
        if marker_pos != -1:
            prefix = prefix[marker_pos + len("nama dosen"):].strip()
        return _clean_name(prefix)

    # Format: "... nama dosen X hanya bisa menjadi pembimbing 2"
    pb2_keywords = [
        "hanya bisa menjadi pembimbing 2",
        "hanya dapat menjadi pembimbing 2",
    ]
    for keyword in pb2_keywords:
        for match in re.finditer(re.escape(keyword), prompt_lower):
            nama = _extract_name_from_prefix(prompt_lower[:match.start()])
            if nama and len(nama) > 2:
                _append_unique(constraints["only_pembimbing_2"], nama)

    # Normalize text for kelompok parsing
    prompt_normalized = re.sub(r"\s+dan\s+kelompok\s+", " dan ", prompt_lower)

    # Format: "... nama dosen X menjadi pembimbing 1 untuk kelompok 1 dan kelompok 2"
    pb1_keyword = "menjadi pembimbing 1 untuk kelompok"
    for match in re.finditer(re.escape(pb1_keyword), prompt_normalized):
        nama = _extract_name_from_prefix(prompt_normalized[:match.start()])
        kelompok_tail = prompt_normalized[match.end():].strip()
        
        # PENTING: stop at next "dan nama dosen" atau "menjadi pembimbing" untuk multipl constraint
        next_marker_match = re.search(r"(dan\s+nama\s+dosen|menjadi\s+pembimbing)", kelompok_tail)
        if next_marker_match:
            kelompok_tail = kelompok_tail[:next_marker_match.start()]
        
        kelompok_nums = sorted(set(int(k) for k in re.findall(r"\d+", kelompok_tail)))
        if nama and len(nama) > 2 and "buatlah dosen" not in nama and kelompok_nums:
            constraints["kelompok_specific"][nama] = kelompok_nums
            _append_unique(constraints["only_pembimbing_1"], nama)
    
    return constraints





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


def _get_candidate_pembimbing_by_context(session, prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None, exclude_disrecommended: bool = False, exclude_dosen_names: List[str] = None):
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
    exclude_names_normalized = []
    if exclude_dosen_names:
        exclude_names_normalized = [_normalize_dosen_name(n) for n in exclude_dosen_names]
    
    for dosen in dosen_rows:
        # Filter: Skip dosen dengan jabatan yang tidak disarankan
        if exclude_disrecommended and _is_disrecommended_position(dosen.jabatan_akademik_desc):
            continue
        
        # Filter: Skip dosen yang di-exclude explicit
        if exclude_names_normalized:
            dosen_name_normalized = _normalize_dosen_name(dosen.nama)
            if any(exclude_name in dosen_name_normalized or dosen_name_normalized in exclude_name for exclude_name in exclude_names_normalized):
                continue
        
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
    persist: bool = False,
    exclude_disrecommended: bool = True,
    constraints: Dict = None,
) -> dict:
    """
    Generate assignment pembimbing ke kelompok dengan support constraints khusus.

    Parameters:
    - exclude_disrecommended: Jika True, exclude dosen dengan jabatan profesor/guru besar
    - constraints: Dict dengan struktur:
        {
            "only_pembimbing_2": ["Nama Dosen"],  # dosen ini hanya bisa jadi pembimbing 2
            "only_pembimbing_1": ["Nama Dosen"],  # dosen ini hanya bisa jadi pembimbing 1
            "kelompok_specific": {"Nama Dosen": [1, 2]},  # dosen hanya untuk kelompok tertentu
            "exclude_dosen": ["Nama Dosen"],  # dosen yang tidak ingin dipilih
        }

    Aturan:
    - Setiap kelompok mendapat minimal 1 pembimbing.
    - Maksimal 2 pembimbing per kelompok.
    - Dosen dengan jabatan lebih tinggi cenderung membimbing lebih sedikit kelompok.
    - Constraint khusus diterapkan jika ada.
    """
    session = SessionLocal()
    try:
        if not constraints:
            constraints = {
                "only_pembimbing_2": [],
                "only_pembimbing_1": [],
                "kelompok_specific": {},
                "exclude_dosen": [],
            }
        
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

        # Ambil candidates dengan filter exclude_disrecommended dan exclude_dosen
        candidates = _get_candidate_pembimbing_by_context(
            session, 
            prodi_id, 
            kategori_pa_id, 
            angkatan_id,
            exclude_disrecommended=exclude_disrecommended,
            exclude_dosen_names=constraints.get("exclude_dosen", [])
        )
        if not candidates:
            session.close()
            return {
                "status": "empty",
                "message": "Tidak ada dosen pembimbing kandidat pada konteks ini",
            }

        # Build constraint maps
        only_pb2_names = [_normalize_dosen_name(n) for n in constraints.get("only_pembimbing_2", [])]
        only_pb1_names = [_normalize_dosen_name(n) for n in constraints.get("only_pembimbing_1", [])]
        kelompok_specific_constraints = constraints.get("kelompok_specific", {})
        
        # Map candidate names untuk matching constraint
        candidate_name_to_user_id: Dict[str, int] = {}
        for c in candidates:
            candidate_name_to_user_id[_normalize_dosen_name(c["nama"])] = c["user_id"]

        explicit_requested_user_ids: Set[int] = set()
        for raw_name in (
            constraints.get("only_pembimbing_1", [])
            + constraints.get("only_pembimbing_2", [])
            + list(kelompok_specific_constraints.keys())
        ):
            matched = _match_dosen_by_name(raw_name, candidates)
            if matched:
                explicit_requested_user_ids.add(matched["user_id"])

        # Map permintaan eksplisit ke user_id dan nomor kelompok target.
        explicit_group_targets: Dict[int, Dict[int, int]] = {}
        for raw_name, kelompok_numbers in kelompok_specific_constraints.items():
            matched = _match_dosen_by_name(raw_name, candidates)
            if not matched:
                continue

            user_id = matched["user_id"]
            requires_pb1 = _normalize_dosen_name(raw_name) in only_pb1_names
            requires_pb2 = _normalize_dosen_name(raw_name) in only_pb2_names
            if not requires_pb1 and not requires_pb2:
                requires_pb1 = True

            for nomor in kelompok_numbers:
                for kelompok in kelompoks:
                    if str(kelompok.nomor_kelompok) != str(nomor):
                        continue
                    explicit_group_targets.setdefault(kelompok.id, {})
                    if requires_pb1:
                        explicit_group_targets[kelompok.id][1] = user_id
                    if requires_pb2:
                        explicit_group_targets[kelompok.id][2] = user_id

        group_ids = [k.id for k in kelompoks]
        kelompok_id_to_nomor = {k.id: k.nomor_kelompok for k in kelompoks}

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

        # PENTING: Pastikan dosen yang ada di explicit_group_targets punya capacity cukup
        # untuk semua kelompok yang diminta
        for kelompok_id, positions in explicit_group_targets.items():
            for position, user_id in positions.items():
                # Count berapa banyak kelompok yang perlu dosen ini
                count_assignments_for_user = 0
                for k_id, pos_map in explicit_group_targets.items():
                    if pos_map.get(position) == user_id:
                        count_assignments_for_user += 1
                
                # Ensure capacity >= count_assignments
                if capacities.get(user_id, 0) < count_assignments_for_user:
                    capacities[user_id] = count_assignments_for_user

        # Calculate total capacity needed including explicit slots and second pembimbing
        explicit_slot_count = sum(len(slot_map) for slot_map in explicit_group_targets.values())
        
        # MINIMUM capacity: at least total_groups (1 pb per group)
        # PLUS explicit slots for constrained dosen
        # PLUS buffer for pb2 slots
        min_total_capacity = total_groups
        if max_per_group >= 2:
            min_total_capacity += max(1, total_groups // 3)  # buffer for pb2
        
        # Ensure total capacity meets minimum
        while sum(capacities.values()) < min_total_capacity:
            for c in candidates:
                capacities[c["user_id"]] += 1
                if sum(capacities.values()) >= min_total_capacity:
                    break

        group_assignments: Dict[int, List[Tuple[int, int]]] = {k.id: [] for k in kelompoks}  # List of (user_id, pembimbing_position)

        def is_pb2_only(user_id: int) -> bool:
            """Check apakah user_id termasuk only_pembimbing_2."""
            if user_id not in candidate_map:
                return False
            dosen_name_normalized = _normalize_dosen_name(candidate_map[user_id]["nama"])
            return any(pb2_name in dosen_name_normalized or dosen_name_normalized in pb2_name for pb2_name in only_pb2_names)

        def is_pb1_only(user_id: int) -> bool:
            """Check apakah user_id termasuk only_pembimbing_1."""
            if user_id not in candidate_map:
                return False
            dosen_name_normalized = _normalize_dosen_name(candidate_map[user_id]["nama"])
            return any(pb1_name in dosen_name_normalized or dosen_name_normalized in pb1_name for pb1_name in only_pb1_names)

        def is_explicit_requested(user_id: int) -> bool:
            return user_id in explicit_requested_user_ids

        def get_allowed_kelompok_for_dosen(user_id: int) -> Optional[Set[int]]:
            """Return set kelompok nomor yang diizinkan untuk dosen, atau None jika semua."""
            if user_id not in candidate_map:
                return None
            dosen_name = _normalize_dosen_name(candidate_map[user_id]["nama"])
            for constraint_name, kelompok_list in kelompok_specific_constraints.items():
                constraint_name_normalized = _normalize_dosen_name(constraint_name)
                if constraint_name_normalized in dosen_name or dosen_name in constraint_name_normalized:
                    return set(kelompok_list)
            return None

        def pick_next_candidate(exclude_user_ids: List[int], pembimbing_position: int = 1, current_kelompok_id: int = None):
            """
            Pick next candidate berdasarkan:
            - pembimbing_position: 1 atau 2 (position di kelompok)
            - exclude_user_ids: user_id yang tidak bisa dipilih
            - current_kelompok_id: kelompok_id saat ini (untuk check constraint kelompok_specific)
            
            Returns candidate dict atau None.
            """
            def collect_available(ignore_group_specific: bool = False):
                available = []
                for c in candidates:
                    user_id = c["user_id"]

                    if user_id in exclude_user_ids:
                        continue

                    if loads[user_id] >= capacities[user_id]:
                        continue

                    if is_pb2_only(user_id) and pembimbing_position != 2:
                        continue

                    if is_pb1_only(user_id) and pembimbing_position != 1:
                        continue

                    # Do not recommend Lektor / Lektor Kepala as Pembimbing 2
                    jabatan_desc = c.get("jabatan_akademik_desc") or ""
                    jabatan_lower = jabatan_desc.lower()
                    if pembimbing_position == 2 and ("lektor" in jabatan_lower) and not is_explicit_requested(user_id):
                        continue

                    if current_kelompok_id is not None:
                        reserved_positions = explicit_group_targets.get(current_kelompok_id, {})
                        reserved_user_for_position = reserved_positions.get(pembimbing_position)
                        if reserved_user_for_position is not None and reserved_user_for_position != user_id:
                            continue

                        if user_id in explicit_requested_user_ids:
                            allowed_kelompok = get_allowed_kelompok_for_dosen(user_id)
                            if allowed_kelompok is not None:
                                kelompok_nomor = kelompok_id_to_nomor.get(current_kelompok_id)
                                if kelompok_nomor not in allowed_kelompok:
                                    continue
                            
                    if current_kelompok_id is not None:
                        reserved_positions = explicit_group_targets.get(current_kelompok_id, {})
                        if pembimbing_position in reserved_positions and reserved_positions[pembimbing_position] == user_id:
                            available.append(c)
                            continue

                    if exclude_disrecommended and _is_disrecommended_position(c.get("jabatan_akademik_desc")) and not is_explicit_requested(user_id):
                        continue

                    if not ignore_group_specific:
                        allowed_kelompok = get_allowed_kelompok_for_dosen(user_id)
                        if allowed_kelompok is not None and current_kelompok_id is not None:
                            kelompok_nomor = kelompok_id_to_nomor.get(current_kelompok_id)
                            if kelompok_nomor not in allowed_kelompok:
                                continue

                    available.append(c)
                return available

            available = collect_available(ignore_group_specific=False)
            if not available:
                available = collect_available(ignore_group_specific=True)
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

        # Pas 0: pasang dosen yang diminta secara eksplisit untuk kelompok tertentu.
        for kelompok in kelompoks:
            reserved_positions = explicit_group_targets.get(kelompok.id, {})
            for pembimbing_position, user_id in reserved_positions.items():
                if any(existing_position == pembimbing_position for _, existing_position in group_assignments[kelompok.id]):
                    continue
                if loads.get(user_id, 0) >= capacities.get(user_id, 0):
                    continue
                group_assignments[kelompok.id].append((user_id, pembimbing_position))
                loads[user_id] += 1

        priority_groups = []
        fallback_groups = []
        for kelompok in kelompoks:
            nomor = str(kelompok.nomor_kelompok)
            has_specific_constraint = any(nomor in {str(n) for n in nums} for nums in kelompok_specific_constraints.values())
            if has_specific_constraint:
                priority_groups.append(kelompok)
            else:
                fallback_groups.append(kelompok)

        random.shuffle(fallback_groups)
        ordered_kelompoks = priority_groups + fallback_groups

        # Pass 1: minimal 1 pembimbing per kelompok.
        # Multi-level fallback untuk ensure semua kelompok ter-assign
        pass1_failed_groups = []
        
        for kelompok in ordered_kelompoks:
            if any(position == 1 for _, position in group_assignments[kelompok.id]):
                continue
            
            # Level 1: Try dengan constraints
            cand = pick_next_candidate([], pembimbing_position=1, current_kelompok_id=kelompok.id)
            
            if not cand:
                pass1_failed_groups.append(kelompok)
                continue

            uid = cand["user_id"]
            group_assignments[kelompok.id].append((uid, 1))
            loads[uid] += 1
        
        # Level 2: Untuk group yang gagal, try ulang dengan least loaded dosen yang paling tepat
        for kelompok in pass1_failed_groups:
            best_cand = None
            best_load_ratio = float('inf')
            
            for c in candidates:
                user_id = c["user_id"]
                
                # Skip if already assigned at position 1 to this kelompok
                if any(uid == user_id and pos == 1 for uid, pos in group_assignments[kelompok.id]):
                    continue
                
                # Skip if PB2-only dan kita butuh PB1
                if is_pb2_only(user_id):
                    continue
                
                # Calculate load ratio
                load_ratio = loads[user_id] / max(1, capacities[user_id])
                if load_ratio < best_load_ratio:
                    best_load_ratio = load_ratio
                    best_cand = c
            
            if best_cand:
                uid = best_cand["user_id"]
                group_assignments[kelompok.id].append((uid, 1))
                loads[uid] += 1
            else:
                # Ultimate fallback: assign anyone who has capacity
                for c in candidates:
                    user_id = c["user_id"]
                    if loads[user_id] < capacities[user_id]:
                        if not is_pb2_only(user_id):
                            group_assignments[kelompok.id].append((user_id, 1))
                            loads[user_id] += 1
                            break
        
        # Verify all kelompok have at least PB1
        for kelompok in kelompoks:
            if not any(position == 1 for _, position in group_assignments[kelompok.id]):
                session.close()
                return {
                    "status": "error",
                    "message": f"Kapasitas pembimbing tidak cukup untuk kelompok {kelompok.nomor_kelompok}",
                }

        # Pass 2: tambah pembimbing kedua jika kapasitas memungkinkan dan constraint memungkinkan.
        if max_per_group >= 2 and total_candidates > 1:
            second_pass_groups = ordered_kelompoks[:]
            random.shuffle(second_pass_groups)
            for kelompok in second_pass_groups:
                if any(position == 2 for _, position in group_assignments[kelompok.id]):
                    continue
                
                # Ambil user_id yang sudah assigned untuk exclude
                exclude_uids = [uid for uid, _ in group_assignments[kelompok.id]]
                
                cand = pick_next_candidate(exclude_uids, pembimbing_position=2, current_kelompok_id=kelompok.id)
                if not cand:
                    continue
                
                uid = cand["user_id"]
                group_assignments[kelompok.id].append((uid, 2))
                loads[uid] += 1

        inserts = []
        dosen_role_inserts = []
        
        # Get active tahun_ajaran_id from database
        active_tahun_ajaran = session.query(TahunAjaran).filter(
            TahunAjaran.status == "Aktif"
        ).first()
        tahun_ajaran_id = active_tahun_ajaran.id if active_tahun_ajaran else 1
        
        # Track kombinasi DosenRole yang sudah ditambah (untuk avoid duplicates dalam loop)
        added_dosen_roles = set()
        
        if persist:
            if existing_assignments and replace_existing:
                session.query(Pembimbing).filter(Pembimbing.kelompok_id.in_(group_ids)).delete(synchronize_session=False)

            now = datetime.now()
            for kelompok in kelompoks:
                for user_id, pembimbing_position in group_assignments[kelompok.id]:
                    inserts.append(
                        Pembimbing(
                            user_id=user_id,
                            kelompok_id=kelompok.id,
                            created_at=now,
                            updated_at=now,
                        )
                    )
                    
                    # Tentukan role_id berdasarkan pembimbing_position
                    # role_id 3 = Pembimbing 1, role_id 5 = Pembimbing 2
                    role_id = 3 if pembimbing_position == 1 else 5
                    
                    # Buat kombinasi key untuk tracking
                    role_key = (user_id, role_id, prodi_id, kategori_pa_id, angkatan_id, tahun_ajaran_id)
                    
                    # Cek apakah sudah ada di dosen_roles database
                    existing_role = session.query(DosenRole).filter(
                        DosenRole.user_id == user_id,
                        DosenRole.role_id == role_id,
                        DosenRole.prodi_id == prodi_id,
                        DosenRole.KPA_id == kategori_pa_id,
                        DosenRole.TM_id == angkatan_id,
                        DosenRole.tahun_ajaran_id == tahun_ajaran_id,
                    ).first()
                    
                    # Jika belum ada di database DAN belum ditambah dalam loop ini
                    if not existing_role and role_key not in added_dosen_roles:
                        dosen_role_inserts.append(
                            DosenRole(
                                user_id=user_id,
                                role_id=role_id,
                                prodi_id=prodi_id,
                                KPA_id=kategori_pa_id,
                                TM_id=angkatan_id,
                                tahun_ajaran_id=tahun_ajaran_id,
                                status="Aktif",
                            )
                        )
                        added_dosen_roles.add(role_key)

            session.add_all(inserts)
            if dosen_role_inserts:
                session.add_all(dosen_role_inserts)
            session.commit()

        grouped_output = []
        for kelompok in kelompoks:
            pembimbing_rows = []
            for user_id, pembimbing_position in group_assignments[kelompok.id]:
                c = candidate_map.get(user_id, {})
                pembimbing_rows.append(
                    {
                        "user_id": user_id,
                        "dosen_nama": c.get("nama", "N/A"),
                        "jabatan_akademik_desc": c.get("jabatan_akademik_desc", "N/A"),
                        "role_name": c.get("role_name", "N/A"),
                        "pembimbing_position": pembimbing_position,
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
                "exclude_disrecommended": exclude_disrecommended,
                "constraints_applied": bool(constraints and any(
                    constraints.get("only_pembimbing_2") or 
                    constraints.get("only_pembimbing_1") or 
                    constraints.get("kelompok_specific") or 
                    constraints.get("exclude_dosen")
                )),
            },
            "groups": grouped_output,
            "dosen_loads": dosen_loads,
            "filters": {
                "prodi_id": prodi_id,
                "kategori_pa_id": kategori_pa_id,
                "angkatan_id": angkatan_id,
            },
            "constraints": constraints if constraints else None,
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
