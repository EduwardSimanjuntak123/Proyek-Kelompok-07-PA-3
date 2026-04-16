"""
Utility untuk menggunakan dosen context dari payload UI
Mengubah parameter dari payload ke tools query parameters
"""

from tools.mahasiswa_tools import get_mahasiswa_by_dosen_context
from tools.kelompok_tools import get_kelompok_by_dosen_context
from tools.dosen_tools import get_dosen_by_dosen_context


def get_mahasiswa_from_context(dosen_context: dict) -> dict:
    """
    Ambil mahasiswa berdasarkan dosen context payload
    
    Args:
        dosen_context: Dict dengan prodi_id dan angkatan_id dari payload
        
    Returns:
        Result dari get_mahasiswa_by_dosen_context
    """
    prodi_id = dosen_context.get('prodi_id')
    angkatan_id = dosen_context.get('angkatan')
    
    print(f"[DEBUG] Getting mahasiswa with context: prodi_id={prodi_id}, angkatan_id={angkatan_id}")
    
    return get_mahasiswa_by_dosen_context(
        prodi_id=prodi_id,
        angkatan_id=angkatan_id
    )


def get_kelompok_from_context(dosen_context: dict) -> dict:
    """
    Ambil kelompok berdasarkan dosen context payload
    
    Args:
        dosen_context: Dict dengan prodi_id dan kategori_pa_id dari payload
        
    Returns:
        Result dari get_kelompok_by_dosen_context
    """
    prodi_id = dosen_context.get('prodi_id')
    kategori_pa_id = dosen_context.get('kategori_pa')
    
    print(f"[DEBUG] Getting kelompok with context: prodi_id={prodi_id}, kategori_pa_id={kategori_pa_id}")
    
    return get_kelompok_by_dosen_context(
        prodi_id=prodi_id,
        kategori_pa_id=kategori_pa_id
    )


def get_dosen_from_context(dosen_context: dict) -> dict:
    """
    Ambil dosen berdasarkan dosen context payload
    
    Args:
        dosen_context: Dict dengan prodi_id dari payload
        
    Returns:
        Result dari get_dosen_by_dosen_context
    """
    prodi_id = dosen_context.get('prodi_id')
    
    print(f"[DEBUG] Getting dosen with context: prodi_id={prodi_id}")
    
    return get_dosen_by_dosen_context(prodi_id=prodi_id)


def process_dosen_context_list(dosen_context_list: list) -> dict:
    """
    Process multiple dosen context dari payload dan aggregate hasil
    
    Args:
        dosen_context_list: List of dosen context dicts dari payload UI
        
    Returns:
        Dict dengan aggregated data dari semua context
    """
    if not dosen_context_list:
        return {
            "status": "error",
            "message": "Dosen context list kosong"
        }
    
    # Ambil context pertama (biasanya hanya ada satu dosen yang login)
    context = dosen_context_list[0] if isinstance(dosen_context_list, list) else dosen_context_list
    
    print(f"[DEBUG] Processing dosen context: {context}")
    
    result = {
        "status": "success",
        "dosen_context": context,
        "data": {
            "mahasiswa": get_mahasiswa_from_context(context),
            "kelompok": get_kelompok_from_context(context),
            "dosen": get_dosen_from_context(context)
        }
    }
    
    return result
