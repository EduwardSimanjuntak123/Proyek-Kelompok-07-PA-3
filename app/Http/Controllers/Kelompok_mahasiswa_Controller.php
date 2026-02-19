<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Kelompok;
use App\Models\KelompokMahasiswa;
use App\Models\Prodi;
use App\Models\TahunAjaran;
use App\Models\TahunMasuk;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class Kelompok_mahasiswa_Controller extends Controller
{public function index($id)
    {
        $token = session('token');
    
        // Ambil data kelompok berdasarkan ID
        $kelompok = Kelompok::findOrFail($id);
    
        // Ambil data mahasiswa yang tergabung dalam kelompok tertentu
        $mahasiswakelompoks = KelompokMahasiswa::where('kelompok_id', $id)->get();
    
        // Ambil data mahasiswa dari API eksternal
        $response = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL') . "library-api/mahasiswa", [
            'limit' => 100
        ]);
    
        $mahasiswa_map = collect();
    
        if ($response->successful()) {
            $data = $response->json();
            $listMahasiswa = $data['data']['mahasiswa'] ?? [];
    
            // Buat map: user_id => mahasiswa
            $mahasiswa_map = collect($listMahasiswa)->keyBy('user_id');
        }
    
        // Gabungkan data user_id lokal + data dari API
        $mahasiswakelompoks->transform(function ($item) use ($mahasiswa_map) {
            $mhs = $mahasiswa_map->get($item->user_id);
            $item->nama = $mhs['nama'] ?? 'N/A';
            $item->nim = $mhs['nim'] ?? 'N/A';
            $item->angkatan = $mhs['angkatan'] ?? 'N/A';
            return $item;
        });
    
        // Kirim juga $kelompok ke view
        return view('pages.Koordinator.kelompok-mahasiswa.index', compact('mahasiswakelompoks', 'kelompok'));
    }
    
    public function create($id)
    {
        $token = session('token');
        $prodiId = session('prodi_id');
        $TahunMasukId =session('TM_id');
        $TahunMasuk =TahunMasuk::where('id', $TahunMasukId)->value('Tahun_Masuk');

        $responseMahasiswa = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL') . "library-api/mahasiswa", [
            "angkatan" =>$TahunMasuk,
            "prodi" => $prodiId,
            'limit' => 100]);
    
        $mahasiswa = $responseMahasiswa->successful()
            ? collect($responseMahasiswa->json()['data']['mahasiswa'] ?? [])
            ->sortBy('nim')
            ->values()
            : collect();
            //cek apakah sudah ada kelompok
        // Ambil 1 data kelompok berdasarkan ID
        $kelompok = Kelompok::findOrFail($id);
        $kpaId = $kelompok->KPA_id; 

        $user_idsudahpunyakelompok = DB::table('kelompok_mahasiswa as km')
        ->join('kelompok as k', 'km.kelompok_id', '=', 'k.id')
        ->where('KPA_id', $kpaId)
        ->pluck('user_id')->toArray();
        $mahasiswabelummasuk = $mahasiswa->filter(function($mhs)use ($user_idsudahpunyakelompok){
            return !in_array($mhs['user_id'], $user_idsudahpunyakelompok);
        })->values();
        return view('pages.Koordinator.kelompok-mahasiswa.create', [
            'mahasiswa' => $mahasiswabelummasuk,
            'kelompok' => $kelompok
        ]);
        
    }

    public function store(Request $request)
    {
    $kpaId = session('KPA_id');
    $maxAllowed = 6; 
    $minAllowed = 4; 

    if ($kpaId == 1) {
        $maxAllowed = 5;
        $minAllowed = 3;
    }

    $validated = $request->validate([
        'user_id' => "required|array",
        'kelompok_id' => 'required|exists:kelompok,id',
        'user_id.*' => 'required|distinct'
    ],
    [
        "user_id.required" => "Pilih minimal $minAllowed Mahasiswa.",
        "user_id.*.distinct" => "Mahasiswa Tidak Boleh Duplikat."
    ]);

    $kelompokId = $request->input('kelompok_id');
    $userIds = $request->input('user_id');

    // Hitung jumlah mahasiswa yang sudah ada di kelompok ini
    $existingCount = KelompokMahasiswa::where('kelompok_id', $kelompokId)->count();
    $newCount = count($userIds);
    $totalCount = $existingCount + $newCount;

    if ($totalCount > $maxAllowed) {
        return back()->withErrors([
            'user_id' => "Jumlah mahasiswa dalam kelompok ini melebihi batas maksimal ($maxAllowed). Saat ini sudah ada $existingCount mahasiswa."
        ])->withInput();
    }

    if ($totalCount < $minAllowed) {
        return back()->withErrors([
            'user_id' => "Jumlah mahasiswa dalam kelompok harus minimal $minAllowed. Saat ini ada $existingCount, dan Anda menambahkan $newCount."
        ])->withInput();
    }

    // Cek apakah user_id sudah terdaftar di kelompok manapun
    $existingUsers = DB::table('kelompok_mahasiswa as km')
    ->join('kelompok as k', 'km.kelompok_id', '=', 'k.id')
    ->where('k.KPA_id', $kpaId)
    ->whereIn('km.user_id', $userIds)
    ->pluck('km.user_id')
    ->toArray();
    if (!empty($existingUsers)) {
        return back()->withErrors([
            'user_id' => 'Beberapa Mahasiswa sudah tergabung dalam kelompok lain: ' . implode(', ', $existingUsers)
        ])->withInput();
    }

    // Simpan data
    foreach ($userIds as $userId) {
        KelompokMahasiswa::create([
            'kelompok_id' => $kelompokId,
            'user_id' => $userId,
        ]);
    }

    return redirect()->route('kelompokMahasiswa.index', ['id' => $kelompokId])
        ->with('success', 'Mahasiswa berhasil ditambahkan.');
    }
    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId); 
        $token = session('token');
        $prodiId = session('prodi_id');
        $TahunMasukId =session('TM_id');
        $TahunMasuk =TahunMasuk::where('id', $TahunMasukId)->value('Tahun_Masuk');
        $kelompokMahasiswa = KelompokMahasiswa::findOrFail($id);
    
        // Ambil semua mahasiswa dari API
        $response = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL') . "library-api/mahasiswa", [
            "angkatan" =>$TahunMasuk,
            "prodi" => $prodiId,
            'limit' => 100]);
    
        $mahasiswaData = $response->successful()
            ? collect($response->json()['data']['mahasiswa'] ?? [])->sortBy('nim')->values()
            : collect();

         //cek apakah sudah ada kelompok
         $user_idsudahpunyakelompok = DB::table('kelompok_mahasiswa')->pluck('user_id')->toArray();
         $mahasiswabelummasuk = $mahasiswaData->filter(function($mhs)use ($user_idsudahpunyakelompok){
             return !in_array($mhs['user_id'], $user_idsudahpunyakelompok);
         });
         //ambil data yang sedang di edit
         $mahasiswaDipilih  = $mahasiswaData->firstWhere('user_id',$kelompokMahasiswa->user_id); 
         
         if ($mahasiswaDipilih && !$mahasiswabelummasuk ->contains('user_id',$kelompokMahasiswa->user_id)){
            $mahasiswabelummasuk ->push($mahasiswaDipilih);
         }

         $mahasiswabelummasuk = $mahasiswabelummasuk->sortBy('nim')->values();
    
        return view('pages.Koordinator.kelompok-mahasiswa.edit', compact('kelompokMahasiswa','mahasiswabelummasuk'));
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
        ],
        [
            "user_id.required" => "Pilih Mahasiswa",
        ]);
        $kelompokMahasiswa = KelompokMahasiswa::findOrFail($id);
    
        // Cek apakah mahasiswa baru sudah tergabung di kelompok lain
        $sudahAda = KelompokMahasiswa::where('user_id', $request->user_id)
            ->where('id', '!=', $id)
            ->exists();
    
        if ($sudahAda) {
            return back()->withErrors(['user_id' => 'Mahasiswa sudah tergabung di kelompok lain.'])->withInput();
        }
    
        $kelompokMahasiswa->user_id = $request->user_id;
        $kelompokMahasiswa->save();
    
        return redirect()->route('kelompokMahasiswa.index', ['id' => $kelompokMahasiswa->kelompok_id])
            ->with('success', 'Data mahasiswa berhasil diperbarui.');
    }
    
    public function destroy($id)
    {
    // Cari data berdasarkan ID
    $mahasiswaKelompok = KelompokMahasiswa::findOrFail($id);

    // Simpan ID kelompok untuk redirect kembali
    $kelompokId = $mahasiswaKelompok->kelompok_id;

    // Hapus data
    $mahasiswaKelompok->delete();

    return redirect()->route('kelompokMahasiswa.index', ['id' => $kelompokId])
        ->with('success', 'Mahasiswa berhasil dihapus dari kelompok.');
    }


}
