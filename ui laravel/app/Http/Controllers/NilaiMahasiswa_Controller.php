<?php

namespace App\Http\Controllers;

use App\Models\Nilai_Mahasiswa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Exports\NilaiAkhirExport;

class NilaiMahasiswa_Controller extends Controller
{
    public function index()
    {
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');
        $token = session('token');


        $data = DB::table('kelompok_mahasiswa as km')
            ->join('kelompok as k', 'km.kelompok_id', '=', 'k.id')

            // Nilai Administrasi & Pameran (berdasarkan kelompok)
            ->leftJoin(
                'nilai_administrasi as na',
                'na.kelompok_id',
                '=',
                'km.kelompok_id'
            )

            // Nilai Seminar (berdasarkan mahasiswa)
            ->leftJoin(
                DB::raw('(
            SELECT
                user_id,
                MAX(nilai_seminar) as nilai_seminar
            FROM nilai_seminar
            GROUP BY user_id
        ) ns'),
                'ns.user_id',
                '=',
                'km.user_id'
            )

            // Nilai Bimbingan (rata-rata seluruh pembimbing)
            ->leftJoin(
                DB::raw('(
            SELECT
                user_id,
                AVG(Total) as rata_bimbingan
            FROM nilai_bimbingan
            GROUP BY user_id
        ) nb'),
                'nb.user_id',
                '=',
                'km.user_id'
            )

            ->where('k.KPA_id', $KPA_id)
            ->where('k.TM_id', $TM_id)
            ->where('k.prodi_id', $prodi_id)
            ->where('k.status', 'Aktif')

            ->select(
                'km.user_id',
                'km.kelompok_id',
                'k.nomor_kelompok',

                'na.Administrasi',
                'na.Pameran',

                'ns.nilai_seminar',

                'nb.rata_bimbingan',

                DB::raw('COALESCE(na.Administrasi,0) * 0.10 AS nilai_administrasi_bobot'),
                DB::raw('COALESCE(na.Pameran,0) * 0.05 AS nilai_pameran_bobot'),
                DB::raw('COALESCE(ns.nilai_seminar,0) * 0.45 AS nilai_seminar_bobot'),
                DB::raw('COALESCE(nb.rata_bimbingan,0) * 0.40 AS nilai_bimbingan_bobot'),

                DB::raw('
            (
                COALESCE(na.Administrasi,0) * 0.10 +
                COALESCE(na.Pameran,0) * 0.05 +
                COALESCE(ns.nilai_seminar,0) * 0.45 +
                COALESCE(nb.rata_bimbingan,0) * 0.40
            ) AS nilai_akhir
        ')
            )
            ->get();
//         dd(
//     DB::table('nilai_seminar')
//         ->select('user_id', 'nilai_seminar')
//         ->limit(10)
//         ->get()
// );
        // Simpan nilai akhir
        foreach ($data as $item) {
            DB::table('nilai_mahasiswa')->updateOrInsert(
                [
                    'user_id' => $item->user_id,
                    'kelompok_id' => $item->kelompok_id,
                ],
                [
                    'nilai_akhir' => $item->nilai_akhir,
                ]
            );
        }

        $nilai_akhir = $data;

        // Ambil data mahasiswa dari API
        $mahasiswa = collect();

        $response = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(
                env('API_URL') . 'library-api/mahasiswa',
                ['limit' => 100]
            );

        if ($response->successful()) {
            $listMahasiswa = $response->json()['data']['mahasiswa'] ?? [];
            $mahasiswa = collect($listMahasiswa)->keyBy('user_id');
        }

        return view(
            'pages.Koordinator.Nilai_Administrasi.NilaiAkhir',
            compact(
                'nilai_akhir',
                'mahasiswa',
                'prodi_id',
                'KPA_id',
                'TM_id'
            )
        );


    }

    public function export($prodi_id, $KPA_id, $TM_id)
    {
        $token = session('token');

        return Excel::download(
            new NilaiAkhirExport($prodi_id, $KPA_id, $TM_id, $token),
            'nilai_akhir.xlsx'
        );
    }
}