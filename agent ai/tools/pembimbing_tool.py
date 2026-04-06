"""
Pembimbing Tool - Lecturer Assignment Queries

Handles all queries related to pembimbing (lecturer assignments to groups):
- Check if any pembimbing exist
- Find groups without pembimbing
- Get pembimbing for specific group
- Find groups with N pembimbing
- List all pembimbing
- etc.
"""

from tools.db_tool import engine
from sqlalchemy import text
from typing import List, Dict, Optional, Any


def check_any_pembimbing_exist(prodi_id: Optional[int] = None, context: Optional[dict] = None) -> bool:
    """
    Check apakah ada pembimbing yang sudah di-assign
    
    Args:
        prodi_id: Filter by program
        context: Dosen context (to get prodi_id if not provided)
        
    Returns:
        bool: True jika ada pembimbing
    """
    if not prodi_id and context:
        prodi_id = context.get("prodi_id") if isinstance(context, dict) else getattr(context, "prodi_id", None)
    
    query = "SELECT COUNT(*) as count FROM pembimbing"
    params = {}
    
    if prodi_id:
        query += " WHERE kelompok_id IN (SELECT id FROM kelompok WHERE prodi_id = :prodi_id)"
        params["prodi_id"] = prodi_id
    
    try:
        with engine.connect() as conn:
            result = conn.execute(text(query), params).fetchone()
            count = result[0] if result else 0
            return count > 0
    except Exception as e:
        print(f"[PEMBIMBING_TOOL] Error checking pembimbing: {e}")
        return False


def get_groups_without_pembimbing(prodi_id: Optional[int] = None, context: Optional[dict] = None) -> List[Dict]:
    """
    Ambil kelompok yang belum memiliki pembimbing
    
    Returns:
        List of groups without pembimbing
    """
    if not prodi_id and context:
        prodi_id = context.get("prodi_id") if isinstance(context, dict) else getattr(context, "prodi_id", None)
    
    query = """
        SELECT k.id, k.nomor_kelompok
        FROM kelompok k
        LEFT JOIN pembimbing pb ON k.id = pb.kelompok_id
        WHERE pb.id IS NULL
    """
    params = {}
    
    if prodi_id:
        query += " AND k.prodi_id = :prodi_id"
        params["prodi_id"] = prodi_id
    
    query += " ORDER BY k.nomor_kelompok"
    
    try:
        with engine.connect() as conn:
            results = conn.execute(text(query), params).fetchall()
            return [dict(row._mapping) for row in results]
    except Exception as e:
        print(f"[PEMBIMBING_TOOL] Error getting groups without pembimbing: {e}")
        return []


def get_pembimbing_by_kelompok(kelompok_id: int) -> List[Dict]:
    """
    Ambil semua pembimbing untuk kelompok tertentu
    
    Args:
        kelompok_id: Group ID
        
    Returns:
        List of pembimbing with dosen info
    """
    query = """
        SELECT pb.*, d.nama, d.email, d.jabatan_akademik_desc, u.name
        FROM pembimbing pb
        LEFT JOIN dosen d ON pb.user_id = d.user_id
        LEFT JOIN users u ON pb.user_id = u.id
        WHERE pb.kelompok_id = :kelompok_id
        ORDER BY d.nama
    """
    
    try:
        with engine.connect() as conn:
            results = conn.execute(text(query), {"kelompok_id": kelompok_id}).fetchall()
            return [dict(row._mapping) for row in results]
    except Exception as e:
        print(f"[PEMBIMBING_TOOL] Error getting pembimbing for group: {e}")
        return []


def get_groups_by_pembimbing_count(count: int, prodi_id: Optional[int] = None, context: Optional[dict] = None) -> List[Dict]:
    """
    Ambil kelompok yang memiliki exactly N pembimbing
    
    Args:
        count: Number of pembimbing (1, 2, 3, etc.)
        prodi_id: Filter by program
        context: Dosen context
        
    Returns:
        List of groups with N pembimbing
    """
    if not prodi_id and context:
        prodi_id = context.get("prodi_id") if isinstance(context, dict) else getattr(context, "prodi_id", None)
    
    query = """
        SELECT k.*, COUNT(pb.id) as pembimbing_count
        FROM kelompok k
        LEFT JOIN pembimbing pb ON k.id = pb.kelompok_id
        WHERE 1=1
    """
    params = {}
    
    if prodi_id:
        query += " AND k.prodi_id = :prodi_id"
        params["prodi_id"] = prodi_id
    
    query += " GROUP BY k.id HAVING COUNT(pb.id) = :pembimbing_count"
    params["pembimbing_count"] = count
    
    query += " ORDER BY k.nomor_kelompok"
    
    try:
        with engine.connect() as conn:
            results = conn.execute(text(query), params).fetchall()
            return [dict(row._mapping) for row in results]
    except Exception as e:
        print(f"[PEMBIMBING_TOOL] Error getting groups by pembimbing count: {e}")
        return []


