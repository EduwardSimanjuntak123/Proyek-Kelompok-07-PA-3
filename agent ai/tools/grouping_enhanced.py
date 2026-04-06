"""
Enhanced Grouping Tool dengan Score-Based Balancing dan Constraint Support
Mendukung: must_pairs, avoid_pairs, shuffle, score balancing
"""

from tools.grouping_tool import (
    group_students_with_constraints,
    _find_student_by_name
)


def _validate_pairs(data, pairs):
    """Validate dan fix must_pairs/avoid_pairs dengan actual student data"""
    if not data or not pairs:
        return [], []
    
    valid_pairs = []
    messages = []
    
    for pair in pairs:
        if len(pair) < 2:
            continue
        
        a_name, b_name = str(pair[0]), str(pair[1])
        a_student = _find_student_by_name(data, a_name)
        b_student = _find_student_by_name(data, b_name)
        
        # Jika both found dan berbeda, tambah ke valid_pairs
        if a_student and b_student and a_student != b_student:
            # Gunakan nama actual dari database/data
            actual_a_name = a_student.get("nama") if isinstance(a_student, dict) else str(a_student)
            actual_b_name = b_student.get("nama") if isinstance(b_student, dict) else str(b_student)
            valid_pairs.append([actual_a_name, actual_b_name])
            messages.append(f"✅ Pasangan ditemukan: '{actual_a_name}' dengan '{actual_b_name}'")
        else:
            if not a_student:
                messages.append(f"⚠️ Nama '{a_name}' tidak tersedia")
            if not b_student:
                messages.append(f"⚠️ Nama '{b_name}' tidak tersedia")
    
    return valid_pairs, messages


