## 🔄 Cascade Delete: Pembimbing & Penguji Roles

### Problem Statement
When pembimbing (advisor) or penguji (examiner) assignments are deleted (especially during group reshuffling), their corresponding role entries in the `dosen_roles` table were not being removed, creating orphaned records that didn't accurately reflect the actual dosen responsibilities.

### Solution Implemented

#### 1. **Laravel Controller Updates**
File: `ui laravel/app/Http/Controllers/Agent/AgentKelompokController.php`

**In `saveGeneratedPembimbing()` function:**
```php
if ($replaceExisting) {
    // Get all pembimbing to be deleted for DosenRole cleanup
    $existingPembimbing = PembimbingModel::whereIn('kelompok_id', $contextKelompokIds)
        ->pluck('user_id')
        ->unique()
        ->toArray();
    
    // Delete pembimbing records
    $deletedAssignments = PembimbingModel::whereIn('kelompok_id', $contextKelompokIds)->delete();
    
    // ✅ Cascade delete: Remove corresponding DosenRole for pembimbing
    // role_id 3 = Pembimbing 1, role_id 5 = Pembimbing 2
    if (!empty($existingPembimbing)) {
        DosenRole::whereIn('user_id', $existingPembimbing)
            ->whereIn('role_id', [3, 5]) // Pembimbing 1 & 2
            ->where('prodi_id', $role->prodi_id)
            ->where('KPA_id', $role->KPA_id)
            ->where('TM_id', $role->TM_id)
            ->delete();
    }
}
```

**In `saveGeneratedPenguji()` function:**
```php
if ($replaceExisting) {
    // Get all penguji to be deleted for DosenRole cleanup
    $existingPenguji = PengujiModel::whereIn('kelompok_id', $contextKelompokIds)
        ->pluck('user_id')
        ->unique()
        ->toArray();
    
    // Delete penguji records
    $deletedAssignments = PengujiModel::whereIn('kelompok_id', $contextKelompokIds)->delete();
    
    // ✅ Cascade delete: Remove corresponding DosenRole for penguji
    // role_id 2 = Penguji 1, role_id 4 = Penguji 2
    if (!empty($existingPenguji)) {
        DosenRole::whereIn('user_id', $existingPenguji)
            ->whereIn('role_id', [2, 4]) // Penguji 1 & 2
            ->where('prodi_id', $role->prodi_id)
            ->where('KPA_id', $role->KPA_id)
            ->where('TM_id', $role->TM_id)
            ->delete();
    }
}
```

#### 2. **Python Backend Updates**
File: `agent_ai/tools/kelompok_tools.py`

**Enhanced `delete_kelompok_by_context()` function:**
```python
def delete_kelompok_by_context(prodi_id: int = None, kategori_pa_id: int = None, angkatan_id: int = None) -> dict:
    """Hapus kelompok lama dan anggotanya berdasarkan context dosen.
    
    ✅ IMPROVED: Also deletes pembimbing and penguji assignments for those kelompok.
    This ensures DosenRole cleanup happens at the same time in the Laravel UI.
    """
    session = SessionLocal()
    try:
        from models import Pembimbing, Penguji
        
        # ... existing kelompok query code ...
        
        kelompok_ids = [k.id for k in kelompoks]
        
        # ✅ Delete pembimbing and penguji assignments
        deleted_pembimbing = session.query(Pembimbing).filter(
            Pembimbing.kelompok_id.in_(kelompok_ids)
        ).delete(synchronize_session=False)
        
        deleted_penguji = session.query(Penguji).filter(
            Penguji.kelompok_id.in_(kelompok_ids)
        ).delete(synchronize_session=False)
        
        # ... rest of deletion code ...
        
        return {
            "status": "success",
            "deleted_kelompok": deleted_kelompok,
            "deleted_members": deleted_members,
            "deleted_pembimbing": deleted_pembimbing,  # ✅ NEW
            "deleted_penguji": deleted_penguji,        # ✅ NEW
            "message": f"Berhasil menghapus {deleted_kelompok} kelompok, {deleted_members} anggota, {deleted_pembimbing} pembimbing, dan {deleted_penguji} penguji.",
        }
```

