from sqlalchemy import text
from app.db import engine
from datetime import datetime


def log(title, data=None):
    print(f"[DB] {title}" + (f" - {data}" if data else ""))


# =========================
# HELPER: Extract context value (handles dict and object)
# =========================
def _get_context_value(context, key, default=None):
    """Extract value from context - handles both dict and object"""
    if isinstance(context, dict):
        return context.get(key, default)
    else:
        return getattr(context, key, default)


# =========================
# GET TAHUN ANGKATAN
# =========================
def get_tahun_angkatan(angkatan_id):

    query = """
    SELECT Tahun_Masuk
    FROM tahun_masuk
    WHERE id = :id
    """

    with engine.connect() as conn:
        result = conn.execute(text(query), {"id": angkatan_id}).fetchone()

    tahun = result[0] if result else None
    
    log(f"get_tahun_angkatan: Query tahun_masuk.id={angkatan_id} → Result: {tahun}" + (f" (Query: {query})" if not tahun else ""))
    
    if not tahun:
        log(f"WARNING: get_tahun_angkatan returned None for id={angkatan_id}")
    
    return tahun


# =========================
# GET MAHASISWA
# =========================
def get_mahasiswa_by_context(context, fields=None, prodi_filter=None):

    if fields is None:
        fields = ["nama", "nim"]

    allowed_fields = {
        "id": "id",
        "user_id": "user_id",
        "nama": "nama",
        "nim": "nim",
        "angkatan": "angkatan",
        "email": "email",
        "prodi": "prodi_name"
    }

    if "*" in fields:
        select_cols = "id, user_id, nama, nim, angkatan, email, prodi_id"
    else:
        cols = [allowed_fields[f] for f in fields if f in allowed_fields]
        # Always include user_id and id for internal use
        if "user_id" not in cols:
            cols.insert(0, "user_id")
        if "id" not in cols:
            cols.insert(0, "id")
        select_cols = ", ".join(cols)

    # Handle both dict and object context
    angkatan_id = _get_context_value(context, "angkatan")
    prodi_id = _get_context_value(context, "prodi_id")
    
    # Resolve angkatan ID to year
    # angkatan_id is ID (1, 2, 3...), but database stores tahun (2019, 2020...)
    angkatan_year = get_tahun_angkatan(angkatan_id) if isinstance(angkatan_id, int) and angkatan_id < 100 else angkatan_id
    log(f"get_mahasiswa_by_context: Using angkatan_year={angkatan_year} (from angkatan_id={angkatan_id})")

    query = f"""
        SELECT {select_cols}
        FROM mahasiswa
        WHERE angkatan = :angkatan_year
    """

    params = {
        "angkatan_year": angkatan_year
    }

    if prodi_filter:
        query += "\nAND prodi_id = :prodi_id"
        params["prodi_id"] = prodi_filter
    elif prodi_id:
        query += "\nAND prodi_id = :prodi_id"
        params["prodi_id"] = prodi_id

    log(f"get_mahasiswa_by_context: Query:\n{query}")
    log(f"get_mahasiswa_by_context: Params: {params}")

    with engine.connect() as conn:
        result = conn.execute(text(query), params).fetchall()

    data = [dict(row._mapping) for row in result]
    log(f"get_mahasiswa: {len(data)} mahasiswa")
    if len(data) == 0:
        log(f"WARNING: get_mahasiswa returned 0 results. Filters: angkatan={angkatan_year}, prodi_id={prodi_id}")
    else:
        log(f"First mahasiswa: {data[0] if data else 'N/A'}")
    return data


def count_mahasiswa_by_context(context, prodi_filter=None):
    # Handle both dict and object context
    angkatan_id = _get_context_value(context, "angkatan")
    prodi_id = _get_context_value(context, "prodi_id")
    
    # Resolve angkatan ID to year
    angkatan_year = get_tahun_angkatan(angkatan_id) if isinstance(angkatan_id, int) and angkatan_id < 100 else angkatan_id
    
    if not angkatan_year:
        log("ERROR: Tahun tidak ditemukan")
        return 0

    query = """
        SELECT COUNT(*) AS total
        FROM mahasiswa
        WHERE angkatan = :angkatan
    """

    params = {"angkatan": angkatan_year}

    if prodi_filter:
        query += "\nAND prodi_name = :prodi_name"
        params["prodi_name"] = prodi_filter
    elif prodi_id:
        query += "\nAND prodi_id = :prodi_id"
        params["prodi_id"] = prodi_id

    with engine.connect() as conn:
        result = conn.execute(text(query), params).fetchone()

    total = result[0] if result else 0
    log(f"count_mahasiswa: {total}")
    return total


