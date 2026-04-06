"""
Kelompok Status Tool - Group existence and status checking

Handles queries like:
- Are there any groups?
- How many groups exist?
- Show existing groups
- etc.
"""

from tools.db_tool import engine
from sqlalchemy import text
from typing import List, Dict, Optional


def check_any_groups_exist(prodi_id: Optional[int] = None, context: Optional[dict] = None) -> bool:
    """
    Check if any groups exist
    
    Args:
        prodi_id: Filter by program
        context: Dosen context
        
    Returns:
        bool: True if groups exist
    """
    if not prodi_id and context:
        prodi_id = context.get("prodi_id") if isinstance(context, dict) else getattr(context, "prodi_id", None)
    
    query = "SELECT COUNT(*) as count FROM kelompok"
    params = {}
    
    if prodi_id:
        query += " WHERE prodi_id = :prodi_id"
        params["prodi_id"] = prodi_id
    
    try:
        with engine.connect() as conn:
            result = conn.execute(text(query), params).fetchone()
            count = result[0] if result else 0
            return count > 0
    except Exception as e:
        print(f"[KELOMPOK_STATUS] Error checking groups: {e}")
        return False


def count_groups(prodi_id: Optional[int] = None, context: Optional[dict] = None) -> int:
    """
    Count total groups
    
    Returns:
        Number of groups
    """
    if not prodi_id and context:
        prodi_id = context.get("prodi_id") if isinstance(context, dict) else getattr(context, "prodi_id", None)
    
    query = "SELECT COUNT(*) as count FROM kelompok"
    params = {}
    
    if prodi_id:
        query += " WHERE prodi_id = :prodi_id"
        params["prodi_id"] = prodi_id
    
    try:
        with engine.connect() as conn:
            result = conn.execute(text(query), params).fetchone()
            return result[0] if result else 0
    except Exception as e:
        print(f"[KELOMPOK_STATUS] Error counting groups: {e}")
        return 0


def get_groups_summary(prodi_id: Optional[int] = None, context: Optional[dict] = None) -> Dict:
    """
    Get summary of existing groups
    
    Returns:
        Dict with group count, total members, etc.
    """
    if not prodi_id and context:
        prodi_id = context.get("prodi_id") if isinstance(context, dict) else getattr(context, "prodi_id", None)
    
    query = """
        SELECT 
            COUNT(DISTINCT k.id) as total_groups,
            COUNT(km.user_id) as total_members,
            AVG(member_counts.count) as avg_members,
            MIN(member_counts.count) as min_members,
            MAX(member_counts.count) as max_members
        FROM kelompok k
        LEFT JOIN kelompok_mahasiswa km ON k.id = km.kelompok_id
        LEFT JOIN (
            SELECT kelompok_id, COUNT(*) as count
            FROM kelompok_mahasiswa
            GROUP BY kelompok_id
        ) member_counts ON k.id = member_counts.kelompok_id
    """
    params = {}
    
    if prodi_id:
        query += " WHERE k.prodi_id = :prodi_id"
        params["prodi_id"] = prodi_id
    
    try:
        with engine.connect() as conn:
            result = conn.execute(text(query), params).fetchone()
            if result:
                row = dict(result._mapping)
                return {
                    "total_groups": row["total_groups"] or 0,
                    "total_members": row["total_members"] or 0,
                    "avg_members_per_group": round(row["avg_members"], 1) if row["avg_members"] else 0,
                    "min_members": row["min_members"] if row["min_members"] else 0,
                    "max_members": row["max_members"] if row["max_members"] else 0,
                }
            return None
    except Exception as e:
        print(f"[KELOMPOK_STATUS] Error getting groups summary: {e}")
        return None


def list_all_groups(prodi_id: Optional[int] = None, context: Optional[dict] = None) -> List[Dict]:
    """
    List all existing groups with details
    
    Returns:
        List of groups
    """
    if not prodi_id and context:
        prodi_id = context.get("prodi_id") if isinstance(context, dict) else getattr(context, "prodi_id", None)
    
    query = """
        SELECT k.id, k.nomor_kelompok, 
               COUNT(km.user_id) as member_count,
               p.id as prodi_id_ref, p.nama_prodi
        FROM kelompok k
        LEFT JOIN kelompok_mahasiswa km ON k.id = km.kelompok_id
        LEFT JOIN prodi p ON k.prodi_id = p.id
    """
    params = {}
    
    if prodi_id:
        query += " WHERE k.prodi_id = :prodi_id"
        params["prodi_id"] = prodi_id
    
    query += " GROUP BY k.id, k.nomor_kelompok, p.id, p.nama_prodi"
    query += " ORDER BY k.nomor_kelompok"
    
    try:
        with engine.connect() as conn:
            results = conn.execute(text(query), params).fetchall()
            return [dict(row._mapping) for row in results]
    except Exception as e:
        print(f"[KELOMPOK_STATUS] Error listing groups: {e}")
        return []


def get_group_status_with_members_and_pembimbing(
    prodi_id: Optional[int] = None, 
    context: Optional[dict] = None
) -> List[Dict]:
    """
    Get detailed status of all groups including members and pembimbing count
    
    Returns:
        List of groups with full details
    """
    if not prodi_id and context:
        prodi_id = context.get("prodi_id") if isinstance(context, dict) else getattr(context, "prodi_id", None)
    
    query = """
        SELECT 
            k.id, k.nomor_kelompok, k.member_count,
            COUNT(DISTINCT pb.id) as pembimbing_count,
            k.created_at,
            p.nama_prodi
        FROM kelompok k
        LEFT JOIN prodi p ON k.prodi_id = p.id
        LEFT JOIN pembimbing pb ON k.id = pb.kelompok_id
    """
    params = {}
    
    if prodi_id:
        query += " WHERE k.prodi_id = :prodi_id"
        params["prodi_id"] = prodi_id
    
    query += " GROUP BY k.id ORDER BY k.nomor_kelompok"
    
    try:
        with engine.connect() as conn:
            results = conn.execute(text(query), params).fetchall()
            return [dict(row._mapping) for row in results]
    except Exception as e:
        print(f"[KELOMPOK_STATUS] Error getting group status: {e}")
        return []