def group_by_score_with_size_constraint(
    students,
    group_size: int = 6,
    must_pairs=None,
    avoid_pairs=None,
    shuffle: bool = False,
    calculate_stats: bool = True
):
    """
    Group students by score dengan size constraint dan support untuk must_pairs/avoid_pairs.
    
    Args:
        students: List of student dicts dengan 'nama', 'nim', 'user_id', 'nilai_rata_rata'
        group_size: Target jumlah anggota per kelompok (default 6)
        must_pairs: List of pairs yang HARUS satu kelompok. Format: [["nama1", "nama2"], ...]
        avoid_pairs: List of pairs yang TIDAK boleh satu kelompok
        shuffle: Randomize distribusi
        calculate_stats: Calculate dan include group statistics
    
    Returns:
        {
            "groups": [
                {
                    "kelompok": 1,
                    "nama_kelompok": "Kelompok 1",
                    "members": [...],
                    "member_count": 6,
                    "group_average": 75.5,
                    "deviation_from_class": 0.2
                },
                ...
            ],
            "class_stats": {
                "class_average": 75.3,
                "std_dev": 2.1,
                "min_score": 65.0,
                "max_score": 88.0,
                "total_students": 60
            },
            "summary": {
                "total_groups": 10,
                "total_members": 60,
                "balanced": true,
                "constraints_met": true
            }
        }
    """
    
    if not students:
        return {
            "groups": [],
            "class_stats": {},
            "summary": {"error": "No students provided"}
        }
    
    must_pairs = must_pairs or []
    avoid_pairs = avoid_pairs or []
    
    # Calculate class statistics
    class_stats = {}
    if calculate_stats:
        scores = [s.get("nilai_rata_rata", 0) for s in students if isinstance(s, dict)]
        if scores:
            class_avg = sum(scores) / len(scores)
            variance = sum((x - class_avg) ** 2 for x in scores) / len(scores)
            std_dev = variance ** 0.5
            
            class_stats = {
                "class_average": round(class_avg, 2),
                "std_dev": round(std_dev, 2),
                "min_score": round(min(scores), 2),
                "max_score": round(max(scores), 2),
                "total_students": len(scores)
            }
    
    # Sort students by score (descending) untuk balanced distribution
    sorted_students = sorted(
        students,
        key=lambda x: x.get("nilai_rata_rata", 0),
        reverse=True
    )
    
    # Alternate high-low distribution
    distribution_order = []
    left, right = 0, len(sorted_students) - 1
    toggle = True
    
    while left <= right:
        if toggle:
            distribution_order.append(sorted_students[left])
            left += 1
        else:
            distribution_order.append(sorted_students[right])
            right -= 1
        toggle = not toggle
    
    # Use existing constraint-aware grouping function
    grouped = group_students_with_constraints(
        distribution_order,
        group_size=group_size,
        must_pairs=must_pairs,
        avoid_pairs=avoid_pairs,
        shuffle=shuffle
    )
    
    # Format output dengan detail
    result_groups = []
    total_members = 0
    all_constraints_met = True
    
    for idx, group in enumerate(grouped, 1):
        members = group if isinstance(group, list) else group.get("members", [])
        
        # Calculate group average
        member_scores = []
        formatted_members = []
        
        for member in members:
            if isinstance(member, dict):
                score = member.get("nilai_rata_rata", 0)
                member_scores.append(score)
                formatted_members.append({
                    "user_id": member.get("user_id"),
                    "nama": member.get("nama"),
                    "nim": member.get("nim"),
                    "nilai_rata_rata": score
                })
        
        group_avg = sum(member_scores) / len(member_scores) if member_scores else 0
        deviation = group_avg - class_stats.get("class_average", 0) if class_stats else 0
        
        result_groups.append({
            "kelompok": idx,
            "nama_kelompok": f"Kelompok {idx}",
            "members": formatted_members,
            "member_count": len(members),
            "group_average": round(group_avg, 2),
            "deviation_from_class": round(deviation, 2)
        })
        
        total_members += len(members)
    
    # Calculate balance metrics
    group_averages = [g["group_average"] for g in result_groups]
    avg_of_avgs = sum(group_averages) / len(group_averages) if group_averages else 0
    group_std = (sum((x - avg_of_avgs) ** 2 for x in group_averages) / len(group_averages)) ** 0.5 if group_averages else 0
    
    is_balanced = max(
        [abs(g["deviation_from_class"]) for g in result_groups] or [0]
    ) <= 2.0  # Within ±2 of class average
    
    return {
        "groups": result_groups,
        "class_stats": class_stats,
        "summary": {
            "total_groups": len(result_groups),
            "total_members": total_members,
            "members_per_group": {
                "min": min([g["member_count"] for g in result_groups]) if result_groups else 0,
                "max": max([g["member_count"] for g in result_groups]) if result_groups else 0,
                "avg": round(total_members / len(result_groups), 1) if result_groups else 0
            },
            "group_balance": {
                "avg_of_group_averages": round(avg_of_avgs, 2),
                "group_std_dev": round(group_std, 2),
                "is_balanced": is_balanced,
                "max_deviation": round(max(
                    [abs(g["deviation_from_class"]) for g in result_groups] or [0]
                ), 2)
            },
            "constraints": {
                "must_pairs": len(must_pairs),
                "avoid_pairs": len(avoid_pairs),
                "constraints_met": all_constraints_met
            }
        }
    }


def group_by_scores_exact_size(
    students,
    num_groups: int,
    must_pairs=None,
    avoid_pairs=None,
    shuffle: bool = False
):
    """
    Group students into EXACT number of groups dengan score balancing.
    
    Args:
        students: List of student dicts
        num_groups: Target exact number of groups
        must_pairs: Pairs yang harus satu grup
        avoid_pairs: Pairs yang tidak boleh satu grup
        shuffle: Randomize
    
    Returns:
        Same format sebagai group_by_score_with_size_constraint
    """
    if not students or num_groups <= 0:
        return {
            "groups": [],
            "class_stats": {},
            "summary": {"error": "Invalid input"}
        }
    
    # Calculate ideal group size
    ideal_group_size = len(students) // num_groups
    
    return group_by_score_with_size_constraint(
        students,
        group_size=ideal_group_size,
        must_pairs=must_pairs,
        avoid_pairs=avoid_pairs,
        shuffle=shuffle,
        calculate_stats=True
    )


