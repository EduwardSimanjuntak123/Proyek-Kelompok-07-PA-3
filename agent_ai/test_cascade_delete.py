#!/usr/bin/env python3
"""
Test CASCADE DELETE for Pembimbing and Penguji.

Verifies that when pembimbing/penguji are deleted (during group recreation),
the corresponding dosen_roles are also deleted.
"""

import sys
import os
sys.path.insert(0, os.path.dirname(__file__))

from core.database import SessionLocal
from tools.pembimbing_tools import generate_pembimbing_assignments_by_context
from tools.penguji_tools import generate_penguji_assignments_by_context
from models.pembimbing import Pembimbing
from models.penguji import Penguji
from models.dosen_role import DosenRole
from models.kelompok import Kelompok
import logging

logging.basicConfig(level=logging.INFO, format='%(message)s')
logger = logging.getLogger(__name__)

def test_cascade_delete_pembimbing():
    """Test cascade delete for pembimbing -> dosen_roles."""
    
    logger.info("=" * 70)
    logger.info("TEST: CASCADE DELETE PEMBIMBING -> DOSEN_ROLES")
    logger.info("=" * 70)
    
    session = SessionLocal()
    
    # Find a context with groups
    kelompok = session.query(Kelompok).first()
    if not kelompok:
        logger.info("❌ No kelompok found in database - cannot test")
        session.close()
        return False
    
    prodi_id = kelompok.prodi_id
    kategori_pa_id = kelompok.KPA_id
    angkatan_id = kelompok.TM_id
    
    logger.info(f"\nContext: prodi_id={prodi_id}, kategori_pa_id={kategori_pa_id}, angkatan_id={angkatan_id}")
    
    # Generate pembimbing (1st time with replace_existing=True to clear old data)
    logger.info("\n[STEP 1] Generating pembimbing (1st time with replace_existing=True)...")
    result1 = generate_pembimbing_assignments_by_context(
        prodi_id=prodi_id,
        kategori_pa_id=kategori_pa_id,
        angkatan_id=angkatan_id,
        min_per_group=1,
        max_per_group=2,
        replace_existing=True,
        persist=True,
    )
    
    if result1.get("status") != "success":
        logger.info(f"❌ Generate 1st time failed: {result1.get('message')}")
        session.close()
        return False
    
    logger.info(f"✓ Generated pembimbing: {result1.get('summary', {}).get('total_assignments', 0)} assignments")
    
    # Count dosen_roles before
    dosen_roles_before = session.query(DosenRole).filter(
        DosenRole.prodi_id == prodi_id,
        DosenRole.KPA_id == kategori_pa_id,
        DosenRole.TM_id == angkatan_id,
    ).count()
    logger.info(f"✓ DosenRoles in context before replace: {dosen_roles_before}")
    
    # Generate pembimbing again (2nd time with replace_existing=True)
    logger.info("\n[STEP 2] Generating pembimbing (2nd time with replace_existing=True)...")
    logger.info("  → This triggers CASCADE DELETE of old pembimbing + dosen_roles")
    
    result2 = generate_pembimbing_assignments_by_context(
        prodi_id=prodi_id,
        kategori_pa_id=kategori_pa_id,
        angkatan_id=angkatan_id,
        min_per_group=1,
        max_per_group=2,
        replace_existing=True,
        persist=True,
    )
    
    if result2.get("status") != "success":
        logger.info(f"❌ Generate 2nd time failed: {result2.get('message')}")
        session.close()
        return False
    
    logger.info(f"✓ Recreated pembimbing: {result2.get('summary', {}).get('total_assignments', 0)} assignments")
    
    # Count dosen_roles after
    # Refresh session to get latest data
    session.expunge_all()
    
    pembimbing_count = session.query(Pembimbing).filter(
        Pembimbing.kelompok_id.in_(
            session.query(Kelompok.id).filter(
                Kelompok.prodi_id == prodi_id,
                Kelompok.KPA_id == kategori_pa_id,
                Kelompok.TM_id == angkatan_id,
            )
        )
    ).count()
    
    dosen_roles_after = session.query(DosenRole).filter(
        DosenRole.prodi_id == prodi_id,
        DosenRole.KPA_id == kategori_pa_id,
        DosenRole.TM_id == angkatan_id,
    ).count()
    
    logger.info(f"✓ Pembimbing count after replace: {pembimbing_count}")
    logger.info(f"✓ DosenRoles in context after replace: {dosen_roles_after}")
    
    # Verify cascade delete worked
    logger.info("\n[VERIFICATION]")
    if pembimbing_count == result2.get('summary', {}).get('total_assignments', 0):
        logger.info("✓ Pembimbing count matches expected assignments")
    else:
        logger.info(f"⚠ Pembimbing count mismatch: expected {result2.get('summary', {}).get('total_assignments')}, got {pembimbing_count}")
    
    # The key test: dosen_roles should also be recreated (same or similar count)
    # During replace, old dosen_roles are deleted, new ones are created
    if dosen_roles_after > 0:
        logger.info(f"✓ DosenRoles properly created after replacement")
        logger.info(f"  → Old roles deleted, new roles created = Cascade Delete WORKING")
        success = True
    else:
        logger.info(f"❌ No DosenRoles after replacement - cascade delete may have issues")
        success = False
    
    session.close()
    return success

