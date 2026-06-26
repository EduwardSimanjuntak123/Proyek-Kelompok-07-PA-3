<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelompok;
use App\Models\TahunMasuk;
use App\Models\tahunAjaran;
use App\Models\Prodi;
use App\Models\DosenRole;
use Illuminate\Support\Facades\Http;



class Histori_Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kelompok::with([
            'tahunAjaran',
            'TahunMasuk',
            'prodi'
        ]);

        // FILTER
        if ($request->tahun_ajaran) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran);
        }

        if ($request->tahun_masuk) {
            $query->where('TM_id', $request->tahun_masuk);
        }

        if ($request->prodi) {
            $query->where('prodi_id', $request->prodi);
        }

        $kelompok = $query->get();

        // Data untuk dropdown filter
        $tahunAjaran = TahunAjaran::all();
        $tahunMasuk  = TahunMasuk::all();
        $prodi       = Prodi::all();

        return view('pages.mahasiswa.Histori.index', compact(
            'kelompok',
            'tahunAjaran',
            'tahunMasuk',
            'prodi'
        ));
    }

    public function detail($id)
{
    $kelompok = Kelompok::with([
        'tahunAjaran',
        'tahunMasuk',
        'prodi',
        'pembimbing',
        'penguji',
        'kelompokMahasiswa'
    ])->findOrFail($id);

    $token = session('token');

    if (!$token) {
        return view('pages.mahasiswa.Histori.detail', compact('kelompok'));
    }

    $response = Http::withHeaders([
        'Authorization' => "Bearer {$token}"
    ])->get(env('API_URL') . 'library-api/dosen');

    if ($response->successful()) {

        $dosenList = collect($response->json()['data']['dosen']);

        // Mapping pembimbing
        foreach ($kelompok->pembimbing as $p) {
            $p->dosen = $dosenList->firstWhere('user_id', $p->user_id);
        }

        // Mapping penguji
        foreach ($kelompok->penguji as $u) {
            $u->dosen = $dosenList->firstWhere('user_id', $u->user_id);
        }
    }

    // Ambil user_id koordinator
$koordinatorId = DosenRole::where('KPA_id', $kelompok->KPA_id)
    ->where('TM_id', $kelompok->TM_id)
    ->where('role_id', 1)
    ->where('status', 'Aktif')
    ->value('user_id');

$kelompok->koordinator = $dosenList->firstWhere('user_id', $koordinatorId);
   
    $responseMahasiswa = Http::withHeaders([
    'Authorization' => "Bearer {$token}"
])->get(env('API_URL') . 'library-api/mahasiswa');

if ($responseMahasiswa->successful()) {

    $mahasiswaList = collect($responseMahasiswa->json()['data']['mahasiswa']);

    foreach ($kelompok->kelompokMahasiswa as $mhs) {
        $mhs->mahasiswa = $mahasiswaList->firstWhere('user_id', $mhs->user_id);
    }
}
    return view('pages.mahasiswa.Histori.detail', compact('kelompok'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
