<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Prodi;
use App\Models\TahunMasuk;
use App\Models\kategoriPA;
use App\Models\Kelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JadwalPengujiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userID = session('user_id');
            $token = session('token');
    
            if (!$userID || !$token) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
            }
    
            // Ambil semua jadwal yang memiliki kelompok dengan penguji = user login
            $jadwal = Jadwal::whereHas('kelompok.penguji', function ($query) use ($userID) {
                    $query->where('user_id', $userID);
                })
                ->with(['kelompok.penguji'])
                ->orderBy('waktu_mulai', 'asc')
                ->get();
    
            // Ambil data nama dosen dari API eksternal
            $response = Http::withHeaders([
                'Authorization' => "Bearer $token"
            ])->get(env('API_URL') . "library-api/dosen", ['limit' => 100]);
    
            $dosenArray = [];
            $namaDosen = 'Tidak Ditemukan';
    
            if ($response->successful()) {
                $dosenData = $response->json('data.dosen');
    
                foreach ($dosenData as $dosen) {
                    $dosenArray[$dosen['user_id']] = $dosen['nama'];
                    if ($dosen['user_id'] == $userID) {
                        $namaDosen = $dosen['nama'];
                    }
                }
            }
    
            // Tambahkan penguji_nama ke setiap jadwal
            foreach ($jadwal as $item) {
                $pengujiNama = [];
    
                if ($item->kelompok && $item->kelompok->penguji) {
                    foreach ($item->kelompok->penguji as $penguji) {
                        $pengujiNama[] = $dosenArray[$penguji->user_id] ?? 'Tidak Ditemukan';
                    }
                }
    
                $item->penguji_nama = !empty($pengujiNama) ? implode('<br>', $pengujiNama) : '-';
            }
    
            return view('pages.Penguji.Jadwal.index', compact('jadwal', 'namaDosen'));
    
        } catch (\Exception $e) {
            Log::error('Error fetching jadwal penguji: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengambil data jadwal.');
        }
    }
}
    // public function index(Request $request){
    //     try{
    //         $userID = session('user_id');
    //         $token = session('token');

    //         if (!$userID || !$token) {
    //             return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
    //         }           
    //         return view('pages.Penguji.Jadwal.index', compact('jadwal'));
    //            } catch (\Exception $e) {
    //                Log::error('Error fetching jadwal penguji: ' . $e->getMessage());
    //                return back()->with('error', 'Gagal mengambil data jadwal.');
    //            }
    //         }
    // }
        // try{
        //     $userID = session('user_id');
        //     $token = session('token');

        //     if (!$userID || !$token) {
        //         return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        //     }           
        //        } catch (\Exception $e) {
        //            Log::error('Error fetching jadwal penguji: ' . $e->getMessage());
        //            return back()->with('error', 'Gagal mengambil data jadwal.');
        //        }
        //     }

       
    // }
    // public function index(Request $request)
    // {
    //     try {
    //         $userID = session('user_id');
    //         $token = session('token');

    //         if (!$userID || !$token) {
    //             return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
    //         }

    //         // $jadwal = Jadwal::where('penguji1', $userID)
    //         //             ->orWhere('penguji2', $userID)
    //         //             ->orderBy('waktu', 'asc')
    //         //             ->get();

    //         $response = Http::withHeaders([
    //             'Authorization' => "Bearer $token"
    //         ])->get(env('API_URL') . "library-api/dosen", ['limit' => 100]);

    //         $namaDosen = 'Tidak Ditemukan'; 
    //         if ($response->successful()) {
    //             $dosenData = $response->json('data.dosen');
    //             foreach ($dosenData as $dosen) {
    //                 if ($dosen['user_id'] == $userID) {
    //                     $namaDosen = $dosen['nama'];
    //                     break;
    //                 }
    //             }
    //         }

    //         return view('pages.Penguji.jadwal.index', compact('jadwal', 'namaDosen'));
    //     } catch (\Exception $e) {
    //         Log::error('Error fetching jadwal penguji: ' . $e->getMessage());
    //         return back()->with('error', 'Gagal mengambil data jadwal.');
    //     }
    // }
// }
// }