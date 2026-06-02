<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;
use App\Models\Mahasiswa;
use App\Models\DosenRole;
use App\Models\Dosen;

class WhatsAppController extends Controller
{

    public function sendtoMahasiswa(Request $request)
    {
        // dd($request->all());
        $KPA_id = session('KPA_id');
        $prodi_id = session('prodi_id');
        $TM_id = session('TM_id');
        $user_id = session('user_id');

        // Ambil mahasiswa sesuai konteks kelompok
        $mahasiswa = Mahasiswa::whereNotNull('nomor_telepon')
            ->whereHas('kelompokMahasiswa.kelompok', function ($q) use ($KPA_id, $prodi_id, $TM_id) {

                $q->where('KPA_id', $KPA_id)
                    ->where('prodi_id', $prodi_id)
                    ->where('TM_id', $TM_id);

            })->get();


        $BASE_URL = 'https://api.wachat-api.com/wachat_api/1.0/message';

        foreach ($mahasiswa as $item) {

            $nomor = $item->nomor_telepon;

            // ubah 08 menjadi 628
            if (substr($nomor, 0, 1) == '0') {
                $nomor = '62' . substr($nomor, 1);
            }
            $pesan = "📢 *PENGUMUMAN PROYEK AKHIR*

                        Halo Mahasiswa/Dosen,

                        Kelompok Proyek Akhir" . $KPA_id . " telah berhasil di-generate oleh sistem.

                        Silakan cek detail pengumuman dan pembagian kelompok pada website *Vokasi Tera*.

                        Terima kasih 😊";

            $response = Http::withHeaders([
                'APIKey' => 'F213E4CC9B967301E7D60D5646947286',
                'Content-Type' => 'application/json; charset=UTF-8'
            ])->post($BASE_URL, [
                        'destination' => $nomor,
                        'message' => $pesan,
                        'queue' => '13538-177987908032603'
                    ]);

            if (!$response->successful()) {

                dd([
                    'gagal_kirim_ke' => $nomor,
                    'response' => $response->body()
                ]);
            }
            sleep(2);
        }

        return redirect()->back()->with(
            'success',
            'Broadcast berhasil dikirim'
        );
    }

    public function sendtoPembimbing(Request $request)
    {
        // dd($request->all());
        $KPA_id = session('KPA_id');
        $prodi_id = session('prodi_id');
        $TM_id = session('TM_id');
        $user_id = session('user_id');
         $BASE_URL = 'https://api.wachat-api.com/wachat_api/1.0/message';
        // Ambil mahasiswa sesuai konteks kelompok


        $listPembimbing = DosenRole::where('KPA_id', $KPA_id)
            ->where('prodi_id', $prodi_id)
            ->where('TM_id', $TM_id)
            ->whereIn('role_id', [3, 5])
            ->where('status', 'Aktif')
            ->pluck('user_id');
        $listDosen = Dosen::whereIn('user_id', $listPembimbing)
            ->whereNotNull('nomor_telepon')
            ->get();
         foreach ($listDosen as $item) {

            $nomor = $item->nomor_telepon;

            // ubah 08 menjadi 628
            if (substr($nomor, 0, 1) == '0') {
                $nomor = '62' . substr($nomor, 1);
            }
            $pesan = "📢 *PENGUMUMAN PROYEK AKHIR*

                        Halo Dosen,

                        Kelompok Proyek Akhir" . $KPA_id . " telah berhasil di-generate oleh sistem.
                        anda telah di asign sebagai pembimbing kelompok pada PA " . $KPA_id . ".

                        Silakan cek detail pengumuman dan pembagian kelompok pada website *Vokasi Tera*.

                        Terima kasih 😊";

            $response = Http::withHeaders([
                'APIKey' => 'F213E4CC9B967301E7D60D5646947286',
                'Content-Type' => 'application/json; charset=UTF-8'
            ])->post($BASE_URL, [
                        'destination' => $nomor,
                        'message' => $pesan,
                        'queue' => '13538-177987908032603'
                    ]);

            if (!$response->successful()) {

                dd([
                    'gagal_kirim_ke' => $nomor,
                    'response' => $response->body()
                ]);
            }
            sleep(2);
        }

        return redirect()->back()->with(
            'success',
            'Broadcast berhasil dikirim'
        );
    }
     public function sendtoPenguji(Request $request)
    {
        // dd($request->all());
        $KPA_id = session('KPA_id');
        $prodi_id = session('prodi_id');
        $TM_id = session('TM_id');
        $user_id = session('user_id');
         $BASE_URL = 'https://api.wachat-api.com/wachat_api/1.0/message';
        // Ambil mahasiswa sesuai konteks kelompok


        $listPenguji = DosenRole::where('KPA_id', $KPA_id)
            ->where('prodi_id', $prodi_id)
            ->where('TM_id', $TM_id)
            ->whereIn('role_id', [2, 4])
            ->where('status', 'Aktif')
            ->pluck('user_id');
        $listDosen = Dosen::whereIn('user_id', $listPenguji)
            ->whereNotNull('nomor_telepon')
            ->get();
         foreach ($listDosen as $item) {

            $nomor = $item->nomor_telepon;

            // ubah 08 menjadi 628
            if (substr($nomor, 0, 1) == '0') {
                $nomor = '62' . substr($nomor, 1);
            }
            $pesan = "📢 *PENGUMUMAN PROYEK AKHIR*

                        Halo Dosen,

                        Kelompok Proyek Akhir" . $KPA_id . " telah berhasil di-generate oleh sistem.
                        anda telah di asign sebagai penguji kelompok pada PA " . $KPA_id . ".

                        Silakan cek detail pengumuman dan pembagian kelompok pada website *Vokasi Tera*.

                        Terima kasih 😊";

            $response = Http::withHeaders([
                'APIKey' => 'F213E4CC9B967301E7D60D5646947286',
                'Content-Type' => 'application/json; charset=UTF-8'
            ])->post($BASE_URL, [
                        'destination' => $nomor,
                        'message' => $pesan,
                        'queue' => '13538-177987908032603'
                    ]);

            if (!$response->successful()) {

                dd([
                    'gagal_kirim_ke' => $nomor,
                    'response' => $response->body()
                ]);
            }
            sleep(2);
        }

        return redirect()->back()->with(
            'success',
            'Broadcast berhasil dikirim'
        );
    }
}