# =========================
# GET DOSEN BY USER_ID
# =========================
def get_dosen_by_user_id(user_id):
    """Get dosen information by user_id"""
    
    query = """
        SELECT nama, email, prodi, jabatan_akademik, user_id
        FROM dosen
        WHERE user_id = :user_id
    """

    with engine.connect() as conn:
        result = conn.execute(text(query), {"user_id": user_id}).fetchone()

    dosen = dict(result._mapping) if result else None
    
    if dosen:
        log(f"[INFO] Dosen ditemukan | user_id={user_id} | nama={dosen.get('nama')}")
    else:
        log(f"[WARNING] Dosen tidak ditemukan | user_id={user_id}")
    
    return dosen


# =========================
# GET DOSEN BY PRODI_ID
# =========================
def get_dosen_by_prodi_id(prodi_id):
    """Get list of dosen (lecturers) by prodi_id (program/department)
    
    Returns: List of dosen with their information
    """
    
    query = """
        SELECT 
            id, 
            nama, 
            email, 
            prodi, 
            prodi_id,
            jabatan_akademik, 
            jabatan_akademik_desc,
            nip, 
            nidn,
            jenjang_pendidikan,
            user_id
        FROM dosen
        WHERE prodi_id = :prodi_id
        ORDER BY nama ASC
    """

    with engine.connect() as conn:
        result = conn.execute(text(query), {"prodi_id": prodi_id}).fetchall()

    dosen_list = [dict(row._mapping) for row in result]
    
    if dosen_list:
        log(f"[INFO] get_dosen_by_prodi_id | prodi_id={prodi_id} | Total dosen: {len(dosen_list)}")
        for dosen in dosen_list:
            log(f"  - {dosen.get('nama')} ({dosen.get('jabatan_akademik', 'N/A')})")
    else:
        log(f"[WARNING] get_dosen_by_prodi_id | prodi_id={prodi_id} | No dosen found")
    
    return dosen_list


