<?php
/**
 * Test Script untuk verifikasi DosenRole auto-create
 * Tempat: Root Laravel project
 * Jalankan: php test_dosen_role_fix.php
 */

require 'ui laravel/vendor/autoload.php';
require 'ui laravel/bootstrap/app.php';

use App\Models\DosenRole;
use App\Models\pembimbing as PembimbingModel;
use App\Models\Kelompok;

echo "=== TEST DOSEN ROLE AUTO-CREATE ===\n\n";

// Test 1: Check pembimbing entries
echo "1. Checking Pembimbing entries in database...\n";
$pembimbingCount = PembimbingModel::count();
echo "   Total Pembimbing entries: $pembimbingCount\n";

// Test 2: Check DosenRole entries related to pembimbing
echo "\n2. Checking DosenRole entries for Pembimbing roles (3, 5)...\n";
$dosenRolesCount = DosenRole::whereIn('role_id', [3, 5])
    ->count();
echo "   Total DosenRole entries for Pembimbing: $dosenRolesCount\n";

// Test 3: Compare user_id between pembimbing and dosen_roles
echo "\n3. Comparing user_id between pembimbing and dosen_roles...\n";
$pembimbingUserIds = PembimbingModel::distinct()
    ->pluck('user_id')
    ->toArray();
    
$dosenRoleUserIds = DosenRole::whereIn('role_id', [3, 5])
    ->distinct()
    ->pluck('user_id')
    ->toArray();

echo "   User IDs in pembimbing table: " . implode(', ', $pembimbingUserIds) . "\n";
echo "   User IDs in dosen_roles table (roles 3,5): " . implode(', ', $dosenRoleUserIds) . "\n";

$missing = array_diff($pembimbingUserIds, $dosenRoleUserIds);
if (empty($missing)) {
    echo "   ✅ SEMUA pembimbing sudah ada di dosen_roles!\n";
} else {
    echo "   ❌ MISSING in dosen_roles: " . implode(', ', $missing) . "\n";
}

// Test 4: Detail check
echo "\n4. Detailed check for each pembimbing...\n";
$pembimbingData = PembimbingModel::with('kelompok')
    ->get();

foreach ($pembimbingData as $pb) {
    $dosenRole = DosenRole::where('user_id', $pb->user_id)
        ->whereIn('role_id', [3, 5])
        ->first();
    
    if ($dosenRole) {
        echo "   ✅ User $pb->user_id (Kelompok $pb->kelompok_id): Found DosenRole (ID: $dosenRole->id, Role: $dosenRole->role_id)\n";
    } else {
        echo "   ❌ User $pb->user_id (Kelompok $pb->kelompok_id): NO DosenRole found!\n";
    }
}

echo "\n=== END TEST ===\n";
