import json
import time
from tools.db_tool import (
    get_mahasiswa_by_context, 
    count_mahasiswa_by_context,
    save_groups_from_result,
    get_dosen_by_prodi_id,
    get_groups_with_pembimbing_by_context,
    get_pembimbing_by_group_id
)
from core.action_dispatcher import get_dispatcher
from tools.grouping_tool import grouping, group_students_with_constraints, _find_student_by_name
from tools.dosen_tool import get_dosen_list_by_prodi, format_dosen_for_response
from tools.pembimbing_assignment_tool import (
    assign_pembimbing_automatically,
    format_groups_with_pembimbing,
    format_assignment_result,
    get_group_with_pembimbing_details
)
from tools.score_tool import (
    get_student_scores_by_category,
    get_class_average,
    group_by_score_balance,
    group_by_score_balance_with_constraints
)
from tools.career_categorization_tool import (
    categorize_subjects,
    score_student_by_expertise,
    create_balanced_groups_by_expertise,
    calculate_group_balance_metrics
)
from memory.memory import get_last_result_by_user, get_latest_grouping_draft


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
            # Gunakan nama actual dari database
            actual_a_name = a_student.get("nama")
            actual_b_name = b_student.get("nama")
            valid_pairs.append([actual_a_name, actual_b_name])
            messages.append(f"✅ Pasangan ditemukan: '{actual_a_name}' dengan '{actual_b_name}'")

        else:
            if not a_student:
                messages.append(f"⚠️ Nama '{a_name}' tidak tersedia di database")
            if not b_student:
                messages.append(f"⚠️ Nama '{b_name}' tidak tersedia di database")
    
    return valid_pairs, messages


def _modify_groups_minimal_change(existing_groups, avoid_pairs, must_pairs, shuffle):
    """
    Modify existing groups dengan constraint baru, HANYA mengubah kelompok yang terkena.
    Kelompok lain tetap tidak berubah.
    
    Contoh: Jika Mei dan Revi tidak boleh satu kelompok, hanya kelompok mereka yang dimodifikasi,
    kelompok lain tetap sama.
    """
    import random
    
    # Flatten all members from existing groups
    all_members = []
    member_to_group_idx = {}  # Map nama student ke index group mereka
    
    for group_idx, group in enumerate(existing_groups):
        for member in group.get("members", []):
            all_members.append(member)
            member_to_group_idx[member.get("nama")] = group_idx
    
    # Validate pairs dengan actual data
    valid_must_pairs, must_pair_messages = _validate_pairs(all_members, must_pairs)
    valid_avoid_pairs, avoid_pair_messages = _validate_pairs(all_members, avoid_pairs)
    
    # Identify affected students (yang terlibat dalam constraint)
    affected_students_set = set()
    
    for pair in valid_avoid_pairs:
        affected_students_set.add(pair[0])
        affected_students_set.add(pair[1])
    
    for pair in valid_must_pairs:
        affected_students_set.add(pair[0])
        affected_students_set.add(pair[1])
    
    if not affected_students_set:
        # Tidak ada constraint, return existing groups as is
        messages = ["ℹ️ Tidak ada constraint yang perlu diubah"]
        return list(existing_groups), messages
    
    # Identify groups yang terkena (yang contain affected students)
    affected_group_indices = set()
    affected_students = []
    unaffected_students = []
    
    for member in all_members:
        member_name = member.get("nama")
        if member_name in affected_students_set:
            affected_students.append(member)
            affected_group_indices.add(member_to_group_idx[member_name])
        else:
            unaffected_students.append(member)
    
    # Build new groups: keep unaffected groups, re-group affected students
    new_groups = []
    
    # First, add unaffected groups as is
    for group_idx, group in enumerate(existing_groups):
        if group_idx not in affected_group_indices:
            # Kelompok ini tidak terkena, copy as is
            new_groups.append({
                "nama_kelompok": group.get("nama_kelompok"),
                "members": list(group.get("members", [])),
                "member_count": group.get("member_count", 0)
            })
    
    # Re-group only affected students with constraints
    group_size = existing_groups[0].get("member_count", 5) if existing_groups else 5
    
    regroup_result = group_students_with_constraints(
        affected_students,
        group_size=group_size,
        avoid_pairs=valid_avoid_pairs,
        must_pairs=valid_must_pairs,
        shuffle=shuffle
    )
    
    # Add regrouped affected students groups
    num_unaffected = len([g for idx, g in enumerate(existing_groups) if idx not in affected_group_indices])
    for idx, group in enumerate(regroup_result):
        new_group_num = num_unaffected + idx + 1
        new_groups.append({
            "nama_kelompok": f"Kelompok {new_group_num}",
            "members": group.get("members", []),
            "member_count": group.get("member_count", 0)
        })
    
    messages = []
    messages.extend(must_pair_messages)
    messages.extend(avoid_pair_messages)
    messages.append(f"✅ Kelompok yang tidak terkena: {len(existing_groups) - len(affected_group_indices)} (tidak berubah)")
    messages.append(f"✅ Kelompok yang dimodifikasi: {len(affected_group_indices)} (re-grouped: {len(regroup_result)} kelompok)")
    messages.append(f"ℹ️ Total student yang diubah: {len(affected_students)}")
    
    return new_groups, messages


def _modify_existing_groups(existing_groups, avoid_pairs, must_pairs, shuffle):
    """Modify existing groups dengan constraint baru"""
    import random
    
# Flatten all members from existing groups
    all_members = []
    for group in existing_groups:
        all_members.extend(group.get("members", []))
    
    # Validate pairs with actual data
    valid_must_pairs, must_pair_messages = _validate_pairs(all_members, must_pairs)
    valid_avoid_pairs, avoid_pair_messages = _validate_pairs(all_members, avoid_pairs)
    
    # Recreate groups with constraints
    group_size = existing_groups[0].get("member_count", 6) if existing_groups else 6
    
    modified_groups = group_students_with_constraints(
        all_members,
        group_size=group_size,
        avoid_pairs=valid_avoid_pairs,
        must_pairs=valid_must_pairs,
        shuffle=shuffle
    )
    
    messages = []
    messages.extend(must_pair_messages)
    messages.extend(avoid_pair_messages)
    messages.append(f"✅ Kelompok dimodifikasi: {len(modified_groups)} kelompok (shuffle: {shuffle})")
    
    return modified_groups, messages