# =========================
# GET EXISTING GROUPS BY USER / COORDINATOR
# =========================
def get_existing_groups_by_user(user_id, context):
    """
    Get existing groups for a user or coordinator
    - If user is Koordinator: Get all groups from their kategori_pa matching context filters
    - If user is Mahasiswa: Get the specific group the student belongs to
    
    Returns group(s) data
    """
    
    # Helper function to extract value from context (either dict or object)
    def get_context_value(ctx, key):
        if isinstance(ctx, dict):
            return ctx.get(key)
        else:
            return getattr(ctx, key, None)
    
    # Extract context values
    role = get_context_value(context, 'role')
    kategori_pa = get_context_value(context, 'kategori_pa')
    prodi_id = get_context_value(context, 'prodi_id')
    angkatan = get_context_value(context, 'angkatan')
    
    log(f"get_existing_groups_by_user: user_id={user_id}, role={role}, kategori_pa={kategori_pa}, prodi_id={prodi_id}, angkatan={angkatan}")
    
    # CASE 1: User is Koordinator - Get all groups from kategori_pa with filters
    if role == "Koordinator" and kategori_pa:
        # angkatan is already the tahun_masuk.id from context
        # (no need to resolve it further)
        tm_id = angkatan if isinstance(angkatan, int) else None
        
        # Get current tahun_ajaran_id (assuming latest/current)
        tahun_ajaran_query = """
            SELECT id FROM tahun_ajaran ORDER BY id DESC LIMIT 1
        """
        with engine.connect() as conn:
            ta_result = conn.execute(text(tahun_ajaran_query)).fetchone()
        
        tahun_ajaran_id = ta_result[0] if ta_result else None
        
        log(f"Resolved: TM_id={tm_id}, tahun_ajaran_id={tahun_ajaran_id}")
        
        # Build query with filters
        query = """
            SELECT k.id, k.nomor_kelompok, k.status, COUNT(km.user_id) as member_count
            FROM kelompok k
            LEFT JOIN kelompok_mahasiswa km ON k.id = km.kelompok_id
            WHERE k.KPA_id = :kategori_pa
        """
        
        params = {"kategori_pa": kategori_pa}
        
        if prodi_id:
            query += " AND k.prodi_id = :prodi_id"
            params["prodi_id"] = prodi_id
        
        if tm_id:
            query += " AND k.TM_id = :tm_id"
            params["tm_id"] = tm_id
        
        if tahun_ajaran_id:
            query += " AND k.tahun_ajaran_id = :tahun_ajaran_id"
            params["tahun_ajaran_id"] = tahun_ajaran_id
        
        query += " GROUP BY k.id ORDER BY k.nomor_kelompok"
        
        log(f"Query: {query}")
        log(f"Params: {params}")
        
        with engine.connect() as conn:
            groups_result = conn.execute(text(query), params).fetchall()
        
        if not groups_result:
            log(f"get_existing_groups_by_user: No groups found for Koordinator with filters")
            return None
        
        groups_data = []
        for row in groups_result:
            group_dict = dict(row._mapping)
            kelompok_id = group_dict['id']
            
            # Get members of this group
            members_query = """
                SELECT m.user_id, m.nama, m.nim
                FROM kelompok_mahasiswa km
                JOIN mahasiswa m ON km.user_id = m.user_id
                WHERE km.kelompok_id = :kelompok_id
                ORDER BY m.nama
            """
            
            with engine.connect() as conn:
                members_result = conn.execute(text(members_query), {"kelompok_id": kelompok_id}).fetchall()
            
            members = [dict(m._mapping) for m in members_result]
            
            groups_data.append({
                "kelompok_id": kelompok_id,
                "nomor_kelompok": group_dict.get("nomor_kelompok"),
                "status": group_dict.get("status"),
                "members": members,
                "member_count": group_dict.get("member_count", 0)
            })
        
        log(f"get_existing_groups_by_user: Found {len(groups_data)} groups for Koordinator")
        return groups_data
    
    # CASE 2: User is Mahasiswa - Get their specific group
    query = """
        SELECT km.kelompok_id
        FROM kelompok_mahasiswa km
        WHERE km.user_id = :user_id
        LIMIT 1
    """
    
    with engine.connect() as conn:
        result = conn.execute(text(query), {"user_id": user_id}).fetchone()
    
    if not result:
        log(f"get_existing_groups_by_user: No group found for user_id={user_id}")
        return None
    
    kelompok_id = result[0]
    
    # Get all members of this group
    query = """
        SELECT m.user_id, m.nama, m.nim
        FROM kelompok_mahasiswa km
        JOIN mahasiswa m ON km.user_id = m.user_id
        WHERE km.kelompok_id = :kelompok_id
        ORDER BY m.nama
    """
    
    with engine.connect() as conn:
        members_result = conn.execute(text(query), {"kelompok_id": kelompok_id}).fetchall()
    
    members = [dict(row._mapping) for row in members_result]
    
    # Get group info
    query = """
        SELECT id, nomor_kelompok, status
        FROM kelompok
        WHERE id = :kelompok_id
    """
    
    with engine.connect() as conn:
        group_result = conn.execute(text(query), {"kelompok_id": kelompok_id}).fetchone()
    
    if not group_result:
        log(f"get_existing_groups_by_user: Group not found | kelompok_id={kelompok_id}")
        return None
    
    group_info = dict(group_result._mapping)
    
    result_data = {
        "kelompok_id": kelompok_id,
        "nomor_kelompok": group_info.get("nomor_kelompok"),
        "status": group_info.get("status"),
        "members": members,
        "member_count": len(members)
    }
    
    log(f"get_existing_groups_by_user: Found group | kelompok_id={kelompok_id} | members={len(members)}")
    return result_data


