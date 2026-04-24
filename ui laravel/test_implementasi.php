<?php
/**
 * Test Script untuk Verify Implementasi DosenRole Auto-Create
 * 
 * Run: php artisan tinker
 * Then: include 'test_implementasi.php'
 */

use App\Models\DosenRole;
use App\Models\pembimbing;
use App\Models\Kelompok;

echo "\n========================================\n";
echo "TEST: Auto-Create DosenRole Implementation\n";
echo "========================================\n\n";

// Test 1: Cek apakah ada data pembimbing yang baru di-assign
echo "✅ TEST 1: Check Pembimbing Table\n";
$latestPembimbing = pembimbing::latest('id')->first();
if ($latestPembimbing) {
    echo "   Latest Pembimbing ID: " . $latestPembimbing->id . "\n";
    echo "   User ID: " . $latestPembimbing->user_id . "\n";
    echo "   Kelompok ID: " . $latestPembimbing->kelompok_id . "\n";
} else {
    echo "   ❌ No pembimbing found\n";
}

// Test 2: Cek apakah DosenRole auto-created
echo "\n✅ TEST 2: Check DosenRole Auto-Create\n";
if ($latestPembimbing) {
    $dosenRoles = DosenRole::where('user_id', $latestPembimbing->user_id)
        ->where('status', 'Aktif')
        ->get();
    
    echo "   Total Aktif Roles untuk user_id=" . $latestPembimbing->user_id . ": " . $dosenRoles->count() . "\n";
    foreach ($dosenRoles as $role) {
        echo "   - Role ID: " . $role->role_id . " (1=Koordinator, 3=Pembimbing 1, 5=Pembimbing 2)\n";
        echo "     Prodi: " . $role->prodi_id . ", PA: " . $role->KPA_id . ", TM: " . $role->TM_id . "\n";
    }
    
    if ($dosenRoles->count() > 0) {
        echo "   ✅ SUCCESS: DosenRole auto-created!\n";
    } else {
        echo "   ❌ FAIL: No DosenRole found\n";
    }
}

// Test 3: Cek session dosen_roles saat login
echo "\n✅ TEST 3: Check Query SEMUA Roles (Not Just First)\n";
if ($latestPembimbing) {
    $allRoles = DosenRole::where('user_id', $latestPembimbing->user_id)
        ->where('status', 'Aktif')
        ->pluck('role_id')
        ->map(function ($role) {
            return (int) $role;
        })
        ->toArray();
    
    echo "   Query Result (should be array): \n";
    echo "   Array: " . json_encode($allRoles) . "\n";
    echo "   Count: " . count($allRoles) . " role(s)\n";
    
    if (count($allRoles) > 1) {
        echo "   ✅ SUCCESS: Multiple roles found (\"Belah 2\" akan bekerja!)\n";
    } else {
        echo "   ⚠️  WARNING: Only 1 role found\n";
    }
}

// Test 4: Simulate sidebar menu rendering
echo "\n✅ TEST 4: Simulate Sidebar Menu Rendering\n";
if ($latestPembimbing) {
    $dosenRoles = DosenRole::where('user_id', $latestPembimbing->user_id)
        ->where('status', 'Aktif')
        ->pluck('role_id')
        ->map(function ($role) {
            return (int) $role;
        })
        ->toArray();
    
    echo "   Menu yang akan muncul (based on dosenRoles = " . json_encode($dosenRoles) . "):\n";
    
    if (in_array(1, $dosenRoles)) {
        echo "   ✅ Menu KOORDINATOR akan muncul\n";
    }
    if (in_array(2, $dosenRoles) || in_array(4, $dosenRoles)) {
        echo "   ✅ Menu PENGUJI akan muncul\n";
    }
    if (in_array(3, $dosenRoles) || in_array(5, $dosenRoles)) {
        echo "   ✅ Menu PEMBIMBING akan muncul\n";
    }
    
    echo "\n   🎉 Expected: Multiple menus (\"Belah 2\" di Sidebar)\n";
}

echo "\n========================================\n";
echo "TEST SELESAI\n";
echo "========================================\n\n";
?>
