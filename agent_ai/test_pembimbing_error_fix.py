#!/usr/bin/env python3
"""
Test pembimbing error message fix.

Verifies that when no groups exist in the context, 
the user gets a helpful error message instead of a cryptic one.
"""

import sys
import os
sys.path.insert(0, os.path.dirname(__file__))

from core.database import get_db
from tools.pembimbing_tools import generate_pembimbing_assignments_by_context
import logging

logging.basicConfig(level=logging.INFO, format='%(message)s')
logger = logging.getLogger(__name__)

def test_pembimbing_no_groups():
    """Test pembimbing generation when no groups exist."""
    
    session = get_db()
    
    # Test context
    prodi_id = 1
    kategori_pa_id = 1
    angkatan_id = 2020
    
    # Try to generate pembimbing (should fail with groups not found)
    logger.info("Testing pembimbing generation with no groups...")
    result = generate_pembimbing_assignments_by_context(
        prodi_id=prodi_id,
        kategori_pa_id=kategori_pa_id,
        angkatan_id=angkatan_id,
        min_per_group=1,
        max_per_group=2,
        replace_existing=True,
        persist=False
    )
    
    logger.info(f"\nResult:")
    logger.info(f"  Status: {result.get('status')}")
    logger.info(f"  Message: {result.get('message')}")
    
    # Verify the error message
    assert result.get('status') == 'empty', "Should have empty status"
    assert "Tidak ada kelompok" in result.get('message', ''), "Should mention no groups"
    
    logger.info("\n✅ Test PASSED - Error message is helpful and clear")
    logger.info("\nUserInterface fix applied: This error message will now show:")
    logger.info("""
<div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:16px;">
  <h3 style="margin-top:0; color:#7f1d1d;">❌ Tidak Ada Kelompok</h3>
  <p style="margin:0 0 8px 0;">Tidak ada kelompok pada konteks ini</p>
  <p style="margin:0; color:#374151;"><strong>Solusi:</strong> Silakan buat kelompok terlebih dahulu sebelum membuat pembimbing. Gunakan perintah seperti:</p>
  <ul style="margin:8px 0 0 20px; color:#374151;">
    <li>"Buat 5 orang per kelompok berdasarkan nilai"</li>
    <li>"Buatkan kelompok dengan 6 anggota"</li>
  </ul>
</div>
""")
    
    session.close()

if __name__ == "__main__":
    try:
        test_pembimbing_no_groups()
    except Exception as e:
        logger.error(f"❌ Test FAILED: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)