def get_group_by_nomor_kelompok(nomor_kelompok, context):
    """
    Get a specific group by its nomor_kelompok (group number) with context filters.
    Filters by kategori_pa, prodi_id, tahun_ajaran_id to get the correct group.
    
    Returns group data with all members
    """
    
    # Helper function to extract value from context (either dict or object)
    def get_context_value(ctx, key):
        if isinstance(ctx, dict):
            return ctx.get(key)
        else:
            return getattr(ctx, key, None)
    
    # Extract context values
    kategori_pa = get_context_value(context, 'kategori_pa')
    prodi_id = get_context_value(context, 'prodi_id')
    angkatan = get_context_value(context, 'angkatan')
    
    log(f"get_group_by_nomor_kelompok: nomor_kelompok={nomor_kelompok}, kategori_pa={kategori_pa}, prodi_id={prodi_id}, angkatan={angkatan}")
    
    # First, get TM_id (tahun_masuk id) from angkatan value if provided
    tm_id = None
    if angkatan:
        tm_id_query = """
            SELECT id FROM tahun_masuk WHERE Tahun_Masuk = :tahun
        """
        with engine.connect() as conn:
            tm_result = conn.execute(text(tm_id_query), {"tahun": angkatan}).fetchone()
        
        tm_id = tm_result[0] if tm_result else None
    
    # Get current tahun_ajaran_id (assuming latest/current)
    tahun_ajaran_query = """
        SELECT id FROM tahun_ajaran ORDER BY id DESC LIMIT 1
    """
    with engine.connect() as conn:
        ta_result = conn.execute(text(tahun_ajaran_query)).fetchone()
    
    tahun_ajaran_id = ta_result[0] if ta_result else None
    
    log(f"Resolved: TM_id={tm_id}, tahun_ajaran_id={tahun_ajaran_id}")
    
    # Format nomor_kelompok - database stores as "Kelompok 1", "Kelompok 2", etc.
    nomor_kelompok_formatted = f"Kelompok {nomor_kelompok}"
    
    # Build query with filters
    query = """
        SELECT id, nomor_kelompok, status
        FROM kelompok
        WHERE nomor_kelompok = :nomor_kelompok
    """
    
    params = {"nomor_kelompok": nomor_kelompok_formatted}
    
    if kategori_pa:
        query += " AND KPA_id = :kategori_pa"
        params["kategori_pa"] = kategori_pa
    
    if prodi_id:
        query += " AND prodi_id = :prodi_id"
        params["prodi_id"] = prodi_id
    
    if tm_id:
        query += " AND TM_id = :tm_id"
        params["tm_id"] = tm_id
    
    if tahun_ajaran_id:
        query += " AND tahun_ajaran_id = :tahun_ajaran_id"
        params["tahun_ajaran_id"] = tahun_ajaran_id
    
    query += " LIMIT 1"
    
    log(f"Query: {query}")
    log(f"Params: {params}")
    
    with engine.connect() as conn:
        group_result = conn.execute(text(query), params).fetchone()
    
    if not group_result:
        log(f"get_group_by_nomor_kelompok: Group not found | nomor_kelompok={nomor_kelompok}")
        return None
    
    group_info = dict(group_result._mapping)
    kelompok_id = group_info.get("id")
    
    # Get all members of this group
    members_query = """
        SELECT m.user_id, m.nama, m.nim
        FROM kelompok_mahasiswa km
        JOIN mahasiswa m ON km.user_id = m.user_id
        WHERE km.kelompok_id = :kelompok_id
        ORDER BY m.nama
    """
    
    with engine.connect() as conn:
        members_result = conn.execute(text(members_query), {"kelompok_id": kelompok_id}).fetchall()
    
    members = [dict(row._mapping) for row in members_result]
    
    result_data = {
        "kelompok_id": kelompok_id,
        "nomor_kelompok": group_info.get("nomor_kelompok"),
        "status": group_info.get("status"),
        "members": members,
        "member_count": len(members)
    }
    
    log(f"get_group_by_nomor_kelompok: Found group | nomor_kelompok={nomor_kelompok} | members={len(members)}")
    return result_data


# =========================
# SAVE GROUPS TO DATABASE
# =========================
def save_kelompok(nomor_kelompok, kpa_id, prodi_id, tm_id, tahun_ajaran_id, status="Aktif"):
    """
    Save a kelompok (group) to database
    
    Returns: kelompok_id (inserted id) or None if failed
    """
    try:
        query = """
            INSERT INTO kelompok (nomor_kelompok, KPA_id, prodi_id, TM_id, tahun_ajaran_id, status)
            VALUES (:nomor_kelompok, :kpa_id, :prodi_id, :tm_id, :tahun_ajaran_id, :status)
        """
        
        params = {
            "nomor_kelompok": nomor_kelompok,
            "kpa_id": kpa_id,
            "prodi_id": prodi_id,
            "tm_id": tm_id,
            "tahun_ajaran_id": tahun_ajaran_id,
            "status": status
        }
        
        with engine.connect() as conn:
            result = conn.execute(text(query), params)
            conn.commit()
            
            # Get the inserted row's ID
            last_id_query = "SELECT LAST_INSERT_ID() as id"
            last_id_result = conn.execute(text(last_id_query)).fetchone()
            kelompok_id = last_id_result[0] if last_id_result else None
        
        log(f"save_kelompok: Kelompok saved | nomor={nomor_kelompok} | kelompok_id={kelompok_id}")
        return kelompok_id
        
    except Exception as e:
        log(f"ERROR: save_kelompok failed | Error: {str(e)}")
        return None


