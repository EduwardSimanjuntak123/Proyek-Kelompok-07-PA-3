import random


def _normalize_student_key(student):
    if isinstance(student, dict):
        return student.get("nim") or student.get("nama")
    return str(student)


def _find_student_by_name(students, name):
    """
    Cari student dari list dengan fuzzy matching:
    - Exact match (case-insensitive)
    - Partial match (name in s_name atau s_name in name)
    - First/last name match (split by space dan cek)
    - Typo tolerance (similar sounding first names)
    """
    name_lower = str(name).lower().strip()
    name_words = name_lower.split()
    first_word = name_words[0] if name_words else ""
    
    print(f"[FIND] Searching: '{name}' -> words: {name_words}, first: '{first_word}'")
    
    # Pass 1: Exact dan partial match
    for s in students:
        if isinstance(s, dict):
            s_name = str(s.get("nama", "")).lower().strip()
            
            # Exact match
            if name_lower == s_name:
                print(f"[FIND]   -> EXACT match: {s_name}")
                return s
            
            # Partial match (either direction)
            if name_lower in s_name or s_name in name_lower:
                print(f"[FIND]   -> PARTIAL match: {s_name}")
                return s
    
    # Pass 2: First name match (more lenient)
    if first_word:
        for s in students:
            if isinstance(s, dict):
                s_name = str(s.get("nama", "")).lower().strip()
                s_words = s_name.split()
                if s_words:
                    s_first_word = s_words[0]
                    # Check if first names are similar enough
                    # Same first word, or first word is substring of s_first_word
                    if first_word == s_first_word or first_word in s_first_word or s_first_word in first_word:
                        print(f"[FIND]   -> FIRST NAME match: {s_name}")
                        return s
                    
                    # Check last name (second word)
                    if len(name_words) > 1 and len(s_words) > 1:
                        search_last = name_words[1]
                        s_last = s_words[-1]  # Take last word as last name
                        if search_last in s_last or s_last in search_last:
                            print(f"[FIND]   -> LAST NAME match: {s_name}")
                            return s
    
    print(f"[FIND]   -> NO match found")
    return None


def _pair_key(a, b):
    return tuple(sorted([str(a), str(b)]))


def check_conflict_in_group(group, avoid_pairs):
    """
    Check if group contains any avoid_pair.
    
    Handles both name-based and key-based (nim) comparisons.
    """
    if not avoid_pairs:
        return False
    
    # Build multiple representations for group members
    group_keys = {_normalize_student_key(s) for s in group}
    group_names_lower = {str(s.get("nama", "")).lower().strip() for s in group if isinstance(s, dict)}
    
    # Check each avoid pair
    for a, b in avoid_pairs:
        a_key = str(a).strip()
        b_key = str(b).strip()
        a_lower = a_key.lower()
        b_lower = b_key.lower()
        
        # Check key match (nim)
        if a_key in group_keys and b_key in group_keys:
            return True
        
        # Check name match (more lenient - substring match)
        a_found = False
        b_found = False
        
        for name in group_names_lower:
            if a_lower in name or name in a_lower:
                a_found = True
            if b_lower in name or name in b_lower:
                b_found = True
        
        if a_found and b_found:
            return True
    
    return False


def _balance_groups_preserve_must_pairs(groups, group_size, assigned, num_groups=None):
    """
    Balance groups sambil preserve must_pairs groups (size 2).
    Untuk grupos tanpa must_pairs, redistribute agar merata.
    
    Jika num_groups ditentukan, pastikan total kelompok = num_groups.
    """
    if not groups:
        return groups

    # Separate must_pair groups (size 2) dan regular groups
    must_pair_groups = [g for g in groups if len(g) == 2]
    regular_groups = [g for g in groups if len(g) != 2]
    
    print(f"[BALANCE] Must-pair groups: {len(must_pair_groups)}, Regular groups: {len(regular_groups)}")
    
    if not regular_groups:
        # Semua adalah must_pair, tidak perlu balance
        return groups
    
    # Flat regular groups
    flat_regular = [s for g in regular_groups for s in g]
    total_regular = len(flat_regular)
    
    # Hitung jumlah regular groups yang diperlukan
    if num_groups:
        # Jika num_groups specified, hitung berapa regular groups yg diperlukan
        target_regular_groups = max(1, num_groups - len(must_pair_groups))
    else:
        # Keep current number of regular groups
        target_regular_groups = len(regular_groups)
    
    # Hitung distribusi ideal
    base_size = total_regular // target_regular_groups if target_regular_groups > 0 else total_regular
    remainder = total_regular % target_regular_groups if target_regular_groups > 0 else 0
    
    # Redistribute regular groups
    new_regular = []
    idx = 0
    for i in range(target_regular_groups):
        size = base_size + (1 if i < remainder else 0)
        if size > 0:
            new_regular.append(flat_regular[idx : idx + size])
            idx += size
    
    # Combine must_pair + balanced regular
    result_groups = must_pair_groups + new_regular
    
    print(f"[BALANCE] After balance: {len(result_groups)} groups (target: {num_groups})")
    for i, g in enumerate(result_groups):
        print(f"  Group {i+1}: {len(g)} members (must_pair={len(g)==2})")
    
    return result_groups