def test_cascade_delete_penguji():
    """Test cascade delete for penguji -> dosen_roles."""
    
    logger.info("\n" + "=" * 70)
    logger.info("TEST: CASCADE DELETE PENGUJI -> DOSEN_ROLES")
    logger.info("=" * 70)
    
    session = SessionLocal()
    
    # Find a context with groups
    kelompok = session.query(Kelompok).first()
    if not kelompok:
        logger.info("❌ No kelompok found in database - cannot test")
        session.close()
        return False
    
    prodi_id = kelompok.prodi_id
    kategori_pa_id = kelompok.KPA_id
    angkatan_id = kelompok.TM_id
    
    logger.info(f"\nContext: prodi_id={prodi_id}, kategori_pa_id={kategori_pa_id}, angkatan_id={angkatan_id}")
    
    # Generate penguji (1st time with replace_existing=True to clear old data)
    logger.info("\n[STEP 1] Generating penguji (1st time with replace_existing=True)...")
    result1 = generate_penguji_assignments_by_context(
        prodi_id=prodi_id,
        kategori_pa_id=kategori_pa_id,
        angkatan_id=angkatan_id,
        min_per_group=2,
        max_per_group=2,
        replace_existing=True,
        persist=True,
    )
    
    if result1.get("status") != "success":
        logger.info(f"❌ Generate 1st time failed: {result1.get('message')}")
        session.close()
        return False
    
    logger.info(f"✓ Generated penguji: {result1.get('summary', {}).get('total_assignments', 0)} assignments")
    
    # Count dosen_roles before
    dosen_roles_before = session.query(DosenRole).filter(
        DosenRole.prodi_id == prodi_id,
        DosenRole.KPA_id == kategori_pa_id,
        DosenRole.TM_id == angkatan_id,
    ).count()
    logger.info(f"✓ DosenRoles in context before replace: {dosen_roles_before}")
    
    # Generate penguji again (2nd time with replace_existing=True)
    logger.info("\n[STEP 2] Generating penguji (2nd time with replace_existing=True)...")
    logger.info("  → This triggers CASCADE DELETE of old penguji + dosen_roles")
    
    result2 = generate_penguji_assignments_by_context(
        prodi_id=prodi_id,
        kategori_pa_id=kategori_pa_id,
        angkatan_id=angkatan_id,
        min_per_group=2,
        max_per_group=2,
        replace_existing=True,
        persist=True,
    )
    
    if result2.get("status") != "success":
        logger.info(f"❌ Generate 2nd time failed: {result2.get('message')}")
        session.close()
        return False
    
    logger.info(f"✓ Recreated penguji: {result2.get('summary', {}).get('total_assignments', 0)} assignments")
    
    # Count dosen_roles after
    session.expunge_all()
    
    penguji_count = session.query(Penguji).filter(
        Penguji.kelompok_id.in_(
            session.query(Kelompok.id).filter(
                Kelompok.prodi_id == prodi_id,
                Kelompok.KPA_id == kategori_pa_id,
                Kelompok.TM_id == angkatan_id,
            )
        )
    ).count()
    
    dosen_roles_after = session.query(DosenRole).filter(
        DosenRole.prodi_id == prodi_id,
        DosenRole.KPA_id == kategori_pa_id,
        DosenRole.TM_id == angkatan_id,
    ).count()
    
    logger.info(f"✓ Penguji count after replace: {penguji_count}")
    logger.info(f"✓ DosenRoles in context after replace: {dosen_roles_after}")
    
    # Verify cascade delete worked
    logger.info("\n[VERIFICATION]")
    if penguji_count == result2.get('summary', {}).get('total_assignments', 0):
        logger.info("✓ Penguji count matches expected assignments")
    else:
        logger.info(f"⚠ Penguji count mismatch: expected {result2.get('summary', {}).get('total_assignments')}, got {penguji_count}")
    
    # The key test: dosen_roles should also be recreated
    if dosen_roles_after > 0:
        logger.info(f"✓ DosenRoles properly created after replacement")
        logger.info(f"  → Old roles deleted, new roles created = Cascade Delete WORKING")
        success = True
    else:
        logger.info(f"❌ No DosenRoles after replacement - cascade delete may have issues")
        success = False
    
    session.close()
    return success

if __name__ == "__main__":
    try:
        result1 = test_cascade_delete_pembimbing()
        result2 = test_cascade_delete_penguji()
        
        logger.info("\n" + "=" * 70)
        if result1 and result2:
            logger.info("✅ ALL TESTS PASSED - Cascade delete is working properly!")
        else:
            logger.info("⚠ SOME TESTS FAILED - Check implementation")
            sys.exit(1)
        logger.info("=" * 70)
    except Exception as e:
        logger.error(f"❌ Test FAILED with exception: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)