def save_kelompok_mahasiswa(kelompok_id, user_id):
    """
    Save a kelompok_mahasiswa record (link student to group)
    
    Returns: True if successful, False otherwise
    """
    try:
        query = """
            INSERT INTO kelompok_mahasiswa (kelompok_id, user_id)
            VALUES (:kelompok_id, :user_id)
        """
        
        params = {
            "kelompok_id": kelompok_id,
            "user_id": user_id
        }
        
        with engine.connect() as conn:
            conn.execute(text(query), params)
            conn.commit()
        
        log(f"save_kelompok_mahasiswa: Member saved | kelompok_id={kelompok_id} | user_id={user_id}")
        return True
        
    except Exception as e:
        log(f"ERROR: save_kelompok_mahasiswa failed | Error: {str(e)}")
        return False


def check_existing_groups_by_context(kpa_id, prodi_id, tm_id, tahun_ajaran_id):
    """
    Check apakah sudah ada kelompok untuk konteks ini
    Returns: jumlah kelompok yang ada, atau 0 jika tidak ada
    """
    try:
        query = """
            SELECT COUNT(*) as total
            FROM kelompok
            WHERE KPA_id = :kpa_id
            AND prodi_id = :prodi_id
            AND TM_id = :tm_id
            AND tahun_ajaran_id = :tahun_ajaran_id
        """
        
        params = {
            "kpa_id": kpa_id,
            "prodi_id": prodi_id,
            "tm_id": tm_id,
            "tahun_ajaran_id": tahun_ajaran_id
        }
        
        with engine.connect() as conn:
            result = conn.execute(text(query), params).fetchone()
        
        total = result[0] if result else 0
        log(f"check_existing_groups_by_context: Found {total} existing groups")
        return total
        
    except Exception as e:
        log(f"ERROR: check_existing_groups_by_context failed | Error: {str(e)}")
        return 0


def delete_existing_groups_by_context(kpa_id, prodi_id, tm_id, tahun_ajaran_id):
    """
    Delete semua kelompok dan anggotanya untuk konteks ini
    Returns: jumlah kelompok yang dihapus
    """
    try:
        # First get all kelompok IDs to delete members
        get_ids_query = """
            SELECT id FROM kelompok
            WHERE KPA_id = :kpa_id
            AND prodi_id = :prodi_id
            AND TM_id = :tm_id
            AND tahun_ajaran_id = :tahun_ajaran_id
        """
        
        params = {
            "kpa_id": kpa_id,
            "prodi_id": prodi_id,
            "tm_id": tm_id,
            "tahun_ajaran_id": tahun_ajaran_id
        }
        
        with engine.connect() as conn:
            # Get kelompok IDs
            kelompok_ids = conn.execute(text(get_ids_query), params).fetchall()
            kelompok_id_list = [row[0] for row in kelompok_ids]
            
            if kelompok_id_list:
                # Delete kelompok_mahasiswa records
                for kelompok_id in kelompok_id_list:
                    delete_members_query = """
                        DELETE FROM kelompok_mahasiswa
                        WHERE kelompok_id = :kelompok_id
                    """
                    conn.execute(text(delete_members_query), {"kelompok_id": kelompok_id})
                
                # Delete kelompok records
                delete_kelompok_query = """
                    DELETE FROM kelompok
                    WHERE KPA_id = :kpa_id
                    AND prodi_id = :prodi_id
                    AND TM_id = :tm_id
                    AND tahun_ajaran_id = :tahun_ajaran_id
                """
                result = conn.execute(text(delete_kelompok_query), params)
                conn.commit()
                
                log(f"delete_existing_groups_by_context: Deleted {len(kelompok_id_list)} groups and their members")
                return len(kelompok_id_list)
        
        return 0
        
    except Exception as e:
        log(f"ERROR: delete_existing_groups_by_context failed | Error: {str(e)}")
        return 0


