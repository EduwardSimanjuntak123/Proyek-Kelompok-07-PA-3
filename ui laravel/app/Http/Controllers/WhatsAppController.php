<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;
use App\Models\Mahasiswa;

class WhatsAppController extends Controller
{
    public function index(Request $request)
    {
    }

    public function create()
    {
    }
    public function store(Request $request)
    {

    }
    public function send(Request $request)
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

Kelompok Proyek Akhir telah berhasil di-generate oleh sistem.

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

    public function edit($encryptedId)
    {

    }

    public function update(Request $request, $encryptedId)
    {

    }



    public function destroy($id)
    {

    }


    public function show($id)
    {

    }

}
