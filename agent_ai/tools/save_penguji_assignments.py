"""
Tool untuk SAVE (CONFIRM & PERSIST) penguji assignments ke database.

Digunakan ketika user klik tombol "Simpan ke Database" / "Hapus lama & Simpan baru" di UI.
"""

from tools.penguji_tools import generate_penguji_assignments_by_context


def confirm_and_save_penguji_assignments(
    prodi_id: int = None,
    kategori_pa_id: int = None,
    angkatan_id: int = None,
) -> dict:
    """
    Confirm dan SAVE (PERSIST) penguji assignments ke database.
    
    Ini dipanggil ketika user confirm dari UI:
    - Ketika ada penguji sebelumnya: hapus lama, simpan baru (replace_existing=True)
    - Ketika tidak ada: langsung simpan (replace_existing=False)
    
    Args:
        prodi_id: Filter by program studi
        kategori_pa_id: Filter by kategori PA
        angkatan_id: Filter by angkatan/tahun masuk
        
    Returns:
        Dict dengan status dan detail assignment yang sudah disimpan
    """
    
    # Generate dengan persist=True (benar-benar simpan ke database)
    result = generate_penguji_assignments_by_context(
        prodi_id=prodi_id,
        kategori_pa_id=kategori_pa_id,
        angkatan_id=angkatan_id,
        min_per_group=2,
        max_per_group=2,
        replace_existing=True,  # Hapus yang lama jika ada
        persist=True,           # SIMPAN KE DATABASE
    )
    
    return {
        "status": result.get("status"),
        "message": f"✓ Penguji berhasil disimpan ke database" if result.get("status") == "success" else result.get("message"),
        "data": result,
    }


def cancel_penguji_generation() -> dict:
    """
    Cancel penguji generation (user klik Batal).
    
    Returns:
        Dict konfirmasi cancel
    """
    
    return {
        "status": "cancelled",
        "message": "Pembuat penguji dibatalkan. Data tidak disimpan.",
    }
