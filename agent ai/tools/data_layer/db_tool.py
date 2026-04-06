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
        select_cols = "id, user_id, nama, nim, angkatan, email, prodi_name"
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
    tahun = get_tahun_angkatan(angkatan_id)

    if not tahun:
        log("ERROR: Tahun tidak ditemukan")
        return []

    query = f"""
        SELECT {select_cols}
        FROM mahasiswa
        WHERE angkatan = :angkatan
    """

    params = {
        "angkatan": tahun
    }

    if prodi_filter:
        query += "\nAND prodi_name = :prodi_name"
        params["prodi_name"] = prodi_filter
    elif prodi_id:
        query += "\nAND prodi_id = :prodi_id"
        params["prodi_id"] = prodi_id
        query += "\nAND prodi_id = :prodi_id"
        params["prodi_id"] = context.prodi_id

    with engine.connect() as conn:
        result = conn.execute(text(query), params).fetchall()

    data = [dict(row._mapping) for row in result]
    log(f"get_mahasiswa: {len(data)} mahasiswa")
    return data


def count_mahasiswa_by_context(context, prodi_filter=None):
    # Handle both dict and object context
    angkatan_id = _get_context_value(context, "angkatan")
    prodi_id = _get_context_value(context, "prodi_id")
    
    tahun = get_tahun_angkatan(angkatan_id)
    if not tahun:
        log("ERROR: Tahun tidak ditemukan")
        return 0

    query = """
        SELECT COUNT(*) AS total
        FROM mahasiswa
        WHERE angkatan = :angkatan
    """

    params = {"angkatan": tahun}

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