def execute(plan_text, dosen_context, existing_groups=None):
    """
    Execute plan dengan dynamic action dispatching
    """
    
    # Initialize dispatcher untuk step execution
    dispatcher = get_dispatcher()
    execution_log = []  # Track all executions
    execution_metadata = {
        "total_steps": 0,
        "successful_steps": 0,
        "failed_steps": 0,
        "total_retries": 0,
        "execution_times": []
    }
    
    plan = json.loads(plan_text)
    
    # Helper to get value from context (handles both dict and object)
    def _get_context_value(context, key, default=None):
        """Extract value from context - handles both dict and object"""
        if isinstance(context, dict):
            return context.get(key, default)
        else:
            return getattr(context, key, default)

    # Handle natural response (unrecognized query dengan LLM response)
    if plan.get("type") == "natural_response":
        return plan, []  # Return empty messages to avoid duplication
    
    # Handle parsing failed response directly
    if plan.get("type") == "parsing_failed":
        return plan, []  # Return empty messages to avoid duplication
    
    # Handle greeting response directly
    if plan.get("type") == "greeting":
        return plan, []  # Return empty messages to avoid duplication
    
    # Handle capability query - agent capabilities
    if plan.get("type") == "capability_query":
        print(f"[EXECUTOR] Handling capability query")
        return plan, []  # Return empty messages to avoid duplication
    
    # Handle dosen query
    if plan.get("type") == "dosen_query":
        print(f"[EXECUTOR] Handling dosen query")
        
        messages = []
        prodi_id = plan.get("prodi_id")
        action = plan.get("action", "list_dosen_current")
        
        # Get context jika tidak ada prodi_id
        if not prodi_id and dosen_context:
            context = dosen_context[0] if isinstance(dosen_context, (list, tuple)) else dosen_context
            prodi_id = _get_context_value(context, 'prodi_id')
        
        if not prodi_id:
            plan["type"] = "dosen_query_no_data"
            messages.append("Program studi tidak ditemukan. Silakan tentukan program studi terlebih dahulu.")
            return plan, messages
        
        # Get dosen list
        print(f"[EXECUTOR] Getting dosen for prodi_id={prodi_id}")
        dosen_data = get_dosen_list_by_prodi(prodi_id)
        
        if not dosen_data.get("success"):
            plan["type"] = "dosen_query_no_data"
            messages.append(f"{dosen_data.get('message', 'Tidak ada dosen ditemukan')}")
            return plan, messages
        
        # Format response
        formatted_response = format_dosen_for_response(dosen_data)
        
        plan["type"] = "dosen_query_result"
        plan["dosen_data"] = dosen_data
        plan["formatted_response"] = formatted_response
        messages.append("Data dosen berhasil diambil")
        
        return plan, messages
    
    # Handle pembimbing command (lecturer assignment)
    if plan.get("type") == "pembimbing_command":
        print(f"[EXECUTOR] Handling pembimbing command")
        
        messages = []
        action = plan.get("action", "view_all_pembimbing")
        prodi_id = plan.get("context", {}).get("prodi_id")
        kpa_id = plan.get("context", {}).get("kategori_pa")
        tm_id = plan.get("context", {}).get("tahun_masuk")
        
        # Get context if needed
        if not prodi_id or not kpa_id or not tm_id:
            context = dosen_context[0] if isinstance(dosen_context, (list, tuple)) and len(dosen_context) > 0 else None
            if context:
                prodi_id = prodi_id or _get_context_value(context, 'prodi_id')
                kpa_id = kpa_id or _get_context_value(context, 'kategori_pa') or _get_context_value(context, 'kpa_id')
                tm_id = tm_id or _get_context_value(context, 'angkatan') or _get_context_value(context, 'tahun_masuk') or _get_context_value(context, 'tm_id')
        
        if not (prodi_id and kpa_id and tm_id):
            plan["type"] = "pembimbing_error"
            messages.append("Context tidak lengkap. Program studi, kategori PA, atau tahun masuk tidak ditemukan.")
            return plan, messages
        
        # Handle different pembimbing actions
        if action == "auto_assign":
            print(f"[EXECUTOR] Auto-assigning pembimbing")
            # Extract jabatan filter from plan if provided
            jabatan_filter = plan.get("parsed", {}).get("jabatan_filter") if isinstance(plan.get("parsed"), dict) else None
            
            # Automatically assign lecturers to groups
            result = assign_pembimbing_automatically(prodi_id, kpa_id, tm_id, jabatan_filter=jabatan_filter)
            
            plan["type"] = "pembimbing_assignment_result"
            plan["assignment_result"] = result
            plan["formatted_response"] = format_assignment_result(result)
            
            if jabatan_filter:
                messages.append(f"Penugasan pembimbing dengan jabatan {jabatan_filter} selesai")
            else:
                messages.append("Penugasan pembimbing selesai")
            
        elif action == "view_all_pembimbing":
            print(f"[EXECUTOR] Fetching all groups with pembimbing")
            # Get all groups with their pembimbing
            groups_data = get_groups_with_pembimbing_by_context(prodi_id, kpa_id, tm_id)
            
            if not groups_data:
                plan["type"] = "pembimbing_no_data"
                messages.append("Tidak ada kelompok ditemukan")
                return plan, messages
            
            plan["type"] = "pembimbing_view_result"
            plan["groups_data"] = groups_data
            plan["formatted_response"] = format_groups_with_pembimbing(groups_data)
            messages.append(f"Ditemukan {len(groups_data)} kelompok")
            
        elif action == "view_group_pembimbing":
            print(f"[EXECUTOR] Fetching specific group with pembimbing")
            kelompok_number = plan.get("kelompok_number")
            
            if not kelompok_number:
                plan["type"] = "pembimbing_error"
                messages.append("Nomor kelompok tidak ditemukan")
                return plan, messages
            
            # Query database to find group by nomor_kelompok
            from tools.db_tool import engine
            from sqlalchemy import text
            
            try:
                query = """
                    SELECT id FROM kelompok 
                    WHERE nomor_kelompok LIKE :kelompok_number
                    AND prodi_id = :prodi_id
                    AND KPA_id = :kpa_id
                    AND TM_id = :tm_id
                    LIMIT 1
                """
                
                with engine.connect() as conn:
                    result = conn.execute(text(query), {
                        "kelompok_number": f"%{kelompok_number}%",
                        "prodi_id": prodi_id,
                        "kpa_id": kpa_id,
                        "tm_id": tm_id
                    }).fetchone()
                
                if not result:
                    plan["type"] = "pembimbing_no_data"
                    messages.append(f"Kelompok {kelompok_number} tidak ditemukan")
                    return plan, messages
                
                kelompok_id = result[0]
                pembimbing_data = get_group_with_pembimbing_details(kelompok_id)
                
                # Cache group context untuk follow-up questions
                try:
                    from memory.advanced_operations import cache_set
                    from tools.db_tool import get_group_by_nomor_kelompok
                    
                    # Get full group data dengan members
                    full_group_data = get_group_by_nomor_kelompok(str(kelompok_number), {
                        'kategori_pa': kpa_id,
                        'prodi_id': prodi_id,
                        'angkatan': tm_id
                    })
                    
                    # Create group context untuk follow-up questions
                    if full_group_data:
                        group_info = {
                            "kelompok": kelompok_number,
                            "kelompok_id": kelompok_id,
                            "members": full_group_data.get("members", [])
                        }
                        
                        # Extract user_id dari dosen_context jika ada
                        user_id = None
                        if isinstance(dosen_context, (list, tuple)) and len(dosen_context) > 0:
                            ctx = dosen_context[0]
                            user_id = _get_context_value(ctx, 'user_id')
                        
                        if user_id:
                            cache_set(
                                user_id=user_id,
                                cache_key=f"last_group_context:{user_id}",
                                cache_value=group_info,
                                data_type="group_context",
                                ttl_minutes=30
                            )
                            print(f"[CACHE] Saved group context for user {user_id}: group {kelompok_number}")
                except Exception as e:
                    print(f"[WARNING] Failed to cache group context: {e}")
                
                plan["type"] = "pembimbing_view_result"
                plan["group_pembimbing"] = pembimbing_data
                
                # Format response
                if pembimbing_data['pembimbing_count'] == 0:
                    html = f"<p>Kelompok {kelompok_number} belum memiliki pembimbing.</p>"
                else:
                    html = f"<p><strong>Kelompok {kelompok_number}</strong> memiliki {pembimbing_data['pembimbing_count']} pembimbing:</p>"
                    html += "<ul>"
                    for pembimbing in pembimbing_data['pembimbing']:
                        html += f"<li>{pembimbing['nama']} ({pembimbing['jabatan_akademik_desc']}) - {pembimbing['email']}</li>"
                    html += "</ul>"
                
                plan["formatted_response"] = html
                messages.append(f"Data pembimbing untuk kelompok {kelompok_number} ditemukan")
                
            except Exception as e:
                plan["type"] = "pembimbing_error"
                messages.append(f"Error: {str(e)}")
                return plan, messages
        
        else:
            plan["type"] = "pembimbing_error"
            messages.append(f"Action '{action}' tidak dikenali")
            return plan, messages
        
        return plan, messages
    
    # Handle combined requests (multi-intent: grouping + dosen + count)
    if plan.get("type") == "combined":
        messages = []
        steps = plan.get("steps", [])
        intents = plan.get("intents", [])
        
        messages.append(f"📋 Combined Request - {len(intents)} intents")
        for intent in intents:
            messages.append(f"   • {intent.get('action', 'unknown')}")
        
        plan["steps"] = steps
        plan["combined_request"] = True
        # Continue to main step loop below
    
    # Handle dynamic grouping (LLM-based parsed requests)
    if plan.get("type") == "dynamic_grouping":
        messages = []
        steps = plan.get("steps", [])
        metadata = plan.get("metadata", {})
        parsed = plan.get("parsed", {})
        
        messages.append(f"📋 Dynamic Grouping Request")
        messages.append(f"   Strategy: {parsed.get('grouping_strategy', 'unknown')}")
        
        if parsed.get("must_pairs"):
            messages.append(f"   Must pairs: {len(parsed['must_pairs'])} pairs")
        
        if parsed.get("avoid_pairs"):
            messages.append(f"   Avoid pairs: {len(parsed['avoid_pairs'])} pairs")
        
        if parsed.get("shuffle"):
            messages.append(f"   🔀 Acak: Ya")
        
        if parsed.get("show_scores"):
            messages.append(f"   📊 Tampilkan nilai: Ya")
        
        # Execute steps using existing step loop
        # Just update plan.get("steps") to return dynamic steps
        plan["steps"] = steps
        # Continue to main step loop below
    
    # Handle view existing groups request
    if plan.get("type") == "view_existing_groups":
        from tools.db_tool import get_existing_groups_by_user
        
        user_id = plan.get("user_id")
        if not user_id:
            # Try to extract from dosen_context
            if isinstance(dosen_context, (list, tuple)) and len(dosen_context) > 0:
                ctx = dosen_context[0]
                user_id = _get_context_value(ctx, 'user_id')
        
        group_data = None
        messages = []
        context = dosen_context[0] if dosen_context else None
        
        if user_id:
            group_data = get_existing_groups_by_user(user_id, context)
        
        if not group_data:
            messages.append("❌ Belum ada kelompok untuk Anda. Apakah Anda ingin membuat kelompok sekarang?")
            plan["type"] = "empty_group"
        else:
            plan["type"] = "view_existing_groups"
            plan["group_data"] = group_data
            
            # Handle both single group (dict) and multiple groups (list)
            if isinstance(group_data, list):
                messages.append(f"📋 Menampilkan {len(group_data)} kelompok PA Anda")
            else:
                messages.append(f"📋 Kelompok Anda: {group_data.get('nomor_kelompok')}")
        
        return plan, messages
    
    # Handle view specific group by number request (e.g., "tampilkan anggota kelompok 1")
    if plan.get("type") == "view_group_by_number":
        from tools.db_tool import get_group_by_nomor_kelompok
        
        group_number = plan.get("group_number")
        context = dosen_context[0] if dosen_context else None
        
        messages = []
        group_data = None
        
        if group_number is not None:
            group_data = get_group_by_nomor_kelompok(group_number, context)
        
        if not group_data:
            messages.append(f"❌ Kelompok nomor {group_number} tidak ditemukan.")
            plan["type"] = "empty_group"
        else:
            plan["type"] = "view_group_by_number"
            plan["group_data"] = group_data
            messages.append(f"📋 Menampilkan anggota Kelompok {group_number}")
        
        return plan, messages

    data = None
    count_result = None
    last_action = None
    last_semester_filter = None
    last_show_rata_rata = False
    messages = []
    response_log = {
        "steps": []
    }
    
    # Tracking for combined requests (multi-intent): track data types collected
    combined_request = plan.get("combined_request", False)
    collected_data = {
        "grouping": None,  # Result from grouping steps
        "dosen": None,     # Result from dosen list steps
        "count": None      # Result from count steps
    }
    data_dosen = None  # Track dosen data separately for combined requests

    for step_idx, step in enumerate(plan.get("steps", [])):

        action = step.get("action")
        params = step.get("params", {})
        
        # === DYNAMIC DISPATCH ATTEMPT (NEW) ===
        # Try dynamic dispatcher first for actions registered in registry
        step_result = None
        dispatch_metadata = None
        dispatch_attempted = False
        
        try:
            # Prepare context for dispatcher
            dispatch_context = {
                "dosen_context": dosen_context,
                "data": data,
                "existing_groups": existing_groups,
                "collected_data": collected_data,
                "data_dosen": data_dosen
            }
            
            # Try to dispatch via registry (automatic retry with backoff)
            step_result, dispatch_metadata = dispatcher.dispatch(
                action,
                params,
                dispatch_context
            )
            
            dispatch_attempted = True
            execution_metadata["successful_steps"] += 1
            execution_metadata["total_retries"] += dispatch_metadata.get("retries", 0)
            execution_metadata["execution_times"].append(dispatch_metadata.get("timing_ms", 0))
            
            # Track execution
            execution_log.append({
                "step": step_idx,
                "action": action,
                "status": "success_via_dispatcher",
                "time_ms": dispatch_metadata.get("timing_ms"),
                "retries": dispatch_metadata.get("retries", 0)
            })
            
            print(f"[DISPATCHER] ✓ {action} executed via dispatcher (retries: {dispatch_metadata.get('retries', 0)})")
            
            # Update data from step result jika ada
            if step_result and isinstance(step_result, dict):
                if "data" in step_result:
                    data = step_result["data"]
                if "count" in step_result:
                    count_result = step_result["count"]
                if "message" in step_result:
                    messages.append(step_result["message"])
            
            # Log to response_log
            response_log["steps"].append({
                "action": action,
                "status": "success",
                "executed_via": "dispatcher"
            })
            
        except Exception as e:
            # Dispatcher failed or not registered - fall through to hardcoded logic below
            print(f"[DISPATCHER] ⚠ {action} not in dispatcher, falling back to hardcoded logic: {str(e)}")
            dispatch_attempted = False
            execution_log.append({
                "step": step_idx,
                "action": action,
                "status": "dispatcher_not_available",
                "error": str(e)
            })

        print(f"[EXEC] Step: {action}")

        # Skip hardcoded logic if dispatcher handled this action successfully
        if dispatch_attempted and step_result is not None:
            print(f"[EXEC] ✓ Dispatcher handled {action}, skipping hardcoded logic")
        
        # === HARDCODED FALLBACK LOGIC (kept for backward compatibility) ===
        elif action == "get_mahasiswa":
            fields = params.get("fields", ["nama", "nim"])
            prodi_filter = params.get("prodi_filter")

            print(f"[EXEC] Step: {action}")
            print(f"[EXEC] Context: {dosen_context[0] if dosen_context else 'None'}")
            print(f"[EXEC] Fields: {fields}, prodi_filter: {prodi_filter}")

            data = get_mahasiswa_by_context(
                dosen_context[0],
                fields,
                prodi_filter
            )

            step_result = {
                "action": "get_mahasiswa",
                "count": len(data),
                "data_sample": data[:3]
            }
            last_action = "get_mahasiswa"
            messages.append(f"✅ Data mahasiswa berhasil diambil: {len(data)} orang")

        elif action == "get_courses":
            """Get list of courses/matakuliah for context"""
            from tools.course_tool import get_courses_by_context
            
            # Check if semester filter is specified in params
            semester_filter = None
            show_rata_rata = False
            if step.get("params"):
                semester_filter = step["params"].get("semester_filter")
                show_rata_rata = step["params"].get("show_rata_rata", False)
            
            courses = get_courses_by_context(dosen_context[0] if dosen_context else None, semester_filter=semester_filter)
            
            filter_text = f" (Semester {semester_filter})" if semester_filter else ""
            step_result = {
                "action": "get_courses",
                "count": len(courses),
                "courses": courses,
                "semester_filter": semester_filter,
                "show_rata_rata": show_rata_rata
            }
            last_action = "get_courses"
            last_semester_filter = semester_filter
            last_show_rata_rata = show_rata_rata
            data = courses
            messages.append(f"✅ Daftar matakuliah berhasil diambil: {len(courses)} matakuliah{filter_text}")

        elif action == "get_student_matkul":
            """Get matakuliah untuk multiple semesters"""
            from tools.course_tool import get_courses_by_semesters
            
            # Extract semesters from params
            semesters = params.get("semesters", [])
            
            if not semesters:
                messages.append("❌ Semester tidak ditemukan dalam permintaan")
                step_result = {
                    "action": "get_student_matkul",
                    "error": "semesters not provided"
                }
            else:
                try:
                    context = dosen_context[0] if dosen_context else None
                    courses_by_sem = get_courses_by_semesters(semesters, context=context)
                    
                    # Build response message
                    sem_str = ", ".join(map(str, semesters))
                    message = f"✅ Daftar matakuliah untuk semester {sem_str}:\n"
                    total_courses = 0
                    for sem in sorted(courses_by_sem.keys()):
                        courses = courses_by_sem.get(sem, [])
                        total_courses += len(courses)
                        message += f"\n📚 **Semester {sem}** ({len(courses)} matakuliah):\n"
                        for course in courses:
                            message += f"  - {course.get('kode_mk', 'N/A')}: {course.get('nama_matkul', 'N/A')} ({course.get('sks', 0)} SKS)\n"
                    
                    step_result = {
                        "action": "get_student_matkul",
                        "semesters": semesters,
                        "total_courses": total_courses,
                        "courses_by_semester": courses_by_sem
                    }
                    data = courses_by_sem
                    messages.append(message)
                except Exception as e:
                    print(f"[EXEC] Error in get_student_matkul: {e}")
                    messages.append(f"❌ Error mengambil matakuliah: {str(e)}")
                    step_result = {
                        "action": "get_student_matkul",
                        "error": str(e)
                    }
            
            last_action = "get_student_matkul"

        elif action == "view_group_by_number":
            """View existing group by group number"""
            from tools.db_tool import get_group_by_nomor_kelompok
            
            group_number = params.get("group_number")
            context = dosen_context[0] if dosen_context else None
            
            if group_number is None:
                messages.append("❌ Nomor kelompok tidak ditemukan dalam permintaan")
                step_result = {
                    "action": "view_group_by_number",
                    "error": "group_number is None"
                }
                last_action = "view_group_by_number"
            else:
                try:
                    group_data = get_group_by_nomor_kelompok(group_number, context)
                    
                    if group_data:
                        step_result = {
                            "action": "view_group_by_number",
                            "group_number": group_number,
                            "found": True,
                            "member_count": len(group_data.get("members", []))
                        }
                        last_action = "view_group_by_number"
                        data = group_data
                        messages.append(f"✅ Kelompok {group_number} ditemukan dengan {len(group_data.get('members', []))} anggota")
                    else:
                        messages.append(f"❌ Kelompok nomor {group_number} tidak ditemukan.")
                        step_result = {
                            "action": "view_group_by_number",
                            "error": f"Group {group_number} not found"
                        }
                        last_action = "view_group_by_number"
                        data = None
                except Exception as e:
                    messages.append(f"❌ Error mengambil kelompok: {str(e)}")
                    step_result = {
                        "action": "view_group_by_number",
                        "error": str(e)
                    }
                    last_action = "view_group_by_number"
                    data = None

        elif action == "count_mahasiswa":
            prodi_filter = params.get("prodi_filter")
            count_result = count_mahasiswa_by_context(dosen_context[0], prodi_filter)
            step_result = {
                "action": "count_mahasiswa",
                "count": count_result
            }
            last_action = "count_mahasiswa"
            
            # Track for combined requests
            if combined_request:
                collected_data["count"] = count_result
            
            messages.append(f"✅ Total mahasiswa: {count_result}")

        elif action == "get_dosen_list":
            """Get daftar dosen untuk prodi"""
            prodi_id = _get_context_value(dosen_context[0], 'prodi_id')
            
            dosen_list = get_dosen_list_by_prodi(prodi_id)
            
            if not dosen_list:
                messages.append(f"⚠️ Tidak ada dosen ditemukan untuk prodi ini")
                dosen_list = []
            else:
                messages.append(f"✅ Daftar dosen berhasil diambil: {len(dosen_list)} dosen")
            
            step_result = {
                "action": "get_dosen_list",
                "count": len(dosen_list),
                "dosen_data": dosen_list
            }
            last_action = "get_dosen_list"
            
            # Store dosen list for later processing
            data_dosen = dosen_list
            
            # Track for combined requests
            if combined_request:
                collected_data["dosen"] = dosen_list

        elif action == "get_student_scores_by_category":
            if not data:
                raise Exception("❌ Data mahasiswa kosong, tidak bisa ambil scores")

            kategori_pa = _get_context_value(dosen_context[0], 'kategori_pa') or 3  # Default kategori 3

            student_scores = get_student_scores_by_category(
                data,
                kategori_pa=kategori_pa
            )

            step_result = {
                "action": "get_student_scores_by_category",
                "count": len(student_scores),
                "kategori_pa": kategori_pa
            }
            last_action = "get_student_scores_by_category"
            messages.append(f"✅ Nilai matakuliah diambil untuk {len(student_scores)} mahasiswa (kategori PA: {kategori_pa})")
            
            # Store scores for next action
            data = student_scores

        elif action == "group_by_score_balance_with_constraints":
            """
            Grouping berdasarkan nilai dengan constraints (must_pairs, avoid_pairs, shuffle)
            Sama seperti grouping_by_score, ini handler untuk step builder generate action
            """
            if not data:
                raise Exception("❌ Data scores kosong, tidak bisa grouping by score")

            group_size = params.get("group_size", 6)
            allow_deviation = params.get("allow_deviation", 0.5)
            avoid_pairs = params.get("avoid_pairs", [])
            must_pairs = params.get("must_pairs", [])
            shuffle = params.get("shuffle", False)

            # Validate names against current student dataset
            score_students = [s.get("student_data", {}) for s in data]
            valid_must_pairs, must_pair_messages = _validate_pairs(score_students, must_pairs)
            valid_avoid_pairs, avoid_pair_messages = _validate_pairs(score_students, avoid_pairs)
            messages.extend(must_pair_messages)
            messages.extend(avoid_pair_messages)

            if valid_must_pairs or valid_avoid_pairs or shuffle:
                grouped_data, class_stats = group_by_score_balance_with_constraints(
                    data,
                    group_size=group_size,
                    allow_deviation=allow_deviation,
                    must_pairs=valid_must_pairs,
                    avoid_pairs=valid_avoid_pairs,
                    shuffle=shuffle
                )
            else:
                grouped_data, class_stats = group_by_score_balance(
                    data,
                    group_size=group_size,
                    allow_deviation=allow_deviation
                )

            step_result = {
                "action": "group_by_score_balance_with_constraints",
                "groups": len(grouped_data),
                "group_size": group_size,
                "class_average": class_stats["class_average"],
                "must_pairs": valid_must_pairs,
                "avoid_pairs": valid_avoid_pairs,
                "shuffle": shuffle
            }
            last_action = "group_by_score_balance_with_constraints"
            messages.append(f"✅ Pengelompokan berdasarkan nilai berhasil: {len(grouped_data)} kelompok")
            messages.append(f"📊 Nilai rata-rata kelas: {class_stats['class_average']:.2f}")
            messages.append(f"📈 Range nilai: {class_stats['min_score']:.2f} - {class_stats['max_score']:.2f}")
            
            # Store result with class stats
            data = {
                "groups": grouped_data,
                "class_stats": class_stats
            }
            
            # Track for combined requests
            if combined_request:
                collected_data["grouping"] = data

        elif action == "grouping_by_score":
            if not data:
                raise Exception("❌ Data scores kosong, tidak bisa grouping by score")

            group_size = params.get("group_size", 6)
            allow_deviation = params.get("allow_deviation", 0.5)
            avoid_pairs = params.get("avoid_pairs", [])
            must_pairs = params.get("must_pairs", [])
            shuffle = params.get("shuffle", False)

            # Validate names against current student dataset.
            score_students = [s.get("student_data", {}) for s in data]
            valid_must_pairs, must_pair_messages = _validate_pairs(score_students, must_pairs)
            valid_avoid_pairs, avoid_pair_messages = _validate_pairs(score_students, avoid_pairs)
            messages.extend(must_pair_messages)
            messages.extend(avoid_pair_messages)

            if valid_must_pairs or valid_avoid_pairs or shuffle:
                grouped_data, class_stats = group_by_score_balance_with_constraints(
                    data,
                    group_size=group_size,
                    allow_deviation=allow_deviation,
                    must_pairs=valid_must_pairs,
                    avoid_pairs=valid_avoid_pairs,
                    shuffle=shuffle
                )
            else:
                grouped_data, class_stats = group_by_score_balance(
                    data,
                    group_size=group_size,
                    allow_deviation=allow_deviation
                )

            step_result = {
                "action": "grouping_by_score",
                "groups": len(grouped_data),
                "group_size": group_size,
                "class_average": class_stats["class_average"],
                "must_pairs": valid_must_pairs,
                "avoid_pairs": valid_avoid_pairs,
                "shuffle": shuffle
            }
            last_action = "grouping_by_score"
            messages.append(f"✅ Pengelompokan berdasarkan nilai berhasil: {len(grouped_data)} kelompok")
            messages.append(f"📊 Nilai rata-rata kelas: {class_stats['class_average']:.2f}")
            messages.append(f"📈 Range nilai: {class_stats['min_score']:.2f} - {class_stats['max_score']:.2f}")
            
            # Store result with class stats
            data = {
                "groups": grouped_data,
                "class_stats": class_stats
            }

        elif action == "grouping_by_career":
            """
            Grouping dengan balance karir (Backend, Frontend, UI/UX, Data AI)
            Memerlukan student scores dari langkah sebelumnya
            """
            if not data or not isinstance(data, list):
                raise Exception("❌ Data scores kosong, tidak bisa grouping by career")

            group_size = params.get("group_size", 4)
            avoid_pairs = params.get("avoid_pairs", [])
            must_pairs = params.get("must_pairs", [])

            # Get student scores
            student_scores = data if isinstance(data, list) else data.get("groups", [])
            
            if not student_scores:
                raise Exception("❌ Data mahasiswa dengan scores tidak ditemukan")

            # Extract subject codes to categorize
            subject_codes = set()
            for student in student_scores:
                scores_by_sem = student.get("scores_by_semester", {})
                for sem_data in scores_by_sem.values():
                    subject_codes.update([(code, code) for code in sem_data.get("kode_mk", [])])
            
            subject_list = list(subject_codes)
            
            # Categorize subjects using LLM
            messages.append(f"🤖 Menganalisis {len(subject_list)} matakuliah untuk kategorisasi karir...")
            subject_categorization = categorize_subjects(subject_list)
            
            if subject_categorization:
                messages.append(f"✅ {len(subject_categorization)} matakuliah berhasil dikategorisasi")
            else:
                # Fallback: Jika LLM categorization gagal, gunakan default categorization
                messages.append(f"⚠️ Kategorisasi LLM gagal, menggunakan default categorization")
                # Assign based on keywords in nama_mk
                for code, _ in subject_list:
                    if "backend" in str(_).lower() or "api" in str(_).lower() or "database" in str(_).lower():
                        subject_categorization[code] = "Backend"
                    elif "frontend" in str(_).lower() or "web" in str(_).lower() or "mobile" in str(_).lower():
                        subject_categorization[code] = "Frontend"
                    elif "ui" in str(_).lower() or "ux" in str(_).lower() or "design" in str(_).lower():
                        subject_categorization[code] = "UI/UX"
                    else:
                        subject_categorization[code] = "Data AI"
            
            # Score students by expertise
            students_with_expertise = []
            for student in student_scores:
                scored = score_student_by_expertise(student, subject_categorization)
                if scored:
                    students_with_expertise.append(scored)
            
            messages.append(f"[INFO] {len(students_with_expertise)} mahasiswa dipetakan ke kategori keahlian")
            
            # Validate pairs
            score_students = [s.get("student_data", {}) for s in student_scores]
            valid_must_pairs, must_pair_messages = _validate_pairs(score_students, must_pairs)
            valid_avoid_pairs, avoid_pair_messages = _validate_pairs(score_students, avoid_pairs)
            messages.extend(must_pair_messages)
            messages.extend(avoid_pair_messages)
            
            # Create balanced groups by expertise
            grouped_data = create_balanced_groups_by_expertise(
                students_with_expertise,
                group_size=group_size,
                avoid_pairs=valid_avoid_pairs,
                must_pairs=valid_must_pairs
            )
            
            # Calculate balance metrics
            balance_metrics = calculate_group_balance_metrics(grouped_data)
            
            # Handle case where balance_metrics is None
            if not balance_metrics:
                balance_metrics = {
                    "overall_balance_score": 0,
                    "total_students": len(students_with_careers),
                    "career_distribution": {},
                    "group_details": [],
                    "class_average": 0
                }
            
            step_result = {
                "action": "grouping_by_career",
                "groups": len(grouped_data),
                "group_size": group_size,
                "balance_score": balance_metrics.get("overall_balance_score", 0),
                "career_distribution": balance_metrics.get("career_distribution", {})
            }
            last_action = "grouping_by_career"
            messages.append(f"✅ Pengelompokan berdasarkan karir berhasil: {len(grouped_data)} kelompok")
            messages.append(f"⚖️ Skor keseimbangan karir: {balance_metrics.get('overall_balance_score', 0):.2f}/100")
            
            # Include warnings about homogeneous groups
            warnings = balance_metrics.get("warnings", [])
            if warnings:
                for warning in warnings:
                    messages.append(warning)
            else:
                messages.append("✅ Semua kelompok memiliki keberagaman karir yang baik!")
            
            # Store result with balance metrics
            data = {
                "groups": grouped_data,
                "balance_metrics": balance_metrics,
                "subject_categorization": subject_categorization
            }

        elif action == "grouping_with_constraints":
            """Generic grouping dengan must_pairs dan avoid_pairs constraints"""
            if not data:
                raise Exception("❌ Data mahasiswa kosong, tidak bisa grouping")
            
            avoid_pairs = params.get("avoid_pairs", [])
            must_pairs = params.get("must_pairs", [])
            shuffle = params.get("shuffle", False)
            group_size = params.get("group_size", 5)
            num_groups = params.get("num_groups")
            
            # Validate pairs dengan data actual
            valid_must_pairs, must_pair_messages = _validate_pairs(data, must_pairs)
            valid_avoid_pairs, avoid_pair_messages = _validate_pairs(data, avoid_pairs)
            
            messages.extend(must_pair_messages)
            messages.extend(avoid_pair_messages)
            
            # Grouping dengan constraints
            data = group_students_with_constraints(
                data,
                group_size=group_size,
                avoid_pairs=valid_avoid_pairs,
                must_pairs=valid_must_pairs,
                shuffle=shuffle
            )
            
            step_result = {
                "action": "grouping_with_constraints",
                "groups": len(data),
                "must_pairs": valid_must_pairs,
                "avoid_pairs": valid_avoid_pairs,
                "shuffle": shuffle
            }
            last_action = "grouping_with_constraints"
            messages.append(f"✅ Grouping berhasil: {len(data)} kelompok berhasil dibuat")
            
            if must_pairs:
                messages.append(f"✅ {len(valid_must_pairs)} pasangan ditempatkan satu kelompok")
            if avoid_pairs:
                messages.append(f"✅ {len(valid_avoid_pairs)} pasangan dipisahkan")
            if shuffle:
                messages.append(f"🔀 Distribusi mahasiswa diacak")

        elif action == "create_balanced_groups_by_career":
            """Expertise-based balanced grouping"""
            if not data:
                raise Exception("[ERROR] Data mahasiswa kosong, tidak bisa grouping")
            
            # First categorize if not already done
            if not step_result or step_result.get("action") != "categorize_subjects":
                # Categorize subjects
                subject_categorization = categorize_subjects(data, dosen_context[0])
                
                step_result = {
                    "action": "categorize_subjects",
                    "categorized_count": len(subject_categorization)
                }
                messages.append(f"[SUCCESS] Subjek dikategorikan untuk {len(subject_categorization)} mahasiswa")
            
            # Score students by expertise
            scored_data = []
            for student in data:
                scored = score_student_by_expertise(student, dosen_context[0])
                scored_data.append(scored)
            
            # Create balanced groups
            avoid_pairs = params.get("avoid_pairs", [])
            must_pairs = params.get("must_pairs", [])
            shuffle = params.get("shuffle", False)
            num_groups = params.get("num_groups")
            
            # Validate pairs
            valid_must_pairs, must_pair_messages = _validate_pairs(scored_data, must_pairs)
            valid_avoid_pairs, avoid_pair_messages = _validate_pairs(scored_data, avoid_pairs)
            
            messages.extend(must_pair_messages)
            messages.extend(avoid_pair_messages)
            
            data = create_balanced_groups_by_expertise(
                scored_data,
                num_groups=num_groups,
                must_pairs=valid_must_pairs,
                avoid_pairs=valid_avoid_pairs,
                shuffle=shuffle
            )
            
            # Calculate balance metrics
            balance_metrics = calculate_group_balance_metrics(data)
            
            step_result = {
                "action": "create_balanced_groups_by_career",
                "groups": len(data),
                "balance_score": balance_metrics.get("balance_score_percentage", 0),
                "expertise_diversity": "high"
            }
            last_action = "create_balanced_groups_by_career"
            messages.append(f"[SUCCESS] Expertise-balanced grouping: {len(data)} kelompok dengan keseimbangan keahlian")
            messages.append(f"[INFO] Balance score: {balance_metrics.get('balance_score_percentage', 0):.1f}%")
            
            if must_pairs:
                messages.append(f"✅ {len(valid_must_pairs)} pasangan ditempatkan satu kelompok")
            if avoid_pairs:
                messages.append(f"✅ {len(valid_avoid_pairs)} pasangan dipisahkan")
            if shuffle:
                messages.append(f"🔀 Distribusi mahasiswa diacak")

        elif action == "grouping":
            if not data:
                raise Exception("❌ Data mahasiswa kosong, tidak bisa grouping")

            group_size = params.get("group_size")
            num_groups = params.get("num_groups")
            avoid_pairs = params.get("avoid_pairs", [])
            must_pairs = params.get("must_pairs", [])
            shuffle = params.get("shuffle", False)
            
            # Default group_size jika tidak ada num_groups
            if not num_groups and not group_size:
                group_size = 6

            # Validate dan fix must_pairs dan avoid_pairs dengan actual data
            valid_must_pairs, must_pair_messages = _validate_pairs(data, must_pairs)
            valid_avoid_pairs, avoid_pair_messages = _validate_pairs(data, avoid_pairs)
            
            messages.extend(must_pair_messages)
            messages.extend(avoid_pair_messages)

            data = group_students_with_constraints(
                data,
                group_size=group_size,
                num_groups=num_groups,
                avoid_pairs=valid_avoid_pairs,
                must_pairs=valid_must_pairs,
                shuffle=shuffle
            )

            step_result = {
                "action": "grouping",
                "groups": len(data),
                "group_size": group_size,
                "num_groups": num_groups,
                "must_pairs": valid_must_pairs,
                "avoid_pairs": valid_avoid_pairs,
                "shuffle": shuffle
            }
            last_action = "grouping"
            
            # Track for combined requests
            if combined_request:
                collected_data["grouping"] = data
            
            if num_groups:
                messages.append(f"✅ Pembagian kelompok berhasil: {len(data)} kelompok (dari {num_groups} target)")
            else:
                messages.append(f"✅ Pembagian kelompok berhasil: {len(data)} kelompok")
                
            if shuffle:
                messages.append(f"🔀 Distribusi mahasiswa diacak")

        elif action == "modify_existing_groups":
            if not existing_groups:
                # Fallback: Treat as regular grouping with shuffle/constraints

                messages.append(f"ℹ️ Data kelompok sebelumnya tidak ditemukan")
                
                # Try to do regular grouping instead
                if data:
                    avoid_pairs = params.get("avoid_pairs", [])
                    must_pairs = params.get("must_pairs", [])
                    shuffle = params.get("shuffle", False)
                    group_size = params.get("group_size", 6)
                    
                    messages.append(f"✅ Membuat kelompok baru dengan instruksi yang diberikan")
                    
                    valid_must_pairs, must_pair_messages = _validate_pairs(data, must_pairs)
                    valid_avoid_pairs, avoid_pair_messages = _validate_pairs(data, avoid_pairs)
                    
                    messages.extend(must_pair_messages)
                    messages.extend(avoid_pair_messages)
                    
                    data = group_students_with_constraints(
                        data,
                        group_size=group_size,
                        avoid_pairs=valid_avoid_pairs,
                        must_pairs=valid_must_pairs,
                        shuffle=shuffle
                    )
                    
                    messages.append(f"✅ Pembagian kelompok berhasil: {len(data)} kelompok")
                    if shuffle:
                        messages.append(f"🔀 Distribusi mahasiswa diacak")
                    
                    step_result = {
                        "action": "grouping",
                        "groups": len(data),
                        "fallback": True
                    }
                    last_action = "grouping"
                else:
                    raise Exception("❌ Tidak ada data untuk membuat kelompok baru")
            else:
                avoid_pairs = params.get("avoid_pairs", [])
                must_pairs = params.get("must_pairs", [])
                shuffle = params.get("shuffle", False)
                
                # Use minimal change modification by default (hanya kelompok yang terkena yang berubah)
                modified_groups, modify_messages = _modify_groups_minimal_change(
                    existing_groups,
                    avoid_pairs=avoid_pairs,
                    must_pairs=must_pairs,
                    shuffle=shuffle
                )

                messages.extend(modify_messages)

                step_result = {
                    "action": "modify_existing_groups",
                    "groups": len(modified_groups),
                    "must_pairs": must_pairs,
                    "avoid_pairs": avoid_pairs,
                    "shuffle": shuffle,
                    "mode": "minimal_change"
                }
                last_action = "modify_existing_groups"
                data = modified_groups

        elif action == "save_group":
            # Save groups from last result to database
            try:
                # Extract context (could be list or single object)
                ctx = dosen_context[0] if dosen_context else None
                if not ctx:
                    raise Exception("Context tidak ditemukan")
                
                user_id = _get_context_value(ctx, "user_id")
                kpa_id = _get_context_value(ctx, "kategori_pa")
                prodi_id = _get_context_value(ctx, "prodi_id")
                angkatan = _get_context_value(ctx, "angkatan")
                
                if not user_id:
                    raise Exception("User ID tidak ditemukan di context")
                
                # === NEW: Check if fresh data from previous grouping step ===
                # When auto-save after grouping, use the data from grouping step instead of memory
                if data and isinstance(data, list) and len(data) > 0 and isinstance(data[0], dict) and 'members' in data[0]:
                    # This is fresh grouped data from previous step
                    print(f"[EXEC] Using fresh grouped data from previous step: {len(data)} groups")
                    last_result = data
                else:
                    # Get last result data from memory (try grouping draft first, then execution results)
                    last_result = get_latest_grouping_draft(user_id)
                    if not last_result:
                        # Fallback to execution results if no draft
                        last_result = get_last_result_by_user(user_id)
                
                if not last_result:
                    messages.append("Tidak ada data kelompok yang disimpan sebelumnya")
                    raise Exception("Tidak ada data kelompok untuk disimpan")
                
                # Get TM_id from angkatan
                tm_id = angkatan if isinstance(angkatan, int) else None
                
                # Get current tahun_ajaran_id (latest)
                from tools.db_tool import engine, check_existing_groups_by_context
                from sqlalchemy import text
                
                tahun_ajaran_query = "SELECT id FROM tahun_ajaran ORDER BY id DESC LIMIT 1"
                with engine.connect() as conn:
                    ta_result = conn.execute(text(tahun_ajaran_query)).fetchone()
                tahun_ajaran_id = ta_result[0] if ta_result else None
                
                # Validate required parameters
                if not all([kpa_id, prodi_id, tm_id, tahun_ajaran_id]):
                    messages.append(f"Parameter tidak lengkap | KPA_id={kpa_id}, prodi_id={prodi_id}, TM_id={tm_id}, tahun_ajaran_id={tahun_ajaran_id}")
                    raise Exception("Parameter tidak lengkap untuk menyimpan grup")
                
                # CHECK EXISTING GROUPS
                existing_groups = check_existing_groups_by_context(kpa_id, prodi_id, tm_id, tahun_ajaran_id)
                
                if existing_groups > 0:
                    # Ask for confirmation instead of auto-saving
                    messages.append(f"Ditemukan {existing_groups} kelompok yang sudah ada di database untuk konteks ini")
                    messages.append(f"Jika melanjutkan, kelompok lama akan dihapus dan diganti dengan yang baru")
                    
                    step_result = {
                        "action": "save_group",
                        "status": "confirmation_needed",
                        "existing_groups": existing_groups,
                        "pending_save": {
                            "kpa_id": kpa_id,
                            "prodi_id": prodi_id,
                            "tm_id": tm_id,
                            "tahun_ajaran_id": tahun_ajaran_id
                        },
                        "new_groups_count": len(last_result),
                        "new_members_count": sum(len(g.get('members', [])) for g in last_result)
                    }
                    last_action = "save_group"
                    data = {
                        "type": "save_group_confirmation",
                        "status": "confirmation_needed",
                        "existing_groups": existing_groups,
                        "new_groups": len(last_result),
                        "new_members": sum(len(g.get('members', [])) for g in last_result)
                    }
                else:
                    # No existing groups, proceed with save
                    save_result = save_groups_from_result(
                        last_result,
                        kpa_id=kpa_id,
                        prodi_id=prodi_id,
                        tm_id=tm_id,
                        tahun_ajaran_id=tahun_ajaran_id,
                        replace_existing=False
                    )
                    
                    if save_result['success']:
                        messages.append(f"✅ Kelompok berhasil disimpan ke database")
                        messages.append(f"📊 Total kelompok disimpan: {save_result['saved_groups']}")
                        messages.append(f"👥 Total mahasiswa disimpan: {save_result['saved_members']}")
                        
                        step_result = {
                            "action": "save_group",
                            "saved_groups": save_result['saved_groups'],
                            "saved_members": save_result['saved_members'],
                            "existing_groups_replaced": 0,
                            "success": True
                        }
                        last_action = "save_group"
                        data = {
                            "type": "save_group",
                            "status": "success",
                            "saved_groups": save_result['saved_groups'],
                            "saved_members": save_result['saved_members'],
                            "existing_groups_replaced": 0
                        }
                    else:
                        if save_result['errors']:
                            for error in save_result['errors']:
                                messages.append(f"❌ Error: {error}")
                        
                        step_result = {
                            "action": "save_group",
                            "saved_groups": save_result['saved_groups'],
                            "saved_members": save_result['saved_members'],
                            "success": False,
                            "errors": save_result['errors']
                        }
                        last_action = "save_group"
                        data = last_result
                    
            except Exception as e:
                messages.append(f"❌ Gagal memproses permintaan simpan: {str(e)}")
                step_result = {
                    "action": "save_group",
                    "success": False,
                    "error": str(e)
                }
                last_action = "save_group"
                data = None

        elif action == "confirm_save_group":
            # User confirmed to replace existing groups
            try:
                # Extract params from pending save info
                params = step.get("params", {})
                kpa_id = params.get("kpa_id")
                prodi_id = params.get("prodi_id")
                tm_id = params.get("tm_id")
                tahun_ajaran_id = params.get("tahun_ajaran_id")
                
                # Extract context for user_id
                ctx = dosen_context[0] if dosen_context else None
                
                user_id = _get_context_value(ctx, "user_id")
                
                # Get last result data from memory (try grouping draft first, then execution results)
                last_result = get_latest_grouping_draft(user_id)
                if not last_result:
                    # Fallback to execution results if no draft
                    last_result = get_last_result_by_user(user_id)
                
                if not last_result:
                    raise Exception("Tidak ada data kelompok untuk disimpan")
                
                # Save with replace_existing=True
                save_result = save_groups_from_result(
                    last_result,
                    kpa_id=kpa_id,
                    prodi_id=prodi_id,
                    tm_id=tm_id,
                    tahun_ajaran_id=tahun_ajaran_id,
                    replace_existing=True
                )
                
                if save_result['success']:
                    messages.append(f"Kelompok berhasil disimpan ke database")
                    messages.append(f"Kelompok lama ({save_result['existing_groups']} grup) telah dihapus")
                    messages.append(f"Total kelompok baru: {save_result['saved_groups']}")
                    messages.append(f"Total mahasiswa: {save_result['saved_members']}")
                    
                    step_result = {
                        "action": "confirm_save_group",
                        "saved_groups": save_result['saved_groups'],
                        "saved_members": save_result['saved_members'],
                        "existing_groups_replaced": save_result['existing_groups'],
                        "success": True
                    }
                    last_action = "confirm_save_group"
                    data = {
                        "type": "save_group",
                        "status": "success",
                        "saved_groups": save_result['saved_groups'],
                        "saved_members": save_result['saved_members'],
                        "existing_groups_replaced": save_result['existing_groups']
                    }
                else:
                    for error in save_result['errors']:
                        messages.append(f"Error: {error}")
                    
                    step_result = {
                        "action": "confirm_save_group",
                        "success": False,
                        "errors": save_result['errors']
                    }
                    last_action = "confirm_save_group"
                    data = None
                    
            except Exception as e:
                messages.append(f"Gagal menyimpan kelompok: {str(e)}")
                step_result = {
                    "action": "confirm_save_group",
                    "success": False,
                    "error": str(e)
                }
                last_action = "confirm_save_group"
                data = None

        elif action == "delete_group":
            """Delete/reset existing groups"""
            try:
                ctx = dosen_context[0] if dosen_context else None
                if not ctx:
                    raise Exception("Context tidak ditemukan")
                
                user_id = _get_context_value(ctx, "user_id")
                kpa_id = _get_context_value(ctx, "kategori_pa")
                prodi_id = _get_context_value(ctx, "prodi_id")
                angkatan = _get_context_value(ctx, "angkatan")
                tm_id = angkatan if isinstance(angkatan, int) else None
                
                # Get current tahun_ajaran_id (latest)
                from tools.db_tool import engine
                from sqlalchemy import text
                
                tahun_ajaran_query = "SELECT id FROM tahun_ajaran ORDER BY id DESC LIMIT 1"
                with engine.connect() as conn:
                    ta_result = conn.execute(text(tahun_ajaran_query)).fetchone()
                tahun_ajaran_id = ta_result[0] if ta_result else None
                
                if not all([kpa_id, prodi_id, tm_id, tahun_ajaran_id]):
                    messages.append(f"Parameter tidak lengkap untuk delete grup")
                    step_result = {
                        "action": "delete_group",
                        "success": False,
                        "error": "Parameter tidak lengkap"
                    }
                else:
                    # Delete groups
                    from tools.db_tool import delete_existing_groups_by_context
                    
                    delete_result = delete_existing_groups_by_context(kpa_id, prodi_id, tm_id, tahun_ajaran_id)
                    
                    step_result = {
                        "action": "delete_group",
                        "success": delete_result.get('success', False),
                        "deleted_groups": delete_result.get('deleted_groups', 0),
                        "deleted_members": delete_result.get('deleted_members', 0)
                    }
                    last_action = "delete_group"
                    
                    if delete_result.get('success', False):
                        messages.append(f"✅ Kelompok berhasil dihapus")
                        messages.append(f"Total kelompok dihapus: {delete_result.get('deleted_groups', 0)}")
                        messages.append(f"Total anggota dihapus: {delete_result.get('deleted_members', 0)}")
                    else:
                        messages.append(f"⚠️ Tidak ada kelompok yang dihapus (mungkin belum ada)")
                    
                    data = delete_result
                    
            except Exception as e:
                messages.append(f"❌ Gagal menghapus kelompok: {str(e)}")
                step_result = {
                    "action": "delete_group",
                    "success": False,
                    "error": str(e)
                }
                last_action = "delete_group"
                data = None

        elif action == "search_student":
            """Search for a student by name"""
            try:
                student_name = params.get("student_name") or params.get("name")
                
                if not student_name:
                    messages.append("❌ Nama mahasiswa tidak ditemukan dalam permintaan")
                    step_result = {
                        "action": "search_student",
                        "error": "name parameter missing"
                    }
                    last_action = "search_student"
                    data = None
                else:
                    # Get all students for the context
                    if not data:
                        # Fetch mahasiswa if not already retrieved
                        data = get_mahasiswa_by_context(
                            dosen_context[0],
                            fields=["id", "user_id", "nama", "nim", "angkatan", "prodi_id"]
                        )
                    
                    # Search for student by name
                    from tools.grouping_tool import _find_student_by_name
                    
                    student = _find_student_by_name(data, student_name)
                    
                    if student:
                        step_result = {
                            "action": "search_student",
                            "found": True,
                            "student": student
                        }
                        last_action = "search_student"
                        data = student  # Update data to single student result
                        messages.append(f"✅ Mahasiswa ditemukan: {student.get('nama', '?')}")
                        messages.append(f"   NIM: {student.get('nim', '?')}")
                    else:
                        messages.append(f"❌ Mahasiswa '{student_name}' tidak ditemukan")
                        step_result = {
                            "action": "search_student",
                            "found": False,
                            "searched_name": student_name
                        }
                        last_action = "search_student"
                        data = None
                        
            except Exception as e:
                messages.append(f"❌ Error mencari mahasiswa: {str(e)}")
                step_result = {
                    "action": "search_student",
                    "error": str(e)
                }
                last_action = "search_student"
                data = None

        else:
            print(f"WARNING: step action tidak dikenali: {action}")
            step_result = {
                "action": action,
                "warning": "tidak di-handle"
            }

        response_log["steps"].append(step_result)



    # Generate formatted response (HTML table) if we have data
    formatted_response = ""
    
    # SPECIAL: Handle combined requests (multiple intents)
    if combined_request and any(collected_data.values()):

        # Build combined response with all collected data
        html = '<div class="combined-result">'
        
        # Add grouping results if available
        if collected_data["grouping"]:
            html += '<div class="grouping-section" style="margin-bottom: 20px;">'
            html += '<h3>📊 Hasil Pembagian Kelompok</h3>'
            
            grouping_data = collected_data["grouping"]
            # Extract groups list - grouping_data is a dict with "groups" and "class_stats"
            groups_list = grouping_data.get("groups", []) if isinstance(grouping_data, dict) else grouping_data
            
            for idx, group in enumerate(groups_list, 1):
                html += f'<div class="group-item" style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">'
                html += f'<strong>Kelompok {group.get("kelompok", idx)} ({group.get("member_count", len(group.get("members", []))) if isinstance(group, dict) else 0} anggota)</strong><br/>'
                
                members = group.get("members", [])
                if members:
                    html += '<ul style="margin: 5px 0;">'
                    for member in members:
                        if isinstance(member, dict):
                            html += f"<li>{member.get('nama', '?')} ({member.get('nim', '?')})</li>"
                        else:
                            html += f"<li>{member}</li>"
                    html += '</ul>'
                html += '</div>'
            
            html += '</div>'
        
        # Add dosen list if available
        if collected_data["dosen"]:
            html += '<div class="dosen-section" style="margin-bottom: 20px;">'
            html += '<h3>👨‍🏫 Daftar Dosen</h3>'
            
            dosen_data = collected_data["dosen"]
            html += '<table class="dosen-table" style="width: 100%; border-collapse: collapse;">'
            html += '<thead style="background-color: #f5f5f5;"><tr>'
            html += '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Nama</th>'
            html += '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">NIP</th>'
            html += '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Jabatan</th>'
            html += '</tr></thead><tbody>'
            
            for dosen in dosen_data:
                if isinstance(dosen, dict):
                    html += '<tr>'
                    html += f'<td style="border: 1px solid #ddd; padding: 8px;">{dosen.get("nama", "?")}</td>'
                    html += f'<td style="border: 1px solid #ddd; padding: 8px;">{dosen.get("nip", "?")}</td>'
                    html += f'<td style="border: 1px solid #ddd; padding: 8px;">{dosen.get("jabatan_akademik_desc", "?")}</td>'
                    html += '</tr>'
            
            html += '</tbody></table>'
            html += '</div>'
        
        # Add count if available
        if collected_data["count"]:
            html += f'<div class="count-section"><p><strong>Total Mahasiswa: {collected_data["count"]}</strong></p></div>'
        
        html += '</div>'
        formatted_response = html
        
        result_dict = {
            "type": "combined_result",
            "data": collected_data,
            "formatted_response": formatted_response,
            "messages": messages
        }
        return result_dict, messages
    
    if last_action == "count_mahasiswa":
        # Format count result
        formatted_response = f"""
        <div class="result-container">
            <h3>📊 Hasil Penghitungan</h3>
            <p style="font-size: 24px; font-weight: bold; color: #2196F3;">
                {count_result} mahasiswa
            </p>
        </div>
        """
        result_dict = {
            "type": plan.get("type", "count_result"),
            "count": count_result,
            "formatted_response": formatted_response
        }
        return result_dict, messages
    
    elif last_action == "get_mahasiswa" and data:
        # Format mahasiswa list as HTML table
        if isinstance(data, list) and len(data) > 0:
            headers = list(data[0].keys()) if isinstance(data[0], dict) else ["Data"]
            
            html = '<table class="data-table" style="width: 100%; border-collapse: collapse;">'
            html += '<thead style="background-color: #f5f5f5;"><tr>'
            for header in headers:
                html += f'<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">{header}</th>'
            html += '</tr></thead><tbody>'
            
            for row in data:
                html += '<tr>'
                if isinstance(row, dict):
                    for key in headers:
                        value = row.get(key, "")
                        html += f'<td style="border: 1px solid #ddd; padding: 8px;">{value}</td>'
                else:
                    html += f'<td style="border: 1px solid #ddd; padding: 8px;">{row}</td>'
                html += '</tr>'
            
            html += '</tbody></table>'
            formatted_response = html
        
        result_dict = {
            "type": plan.get("type", "data_result"),
            "data_count": len(data),
            "data": data,
            "formatted_response": formatted_response
        }
        return result_dict, messages
    
    elif last_action == "grouping" and data:
        # Format grouping result
        if isinstance(data, list) and len(data) > 0:
            html = '<div class="grouping-result">'
            for idx, group in enumerate(data, 1):
                html += f'<div class="group" style="margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">'
                html += f'<h4>Kelompok {idx}</h4>'
                if isinstance(group, dict):
                    members = group.get("members", [])
                    html += '<ul>'
                    for member in members:
                        if isinstance(member, dict):
                            html += f'<li>{member.get("nama", member)}</li>'
                        else:
                            html += f'<li>{member}</li>'
                    html += '</ul>'
                elif isinstance(group, list):
                    html += '<ul>'
                    for member in group:
                        if isinstance(member, dict):
                            html += f'<li>{member.get("nama", member)}</li>'
                        else:
                            html += f'<li>{member}</li>'
                    html += '</ul>'
                html += '</div>'
            
            # Add recommendations for saving/exporting
            html += '<div style="margin-top: 20px; padding: 15px; background-color: #f0f8ff; border-left: 4px solid #2196F3; border-radius: 4px;">'
            html += '<h4>💾 Selanjutnya?</h4>'
            html += '<p style="margin: 8px 0;">Apakah Anda ingin:</p>'
            html += '<ul style="margin: 8px 0;">'
            html += '<li><strong>Simpan ke Database:</strong> "Simpan kelompok ke database" atau "Lanjutkan"</li>'
            html += '<li><strong>Export ke Excel:</strong> "Export kelompok ke excel" atau "Buat template excel"</li>'
            html += '<li><strong>Modifikasi:</strong> "Ganti anggota" atau "Acak ulang"</li>'
            html += '</ul>'
            html += '</div>'
            
            html += '</div>'
            formatted_response = html
        
        result_dict = {
            "type": plan.get("type", "grouping_result"),
            "group_count": len(data),
            "data": data,
            "formatted_response": formatted_response,
            "recommendations": {
                "actions": [
                    {"text": "Simpan ke Database", "instruction": "simpan kelompok ke database"},
                    {"text": "Export ke Excel", "instruction": "export kelompok ke excel"},
                    {"text": "Modifikasi Kelompok", "instruction": "modifikasi anggota kelompok"}
                ]
            }
        }
        return result_dict, messages
    
    elif last_action == "search_student" and data:
        # Format student search result
        if isinstance(data, dict):
            # Single student result
            html = '<div class="student-result" style="padding: 15px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">'
            html += f'<h4>📋 Informasi Mahasiswa</h4>'
            html += '<table style="width: 100%; border-collapse: collapse;">'
            
            for key, value in data.items():
                if key not in ['id', 'user_id']:  # Skip internal IDs
                    display_key = key.replace('_', ' ').title()
                    html += f'<tr><td style="padding: 8px; font-weight: bold; border-bottom: 1px solid #eee;">{display_key}:</td>'
                    html += f'<td style="padding: 8px; border-bottom: 1px solid #eee;">{value}</td></tr>'
            
            html += '</table>'
            html += '</div>'
            formatted_response = html
            
            result_dict = {
                "type": plan.get("type", "student_query_result"),
                "student": data,
                "formatted_response": formatted_response
            }
        else:
            result_dict = {
                "type": plan.get("type", "student_query_result"),
                "data": data,
                "formatted_response": "<p>Mahasiswa tidak ditemukan</p>"
            }
        return result_dict, messages
    
    elif last_action == "get_courses":
        # Format courses result (handle both empty and non-empty lists)
        from tools.course_tool import format_courses_for_display
        
        courses = data if data else []
        html = format_courses_for_display(courses, show_rata_rata=last_show_rata_rata)
        formatted_response = html
        
        result_dict = {
            "type": "course_list_result",
            "courses": courses,
            "formatted_response": formatted_response
        }
        return result_dict, messages
    
    # Default: wrap data in dict
    result_dict = {
        "type": plan.get("type", "execution_result"),
        "last_action": last_action,
        "data": data,
        "formatted_response": formatted_response
    }
    
    # Add execution metadata to result
    result_dict["execution_metadata"] = {
        "total_steps": len(plan.get("steps", [])),
        "successful_steps": execution_metadata["successful_steps"],
        "failed_steps": execution_metadata["failed_steps"],
        "total_retries": execution_metadata["total_retries"],
        "avg_execution_time_ms": sum(execution_metadata["execution_times"]) / len(execution_metadata["execution_times"]) if execution_metadata["execution_times"] else 0,
        "execution_log": execution_log
    }
    
    return result_dict, messages