def save_groups_from_result(result_data, kpa_id, prodi_id, tm_id, tahun_ajaran_id, replace_existing=False):
    """
    Save groups from result data (list of groups with members) to database
    
    Expected result_data format: [
        {
            'kelompok': 'Kelompok 1',
            'members': [
                {'user_id': 123, 'nama': 'John', 'nim': '001'},
                {'user_id': 456, 'nama': 'Jane', 'nim': '002'},
                ...
            ]
        },
        ...
    ]
    
    Args:
        replace_existing: Jika True, delete kelompok yang sudah ada terlebih dahulu
    
    Returns: {
        'success': bool,
        'saved_groups': int,
        'saved_members': int,
        'errors': list,
        'existing_groups': int  # Jumlah kelompok yang ada sebelumnya
    }
    """
    try:
        if not result_data or not isinstance(result_data, list):
            log(f"ERROR: save_groups_from_result - Invalid result_data format")
            return {
                'success': False,
                'saved_groups': 0,
                'saved_members': 0,
                'errors': ['Invalid result_data format'],
                'existing_groups': 0
            }
        
        # Check existing groups
        existing_groups = check_existing_groups_by_context(kpa_id, prodi_id, tm_id, tahun_ajaran_id)
        
        # Delete existing groups if replace_existing=True
        if existing_groups > 0 and replace_existing:
            deleted = delete_existing_groups_by_context(kpa_id, prodi_id, tm_id, tahun_ajaran_id)
            log(f"save_groups_from_result: Deleted {deleted} existing groups before saving new ones")
        
        saved_groups = 0
        saved_members = 0
        errors = []
        
        for idx, group_data in enumerate(result_data):
            try:
                # Get group name
                nomor_kelompok = group_data.get('kelompok')
                if not nomor_kelompok:
                    errors.append(f"Group {idx+1}: Missing 'kelompok' name")
                    continue
                
                # Format nomor_kelompok as string if it's a number
                if isinstance(nomor_kelompok, int):
                    nomor_kelompok = f"Kelompok {nomor_kelompok}"
                
                # Save kelompok record
                kelompok_id = save_kelompok(
                    nomor_kelompok=nomor_kelompok,
                    kpa_id=kpa_id,
                    prodi_id=prodi_id,
                    tm_id=tm_id,
                    tahun_ajaran_id=tahun_ajaran_id,
                    status="Aktif"
                )
                
                if not kelompok_id:
                    errors.append(f"Group '{nomor_kelompok}': Failed to save kelompok record")
                    continue
                
                saved_groups += 1
                
                # Get members
                members = group_data.get('members', [])
                if not members:
                    log(f"WARNING: Group '{nomor_kelompok}' has no members")
                    continue
                
                log(f"[SAVE_DEBUG] Group '{nomor_kelompok}' has {len(members)} members, saving...")
                log(f"[SAVE_DEBUG] First member: {members[0] if members else 'N/A'}")
                
                # Save each member
                for member_idx, member in enumerate(members):
                    user_id = member.get('user_id') if isinstance(member, dict) else None
                    
                    if not user_id:
                        log(f"[SAVE_DEBUG] Group '{nomor_kelompok}' Member {member_idx}: Missing user_id | Type: {type(member)} | Content: {member}")
                        errors.append(f"Group '{nomor_kelompok}' Member {member_idx}: Missing user_id")
                        continue
                    
                    success = save_kelompok_mahasiswa(kelompok_id, user_id)
                    if success:
                        saved_members += 1
                    else:
                        errors.append(f"Group '{nomor_kelompok}': Failed to save member user_id={user_id}")
                
            except Exception as e:
                errors.append(f"Group {idx+1}: {str(e)}")
                continue
        
        log(f"save_groups_from_result: Completed | Groups saved: {saved_groups} | Members saved: {saved_members}")
        
        return {
            'success': len(errors) == 0,
            'saved_groups': saved_groups,
            'saved_members': saved_members,
            'errors': errors,
            'existing_groups': existing_groups if existing_groups > 0 else 0
        }
        
    except Exception as e:
        log(f"ERROR: save_groups_from_result - {str(e)}")
        return {
            'success': False,
            'saved_groups': 0,
            'saved_members': 0,
            'errors': [str(e)],
            'existing_groups': 0
        }


# =========================
# PEMBIMBING (LECTURER ASSIGNMENT) FUNCTIONS
# =========================

def get_distinct_jabatan():
    """Get list of distinct academic ranks/jabatan from dosen table"""
    try:
        query = """
            SELECT DISTINCT jabatan_akademik, jabatan_akademik_desc
            FROM dosen
            WHERE jabatan_akademik IS NOT NULL
            ORDER BY jabatan_akademik ASC
        """
        
        with engine.connect() as conn:
            result = conn.execute(text(query)).fetchall()
        
        jabatan_list = [dict(row._mapping) for row in result]
        log(f"[INFO] get_distinct_jabatan | Found {len(jabatan_list)} distinct ranks")
        return jabatan_list
        
    except Exception as e:
        log(f"ERROR: get_distinct_jabatan failed | Error: {str(e)}")
        return []


