"""
Pembimbing (Lecturer Assignment) Management Tool
Handles automatic assignment of lecturers to groups based on rank and available capacity
"""

import random
from tools.db_tool import (
    get_dosen_for_assignment,
    get_groups_with_pembimbing_by_context,
    get_pembimbing_by_group_id,
    create_pembimbing,
    update_pembimbing,
    delete_pembimbing,
    delete_all_pembimbing_by_kelompok,
    get_distinct_jabatan
)


def log(title, data=None):
    print(f"[PEMBIMBING] {title}" + (f" - {data}" if data else ""))


# =========================
# RANK-BASED WORKLOAD MAPPING
# =========================

def get_rank_workload_capacity(jabatan_akademik_desc):
    """
    Determine maximum number of groups for each academic rank.
    Based on jabatan_akademik_desc field.
    
    Higher ranks = fewer groups (more senior, less teaching load)
    Lower ranks = more groups (more junior, more teaching load)
    """
    
    # Rank priority (higher = more senior)
    rank_mapping = {
        'Prof': {'priority': 5, 'max_groups': 2},       # Professor
        'Dr': {'priority': 4, 'max_groups': 4},        # Doctor/Doctorate
        'M.T': {'priority': 3, 'max_groups': 5},        # Master
        'S.T': {'priority': 2, 'max_groups': 6},        # Bachelor/Engineer
        'S.Kom': {'priority': 2, 'max_groups': 6},     # Bachelor/Computer Science
        'S.Si': {'priority': 2, 'max_groups': 6},      # Bachelor/Science
        'S.E': {'priority': 2, 'max_groups': 6},       # Bachelor/Economics
        'S.H': {'priority': 2, 'max_groups': 6},       # Bachelor/Law
        'S.Pd': {'priority': 2, 'max_groups': 6},      # Bachelor/Education
    }
    
    # Try exact match first
    if jabatan_akademik_desc in rank_mapping:
        return rank_mapping[jabatan_akademik_desc]
    
    # Try partial match (for variations like "Dr." or "Prof.")
    for rank, config in rank_mapping.items():
        if rank.lower() in str(jabatan_akademik_desc).lower():
            return config
    
    # Default to Bachelor level if not found
    log(f"WARNING: Unknown jabatan '{jabatan_akademik_desc}', using default S.T capacity")
    return {'priority': 2, 'max_groups': 6}


