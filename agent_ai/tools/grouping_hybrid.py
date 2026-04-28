"""
Tools untuk membentuk kelompok dengan constraint (NIM spesifik) + otomatis berdasarkan nilai.

Contoh penggunaan:
  "11419013, 11419027, 11419030 satu kelompok, sisanya berdasarkan nilai"
  "Revi, Mei harus satu kelompok, yang lain by grades"
  "kelompok A: john, santo, ivanos; sisanya by grades"
"""

import math
import statistics
import re
from typing import Dict, List, Set, Tuple
from core.database import SessionLocal
from models.mahasiswa import Mahasiswa
from models.kelompokMahasiswa import KelompokMahasiswa
from models.tahun_masuk import TahunMasuk
from tools.grouping_by_grades import calculate_student_average_grades, balance_group_by_grades


def _extract_nim_constraints_from_prompt(prompt: str) -> List[List[str]]:
    """
    Extract list of NIMs/names that should be grouped together.
    
    Patterns:
      - "11419013, 11419027, 11419030 satu kelompok"
      - "11419013, 11419027, 11419030 harus satu kelompok"
      - "john, santo, ivanos satu kelompok" 
      - "dimana revi, malino, mei harus satu kelompok"
      - "kelompok A: 11419013, 11419027; kelompok B: 11419030, 11419032"
    
    Returns: List[List[str]] - each inner list is a group of NIMs/names
    """
    groups = []
    prompt_lower = prompt.lower()
    
    # Pattern 1: "dimana|dengan X, Y, Z harus satu kelompok|satu kelompok"
    # Support both comma-separated and "dan" (and) separated names
    pattern1 = r"(?:dimana|dengan|mana)\s+([^.!?]+?)(?:harus\s+satu|satu\s+kelompok|satu\s+grup)"
    for match in re.finditer(pattern1, prompt_lower):
        # Extract names from the matched group - support both "," and "dan" as separators
        names_str = match.group(1).strip()
        # Split by both comma and "dan" (and)
        members = re.split(r'\s*(?:,\s*|\s+dan\s+)\s*', names_str)
        members = [m.strip() for m in members if m and len(m.strip()) > 0]
        if members and len(members) >= 1:  # At least 1 person
            groups.append(members)
    
    # Pattern 2: "X, Y, Z satu kelompok" (without dimana/dengan prefix)
    # Only match if it's clearly a list of names (contains comma)
    pattern2 = r"(\w+(?:\s+\w+)*)\s*,\s*(\w+(?:\s+\w+)*(?:,\s*\w+(?:\s+\w+)*)+)\s*(?:harus\s+satu|satu\s+kelompok|satu\s+grup|harus\s+satu\s+kelompok)"
    for match in re.finditer(pattern2, prompt_lower):
        # Full match includes first name, so reconstruct the list
        full_str = match.group(0)
        # Find the constraint keyword and extract names before it
        for keyword in ["harus satu kelompok", "satu kelompok", "harus satu", "satu grup"]:
            if keyword in full_str:
                names_part = full_str[:full_str.find(keyword)].strip()
                members = [m.strip() for m in names_part.split(",")]
                members = [m for m in members if m and len(m) > 0]
                if len(members) >= 2:  # At least 2 people for a constraint
                    groups.append(members)
                break
    
    # Pattern 3: "kelompok [A-Z]: X, Y, Z"
    pattern3 = r"kelompok\s+[A-Z]:\s*([^;]+)(?:;|$)"
    for match in re.finditer(pattern3, prompt_lower):
        members = [m.strip() for m in match.group(1).split(",")]
        members = [m for m in members if m and len(m) > 0]
        if members:
            groups.append(members)
    
    return groups


def _extract_members_per_group(prompt: str) -> int:
    """
    Extract members per group from prompt.
    
    Patterns:
      - "kelompok 6 orang perkelompok"
      - "6 orang perkelompok"
      - "kelompok dengan 6 orang"
      - "X orang per kelompok"
    
    Returns: int (members per group) or 0 if not found
    """
    prompt_lower = prompt.lower()
    
    # Pattern 1: "kelompok X orang perkelompok"
    pattern1 = r"kelompok\s+(\d+)\s+orang\s+perkelompok"
    match = re.search(pattern1, prompt_lower)
    if match:
        return int(match.group(1))
    
    # Pattern 2: "X orang perkelompok"
    pattern2 = r"(\d+)\s+orang\s+perkelompok"
    match = re.search(pattern2, prompt_lower)
    if match:
        return int(match.group(1))
    
    # Pattern 3: "kelompok dengan X orang"
    pattern3 = r"kelompok\s+dengan\s+(\d+)\s+orang"
    match = re.search(pattern3, prompt_lower)
    if match:
        return int(match.group(1))
    
    # Pattern 4: "X orang per kelompok"
    pattern4 = r"(\d+)\s+orang\s+per\s+kelompok"
    match = re.search(pattern4, prompt_lower)
    if match:
        return int(match.group(1))
    
    return 0


