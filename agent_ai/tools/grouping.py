"""Tools untuk membentuk kelompok berdasarkan instruksi user."""

import math
import random
import re
from typing import Dict, List

from core.database import SessionLocal
from models.kelompok import Kelompok
from models.kelompokMahasiswa import KelompokMahasiswa
from models.mahasiswa import Mahasiswa
from models.tahun_masuk import TahunMasuk


def _extract_grouping_instruction(prompt: str) -> Dict:
    """Extract aturan pembagian kelompok dari prompt natural language."""
    prompt_lower = (prompt or "").lower()

    group_count = None
    group_size = None
    min_size = None
    max_size = None

    count_patterns = [
        r"buat\s+(\d+)\s+kelompok",
        r"bagi\s+jadi\s+(\d+)\s+kelompok",
        r"(\d+)\s+kelompok",
    ]
    for pattern in count_patterns:
        match = re.search(pattern, prompt_lower)
        if match:
            group_count = int(match.group(1))
            break

    # Support explicit range, contoh: "5-6 orang perkelompok"
    range_match = re.search(
        r"(\d+)\s*[-/]\s*(\d+)\s*(?:orang|mahasiswa)?\s*(?:per\s*kelompok|perkelompok|tiap\s*kelompok)?",
        prompt_lower,
    )
    if range_match:
        left = int(range_match.group(1))
        right = int(range_match.group(2))
        min_size = min(left, right)
        max_size = max(left, right)

    size_patterns = [
        r"(\d+)\s+(?:orang|mahasiswa)\s+(?:per|tiap)\s+kelompok",
        r"(\d+)\s+(?:orang|mahasiswa)\s+perkelompok",
        r"kelompok\s+(?:isi|berisi)\s+(\d+)",
        r"masing(?:-|\s*)masing\s+(\d+)",
    ]
    for pattern in size_patterns:
        match = re.search(pattern, prompt_lower)
        if match:
            group_size = int(match.group(1))
            break

    min_match = re.search(r"minimal\s+(\d+)", prompt_lower)
    if min_match:
        min_size = int(min_match.group(1))

    max_match = re.search(r"maks(?:imal)?\s+(\d+)", prompt_lower)
    if max_match:
        max_size = int(max_match.group(1))

    randomize = True
    if any(word in prompt_lower for word in ["tanpa acak", "urut", "berdasarkan nim", "jangan random"]):
        randomize = False

    use_previous_group_size = any(
        phrase in prompt_lower
        for phrase in [
            "ukuran kelompok sebelumnya",
            "sesuai ukuran kelompok sebelumnya",
            "mengikuti ukuran kelompok sebelumnya",
            "seperti kelompok sebelumnya",
            "ikuti ukuran kelompok sebelumnya",
            "n+1",
            "kelompok berikutnya",
        ]
    )

    return {
        "group_count": group_count,
        "group_size": group_size,
        "min_size": min_size,
        "max_size": max_size,
        "randomize": randomize,
        "use_previous_group_size": use_previous_group_size,
    }


