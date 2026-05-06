"""
Tool untuk SAVE (CONFIRM & PERSIST) jadwal seminar ke database.

Digunakan ketika user klik tombol "Simpan ke Database" di UI.
"""

from tools.jadwal_seminar_tools import save_jadwal_to_database


def confirm_and_save_jadwal(
    jadwal_list: list,
    prodi_id: int = None,
    kategori_pa_id: int = None,
    angkatan_id: int = None,
    user_id: int = None,
) -> dict:
    """
    Confirm dan SAVE (PERSIST) jadwal seminar ke database.
    
    Ini dipanggil ketika user confirm dari UI untuk menyimpan jadwal.
    
    Args:
        jadwal_list: List jadwal entries dari generate_seminar_schedule
        prodi_id: Program studi
        kategori_pa_id: Kategori PA
        angkatan_id: Angkatan/Tahun Masuk
        user_id: User ID dosen yang membuat jadwal
        
    Returns:
        Dict dengan status dan detail jadwal yang sudah disimpan
    """
    
    # Save jadwal ke database
    result = save_jadwal_to_database(
        jadwal_list=jadwal_list,
        prodi_id=prodi_id,
        kategori_pa_id=kategori_pa_id,
        angkatan_id=angkatan_id,
        user_id=user_id,
    )
    
    return {
        "status": result.get("status"),
        "message": result.get("message"),
        "count": result.get("count", 0),
    }


def cancel_jadwal_generation() -> dict:
    """
    Cancel jadwal generation (user klik Batal).
    
    Returns:
        Dict konfirmasi cancel
    """
    
    return {
        "status": "cancelled",
        "message": "Pembuatan jadwal seminar dibatalkan. Data tidak disimpan.",
    }