def _find_mahasiswa_by_nim_or_name(
    session,
    nim_or_name: str,
    prodi_id: int = None,
    angkatan_id: int = None,
) -> Mahasiswa:
    """
    Find mahasiswa by NIM or name (partial match).
    """
    query = session.query(Mahasiswa)
    if prodi_id:
        query = query.filter(Mahasiswa.prodi_id == prodi_id)
    if angkatan_id:
        tahun_masuk = session.query(TahunMasuk).filter(TahunMasuk.id == angkatan_id).first()
        if tahun_masuk:
            query = query.filter(Mahasiswa.angkatan == tahun_masuk.Tahun_Masuk)
    
    # Try NIM first (exact match)
    result = query.filter(Mahasiswa.nim == nim_or_name).first()
    if result:
        return result
    
    # Try name (case-insensitive partial match)
    nim_or_name_lower = nim_or_name.lower()
    all_mhs = query.all()
    for mhs in all_mhs:
        if mhs.nama and nim_or_name_lower in mhs.nama.lower():
            return mhs
    
    return None


def create_group_hybrid(
    prompt: str,
    prodi_id: int = None,
    kategori_pa_id: int = None,
    angkatan_id: int = None,
    exclude_existing: bool = True,
) -> Dict:
    """
    Create groups with constraint (user-specified members) + auto-grouping by grades.
    
    Args:
        prompt: e.g., "john, santo, ivanos satu kelompok, sisanya berdasarkan nilai"
        prodi_id, kategori_pa_id, angkatan_id: context filters
        exclude_existing: skip mahasiswa already in groups
    
    Returns:
        dict with status, groups (both constrained + auto), summary
    """
    session = SessionLocal()
    try:
        # Extract constraint groups from prompt
        constraint_groups = _extract_nim_constraints_from_prompt(prompt)
        
        # IMPORTANT: Extract target members per group EARLY
        members_per_group = _extract_members_per_group(prompt)
        
        # Get all candidates for this context
        query = session.query(Mahasiswa)
        if prodi_id:
            query = query.filter(Mahasiswa.prodi_id == prodi_id)
        if angkatan_id:
            tahun_masuk = session.query(TahunMasuk).filter(TahunMasuk.id == angkatan_id).first()
            if tahun_masuk:
                query = query.filter(Mahasiswa.angkatan == tahun_masuk.Tahun_Masuk)
        
        all_mahasiswas = query.all()
        if not all_mahasiswas:
            return {
                "status": "empty",
                "message": "Tidak ada mahasiswa sesuai konteks untuk dibagi kelompok.",
            }
        
        occupied_user_ids = set()
        if exclude_existing:
            occupied = session.query(KelompokMahasiswa.user_id).all()
            occupied_user_ids = {row[0] for row in occupied if row and row[0] is not None}
        
        candidates_all = [m for m in all_mahasiswas if m.user_id not in occupied_user_ids]
        if not candidates_all:
            return {
                "status": "empty",
                "message": "Semua mahasiswa pada konteks ini sudah memiliki kelompok.",
            }
        
        # Get grades data for all candidates (needed to fill constraint groups)
        grades_data = calculate_student_average_grades(
            prodi_id=prodi_id,
            kategori_pa_id=kategori_pa_id,
            angkatan_id=angkatan_id,
            exclude_existing=True,
        )
        
        all_student_grades = grades_data.get("student_grades", [])
        class_stats = grades_data.get("class_statistics", {})
        
        # Create lookup: user_id -> grade info
        grades_by_user = {sg["user_id"]: sg for sg in all_student_grades}
        
        # Process constraint groups and FILL THEM if members_per_group is specified
        result_groups = []
        constrained_user_ids: Set[int] = set()
        constraint_errors = []
        
        for idx, members_list in enumerate(constraint_groups, start=1):
            group_members = []
            group_avg_grade = 0.0
            
            # First: collect explicitly specified members
            for member_identifier in members_list:
                mhs = _find_mahasiswa_by_nim_or_name(
                    session, member_identifier, prodi_id, angkatan_id
                )
                if not mhs:
                    constraint_errors.append(f"Tidak ditemukan: {member_identifier}")
                    continue
                
                if mhs.user_id in occupied_user_ids:
                    constraint_errors.append(f"{member_identifier} sudah ada di kelompok lain")
                    continue
                
                group_members.append(mhs)
                constrained_user_ids.add(mhs.user_id)
            
            if not group_members:
                continue
            
            # Second: if members_per_group specified, FILL constraint group to that size
            if members_per_group > 0 and len(group_members) < members_per_group:
                # Calculate average grade of constraint group
                constraint_grades = [
                    grades_by_user.get(m.user_id, {}).get("average_grade", 0.0)
                    for m in group_members
                ]
                group_avg_grade = sum(constraint_grades) / len(constraint_grades) if constraint_grades else 0.0
                
                # Find candidates to fill the group (by closest grade)
                available_for_fill = [
                    m for m in candidates_all
                    if m.user_id not in constrained_user_ids and m.user_id not in occupied_user_ids
                ]
                
                # Sort by grade proximity to group average
                available_for_fill.sort(
                    key=lambda m: abs(grades_by_user.get(m.user_id, {}).get("average_grade", 0.0) - group_avg_grade)
                )
                
                # Add students until we reach members_per_group
                num_to_add = members_per_group - len(group_members)
                for mhs in available_for_fill[:num_to_add]:
                    group_members.append(mhs)
                    constrained_user_ids.add(mhs.user_id)
            
            # Add finalized constraint group to results
            if group_members:
                result_groups.append({
                    "type": "constraint",
                    "members": group_members,
                    "member_count": len(group_members),
                })
        
        # Remaining candidates for auto-grouping by grades
        remaining_candidates = [
            m for m in candidates_all if m.user_id not in constrained_user_ids
        ]
        
        # Apply grades-based grouping to remaining
        if remaining_candidates:
            # Filter student_grades to only include remaining candidates
            remaining_user_ids = {m.user_id for m in remaining_candidates}
            filtered_grades = [
                sg for sg in all_student_grades
                if sg["user_id"] in remaining_user_ids
            ]
            
            # Auto-calculate group count for remaining
            if members_per_group > 0:
                # User explicitly specified group size (e.g., "6 orang perkelompok")
                auto_group_count = max(1, math.ceil(len(remaining_candidates) / members_per_group))
            else:
                # Default: ~6 orang per kelompok
                auto_group_count = max(1, (len(remaining_candidates) + 5) // 6)
            
            # Balance groups by grades - pass mean and std_dev separately
            balanced = balance_group_by_grades(
                filtered_grades,
                auto_group_count,
                class_stats.get("mean", 0.0),
                class_stats.get("std_dev", 0.0)
            )
            
            # Process balanced result
            if balanced.get("status") == "success":
                for auto_group in balanced.get("groups", []):
                    group_members = []
                    # auto_group["members"] contains student dicts from balance_group_by_grades
                    for member_dict in auto_group.get("members", []):
                        user_id = member_dict.get("user_id")
                        mhs = next((m for m in remaining_candidates if m.user_id == user_id), None)
                        if mhs:
                            group_members.append(mhs)
                    
                    if group_members:
                        result_groups.append({
                            "type": "auto",
                            "members": group_members,
                            "member_count": len(group_members),
                        })
        
        # Prepare output with grades information
        output_groups = []
        for idx, group in enumerate(result_groups, start=1):
            # Collect member data with grades
            members_data = []
            group_grades = []
            
            for m in group["members"]:
                member_grade = grades_by_user.get(m.user_id, {}).get("average_grade", 0.0)
                group_grades.append(member_grade)
                members_data.append({
                    "user_id": m.user_id,
                    "nim": m.nim,
                    "nama": m.nama,
                    "nilai": round(member_grade, 2),
                })
            
            # Calculate group average and std dev
            group_avg = sum(group_grades) / len(group_grades) if group_grades else 0.0
            group_std = statistics.stdev(group_grades) if len(group_grades) > 1 else 0.0
            
            output_groups.append({
                "group_number": idx,
                "members": members_data,
                "rata_rata_kelompok": round(group_avg, 2),
                "std_dev_kelompok": round(group_std, 2),
            })
        
        return {
            "status": "success",
            "summary": {
                "total_candidates": len(candidates_all),
                "constrained_members": len(constrained_user_ids),
                "auto_grouped_members": len(candidates_all) - len(constrained_user_ids),
                "total_groups": len(output_groups),
                "rata_kelas": round(class_stats.get("mean", 0.0), 2),
                "jarak_deviasi": round(class_stats.get("std_dev", 0.0), 2),
                "constraint_errors": constraint_errors,
            },
            "groups": output_groups,
        }
    
    except Exception as e:
        return {
            "status": "error",
            "message": f"Error saat membuat kelompok hybrid: {str(e)}",
        }
    finally:
        session.close()