def get_groups_with_pembimbing_by_context(prodi_id, kpa_id, tm_id):
    """Get all groups with their current pembimbing (lecturers) for given context
    
    Returns: List of groups with pembimbing info
    Format: [
        {
            'kelompok_id': 1,
            'nomor_kelompok': 'Kelompok 1',
            'pembimbing_count': 2,
            'pembimbing': [
                {'user_id': 123, 'nama': 'Dr. A', 'jabatan_akademik_desc': 'Dr'},
                {'user_id': 456, 'nama': 'Dr. B', 'jabatan_akademik_desc': 'Dr'}
            ]
        },
        ...
    ]
    """
    try:
        query = """
            SELECT 
                k.id as kelompok_id,
                k.nomor_kelompok,
                d.user_id,
                d.nama,
                d.jabatan_akademik,
                d.jabatan_akademik_desc,
                d.email
            FROM kelompok k
            LEFT JOIN pembimbing p ON k.id = p.kelompok_id
            LEFT JOIN dosen d ON p.user_id = d.user_id
            WHERE k.prodi_id = :prodi_id
            AND k.KPA_id = :kpa_id
            AND k.TM_id = :tm_id
            ORDER BY k.nomor_kelompok ASC, p.id ASC
        """
        
        params = {
            "prodi_id": prodi_id,
            "kpa_id": kpa_id,
            "tm_id": tm_id
        }
        
        with engine.connect() as conn:
            results = conn.execute(text(query), params).fetchall()
        
        # Group by kelompok_id
        groups_dict = {}
        for row in results:
            row_dict = dict(row._mapping)
            kelompok_id = row_dict['kelompok_id']
            
            if kelompok_id not in groups_dict:
                groups_dict[kelompok_id] = {
                    'kelompok_id': kelompok_id,
                    'nomor_kelompok': row_dict['nomor_kelompok'],
                    'pembimbing_count': 0,
                    'pembimbing': []
                }
            
            # Add pembimbing if user_id exists
            if row_dict['user_id']:
                groups_dict[kelompok_id]['pembimbing'].append({
                    'user_id': row_dict['user_id'],
                    'nama': row_dict['nama'],
                    'jabatan_akademik': row_dict['jabatan_akademik'],
                    'jabatan_akademik_desc': row_dict['jabatan_akademik_desc'],
                    'email': row_dict['email']
                })
                groups_dict[kelompok_id]['pembimbing_count'] += 1
        
        groups_list = list(groups_dict.values())
        log(f"[INFO] get_groups_with_pembimbing_by_context | Found {len(groups_list)} groups")
        return groups_list
        
    except Exception as e:
        log(f"ERROR: get_groups_with_pembimbing_by_context failed | Error: {str(e)}")
        return []


def get_pembimbing_by_group_id(kelompok_id):
    """Get pembimbing (lecturers) for a specific group
    
    Returns: List of pembimbing for the group
    """
    try:
        query = """
            SELECT 
                p.id as pembimbing_id,
                p.user_id,
                d.nama,
                d.jabatan_akademik,
                d.jabatan_akademik_desc,
                d.email,
                d.nip,
                d.nidn
            FROM pembimbing p
            JOIN dosen d ON p.user_id = d.user_id
            WHERE p.kelompok_id = :kelompok_id
            ORDER BY p.id ASC
        """
        
        with engine.connect() as conn:
            results = conn.execute(text(query), {"kelompok_id": kelompok_id}).fetchall()
        
        pembimbing_list = [dict(row._mapping) for row in results]
        log(f"[INFO] get_pembimbing_by_group_id | kelompok_id={kelompok_id} | Found {len(pembimbing_list)} pembimbing")
        return pembimbing_list
        
    except Exception as e:
        log(f"ERROR: get_pembimbing_by_group_id failed | Error: {str(e)}")
        return []


