<?php

namespace App\Http\Controllers;
use App\Models\DosenRole;
use Illuminate\Http\Request;
use App\Models\Prodi;
use Illuminate\Support\Facades\Http;
class DosenController extends Controller
{
    public function index()
    {
        $token = session('token');

        $responseDosen = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL') . "library-api/dosen", [
                    'limit' => 100
                ]);

        $dosen = $responseDosen->successful()
            ? $responseDosen->json()['data']['dosen'] ?? []
            : [];

        // 🔥 Tentukan nama prodi yang ingin ditampilkan
        $namaProdi = [
            "DIII Teknologi Informasi",
            "DIV Teknologi Rekayasa Perangkat Lunak",
            "DIII Teknologi Komputer"
        ];

        // Samakan semua jadi lowercase dulu
        $namaProdi = array_map(fn($p) => strtolower(trim($p)), $namaProdi);

        $dosen = collect($dosen)
            ->filter(function ($item) use ($namaProdi) {
                return in_array(
                    strtolower(trim($item['prodi'])),
                    $namaProdi
                );
            })
            ->values()
            ->toArray();

        return view('pages.BAAK.listDosen.index', compact('dosen'));
    }
}