def assign_pembimbing_automatically(prodi_id, kpa_id, tm_id, jabatan_filter=None):
    """
    Automatically assign lecturers to groups based on:
    1. Rank-based workload capacity (higher rank = fewer groups)
    2. Current load (fewer groups assigned so far gets priority)
    3. Random distribution (some groups get 1, some get 2 lecturers)
    
    Args:
        prodi_id: Program Studi ID
        kpa_id: Kategori PA ID
        tm_id: Tahun Masuk ID
        jabatan_filter: Optional - filter lecturers by academic rank (e.g., "Prof", "Dr", "S.T")
    
    Returns: {
        'success': bool,
        'message': str,
        'created_assignments': int,
        'groups_with_1_lecturer': int,
        'groups_with_2_lecturers': int,
        'groups_with_0_lecturers': int,
        'details': dict
    }
    """
    try:
        # Get all dosen with their current load
        dosen_list = get_dosen_for_assignment(prodi_id, kpa_id, tm_id)
        if not dosen_list:
            return {
                'success': False,
                'message': 'Tidak ada dosen tersedia untuk penugasan',
                'created_assignments': 0,
                'groups_with_1_lecturer': 0,
                'groups_with_2_lecturers': 0,
                'groups_with_0_lecturers': 0,
                'details': {}
            }
        
        # Filter by jabatan if specified
        if jabatan_filter:
            dosen_list = [d for d in dosen_list if d.get('jabatan_akademik_desc', '').startswith(jabatan_filter)]
            if not dosen_list:
                return {
                    'success': False,
                    'message': f'Tidak ada dosen dengan jabatan {jabatan_filter} tersedia',
                    'created_assignments': 0,
                    'groups_with_1_lecturer': 0,
                    'groups_with_2_lecturers': 0,
                    'groups_with_0_lecturers': 0,
                    'details': {}
                }
        
        # Get all groups
        groups = get_groups_with_pembimbing_by_context(prodi_id, kpa_id, tm_id)
        if not groups:
            return {
                'success': False,
                'message': 'Tidak ada kelompok untuk penugasan',
                'created_assignments': 0,
                'groups_with_1_lecturer': 0,
                'groups_with_2_lecturers': 0,
                'groups_with_0_lecturers': 0,
                'details': {}
            }
        
        # Enrich dosen with capacity info
        for dosen in dosen_list:
            capacity = get_rank_workload_capacity(dosen['jabatan_akademik_desc'])
            dosen['max_groups'] = capacity['max_groups']
            dosen['priority'] = capacity['priority']
            dosen['remaining_capacity'] = capacity['max_groups'] - dosen['current_group_count']
        
        log(f"Assignment starting", f"Total dosen: {len(dosen_list)}, Total groups: {len(groups)}")
        
        # Assignment algorithm:
        # 1. For each group, assign 1-2 lecturers randomly
        # 2. Prioritize assigning to lecturers with more remaining capacity
        # 3. Don't exceed each dosen's max_groups capacity
        
        created_assignments = 0
        assignment_details = []
        
        for group in groups:
            kelompok_id = group['kelompok_id']
            current_pembimbing = group['pembimbing_count']
            need_assignment = group['pembimbing_count'] == 0  # Only if no pembimbing yet
            
            if not need_assignment:
                log(f"Group {group['nomor_kelompok']}", f"Already has {current_pembimbing} pembimbing, skipping")
                continue
            
            # Decide 1 or 2 lecturers for this group (random, ~60% chance for 2)
            num_lecturers = 2 if random.random() < 0.6 else 1
            
            # Get available dosen (those who still have capacity and haven't used all slots)
            available_dosen = [d for d in dosen_list if d['remaining_capacity'] > 0]
            
            if not available_dosen:
                log(f"Group {group['nomor_kelompok']}", "No available dosen with remaining capacity")
                assignment_details.append({
                    'kelompok': group['nomor_kelompok'],
                    'assigned': 0,
                    'reason': 'Tidak ada dosen dengan kapasitas tersisa'
                })
                continue
            
            # Sort by priority (higher priority first) and remaining capacity (more capacity first)
            available_dosen.sort(key=lambda d: (-d['priority'], -d['remaining_capacity']))
            
            # Assign up to num_lecturers lecturers
            assigned_for_group = 0
            selected_dosen = []
            
            for _ in range(min(num_lecturers, len(available_dosen))):
                # Pick a dosen from available pool (with some randomness to avoid always picking top)
                if len(available_dosen) > 1 and random.random() < 0.4:
                    # 40% chance to pick 2nd or 3rd best instead of best (for variety)
                    dosen_to_assign = available_dosen[min(1, len(available_dosen) - 1)]
                else:
                    dosen_to_assign = available_dosen[0]
                
                selected_dosen.append(dosen_to_assign)
                
                # Create assignment
                pembimbing_id = create_pembimbing(dosen_to_assign['user_id'], kelompok_id)
                if pembimbing_id:
                    created_assignments += 1
                    assigned_for_group += 1
                    dosen_to_assign['current_group_count'] += 1
                    dosen_to_assign['remaining_capacity'] -= 1
                    log(f"Assigned", f"{dosen_to_assign['nama']} to {group['nomor_kelompok']}")
                    
                    # Remove from available pool if capacity exhausted
                    if dosen_to_assign['remaining_capacity'] <= 0:
                        available_dosen.remove(dosen_to_assign)
                else:
                    log(f"WARNING: Failed to create pembimbing for {dosen_to_assign['nama']}")
            
            assignment_details.append({
                'kelompok': group['nomor_kelompok'],
                'assigned': assigned_for_group,
                'dosen_names': [d['nama'] for d in selected_dosen],
                'reason': 'Successfully assigned' if assigned_for_group > 0 else 'No dosen assigned'
            })
        
        # Get final status
        groups_final = get_groups_with_pembimbing_by_context(prodi_id, kpa_id, tm_id)
        groups_with_0 = sum(1 for g in groups_final if g['pembimbing_count'] == 0)
        groups_with_1 = sum(1 for g in groups_final if g['pembimbing_count'] == 1)
        groups_with_2 = sum(1 for g in groups_final if g['pembimbing_count'] == 2)
        
        return {
            'success': True,
            'message': f'Penugasan selesai. Total pembimbing dibuat: {created_assignments}',
            'created_assignments': created_assignments,
            'groups_with_1_lecturer': groups_with_1,
            'groups_with_2_lecturers': groups_with_2,
            'groups_with_0_lecturers': groups_with_0,
            'details': assignment_details
        }
        
    except Exception as e:
        log(f"ERROR: assign_pembimbing_automatically - {str(e)}")
        return {
            'success': False,
            'message': f'Error during assignment: {str(e)}',
            'created_assignments': 0,
            'groups_with_1_lecturer': 0,
            'groups_with_2_lecturers': 0,
            'groups_with_0_lecturers': 0,
            'details': {}
        }