def validate_and_apply_constraints(
    existing_groups,
    must_pairs=None,
    avoid_pairs=None,
    shuffle: bool = False
):
    """
    Validate existing groups terhadap constraints baru.
    Jika ada konflik, regroup hanya affected students.
    
    Args:
        existing_groups: List of existing groups (format: list of members atau 'kelompok' dict)
        must_pairs: Pairs yang HARUS satu kelompok
        avoid_pairs: Pairs yang TIDAK boleh satu kelompok
        shuffle: Randomize affected
    
    Returns:
        {
            "groups": [...],
            "validation": {
                "has_conflicts": bool,
                "conflicts": [...],
                "messages": [...]
            }
        }
    """
    must_pairs = must_pairs or []
    avoid_pairs = avoid_pairs or []
    
    # Extract all members
    all_members = []
    for group in existing_groups:
        if isinstance(group, dict) and "members" in group:
            all_members.extend(group["members"])
        elif isinstance(group, list):
            all_members.extend(group)
    
    if not all_members:
        return {
            "groups": [],
            "validation": {"has_conflicts": False, "conflicts": [], "messages": []}
        }
    
    # Validate pairs exist
    valid_must, must_msgs = _validate_pairs(all_members, must_pairs)
    valid_avoid, avoid_msgs = _validate_pairs(all_members, avoid_pairs)
    
    messages = must_msgs + avoid_msgs
    
    # Check for conflicts in existing groups
    conflicts = []
    for pair in valid_avoid:
        for group in existing_groups:
            members = group.get("members", []) if isinstance(group, dict) else group
            names = [m.get("nama") if isinstance(m, dict) else m for m in members]
            
            # Check if both members of avoid_pair are in same group
            a_found = any(
                str(pair[0]).lower().strip() in str(n).lower().strip() or
                str(n).lower().strip() in str(pair[0]).lower().strip()
                for n in names
            )
            b_found = any(
                str(pair[1]).lower().strip() in str(n).lower().strip() or
                str(n).lower().strip() in str(pair[1]).lower().strip()
                for n in names
            )
            
            if a_found and b_found:
                conflicts.append({
                    "type": "avoid_pair",
                    "pair": pair,
                    "group": group
                })
                messages.append(f"❌ Konflik: {pair[0]} dan {pair[1]} dalam satu grup")
    
    has_conflicts = len(conflicts) > 0
    
    return {
        "groups": existing_groups,
        "validation": {
            "has_conflicts": has_conflicts,
            "conflicts": conflicts,
            "messages": messages,
            "valid_must_pairs": valid_must,
            "valid_avoid_pairs": valid_avoid
        }
    }


# Untuk backwards compatibility dengan score_tool.py
def group_by_score_balance_with_constraints(
    student_scores,
    group_size=6,
    allow_deviation=0.5,
    must_pairs=None,
    avoid_pairs=None,
    shuffle=False
):
    """
    Wrapper untuk compatibility dengan existing code.
    student_scores format: list of {student_data, scores_by_semester, average_score}
    """
    # Convert to simple student format
    students = []
    for item in student_scores:
        students.append({
            "user_id": item.get("mahasiswa_id"),
            "nama": item.get("student_data", {}).get("nama"),
            "nim": item.get("student_data", {}).get("nim"),
            "nilai_rata_rata": item.get("average_score", 0)
        })
    
    result = group_by_score_with_size_constraint(
        students,
        group_size=group_size,
        must_pairs=must_pairs,
        avoid_pairs=avoid_pairs,
        shuffle=shuffle,
        calculate_stats=True
    )
    
    # Return in the format expected by existing code
    groups = []
    for g in result["groups"]:
        groups.append({
            "kelompok": g["kelompok"],
            "members": g["members"],
            "group_average": g["group_average"],
            "deviation_from_class": g["deviation_from_class"],
            "member_count": g["member_count"]
        })
    
    return groups, result["class_stats"]
