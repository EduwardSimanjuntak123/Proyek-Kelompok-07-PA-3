"""
Tool untuk SAVE (CONFIRM & PERSIST) pembimbing assignments ke database.

Digunakan ketika user klik tombol "Simpan ke Database" / "Hapus lama & Simpan baru" di UI.
"""

from tools.pembimbing_tools import generate_pembimbing_assignments_by_context


def confirm_and_save_pembimbing_assignments(
    prodi_id: int = None,
    kategori_pa_id: int = None,
    angkatan_id: int = None,
    min_per_group: int = 1,
    max_per_group: int = 2,
    constraints: dict = None,
) -> dict:
    """
    Confirm dan SAVE (PERSIST) pembimbing assignments ke database.
    
    Ini dipanggil ketika user confirm dari UI:
    - Ketika ada pembimbing sebelumnya: hapus lama, simpan baru (replace_existing=True)
    - Ketika tidak ada: langsung simpan (replace_existing=False)
    
    Args:
        prodi_id: Filter by program studi
        kategori_pa_id: Filter by kategori PA
        angkatan_id: Filter by angkatan/tahun masuk
        min_per_group: Minimum pembimbing per kelompok (default 1)
        max_per_group: Maximum pembimbing per kelompok (default 2)
        constraints: Constraints khusus (only_pembimbing_1, only_pembimbing_2, dll)
        
    Returns:
        Dict dengan status dan detail assignment yang sudah disimpan
    """
    
    # Generate dengan persist=True (benar-benar simpan ke database)
    result = generate_pembimbing_assignments_by_context(
        prodi_id=prodi_id,
        kategori_pa_id=kategori_pa_id,
        angkatan_id=angkatan_id,
        min_per_group=min_per_group,
        max_per_group=max_per_group,
        replace_existing=True,  # Hapus yang lama jika ada
        persist=True,           # SIMPAN KE DATABASE
        exclude_disrecommended=True,
        constraints=constraints,
    )
    
    return {
        "status": result.get("status"),
        "message": f"✓ Pembimbing berhasil disimpan ke database" if result.get("status") == "success" else result.get("message"),
        "data": result,
    }


def cancel_pembimbing_generation() -> dict:
    """
    Cancel pembimbing generation (user klik Batal).
    
    Returns:
        Dict konfirmasi cancel
    """
    
    return {
        "status": "cancelled",
        "message": "Pembuat pembimbing dibatalkan. Data tidak disimpan.",
    }
