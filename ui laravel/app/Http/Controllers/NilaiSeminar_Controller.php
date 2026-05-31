<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiSeminar_Controller extends Controller
{
    public function index()
    {
        // PERBAIKAN #2: tambahkan filter prodi_id, KPA_id, TM_id
        // agar nilai seminar tidak tercampur antar prodi / periode
        $prodi_id = session('prodi_id');
        $KPA_id   = session('KPA_id');
        $TM_id    = session('TM_id');

        $data = DB::table('kelompok_mahasiswa as km')
            ->select(
                'km.user_id',
                'km.kelompok_id',

                // Nilai kelompok per role
                DB::raw("COALESCE(MAX(CASE WHEN nk.role_id = 2 THEN nk.A_total ELSE 0 END), 0) as nilai_kelompok_role_2"),
                DB::raw("COALESCE(MAX(CASE WHEN nk.role_id = 3 THEN nk.A_total ELSE 0 END), 0) as nilai_kelompok_role_3"),
                DB::raw("COALESCE(MAX(CASE WHEN nk.role_id = 4 THEN nk.A_total ELSE 0 END), 0) as nilai_kelompok_role_4"),
                DB::raw("COALESCE(MAX(CASE WHEN nk.role_id = 5 THEN nk.A_total ELSE 0 END), 0) as nilai_kelompok_role_5"),

                // Nilai individu per role
                DB::raw("COALESCE(MAX(CASE WHEN ni.role_id = 2 THEN ni.B_total ELSE 0 END), 0) as nilai_individu_role_2"),
                DB::raw("COALESCE(MAX(CASE WHEN ni.role_id = 3 THEN ni.B_total ELSE 0 END), 0) as nilai_individu_role_3"),
                DB::raw("COALESCE(MAX(CASE WHEN ni.role_id = 4 THEN ni.B_total ELSE 0 END), 0) as nilai_individu_role_4"),
                DB::raw("COALESCE(MAX(CASE WHEN ni.role_id = 5 THEN ni.B_total ELSE 0 END), 0) as nilai_individu_role_5"),

                // Total per role (kelompok + individu)
                DB::raw("
                    COALESCE(MAX(CASE WHEN nk.role_id = 2 THEN nk.A_total ELSE 0 END), 0) +
                    COALESCE(MAX(CASE WHEN ni.role_id = 2 THEN ni.B_total ELSE 0 END), 0) as total_role_2
                "),
                DB::raw("
                    COALESCE(MAX(CASE WHEN nk.role_id = 3 THEN nk.A_total ELSE 0 END), 0) +
                    COALESCE(MAX(CASE WHEN ni.role_id = 3 THEN ni.B_total ELSE 0 END), 0) as total_role_3
                "),
                DB::raw("
                    COALESCE(MAX(CASE WHEN nk.role_id = 4 THEN nk.A_total ELSE 0 END), 0) +
                    COALESCE(MAX(CASE WHEN ni.role_id = 4 THEN ni.B_total ELSE 0 END), 0) as total_role_4
                "),
                DB::raw("
                    COALESCE(MAX(CASE WHEN nk.role_id = 5 THEN nk.A_total ELSE 0 END), 0) +
                    COALESCE(MAX(CASE WHEN ni.role_id = 5 THEN ni.B_total ELSE 0 END), 0) as total_role_5
                "),

                // Nilai seminar final dengan kondisi jumlah penguji
                // Jika 3 penguji (role 2,3,4): 0.35×P1 + 0.30×PB1 + 0.35×P2
                // Jika 4 penguji (role 2,3,4,5): 0.35×P1 + 0.20×PB1 + 0.35×P2 + 0.10×PB2
                DB::raw('
                    CASE
                        WHEN COUNT(DISTINCT nk.role_id) = 3 AND MAX(nk.role_id) IN (3, 4, 5)
                        THEN
                            (0.35 * (
                                COALESCE(MAX(CASE WHEN nk.role_id = 2 THEN nk.A_total ELSE 0 END), 0) +
                                COALESCE(MAX(CASE WHEN ni.role_id = 2 THEN ni.B_total ELSE 0 END), 0)
                            )) +
                            (0.30 * (
                                COALESCE(MAX(CASE WHEN nk.role_id = 3 THEN nk.A_total ELSE 0 END), 0) +
                                COALESCE(MAX(CASE WHEN ni.role_id = 3 THEN ni.B_total ELSE 0 END), 0)
                            )) +
                            (0.35 * (
                                COALESCE(MAX(CASE WHEN nk.role_id = 4 THEN nk.A_total ELSE 0 END), 0) +
                                COALESCE(MAX(CASE WHEN ni.role_id = 4 THEN ni.B_total ELSE 0 END), 0)
                            ))
                        ELSE
                            (0.35 * (
                                COALESCE(MAX(CASE WHEN nk.role_id = 2 THEN nk.A_total ELSE 0 END), 0) +
                                COALESCE(MAX(CASE WHEN ni.role_id = 2 THEN ni.B_total ELSE 0 END), 0)
                            )) +
                            (0.20 * (
                                COALESCE(MAX(CASE WHEN nk.role_id = 3 THEN nk.A_total ELSE 0 END), 0) +
                                COALESCE(MAX(CASE WHEN ni.role_id = 3 THEN ni.B_total ELSE 0 END), 0)
                            )) +
                            (0.35 * (
                                COALESCE(MAX(CASE WHEN nk.role_id = 4 THEN nk.A_total ELSE 0 END), 0) +
                                COALESCE(MAX(CASE WHEN ni.role_id = 4 THEN ni.B_total ELSE 0 END), 0)
                            )) +
                            (0.10 * (
                                COALESCE(MAX(CASE WHEN nk.role_id = 5 THEN nk.A_total ELSE 0 END), 0) +
                                COALESCE(MAX(CASE WHEN ni.role_id = 5 THEN ni.B_total ELSE 0 END), 0)
                            ))
                    END as nilai_seminar
                '),
            )
            ->leftJoin('kelompok as k', 'k.id', '=', 'km.kelompok_id')
            // PERBAIKAN #2: filter yang konsisten dengan controller lain
            ->where('k.status', 'Aktif')
            ->where('k.prodi_id', $prodi_id)
            ->where('k.KPA_id', $KPA_id)
            ->where('k.TM_id', $TM_id)
            ->leftJoin('nilai_kelompok as nk', 'nk.kelompok_id', '=', 'km.kelompok_id')
            ->leftJoin('nilai_individu as ni', 'ni.user_id', '=', 'km.user_id')
            ->groupBy('km.user_id', 'km.kelompok_id')
            ->get();

        foreach ($data as $item) {
            DB::table('nilai_seminar')->updateOrInsert(
                [
                    'user_id'     => $item->user_id,
                    'kelompok_id' => $item->kelompok_id,
                ],
                [
                    'nilai_kelompok_role_2' => $item->nilai_kelompok_role_2,
                    'nilai_individu_role_2' => $item->nilai_individu_role_2,
                    'total_role_2'          => $item->total_role_2,
                    'nilai_kelompok_role_3' => $item->nilai_kelompok_role_3,
                    'nilai_individu_role_3' => $item->nilai_individu_role_3,
                    'total_role_3'          => $item->total_role_3,
                    'nilai_kelompok_role_4' => $item->nilai_kelompok_role_4,
                    'nilai_individu_role_4' => $item->nilai_individu_role_4,
                    'total_role_4'          => $item->total_role_4,
                    'nilai_kelompok_role_5' => $item->nilai_kelompok_role_5,
                    'nilai_individu_role_5' => $item->nilai_individu_role_5,
                    'total_role_5'          => $item->total_role_5,
                    'nilai_seminar'         => $item->nilai_seminar,
                ]
            );
        }
    }
}