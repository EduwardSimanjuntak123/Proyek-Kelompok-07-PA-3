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
    public function index() {
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');
        $token = session('token');  

        $data = DB::table('kelompok_mahasiswa as km')
        ->join('kelompok as k', 'km.kelompok_id', '=', 'k.id')
        ->where('k.KPA_id', $KPA_id)
        ->where('k.TM_id', $TM_id)
        ->where('k.prodi_id', $prodi_id)
        ->where('k.status','Aktif')
        ->select(
            'km.user_id',
            'km.kelompok_id',
            'k.nomor_kelompok',
               DB::raw('
                    0.05 * COALESCE(na.pameran, 0) +
                    0.45 * COALESCE(ns.nilai_seminar, 0) +
                    0.1  * COALESCE(na.administrasi, 0) +
                    0.4  * COALESCE(nb.total, 0) AS nilai_akhir
                ')
            )
            ->leftJoin(DB::raw('(
                SELECT kelompok_id, MAX("Pameran") AS pameran, MAX("Administrasi") AS administrasi
                FROM nilai_administrasi
                GROUP BY kelompok_id
            ) as na'), 'na.kelompok_id', '=', 'km.kelompok_id')

            ->leftJoin(DB::raw('(
                SELECT user_id, MAX(nilai_seminar) AS nilai_seminar
                FROM nilai_seminar
                GROUP BY user_id
            ) as ns'), 'ns.user_id', '=', 'km.user_id')

           ->leftJoin(DB::raw('(
                SELECT user_id, MAX("Total") AS total
                FROM nilai_bimbingan
                GROUP BY user_id
            ) as nb'), 'nb.user_id', '=', 'km.user_id')

            ->get();
      
            foreach ($data as $item){
                DB::table('nilai_mahasiswa')->updateOrInsert([
                    'user_id' => $item->user_id,
                    'kelompok_id' => $item->kelompok_id
                ],
                [
                    'nilai_akhir' => $item->nilai_akhir
                ]
                );
            }
            // dd($data);
            // $nilai_akhir = DB::table('nilai_mahasiswa')
            // ->join('kelompok_mahasiswa', 'nilai_mahasiswa.user_id', '=', 'kelompok_mahasiswa.user_id')
            // ->join('kelompok', 'kelompok_mahasiswa.kelompok_id', '=', 'kelompok.id')  // Ganti dengan join yang benar
            // ->where('kelompok.prodi_id', $prodi_id)
            // ->where('kelompok.KPA_id', $KPA_id)
            // ->where('kelompok.TM_id', $TM_id)
            // ->select('nilai_mahasiswa.*', 'kelompok.nomor_kelompok', 'kelompok.KPA_id', 'kelompok.id AS kelompok_id')
            // ->get();
            // dd($nilai_akhir); 
            $nilai_akhir = $data;  
           
                // Ambil semua mahasiswa dari API
            $response = Http::withHeaders([
                'Authorization' => "Bearer $token"
            ])->get(env('API_URL') . "library-api/mahasiswa", [
                'limit' => 100
            ]);
        
            $mahasiswa = collect();
            if ($response->successful()) {
                $data = $response->json();
                $listMahasiswa = $data['data']['mahasiswa'] ?? [];
                $mahasiswa = collect($listMahasiswa)->keyBy('user_id');
            }
            

            //   dd($nilai_akhir);
            return view('pages.Koordinator.Nilai_Administrasi.NilaiAkhir', compact('nilai_akhir','mahasiswa','prodi_id','KPA_id','TM_id'));

    }
    
    public function export($prodi_id,$KPA_id,$TM_id){
        $token = session('token');
         // Ekspor data ke Excel dengan parameter yang diterima
         return Excel::download(new NilaiAkhirExport($prodi_id, $KPA_id, $TM_id,$token), 'nilai_akhir.xlsx');
    
    }
   
}
