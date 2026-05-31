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
        $KPA_id   = session('KPA_id');
        $TM_id    = session('TM_id');
        $token    = session('token');

        $data = DB::table('kelompok_mahasiswa as km')
            ->join('kelompok as k', 'km.kelompok_id', '=', 'k.id')
            ->where('k.KPA_id', $KPA_id)
            ->where('k.TM_id', $TM_id)
            ->where('k.prodi_id', $prodi_id)
            ->where('k.status', 'Aktif')
            ->select(
                'km.user_id',
                'km.kelompok_id',
                'k.nomor_kelompok',

                // Rumus nilai akhir:
                // 5%  × Pameran
                // 10% × Administrasi
                // 45% × Nilai Seminar
                // 40% × Rata-rata Nilai Bimbingan (pembimbing 1 & 2)
                DB::raw('
                    0.05 * COALESCE(na.pameran, 0) +
                    0.10 * COALESCE(na.administrasi, 0) +
                    0.45 * COALESCE(ns.nilai_seminar, 0) +
                    0.40 * COALESCE(nb.rata_bimbingan, 0) AS nilai_akhir
                ')
            )

            // Subquery administrasi — per kelompok
            ->leftJoin(DB::raw('(
                SELECT kelompok_id,
                       MAX("Pameran")       AS pameran,
                       MAX("Administrasi")  AS administrasi
                FROM nilai_administrasi
                GROUP BY kelompok_id
            ) as na'), 'na.kelompok_id', '=', 'km.kelompok_id')

            // Subquery nilai seminar — per mahasiswa
            ->leftJoin(DB::raw('(
                SELECT user_id, MAX(nilai_seminar) AS nilai_seminar
                FROM nilai_seminar
                GROUP BY user_id
            ) as ns'), 'ns.user_id', '=', 'km.user_id')

            // PERBAIKAN #3: ganti MAX menjadi AVG agar nilai kedua pembimbing
            // sama-sama diperhitungkan, bukan hanya ambil yang tertinggi saja.
            // Jika hanya ada 1 pembimbing, AVG tetap menghasilkan nilai yang benar.
            ->leftJoin(DB::raw('(
                SELECT user_id, AVG("Total") AS rata_bimbingan
                FROM nilai_bimbingan
                GROUP BY user_id
            ) as nb'), 'nb.user_id', '=', 'km.user_id')

            ->get();

        // Simpan / perbarui nilai akhir di tabel nilai_mahasiswa
        foreach ($data as $item) {
            DB::table('nilai_mahasiswa')->updateOrInsert(
                [
                    'user_id'     => $item->user_id,
                    'kelompok_id' => $item->kelompok_id,
                ],
                [
                    'nilai_akhir' => $item->nilai_akhir,
                ]
            );
        }

        $nilai_akhir = $data;

        // Ambil data nama & nim mahasiswa dari API
        $mahasiswa = collect();
        $response  = Http::withHeaders(['Authorization' => "Bearer $token"])
            ->get(env('API_URL') . 'library-api/mahasiswa', ['limit' => 100]);

        if ($response->successful()) {
            $listMahasiswa = $response->json()['data']['mahasiswa'] ?? [];
            $mahasiswa     = collect($listMahasiswa)->keyBy('user_id');
        }

        return view(
            'pages.Koordinator.Nilai_Administrasi.NilaiAkhir',
            compact('nilai_akhir', 'mahasiswa', 'prodi_id', 'KPA_id', 'TM_id')
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