def format_groups_with_pembimbing(groups_data):
    """Format groups with pembimbing data into HTML table response"""
    
    if not groups_data:
        return "<p>Tidak ada kelompok ditemukan.</p>"
    
    html = """
    <table style="width: 100%; border-collapse: collapse; font-size: 13px; margin: 10px 0;">
        <thead>
            <tr style="background-color: #f0f0f0; border-bottom: 2px solid #333;">
                <th style="padding: 8px; border: 1px solid #ccc; text-align: left;">No</th>
                <th style="padding: 8px; border: 1px solid #ccc; text-align: left;">Kelompok</th>
                <th style="padding: 8px; border: 1px solid #ccc; text-align: center;">Jumlah Pembimbing</th>
                <th style="padding: 8px; border: 1px solid #ccc; text-align: left;">Nama Pembimbing</th>
                <th style="padding: 8px; border: 1px solid #ccc; text-align: left;">Jabatan</th>
                <th style="padding: 8px; border: 1px solid #ccc; text-align: left;">Email</th>
            </tr>
        </thead>
        <tbody>
    """
    
    for idx, group in enumerate(groups_data, 1):
        kelompok_id = group.get('kelompok_id', '')
        nomor_kelompok = group.get('nomor_kelompok', '')
        pembimbing_count = group.get('pembimbing_count', 0)
        pembimbing_list = group.get('pembimbing', [])
        
        if pembimbing_count == 0:
            # Group with no pembimbing
            html += f"""
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 8px; border: 1px solid #ccc;">{idx}</td>
                <td style="padding: 8px; border: 1px solid #ccc;"><strong>{nomor_kelompok}</strong></td>
                <td style="padding: 8px; border: 1px solid #ccc; text-align: center; color: red;">0</td>
                <td style="padding: 8px; border: 1px solid #ccc; color: #999;">-</td>
                <td style="padding: 8px; border: 1px solid #ccc; color: #999;">-</td>
                <td style="padding: 8px; border: 1px solid #ccc; color: #999;">-</td>
            </tr>
            """
        else:
            # Group with pembimbing - show each pembimbing on separate row
            for pem_idx, pembimbing in enumerate(pembimbing_list):
                is_first_row = (pem_idx == 0)
                html += f"""
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 8px; border: 1px solid #ccc;">{idx if is_first_row else ''}</td>
                <td style="padding: 8px; border: 1px solid #ccc;"><strong>{nomor_kelompok if is_first_row else ''}</strong></td>
                <td style="padding: 8px; border: 1px solid #ccc; text-align: center;">{pembimbing_count if is_first_row else ''}</td>
                <td style="padding: 8px; border: 1px solid #ccc;">{pembimbing.get('nama', '-')}</td>
                <td style="padding: 8px; border: 1px solid #ccc;">{pembimbing.get('jabatan_akademik_desc', '-')}</td>
                <td style="padding: 8px; border: 1px solid #ccc;">{pembimbing.get('email', '-')}</td>
            </tr>
                """
    
    html += """
        </tbody>
    </table>
    """
    
    return html


def format_assignment_result(result_data):
    """Format automatic assignment result into readable response"""
    
    if not result_data.get('success'):
        return f"<p style='color: red;'><strong>Pemberian Tugas Gagal:</strong> {result_data.get('message', 'Unknown error')}</p>"
    
    html = f"""
    <div style="font-size: 13px; line-height: 1.6;">
        <p><strong style="color: green;">✓ Pemberian Tugas Berhasil</strong></p>
        <p><strong>{result_data.get('message', '')}</strong></p>
        
        <table style="margin: 10px 0; border-collapse: collapse;">
            <tr style="background-color: #f9f9f9;">
                <td style="padding: 5px 15px; border: 1px solid #ddd;"><strong>Total Pembimbing Dibuat:</strong></td>
                <td style="padding: 5px 15px; border: 1px solid #ddd; color: #2196F3;"><strong>{result_data.get('created_assignments', 0)}</strong></td>
            </tr>
            <tr>
                <td style="padding: 5px 15px; border: 1px solid #ddd;"><strong>Kelompok dengan 1 Pembimbing:</strong></td>
                <td style="padding: 5px 15px; border: 1px solid #ddd; color: #FF9800;">{result_data.get('groups_with_1_lecturer', 0)}</td>
            </tr>
            <tr style="background-color: #f9f9f9;">
                <td style="padding: 5px 15px; border: 1px solid #ddd;"><strong>Kelompok dengan 2 Pembimbing:</strong></td>
                <td style="padding: 5px 15px; border: 1px solid #ddd; color: #4CAF50;">{result_data.get('groups_with_2_lecturers', 0)}</td>
            </tr>
            <tr>
                <td style="padding: 5px 15px; border: 1px solid #ddd;"><strong>Kelompok tanpa Pembimbing:</strong></td>
                <td style="padding: 5px 15px; border: 1px solid #ddd; color: red;">{result_data.get('groups_with_0_lecturers', 0)}</td>
            </tr>
        </table>
        
        <p style="margin-top: 10px; color: #666; font-size: 12px;"><em>Penugasan berdasarkan kapasitas jabatan akademik dan ketersediaan dosen.</em></p>
    </div>
    """
    
    return html


def get_group_with_pembimbing_details(kelompok_id):
    """Get detailed info for a specific group with its pembimbing
    
    Returns: {
        'kelompok_id': int,
        'pembimbing': list of dosen info
    }
    """
    try:
        pembimbing = get_pembimbing_by_group_id(kelompok_id)
        return {
            'kelompok_id': kelompok_id,
            'pembimbing': pembimbing,
            'pembimbing_count': len(pembimbing)
        }
    except Exception as e:
        log(f"ERROR: get_group_with_pembimbing_details - {str(e)}")
        return {
            'kelompok_id': kelompok_id,
            'pembimbing': [],
            'pembimbing_count': 0
        }