def get_pembimbing_for_kelompok_number(kelompok_number: str, context: Optional[dict] = None) -> Dict:
    """
    Ambil pembimbing untuk kelompok dengan nomor spesifik (e.g., "Kelompok 1")
    
    Args:
        kelompok_number: Group number (e.g., "1", "2", etc.)
        context: Dosen context
        
    Returns:
        Dict with pembimbing info
    """
    prodi_id = None
    if context:
        prodi_id = context.get("prodi_id") if isinstance(context, dict) else getattr(context, "prodi_id", None)
    
    query = """
        SELECT k.id, k.nomor_kelompok, COUNT(pb.id) as pembimbing_count,
               GROUP_CONCAT(DISTINCT d.nama) as pembimbing_names,
               GROUP_CONCAT(DISTINCT d.email) as pembimbing_emails
        FROM kelompok k
        LEFT JOIN pembimbing pb ON k.id = pb.kelompok_id
        LEFT JOIN dosen d ON pb.user_id = d.user_id
        WHERE k.nomor_kelompok LIKE :kelompok_number
    """
    params = {"kelompok_number": f"%{kelompok_number}%"}
    
    if prodi_id:
        query += " AND k.prodi_id = :prodi_id"
        params["prodi_id"] = prodi_id
    
    query += " GROUP BY k.id LIMIT 1"
    
    try:
        with engine.connect() as conn:
            result = conn.execute(text(query), params).fetchone()
            if result:
                row = dict(result._mapping)
                return {
                    "kelompok_id": row["id"],
                    "kelompok_nomor": row["nomor_kelompok"],
                    "pembimbing_count": row["pembimbing_count"],
                    "pembimbing_names": row["pembimbing_names"],
                    "pembimbing_emails": row["pembimbing_emails"],
                    "has_pembimbing": row["pembimbing_count"] > 0
                }
            return None
    except Exception as e:
        print(f"[PEMBIMBING_TOOL] Error getting pembimbing for group number: {e}")
        return None


def get_all_pembimbing_assignments(prodi_id: Optional[int] = None, context: Optional[dict] = None) -> List[Dict]:
    """
    Ambil semua pembimbing assignments dengan detail dosen dan kelompok
    
    Returns:
        List of all pembimbing assignments
    """
    if not prodi_id and context:
        prodi_id = context.get("prodi_id") if isinstance(context, dict) else getattr(context, "prodi_id", None)
    
    query = """
        SELECT pb.id, pb.kelompok_id, pb.user_id,
               k.nomor_kelompok, k.member_count,
               d.nama as dosen_nama, d.email, d.jabatan_akademik_desc,
               u.name as user_name
        FROM pembimbing pb
        LEFT JOIN kelompok k ON pb.kelompok_id = k.id
        LEFT JOIN dosen d ON pb.user_id = d.user_id
        LEFT JOIN users u ON pb.user_id = u.id
        WHERE 1=1
    """
    params = {}
    
    if prodi_id:
        query += " AND k.prodi_id = :prodi_id"
        params["prodi_id"] = prodi_id
    
    query += " ORDER BY k.nomor_kelompok, d.nama"
    
    try:
        with engine.connect() as conn:
            results = conn.execute(text(query), params).fetchall()
            return [dict(row._mapping) for row in results]
    except Exception as e:
        print(f"[PEMBIMBING_TOOL] Error getting all pembimbing assignments: {e}")
        return []


def get_pembimbing_groups_summary(prodi_id: Optional[int] = None, context: Optional[dict] = None) -> Dict:
    """
    Ambil summary pembimbing - berapa kelompok sudah ada pembimbing, berapa belum, etc.
    
    Returns:
        Summary dict
    """
    if not prodi_id and context:
        prodi_id = context.get("prodi_id") if isinstance(context, dict) else getattr(context, "prodi_id", None)
    
    try:
        # Total groups
        total_query = "SELECT COUNT(*) as count FROM kelompok"
        total_params = {}
        if prodi_id:
            total_query += " WHERE prodi_id = :prodi_id"
            total_params["prodi_id"] = prodi_id
        
        # Groups with pembimbing
        with_pb_query = """
            SELECT COUNT(DISTINCT k.id) as count 
            FROM kelompok k
            WHERE EXISTS (SELECT 1 FROM pembimbing pb WHERE pb.kelompok_id = k.id)
        """
        with_pb_params = {}
        if prodi_id:
            with_pb_query += " AND k.prodi_id = :prodi_id"
            with_pb_params["prodi_id"] = prodi_id
        
        # Groups without pembimbing
        without_pb_query = """
            SELECT COUNT(DISTINCT k.id) as count 
            FROM kelompok k
            WHERE NOT EXISTS (SELECT 1 FROM pembimbing pb WHERE pb.kelompok_id = k.id)
        """
        without_pb_params = {}
        if prodi_id:
            without_pb_query += " AND k.prodi_id = :prodi_id"
            without_pb_params["prodi_id"] = prodi_id
        
        with engine.connect() as conn:
            total = conn.execute(text(total_query), total_params).fetchone()[0]
            with_pb = conn.execute(text(with_pb_query), with_pb_params).fetchone()[0]
            without_pb = conn.execute(text(without_pb_query), without_pb_params).fetchone()[0]
            
            return {
                "total_groups": total,
                "groups_with_pembimbing": with_pb,
                "groups_without_pembimbing": without_pb,
                "coverage_percentage": round((with_pb / total * 100) if total > 0 else 0, 2)
            }
    except Exception as e:
        print(f"[PEMBIMBING_TOOL] Error getting pembimbing summary: {e}")
        return None
