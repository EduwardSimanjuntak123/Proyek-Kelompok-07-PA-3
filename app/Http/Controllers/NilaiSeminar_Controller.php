<?php

namespace App\Http\Controllers;

use App\Models\kategoriPA;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
class NilaiSeminar_Controller extends Controller
{
public function index(){
        $data = DB::table('kelompok_mahasiswa as km')
        ->select(
            'km.user_id',
            'km.kelompok_id',
             // Mengambil nilai kelompok A_total untuk role 2
            DB::raw('
                COALESCE(MAX(CASE WHEN nk.role_id = 2 THEN nk.A_total ELSE 0 END), 0)
                as nilai_kelompok_role_2
            '),

            // Mengambil nilai individu B_total untuk role 2
            DB::raw('
                COALESCE(MAX(CASE WHEN ni.role_id = 2 THEN ni.B_total ELSE 0 END), 0)
                as nilai_individu_role_2
            '),

            // Menjumlahkan nilai kelompok + nilai individu untuk role 2
            DB::raw('
                COALESCE(MAX(CASE WHEN nk.role_id = 2 THEN nk.A_total ELSE 0 END), 0) +
                COALESCE(MAX(CASE WHEN ni.role_id = 2 THEN ni.B_total ELSE 0 END), 0) as total_role_2
            '),

            //untuk role_id 3
             // Mengambil nilai kelompok A_total untuk role 2
             DB::raw('
             COALESCE(MAX(CASE WHEN nk.role_id = 3 THEN nk.A_total ELSE 0 END), 0)
                as nilai_kelompok_role_3
            '),

            // Mengambil nilai individu B_total untuk role 2
            DB::raw('
                COALESCE(MAX(CASE WHEN ni.role_id = 3 THEN ni.B_total ELSE 0 END), 0)
                as nilai_individu_role_3
            '),

            // Menjumlahkan nilai kelompok + nilai individu untuk role 2
            DB::raw('
                COALESCE(MAX(CASE WHEN nk.role_id = 3 THEN nk.A_total ELSE 0 END), 0) +
                COALESCE(MAX(CASE WHEN ni.role_id = 3 THEN ni.B_total ELSE 0 END), 0) as total_role_3
            '),
            //untuk role_id 4
             // Mengambil nilai kelompok A_total untuk role 4
             DB::raw('
             COALESCE(MAX(CASE WHEN nk.role_id = 4 THEN nk.A_total ELSE 0 END), 0)
                as nilai_kelompok_role_4
            '),

            // Mengambil nilai individu B_total untuk role 4
            DB::raw('
                COALESCE(MAX(CASE WHEN ni.role_id = 4 THEN ni.B_total ELSE 0 END), 0)
                as nilai_individu_role_4
            '),

            // Menjumlahkan nilai kelompok + nilai individu untuk role 2
            DB::raw('
                COALESCE(MAX(CASE WHEN nk.role_id = 4 THEN nk.A_total ELSE 0 END), 0) +
                COALESCE(MAX(CASE WHEN ni.role_id = 4 THEN ni.B_total ELSE 0 END), 0) as total_role_4
            '),
            //untuk role_id 5
             // Mengambil nilai kelompok A_total untuk role 5
             DB::raw('
             COALESCE(MAX(CASE WHEN nk.role_id = 5 THEN nk.A_total ELSE 0 END), 0)
                as nilai_kelompok_role_5
            '),

            // Mengambil nilai individu B_total untuk role 5
            DB::raw('
                COALESCE(MAX(CASE WHEN ni.role_id = 5 THEN ni.B_total ELSE 0 END), 0)
                as nilai_individu_role_5
            '),

            // Menjumlahkan nilai kelompok + nilai individu untuk role 2
            DB::raw('
                COALESCE(MAX(CASE WHEN nk.role_id = 5 THEN nk.A_total ELSE 0 END), 0) +
                COALESCE(MAX(CASE WHEN ni.role_id = 5 THEN ni.B_total ELSE 0 END), 0) as total_role_5
            '),
            DB::raw('
            CASE 
        WHEN COUNT(DISTINCT nk.role_id) = 3 AND MAX(nk.role_id) IN (3, 4, 5)
        THEN
            (0.35 * (
                COALESCE(MAX(CASE WHEN nk.role_id = 2 THEN nk.A_total ELSE 0 END), 0) +
                COALESCE(MAX(CASE WHEN ni.role_id = 2 THEN ni.B_total ELSE 0 END), 0)
            )) +
            (0.3 * (
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
            (0.2 * (
                COALESCE(MAX(CASE WHEN nk.role_id = 3 THEN nk.A_total ELSE 0 END), 0) +
                COALESCE(MAX(CASE WHEN ni.role_id = 3 THEN ni.B_total ELSE 0 END), 0)
            )) +
            (0.35 * (
                COALESCE(MAX(CASE WHEN nk.role_id = 4 THEN nk.A_total ELSE 0 END), 0) +
                COALESCE(MAX(CASE WHEN ni.role_id = 4 THEN ni.B_total ELSE 0 END), 0)
            )) +
            (0.1 * (
                COALESCE(MAX(CASE WHEN nk.role_id = 5 THEN nk.A_total ELSE 0 END), 0) +
                COALESCE(MAX(CASE WHEN ni.role_id = 5 THEN ni.B_total ELSE 0 END), 0)
            ))
                END as nilai_seminar
        '),

            )
        ->leftJoin('kelompok as k', 'k.id', '=', 'km.kelompok_id')
        ->where('k.status','=','Aktif')
        ->leftJoin('nilai_kelompok as nk', 'nk.kelompok_id', '=', 'km.kelompok_id')
        ->leftJoin('nilai_individu as ni', 'ni.user_id', '=', 'km.user_id')
        ->groupBy('km.user_id', 'km.kelompok_id')
        ->get();
        foreach ($data as $item) {
            DB::table('nilai_seminar')->updateOrInsert(
                [
                    'user_id' => $item->user_id,
                    'kelompok_id' => $item->kelompok_id
                ],
                [
                    'nilai_kelompok_role_2' => $item->nilai_kelompok_role_2,
                    'nilai_individu_role_2' => $item->nilai_individu_role_2,
                    'total_role_2' => $item->total_role_2,
                    'nilai_kelompok_role_3' => $item->nilai_kelompok_role_3,
                    'nilai_individu_role_3' => $item->nilai_individu_role_3,
                    'total_role_3' => $item->total_role_3,
                    'nilai_kelompok_role_4' => $item->nilai_kelompok_role_4,
                    'nilai_individu_role_4' => $item->nilai_individu_role_4,
                    'total_role_4' => $item->total_role_4,
                    'nilai_kelompok_role_5' => $item->nilai_kelompok_role_5,
                    'nilai_individu_role_5' => $item->nilai_individu_role_5,
                    'total_role_5' => $item->total_role_5,
                    'nilai_seminar' => $item->nilai_seminar,
                ]
            );
            
}
}
    
}