def group_students_with_constraints(students, group_size=6, num_groups=None, avoid_pairs=None, must_pairs=None, shuffle=False):
    """Group students dengan constraints.
    
    Args:
        students: List of student dicts
        group_size: Target size per group (used if num_groups is None)
        num_groups: Target NUMBER of groups (takes precedence over group_size)
        avoid_pairs: List of pairs that should NOT be in same group
        must_pairs: List of pairs that MUST be in same group
        shuffle: Whether to randomize distribution
    """
    if not students:
        return []

    avoid_pairs = avoid_pairs or []
    must_pairs = must_pairs or []

    # Normalize students to list of dicts
    student_list = list(students)  # Copy list
    
    # Jika num_groups ditentukan, hitung ideal group_size
    if num_groups and num_groups > 0:
        total_students = len(student_list)
        group_size = total_students // num_groups
        if group_size <= 0:
            group_size = 1
        print(f"[GROUPING] Target: {num_groups} kelompok, {total_students} mahasiswa => ~{group_size} per kelompok")
    elif group_size is None or group_size <= 0:
        group_size = 6
    
    # Shuffle jika diminta
    if shuffle:
        random.shuffle(student_list)

    # Build map berdasarkan nim/nama
    key_to_student = {}
    for s in student_list:
        nim = str(s.get("nim", "")).strip()
        nama = str(s.get("nama", "")).lower().strip()
        if nim:
            key_to_student[nim] = s
        if nama:
            key_to_student[nama] = s

    # Track assigned students
    assigned = set()
    
    # Build initial groups from must_pairs
    must_pair_groups = []
    
    for pair in must_pairs:
        a_name = str(pair[0]).lower().strip()
        b_name = str(pair[1]).lower().strip()
        
        # Find students dalam list dengan matching
        a_student = _find_student_by_name(student_list, a_name)
        b_student = _find_student_by_name(student_list, b_name)
        
        if a_student and b_student and a_student != b_student:
            a_key = _normalize_student_key(a_student)
            b_key = _normalize_student_key(b_student)
            
            # Jangan tambah jika sudah assigned
            if a_key not in assigned and b_key not in assigned:
                group = [a_student, b_student]
                must_pair_groups.append(group)
                assigned.add(a_key)
                assigned.add(b_key)

    # Add unassigned students to existing groups atau buat baru
    groups = list(must_pair_groups)  # Copy must_pair groups

    print(f"[GROUPING] Initial must_pair groups: {len(groups)}")
    print(f"[GROUPING] Assigned students so far: {len(assigned)}")
    print(f"[GROUPING] Total students: {len(student_list)}")
    print(f"[GROUPING] Unassigned: {len(student_list) - len(assigned)}")
    
    for student in student_list:
        student_key = _normalize_student_key(student)
        if student_key in assigned:
            continue
        
        # Try to place di existing group yang belum penuh
        placed = False
        for group in groups:
            if len(group) < group_size:
                candidate = group + [student]
                if not check_conflict_in_group(candidate, avoid_pairs):
                    group.append(student)
                    assigned.add(student_key)
                    placed = True
                    break
        
        # Jika tidak bisa, buat group baru
        if not placed:
            groups.append([student])
            assigned.add(student_key)

    print(f"[GROUPING] After assignment: {len(groups)} groups")
    for i, g in enumerate(groups):
        print(f"  Group {i+1}: {len(g)} members")

    # Always balance groups (even with must_pairs, balance works by preserving them)
    print(f"[GROUPING] Balancing groups...")
    groups = _balance_groups_preserve_must_pairs(groups, group_size, assigned, num_groups=num_groups)
    
    # Shuffle distribution jika diminta
    if shuffle:
        print(f"[GROUPING] Shuffling distribution...")
        # Untuk setiap group (except must_pair groups yg size 2), shuffle members
        for group in groups:
            if len(group) > 2:  # Jangan shuffle must_pair groups
                random.shuffle(group)
        # Juga shuffle order of groups
        random.shuffle(groups)

    # Validate avoid pairs tidak ada di satu group
    for group in groups:
        if check_conflict_in_group(group, avoid_pairs):
            raise Exception("Tidak dapat membuat kelompok karena konflik avoid_pairs")

    # Hasil akhir
    result = []
    for idx, members in enumerate(groups, start=1):
        # Ensure members preserve user_id and essential fields
        formatted_members = []
        for member in members:
            if isinstance(member, dict):
                formatted_members.append({
                    "user_id": member.get("user_id"),
                    "nama": member.get("nama"),
                    "nim": member.get("nim"),
                    **{k: v for k, v in member.items() if k not in ["user_id", "nama", "nim"]}
                })
            else:
                formatted_members.append(member)
        
        result.append({"kelompok": idx, "members": formatted_members})

    return result


def grouping(students, group_size=None, num_groups=None, shuffle=False):
    return group_students_with_constraints(students, group_size=group_size, num_groups=num_groups, shuffle=shuffle)