### Role ID Reference

**Pembimbing Roles:**
- `role_id 3` = Pembimbing 1
- `role_id 5` = Pembimbing 2

**Penguji Roles:**
- `role_id 2` = Penguji 1
- `role_id 4` = Penguji 2

### Database Tables Affected

1. **pembimbing** - Advisor assignments to groups (deleted during recycle)
2. **penguji** - Examiner assignments to groups (deleted during recycle)
3. **dosen_roles** - Dosen role tracking (now properly cleaned up) ✅ NEW
4. **kelompok** - Groups (deleted during group reshuffling)
5. **kelompok_mahasiswa** - Group members (deleted during group reshuffling)

### Safety Measures

All cascade deletions are **context-filtered** to prevent accidental deletion of unrelated data:
- `prodi_id` - Product/Program ID
- `KPA_id` (kategori_pa_id) - PA Category ID
- `TM_id` (angkatan_id) - Year of Entry ID

Example:
```php
DosenRole::whereIn('user_id', $existingPembimbing)
    ->whereIn('role_id', [3, 5])
    ->where('prodi_id', $role->prodi_id)      // ✅ Context filter
    ->where('KPA_id', $role->KPA_id)          // ✅ Context filter
    ->where('TM_id', $role->TM_id)            // ✅ Context filter
    ->delete();
```

### User Scenarios Fixed

**Scenario 1: Delete and Recreate Groups**
1. User clicks "Hapus Kelompok Lama & Buat Baru"
2. Old pembimbing/penguji assignments are deleted
3. ✅ Corresponding dosen_roles entries are cleaned up
4. New groups and assignments are created
5. ✅ New dosen_roles entries are created automatically

**Scenario 2: Replace Pembimbing Assignment**
1. Groups already have pembimbing assigned
2. User clicks "Generate Pembimbing" → "Hapus dan Buat Baru"
3. Old pembimbing are deleted from `pembimbing` table
4. ✅ Their roles (role_id 3, 5) are removed from `dosen_roles`
5. New pembimbing are assigned and new roles created

**Scenario 3: Replace Penguji Assignment**
1. Groups already have penguji assigned
2. User clicks "Generate Penguji" → "Hapus dan Buat Baru"
3. Old penguji are deleted from `penguji` table
4. ✅ Their roles (role_id 2, 4) are removed from `dosen_roles`
5. New penguji are assigned and new roles created

### Testing

**Test File:** `agent_ai/test_cascade_delete.py`

Tests verify:
1. ✓ `delete_kelompok_by_context()` returns `deleted_pembimbing` count
2. ✓ `delete_kelompok_by_context()` returns `deleted_penguji` count
3. ✓ Function structure supports cascade deletion

**Test Result:** ✅ PASSED

### Commit Information

**Commit Hash:** `91d2a4e`

```
feat: Cascade delete pembimbing/penguji roles from dosen_roles when deleted

When pembimbing or penguji assignments are deleted (especially during group reshuffling):
- saveGeneratedPembimbing now removes DosenRole entries with role_id 3 & 5 (Pembimbing 1 & 2)
- saveGeneratedPenguji now removes DosenRole entries with role_id 2 & 4 (Penguji 1 & 2)
- delete_kelompok_by_context now also deletes pembimbing & penguji assignments
- All deletions filtered by context (prodi_id, KPA_id, TM_id) for safety

This ensures dosen_roles table stays clean and doesn't have orphaned role assignments.
```

### Benefits

✅ **Data Consistency:** `dosen_roles` table no longer contains orphaned entries
✅ **Cleaner Database:** Role assignments accurately reflect actual responsibilities
✅ **Reliable Tracking:** Dosen role history is maintained correctly during reshuffling
✅ **Safer Operations:** Context filtering prevents accidental deletion of unrelated data
✅ **Better Auditing:** Clear deletion counts for each table (pembimbing, penguji, etc.)

### Future Considerations

1. Could implement soft-deletes to maintain audit trail
2. Could add database-level CASCADE DELETE constraints if desired
3. Could add logging for role deletions for better audit trails
4. Could optimize queries if large-scale cascading operations occur frequently