def get_dosen_for_assignment(prodi_id, kpa_id, tm_id):
    """Get available dosen (lecturers) for assignment, filtered by context
    Also returns current group count for each dosen
    
    Returns: List of dosen with their info and current group count
    """
    try:
        query = """
            SELECT 
                d.id,
                d.user_id,
                d.nama,
                d.jabatan_akademik,
                d.jabatan_akademik_desc,
                d.email,
                d.nip,
                COUNT(DISTINCT p.kelompok_id) as current_group_count
            FROM dosen d
            LEFT JOIN pembimbing p ON d.user_id = p.user_id
            WHERE d.prodi_id = :prodi_id
            GROUP BY d.id, d.user_id, d.nama, d.jabatan_akademik, d.jabatan_akademik_desc, d.email, d.nip
            ORDER BY d.nama ASC
        """
        
        params = {
            "prodi_id": prodi_id,
            "kpa_id": kpa_id,
            "tm_id": tm_id
        }
        
        with engine.connect() as conn:
            results = conn.execute(text(query), params).fetchall()
        
        dosen_list = [dict(row._mapping) for row in results]
        log(f"[INFO] get_dosen_for_assignment | prodi_id={prodi_id} | Found {len(dosen_list)} dosen")
        return dosen_list
        
    except Exception as e:
        log(f"ERROR: get_dosen_for_assignment failed | Error: {str(e)}")
        return []


def create_pembimbing(user_id, kelompok_id):
    """Create a new pembimbing (lecturer assignment) record
    
    Returns: pembimbing_id if successful, None if failed
    """
    try:
        # Check if this dosen is already assigned to this group
        check_query = """
            SELECT id FROM pembimbing
            WHERE user_id = :user_id AND kelompok_id = :kelompok_id
        """
        
        with engine.connect() as conn:
            existing = conn.execute(text(check_query), {"user_id": user_id, "kelompok_id": kelompok_id}).fetchone()
        
        if existing:
            log(f"WARNING: create_pembimbing | user_id={user_id} already assigned to kelompok_id={kelompok_id}")
            return None
        
        query = """
            INSERT INTO pembimbing (user_id, kelompok_id)
            VALUES (:user_id, :kelompok_id)
        """
        
        params = {
            "user_id": user_id,
            "kelompok_id": kelompok_id
        }
        
        with engine.connect() as conn:
            result = conn.execute(text(query), params)
            conn.commit()
            
            # Get the inserted ID
            last_id_query = "SELECT LAST_INSERT_ID() as id"
            last_id_result = conn.execute(text(last_id_query)).fetchone()
            pembimbing_id = last_id_result[0] if last_id_result else None
        
        log(f"[INFO] create_pembimbing | user_id={user_id}, kelompok_id={kelompok_id} | pembimbing_id={pembimbing_id}")
        return pembimbing_id
        
    except Exception as e:
        log(f"ERROR: create_pembimbing failed | Error: {str(e)}")
        return None


def update_pembimbing(pembimbing_id, user_id):
    """Update pembimbing assignment (change lecturer)
    
    Returns: True if successful, False if failed
    """
    try:
        query = """
            UPDATE pembimbing
            SET user_id = :user_id
            WHERE id = :pembimbing_id
        """
        
        params = {
            "user_id": user_id,
            "pembimbing_id": pembimbing_id
        }
        
        with engine.connect() as conn:
            conn.execute(text(query), params)
            conn.commit()
        
        log(f"[INFO] update_pembimbing | pembimbing_id={pembimbing_id}, new user_id={user_id}")
        return True
        
    except Exception as e:
        log(f"ERROR: update_pembimbing failed | Error: {str(e)}")
        return False


def delete_pembimbing(pembimbing_id):
    """Delete pembimbing (remove lecturer assignment) record
    
    Returns: True if successful, False if failed
    """
    try:
        query = """
            DELETE FROM pembimbing
            WHERE id = :pembimbing_id
        """
        
        with engine.connect() as conn:
            conn.execute(text(query), {"pembimbing_id": pembimbing_id})
            conn.commit()
        
        log(f"[INFO] delete_pembimbing | pembimbing_id={pembimbing_id}")
        return True
        
    except Exception as e:
        log(f"ERROR: delete_pembimbing failed | Error: {str(e)}")
        return False


def delete_all_pembimbing_by_kelompok(kelompok_id):
    """Delete all pembimbing records for a group
    
    Returns: Number of pembimbing deleted
    """
    try:
        query = """
            DELETE FROM pembimbing
            WHERE kelompok_id = :kelompok_id
        """
        
        with engine.connect() as conn:
            result = conn.execute(text(query), {"kelompok_id": kelompok_id})
            conn.commit()
            deleted_count = result.rowcount
        
        log(f"[INFO] delete_all_pembimbing_by_kelompok | kelompok_id={kelompok_id} | Deleted {deleted_count} pembimbing")
        return deleted_count
        
    except Exception as e:
        log(f"ERROR: delete_all_pembimbing_by_kelompok failed | Error: {str(e)}")
        return 0