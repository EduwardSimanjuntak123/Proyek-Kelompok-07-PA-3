<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KelompokMahasiswa;
use App\Models\Tugas;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Http;

class JadwalMahasiswaController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        $token = session('token');
        $kelompokId = session('kelompok_id');
        // dd($kelompokId);

        $kelompokMahasiswa = KelompokMahasiswa::with(['kelompok.jadwal', 'kelompok.pembimbing', 'kelompok.penguji'])
            ->where('user_id', $userId)
            ->first();

        if (!$kelompokMahasiswa || !$kelompokMahasiswa->kelompok) {
            return back()->with('error', 'Anda belum tergabung dalam kelompok.');
        }

        $kelompok = $kelompokMahasiswa->kelompok;
        $jadwalUtama = $kelompok->jadwal;

        if (!$jadwalUtama) {
           
            return view('pages.Mahasiswa.Jadwal.index', compact('kelompok'))
                ->with('error', 'Jadwal belum tersedia untuk kelompok Anda.');
        }

        $jadwalLain = Jadwal::with(['kelompok.penguji'])
            ->where('KPA_id', $jadwalUtama->KPA_id)
            ->where('prodi_id', $jadwalUtama->prodi_id)
            ->where('TM_id', $jadwalUtama->TM_id)
            ->where('id', '!=', $jadwalUtama->id)
            ->get();

        $responseDosen = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL') . "library-api/dosen", ['limit' => 100]);

        $dosenData = $responseDosen->successful() ? $responseDosen->json()['data']['dosen'] : [];

        $dosenArray = [];
        foreach ($dosenData as $dosen) {
            $dosenArray[$dosen['user_id']] = $dosen['nama'];
        }

        // Ambil nama pembimbing
        $pembimbingNama = [];
        if ($kelompok->pembimbing && $kelompok->pembimbing->isNotEmpty()) {
            foreach ($kelompok->pembimbing as $pembimbing) {
                $userIdPembimbing = $pembimbing->user_id;
                $pembimbingNama[] = $dosenArray[$userIdPembimbing] ?? 'Tidak Ditemukan';
            }
        }

        // Ambil nama penguji untuk kelompok sendiri
        $pengujiNama = '-';
        if ($kelompok->penguji && $kelompok->penguji->isNotEmpty()) {
            $pengujiNamaArray = [];
            foreach ($kelompok->penguji as $penguji) {
                $userIdPenguji = $penguji->user_id;
                $pengujiNamaArray[] = $dosenArray[$userIdPenguji] ?? 'Tidak Ditemukan';
            }
            $pengujiNama = implode('<br>', $pengujiNamaArray);
        }

        // Ambil nama penguji untuk jadwal lain
        foreach ($jadwalLain as $jadwal) {
            $pengujiLainNama = [];
            if ($jadwal->kelompok && $jadwal->kelompok->penguji) {
                foreach ($jadwal->kelompok->penguji as $penguji) {
                    $userIdPenguji = $penguji->user_id;
                    $pengujiLainNama[] = $dosenArray[$userIdPenguji] ?? 'Tidak Ditemukan';
                }
            }
            $jadwal->penguji_nama = !empty($pengujiLainNama) ? implode('<br>', $pengujiLainNama) : '-';
        }
        return view('pages.Mahasiswa.Jadwal.index', [
            'jadwalUtama' => $jadwalUtama,
            'jadwalLain' => $jadwalLain,
            'kelompok' => $kelompok,
            'pembimbingNama' => $pembimbingNama,
            'pengujiNama' => $pengujiNama
        ]);
    }

    public function jadwalSeminar(){
        $userId = session('user_id');
        $token = session('token');

        $kelompokMahasiswa = KelompokMahasiswa::with(['kelompok.jadwal', 'kelompok.pembimbing', 'kelompok.penguji'])
            ->where('user_id', $userId)
            ->first();

        if (!$kelompokMahasiswa || !$kelompokMahasiswa->kelompok) {
            return back()->with('error', 'Anda belum tergabung dalam kelompok.');
        }

        $kelompok = $kelompokMahasiswa->kelompok;
        $jadwalUtama = $kelompok->jadwal;

            // return view('pages.Mahasiswa.Artefak.jadwal', compact('kelompok'))
            //     ->with('error', 'Jadwal belum tersedia untuk kelompok Anda.');
        
        $responseDosen = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL') . "library-api/dosen", ['limit' => 100]);

        $dosenData = $responseDosen->successful() ? $responseDosen->json()['data']['dosen'] : [];

        $dosenArray = [];
        foreach ($dosenData as $dosen) {
            $dosenArray[$dosen['user_id']] = $dosen['nama'];
        }

        // Ambil nama pembimbing
        $pembimbingNama = [];
        if ($kelompok->pembimbing && $kelompok->pembimbing->isNotEmpty()) {
            foreach ($kelompok->pembimbing as $pembimbing) {
                $userIdPembimbing = $pembimbing->user_id;
                $pembimbingNama[] = $dosenArray[$userIdPembimbing] ?? 'Tidak Ditemukan';
            }
        }

        // Ambil nama penguji untuk kelompok sendiri
        $pengujiNama = '-';
        if ($kelompok->penguji && $kelompok->penguji->isNotEmpty()) {
            $pengujiNamaArray = [];
            foreach ($kelompok->penguji as $penguji) {
                $userIdPenguji = $penguji->user_id;
                $pengujiNamaArray[] = $dosenArray[$userIdPenguji] ?? 'Tidak Ditemukan';
            }
            $pengujiNama = implode('<br>', $pengujiNamaArray);
        }
        $prodi_id = session('prodi_id');
            $KPA_id = session('KPA_id');
            $TM_id = session('TM_id');
            
            $artefak = Tugas::with(['prodi', 'tahunMasuk', 'kategoripa'])
            ->where('prodi_id', $prodi_id)
            ->where('KPA_id', $KPA_id)
            ->where('TM_id', $TM_id)
            ->where('kategori_tugas','Artefak')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pages.Mahasiswa.Artefak.jadwal', [
            'jadwalUtama' => $jadwalUtama,
            'kelompok' => $kelompok,
            'pembimbingNama' => $pembimbingNama,
            'pengujiNama' => $pengujiNama,
            'artefak' =>$artefak
        ]);
    }
}
