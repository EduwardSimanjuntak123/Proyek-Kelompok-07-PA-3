<?php
/**
 * VERIFICATION SCRIPT: Buktikan "Belah 2" DosenRole
 * 
 * Run: php artisan tinker
 * Then: include 'verify_dosen_roles.php'
 */

use App\Models\DosenRole;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "BUKTI: Dosen dengan Multiple Roles (BELAH 2)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Cari dosen bernama "Oppir" atau yang sesuai
echo "📍 Step 1: Cari dosen yang sedang login\n";
echo "─────────────────────────────────────────────────────────────\n";

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
            ->with('role', 'prodi', 'kategoriPA', 'tahunMasuk', 'tahunAjaran')
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
            echo "      • Tahun Ajaran: " . ($role->tahunAjaran->tahun_akademik ?? "N/A") . "\n";
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

// Detail query untuk copy-paste di SQL client
echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "📌 BONUS: SQL Query untuk verify di Database Client\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "-- Query: Cari dosen yang punya 2+ roles aktif\n";
echo "SELECT \n";
echo "    dr.user_id,\n";
echo "    COUNT(*) as total_roles,\n";
echo "    GROUP_CONCAT(r.role_name SEPARATOR ' + ') as roles\n";
echo "FROM dosen_roles dr\n";
echo "JOIN roles r ON dr.role_id = r.id\n";
echo "WHERE dr.status = 'Aktif'\n";
echo "GROUP BY dr.user_id\n";
echo "HAVING COUNT(*) > 1\n";
echo "ORDER BY dr.user_id;\n\n";

echo "-- Query: Detail semua roles untuk satu user (ganti 12345 dengan user_id)\n";
echo "SELECT \n";
echo "    dr.id,\n";
echo "    dr.user_id,\n";
echo "    r.role_name,\n";
echo "    p.nama_prodi,\n";
echo "    kpa.nama_kategori,\n";
echo "    tm.tahun,\n";
echo "    dr.status,\n";
echo "    dr.created_at\n";
echo "FROM dosen_roles dr\n";
echo "JOIN roles r ON dr.role_id = r.id\n";
echo "LEFT JOIN prodi p ON dr.prodi_id = p.id\n";
echo "LEFT JOIN kategori_pa kpa ON dr.KPA_id = kpa.id\n";
echo "LEFT JOIN tahun_masuk tm ON dr.TM_id = tm.id\n";
echo "WHERE dr.user_id = 12345\n";
echo "ORDER BY dr.created_at DESC;\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "✅ VERIFICATION COMPLETE\n";
echo "═══════════════════════════════════════════════════════════════\n\n";
?>