def _get_context_group_start_number(session, prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> int:
    """Ambil nomor kelompok awal untuk kelompok baru (max existing + 1) pada konteks aktif."""
    query = session.query(Kelompok)
    if prodi_id:
        query = query.filter(Kelompok.prodi_id == prodi_id)
    if kategori_pa_id:
        query = query.filter(Kelompok.KPA_id == kategori_pa_id)
    if angkatan_id:
        query = query.filter(Kelompok.TM_id == angkatan_id)

    max_nomor = 0
    for row in query.with_entities(Kelompok.nomor_kelompok).all():
        raw = row[0] if row else None
        if raw is None:
            continue
        text = str(raw).strip()
        match = re.search(r"\d+", text)
        if not match:
            continue
        value = int(match.group(0))
        if value > max_nomor:
            max_nomor = value

    return max_nomor + 1


def _get_previous_group_member_size(session, prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> int:
    """Ambil ukuran anggota dari kelompok bernomor terbesar pada konteks aktif."""
    query = session.query(Kelompok)
    if prodi_id:
        query = query.filter(Kelompok.prodi_id == prodi_id)
    if kategori_pa_id:
        query = query.filter(Kelompok.KPA_id == kategori_pa_id)
    if angkatan_id:
        query = query.filter(Kelompok.TM_id == angkatan_id)

    latest_kelompok = None
    latest_nomor = -1
    for k in query.all():
        text = str(k.nomor_kelompok or "").strip()
        match = re.search(r"\d+", text)
        if not match:
            continue
        nomor = int(match.group(0))
        if nomor > latest_nomor:
            latest_nomor = nomor
            latest_kelompok = k

    if not latest_kelompok:
        return 0

    count = session.query(KelompokMahasiswa).filter(KelompokMahasiswa.kelompok_id == latest_kelompok.id).count()
    return int(count or 0)


def _build_target_group_sizes(total_members: int, instruction: Dict) -> Dict:
    """Hitung ukuran tiap kelompok berdasarkan total mahasiswa dan instruksi."""
    group_count = instruction.get("group_count")
    group_size = instruction.get("group_size")
    min_size = instruction.get("min_size")
    max_size = instruction.get("max_size")

    if total_members <= 0:
        return {"status": "error", "message": "Tidak ada mahasiswa untuk dibagi."}

    if group_count and group_count <= 0:
        return {"status": "error", "message": "Jumlah kelompok harus lebih dari 0."}
    if group_size and group_size <= 0:
        return {"status": "error", "message": "Ukuran kelompok harus lebih dari 0."}

    no_size_instruction = group_count is None and group_size is None and min_size is None and max_size is None

    if group_count is None:
        if max_size:
            group_count = max(1, math.ceil(total_members / max_size))
        elif group_size:
            group_count = max(1, math.ceil(total_members / group_size))
        elif no_size_instruction:
            # Default behavior: target 5-6 orang per kelompok bila user tidak menyebut ukuran.
            group_count = max(1, round(total_members / 5.5))
        else:
            group_count = max(1, math.ceil(total_members / 4))

    if max_size and total_members > group_count * max_size:
        if instruction.get("group_count") is not None:
            return {
                "status": "error",
                "message": f"Instruksi tidak valid: {total_members} mahasiswa tidak muat dalam {group_count} kelompok dengan maksimal {max_size} orang per kelompok.",
            }
        group_count = math.ceil(total_members / max_size)

    if min_size and total_members < group_count * min_size:
        if instruction.get("group_count") is not None:
            return {
                "status": "error",
                "message": f"Instruksi tidak valid: {total_members} mahasiswa tidak cukup untuk minimal {min_size} orang di {group_count} kelompok.",
            }
        group_count = max(1, total_members // min_size)

    base = total_members // group_count
    remainder = total_members % group_count
    sizes = [base + (1 if i < remainder else 0) for i in range(group_count)]

    if min_size and any(size < min_size for size in sizes):
        return {
            "status": "error",
            "message": "Instruksi minimal anggota per kelompok tidak bisa dipenuhi dengan jumlah mahasiswa saat ini.",
        }

    if max_size and any(size > max_size for size in sizes):
        return {
            "status": "error",
            "message": "Instruksi maksimal anggota per kelompok tidak bisa dipenuhi dengan jumlah mahasiswa saat ini.",
        }

    return {
        "status": "success",
        "group_count": group_count,
        "sizes": sizes,
    }


def create_group(
    prompt: str,
    prodi_id: int = None,
    kategori_pa_id: int = None,
    angkatan_id: int = None,
    exclude_existing: bool = True,
) -> Dict:
    """
    Bentuk kelompok dari data mahasiswa berdasarkan instruksi bahasa natural.

    Args:
        prompt: kalimat instruksi user, contoh "buat 5 kelompok, maksimal 4 orang"
        prodi_id: filter prodi dari context dosen
        angkatan_id: filter angkatan dari context dosen
        exclude_existing: abaikan mahasiswa yang sudah masuk kelompok
    """
    session = SessionLocal()
    try:
        instruction = _extract_grouping_instruction(prompt)
        start_group_number = _get_context_group_start_number(session, prodi_id, kategori_pa_id, angkatan_id)

        query = session.query(Mahasiswa)
        if prodi_id:
            query = query.filter(Mahasiswa.prodi_id == prodi_id)
        if angkatan_id:
            tahun_masuk = session.query(TahunMasuk).filter(TahunMasuk.id == angkatan_id).first()
            if tahun_masuk:
                query = query.filter(Mahasiswa.angkatan == tahun_masuk.Tahun_Masuk)

        mahasiswas = query.all()
        if not mahasiswas:
            return {
                "status": "empty",
                "message": "Tidak ada mahasiswa yang sesuai konteks untuk dibagi kelompok.",
            }

        occupied_user_ids = set()
        if exclude_existing:
            occupied = session.query(KelompokMahasiswa.user_id).all()
            occupied_user_ids = {row[0] for row in occupied if row and row[0] is not None}

        candidates = [m for m in mahasiswas if m.user_id not in occupied_user_ids]
        if not candidates:
            return {
                "status": "empty",
                "message": "Semua mahasiswa pada konteks ini sudah memiliki kelompok.",
            }

        if instruction.get("randomize"):
            random.shuffle(candidates)
        else:
            candidates = sorted(candidates, key=lambda m: (m.nim or "", m.nama or ""))

        # Jika user meminta ukuran mengikuti kelompok sebelumnya, gunakan ukuran kelompok terakhir.
        if instruction.get("use_previous_group_size") and not instruction.get("group_size"):
            previous_size = _get_previous_group_member_size(session, prodi_id, kategori_pa_id, angkatan_id)
            if previous_size > 0:
                instruction["group_size"] = previous_size

        size_result = _build_target_group_sizes(len(candidates), instruction)
        if size_result.get("status") != "success":
            return size_result

        sizes = size_result["sizes"]
        groups: List[Dict] = []
        index = 0
        for group_idx, size in enumerate(sizes, start=start_group_number):
            members = candidates[index:index + size]
            index += size

            groups.append(
                {
                    "group_number": group_idx,
                    "member_count": len(members),
                    "members": [
                        {
                            "user_id": member.user_id,
                            "nim": member.nim,
                            "nama": member.nama,
                            "angkatan": member.angkatan,
                            "prodi_id": member.prodi_id,
                        }
                        for member in members
                    ],
                }
            )

        return {
            "status": "success",
            "summary": {
                "total_candidates": len(candidates),
                "total_groups": len(groups),
                "group_sizes": sizes,
                "excluded_existing_members": len(mahasiswas) - len(candidates),
                "start_group_number": start_group_number,
            },
            "instruction": instruction,
            "groups": groups,
        }
    except Exception as e:
        return {"status": "error", "message": f"Error saat membuat kelompok: {str(e)}"}
    finally:
        session.close()