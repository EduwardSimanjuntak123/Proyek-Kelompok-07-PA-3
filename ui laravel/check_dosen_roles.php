<?php
/**
 * Direct check - no tinker needed
 */

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DosenRole;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "BUKTI: Dosen dengan Multiple Roles (BELAH 2)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Query dosen yang punya lebih dari 1 role aktif
$dosensWithMultipleRoles = DB::table('dosen_roles')
    ->select('user_id')
    ->where('status', 'Aktif')
    ->groupBy('user_id')
    ->havingRaw('COUNT(*) > 1')
    ->get();

echo "✅ Dosen dengan 2+ roles aktif:\n\n";

if ($dosensWithMultipleRoles->count() > 0) {
    foreach ($dosensWithMultipleRoles as $dosen) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "👤 User ID: " . $dosen->user_id . "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        // Query semua roles untuk user ini
        $roles = DosenRole::where('user_id', $dosen->user_id)
            ->where('status', 'Aktif')
            ->with('role', 'prodi', 'kategoriPA', 'tahunMasuk')
            ->get();
        
        echo "   📊 ROLES AKTIF:\n";
        echo "   ────────────────────────────────────────────────────\n";
        
        $roleNames = [];
        foreach ($roles as $i => $role) {
            $roleId = $role->role_id;
            $roleName = $role->role->role_name ?? "Unknown";
            $roleNames[] = $roleName;
            
            echo "\n   Role #" . ($i + 1) . ":\n";
            echo "      • Role ID: " . $roleId . " (" . $roleName . ")\n";
            echo "      • Prodi: " . ($role->prodi->nama_prodi ?? "N/A") . "\n";
            echo "      • PA: " . ($role->kategoriPA->nama_kategori ?? "N/A") . "\n";
            echo "      • Tahun Masuk: " . ($role->tahunMasuk->tahun ?? "N/A") . "\n";
            echo "      • Status: " . $role->status . "\n";
        }
        
        echo "\n   ✅ KESIMPULAN:\n";
        echo "      Dosen ini punya ROLES: " . implode(" + ", $roleNames) . "\n";
        echo "      🎉 BELAH " . count($roles) . " TERBUKTI!\n";
        
        // Simulate sidebar rendering
        $dosenRolesArray = $roles->pluck('role_id')->map(function($id) { return (int)$id; })->toArray();
        echo "\n   📋 SIDEBAR MENU YANG MUNCUL:\n";
        echo "      ────────────────────────────────────────────────────\n";
        if (in_array(1, $dosenRolesArray)) {
            echo "      ✅ Menu KOORDINATOR\n";
        }
        if (in_array(2, $dosenRolesArray) || in_array(4, $dosenRolesArray)) {
            echo "      ✅ Menu PENGUJI\n";
        }
        if (in_array(3, $dosenRolesArray) || in_array(5, $dosenRolesArray)) {
            echo "      ✅ Menu PEMBIMBING\n";
        }
        
        echo "\n";
    }
} else {
    echo "❌ Tidak ada dosen dengan multiple roles aktif\n\n";
    echo "📌 NEXT STEP: Anda perlu assign dosen jadi pembimbing terlebih dahulu!\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "✅ VERIFICATION COMPLETE\n";
echo "═══════════════════════════════════════════════════════════════\n\n";
?>
