<?php

namespace App\Http\Controllers;

use App\Models\DosenRole;
use App\Models\Kelompok;
use App\Models\Penguji;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Exception;


class Penguji_Controller extends Controller
{
    public function index(){
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');

        // Ambil kelompok yang sudah digenerate
        $kelompok = Kelompok::with('penguji')
            ->where('prodi_id', $prodi_id)
            ->where('KPA_id', $KPA_id)
            ->where('TM_id', $TM_id)
            ->get();

        // Ambil data dosen dari tabel dosen
        $dosen = \App\Models\Dosen::get()->keyBy('user_id');

        // Tambahkan nama dosen ke penguji
        $kelompok->each(function ($item) use ($dosen) {

            $item->penguji->each(function ($pg) use ($dosen) {

                $pg->nama = $dosen[$pg->user_id]->nama ?? '-';

            });

        });

        return view('pages.Koordinator.penguji.index', compact('kelompok'));
    }

    public function indexpenguji2(){
        $token = session('token');
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');

        $penguji = penguji::with(['kelompok', 'dosenRoles.role'])
        ->whereHas('dosenRoles.role', function($query){
            $query->where('role_name','Penguji 2');
        })
        ->whereHas('kelompok', function ($query) use ($prodi_id,$KPA_id,$TM_id){
            $query->where('prodi_id', $prodi_id)
            ->where('KPA_id', $KPA_id)
            ->where('TM_id', $TM_id);
        })->get();

     // Ambil data dosen dari API eksternal
     $responseDosen = Http::withHeaders([
        'Authorization' => "Bearer $token"
    ])->get(env('API_URL') . "library-api/dosen", ['limit' => 100]);

    // Cek apakah request ke API sukses
    if ($responseDosen->successful()) {
        $dosen_list = $responseDosen->json()['data']['dosen'] ?? [];
        
        // Buat map user_id => nama
        $dosen_map = collect($dosen_list)->keyBy('user_id');
        
        // Tambahkan nama dosen ke setiap data dosen_roles
        $penguji->transform(function ($role) use ($dosen_map) {
            $role->nama = $dosen_map[$role->user_id]['nama'] ?? 'N/A';
            return $role;
        });
    } else {
        // Tangani jika API gagal
        $penguji->each(function ($role) {
            $role->nama = 'N/A'; // Tampilkan N/A jika API gagal
        });
    }
    
    return view('pages.Koordinator.penguji.indexp2',compact('penguji'));

    }

    public function create($id)
    {
        $kelompok_id = Crypt::decrypt($id);
        $token = session('token');

        // ==============================
        // AMBIL DATA DOSEN DARI API
        // ==============================
        $prodi_id = session('prodi_id');
            $responseDosen = Http::withHeaders([
                'Authorization' => "Bearer $token"
            ])->get(env('API_URL') . "library-api/dosen", ['limit' => 100]);

            $dosenlist = [];
            if ($responseDosen->successful()) {
                $dosenList = collect($responseDosen->json()['data']['dosen'] ?? [])
                    ->where('prodi_id', $prodi_id) // filter berdasarkan prodi session
                    ->values()
                    ->toArray();
                $dosenApiMap = collect($dosenList)->keyBy('user_id');
            }
        // ==============================
        // AMBIL DOSEN DARI DATABASE (prodi_id = 4)
        // ==============================
        $dosen = \App\Models\Dosen::where('prodi_id', 4)->get(); // filter prodi_id sesuai kebutuhan

        $dosenFinal = $dosen->map(function ($d) use ($dosenApiMap) {
            $apiData = $dosenApiMap->get($d->user_id);

            return [
                'user_id' => $d->user_id,
                'nama' => $apiData['nama'] ?? $d->nama ?? 'Nama Tidak Diketahui',
            ];
        });

        return view('pages.Koordinator.penguji.create', [
            'dosen' => $dosenFinal,
            'kelompok_id' => $kelompok_id
        ]);
    }
public function createpenguji2(){
$token = session('token');

//  Ambil data dosen dari API eksternal
 $responseDosen = Http::withHeaders([
  'Authorization' => "Bearer $token"
])->get(env('API_URL') . "library-api/dosen", ['limit' => 100]);

$dosenApiMap = collect();
// Buat map user_id => nama
if ($responseDosen->successful()){
  $dosenlist = $responseDosen->json()['data']['dosen'] ?? [];
  $dosenApiMap =  collect($dosenlist)->keyBy('user_id');
}
//ambil data dosen berdasarkan session
  $prodi_id = session('prodi_id');
  $KPA_id = session('KPA_id');
  $TM_id = session('TM_id');
 
    $dosen = DosenRole::with(['prodi', 'tahunMasuk', 'kategoripa','role'])
    ->where('prodi_id', $prodi_id)
    ->where('KPA_id', $KPA_id)
    ->where('TM_id', $TM_id)
     ->where('role_id','4')
    ->whereHas('role', function ($query) {
      $query->where('id', '4');
  })
    ->get();
// Ambil nama dosen dari data API berdasarkan user_id
$dosenFinal = $dosen->map(function ($dr) use ($dosenApiMap) {
  return [
      'user_id' => $dr->user_id,
      'nama' => $dosenApiMap[$dr->user_id]['nama'] ?? 'Nama Tidak Diketahui',
      'prodi' => $dr->prodi->nama_prodi ?? '',
      'role' => $dr->role->role_name ?? '',
      'tahun_masuk' => $dr->tahunMasuk->tahun ?? '',
      'kategori' => $dr->kategoripa->nama_kategori ?? '',
  ];
});

// Ambil semua kelompok berdasarkan session
$Kelompok = Kelompok::with(['prodi', 'tahunMasuk', 'kategoripa'])
->where('prodi_id', $prodi_id)
->where('KPA_id', $KPA_id)
->where('TM_id', $TM_id)
->get();

// Ambil ID kelompok yang sudah punya pembimbing 2
$kelompokIdSudahPunyaP2 = DB::table('penguji')
->join('dosen_roles', 'penguji.user_id', '=', 'dosen_roles.user_id')
->join('roles', 'dosen_roles.role_id', '=', 'roles.id')
->where('roles.id', '4')
->pluck('penguji.kelompok_id')
->toArray();

// Filter hanya kelompok yang BELUM punya pembimbing 2
$kelompokbelummasuk = $Kelompok->filter(function ($klmpk) use ($kelompokIdSudahPunyaP2) {
return !in_array($klmpk->id, $kelompokIdSudahPunyaP2);
})->values();


// dd($dosenApiMap);
return view('pages.Koordinator.penguji.createp2',[
'dosen' => $dosenFinal,
'kelompok' => $kelompokbelummasuk,
]);
}

    public function store(Request $request)
    {
        $request->validate([
            'kelompok_id' => 'required',
            'penguji1' => 'required',
            'penguji2' => 'nullable|different:penguji1'
        ]);

        Penguji::create([
            'user_id' => $request->penguji1,
            'kelompok_id' => $request->kelompok_id
        ]);

        // ✅ AUTO-CREATE DosenRole untuk Penguji 1 jika belum ada
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');
        $tahun_ajaran_id = session('tahun_ajaran_id') ?? 1;
        
        $existingRole = DosenRole::where('user_id', $request->penguji1)
            ->where('role_id', 2) // Penguji 1
            ->where('prodi_id', $prodi_id)
            ->where('KPA_id', $KPA_id)
            ->where('TM_id', $TM_id)
            ->first();
        
        if (!$existingRole) {
            DosenRole::create([
                'user_id' => $request->penguji1,
                'role_id' => 2, // Penguji 1
                'prodi_id' => $prodi_id,
                'KPA_id' => $KPA_id,
                'TM_id' => $TM_id,
                'tahun_ajaran_id' => $tahun_ajaran_id,
                'status' => 'Aktif'
            ]);
        }

        if ($request->penguji2) {

            Penguji::create([
                'user_id' => $request->penguji2,
                'kelompok_id' => $request->kelompok_id
            ]);

            // ✅ AUTO-CREATE DosenRole untuk Penguji 2 jika belum ada
            $existingRole2 = DosenRole::where('user_id', $request->penguji2)
                ->where('role_id', 4) // Penguji 2
                ->where('prodi_id', $prodi_id)
                ->where('KPA_id', $KPA_id)
                ->where('TM_id', $TM_id)
                ->first();
            
            if (!$existingRole2) {
                DosenRole::create([
                    'user_id' => $request->penguji2,
                    'role_id' => 4, // Penguji 2
                    'prodi_id' => $prodi_id,
                    'KPA_id' => $KPA_id,
                    'TM_id' => $TM_id,
                    'tahun_ajaran_id' => $tahun_ajaran_id,
                    'status' => 'Aktif'
                ]);
            }
        }

        return redirect()->route('penguji.index')
            ->with('success', 'Penguji berhasil disimpan');
    }

public function storepenguji2(Request $request){
    $validated = $request->validate([
      'user_id'   => 'required|numeric',
      'kelompok_id'  => 'required|array',
      'kelompok_id.*' => 'exists:kelompok,id',
    ]);
  
       foreach ($validated['kelompok_id'] as $kelompokId) {
        // Cek apakah user sudah jadi pembimbing di kelompok tersebut
        $isAlreadyPembimbing = DB::table('pembimbing')
            ->where('user_id', $validated['user_id'])
            ->where('kelompok_id', $kelompokId)
            ->exists();

        if ($isAlreadyPembimbing) {
            // Ambil data kelompok untuk nomor kelompok
            $kelompok = DB::table('kelompok')->where('id', $kelompokId)->first();
            $nomorKelompok = $kelompok ? $kelompok->nomor_kelompok : $kelompokId;

            return redirect()->back()->withErrors([
                'user_id' => "Dosen sudah menjadi pembimbing di kelompok nomor $nomorKelompok dan tidak bisa menjadi penguji !.",
            ])->withInput();
        }

        // Simpan ke tabel penguji
        Penguji::create([
            'user_id' => $validated['user_id'],
            'kelompok_id' => $kelompokId,
        ]);

        // ✅ AUTO-CREATE DosenRole untuk Penguji 2 jika belum ada
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');
        $tahun_ajaran_id = session('tahun_ajaran_id') ?? 1;
        
        $existingRole = DosenRole::where('user_id', $validated['user_id'])
            ->where('role_id', 4) // Penguji 2
            ->where('prodi_id', $prodi_id)
            ->where('KPA_id', $KPA_id)
            ->where('TM_id', $TM_id)
            ->first();
        
        if (!$existingRole) {
            DosenRole::create([
                'user_id' => $validated['user_id'],
                'role_id' => 4, // Penguji 2
                'prodi_id' => $prodi_id,
                'KPA_id' => $KPA_id,
                'TM_id' => $TM_id,
                'tahun_ajaran_id' => $tahun_ajaran_id,
                'status' => 'Aktif'
            ]);
        }
    }
    return redirect()->route('penguji2.index')->with('succes', 'Data Berhasil disimpan');
  }
    public function edit($encryptedId)
    {
        try {
            $token = session('token');
            $kelompok_id = Crypt::decrypt($encryptedId);

            // ======================
            // AMBIL KELOMPOK
            // ======================
            $kelompok = Kelompok::findOrFail($kelompok_id);

            // ======================
            // AMBIL PENGUJI KELOMPOK
            // ======================
            $penguji = Penguji::where('kelompok_id', $kelompok_id)->get();

            $penguji1 = $penguji->first();
            $penguji2 = $penguji->skip(1)->first();

            // ======================
            // AMBIL NAMA DOSEN DARI TABEL DOSEN
            // ======================
            $dosenPenguji1 = $penguji1 ? \App\Models\Dosen::where('user_id', $penguji1->user_id)->first() : null;
            $dosenPenguji2 = $penguji2 ? \App\Models\Dosen::where('user_id', $penguji2->user_id)->first() : null;

            // ======================
            // API DOSEN (UNTUK DROPDOWN)
            // ======================
            $prodi_id = session('prodi_id');
            $responseDosen = Http::withHeaders([
                'Authorization' => "Bearer $token"
            ])->get(env('API_URL') . "library-api/dosen", ['limit' => 100]);

            $dosenlist = [];
            if ($responseDosen->successful()) {
                $dosenlist = collect($responseDosen->json()['data']['dosen'] ?? [])
                    ->where('prodi_id', $prodi_id) // filter berdasarkan prodi session
                    ->values()
                    ->toArray();
            }

            return view('pages.Koordinator.penguji.edit', [
                'dosen' => $dosenlist,
                'penguji1' => $penguji1,
                'penguji2' => $penguji2,
                'dosenPenguji1' => $dosenPenguji1,
                'dosenPenguji2' => $dosenPenguji2,
                'kelompok' => $kelompok,
                'kelompok_id' => $kelompok_id
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menampilkan data: ' . $e->getMessage());
        }
    }
public function editpenguji2($encryptedId)
{
try {
    // Dekripsi ID dan ambil token
    $token = session('token');
    $id = Crypt::decrypt($encryptedId);

    $penguji = penguji::findOrFail($id);

    // Ambil data dosen dari API eksternal
    $responseDosen = Http::withHeaders([
        'Authorization' => "Bearer $token"
    ])->get(env('API_URL') . "library-api/dosen", ['limit' => 100]);

    $dosenApiMap = collect();
    if ($responseDosen->successful()) {
        $dosenlist = $responseDosen->json()['data']['dosen'] ?? [];
        $dosenApiMap = collect($dosenlist)->keyBy('user_id'); // keyBy agar bisa dicari pakai user_id
    }

    // Ambil data session
    $prodi_id = session('prodi_id');
    $KPA_id = session('KPA_id');
    $TM_id = session('TM_id');

    // Ambil dosen dari tabel dosen_role berdasarkan session dan role 'pembimbing 1'
    $dosen = DosenRole::with(['prodi', 'tahunMasuk', 'kategoripa', 'role'])
        ->where('prodi_id', $prodi_id)
        ->where('KPA_id', $KPA_id)
        ->where('TM_id', $TM_id)
         ->where('role_id','4')
        // ->whereHas('role', function ($query) {
        //     $query->where('role_name', 'penguji 2');
        // })
        ->get();

    // Format data dosen final
    $dosenFinal = $dosen->map(function ($dr) use ($dosenApiMap) {
        return [
            'user_id' => $dr->user_id,
            'nama' => $dosenApiMap[$dr->user_id]['nama'] ?? 'Nama Tidak Diketahui',
            'prodi' => $dr->prodi->nama_prodi ?? '',
            'role' => $dr->role->role_name ?? '',
            'tahun_masuk' => $dr->tahunMasuk->tahun ?? '',
            'kategori' => $dr->kategoripa->nama_kategori ?? '',
        ];
    });

    // Ambil semua kelompok
    $Kelompok = Kelompok::with(['prodi', 'tahunMasuk', 'kategoripa'])
->where('prodi_id', $prodi_id)
->where('KPA_id', $KPA_id)
->where('TM_id', $TM_id)
->get();
$kelompokIdsudahpunyapenguji = DB ::table('penguji')
->join('dosen_roles', 'penguji.user_id', '=', 'dosen_roles.user_id')
->join('roles','dosen_roles.role_id', '=', 'roles.id')
->where('roles.role_name','=','penguji 2')
->pluck('kelompok_id')
->toArray();
$kelompokbelummasuk =  $Kelompok->filter(function($klmpk)use($kelompokIdsudahpunyapenguji){
    return !in_array($klmpk['id'],$kelompokIdsudahpunyapenguji);
})->values();

    // Kirim ke view
    return view('pages.Koordinator.penguji.editp2', [
      'dosen' => $dosenFinal,
      'Kelompok' => $kelompokbelummasuk,
      'penguji' => $penguji
  ]);

} catch (Exception $e) {
    return redirect()->back()->with('error', 'Gagal menampilkan data: ' . $e->getMessage());
}
}
    public function update(Request $request, $encryptedId)
    {
        $kelompok_id = Crypt::decrypt($encryptedId);

        $request->validate([
            'penguji1' => 'required',
            'penguji2' => 'nullable'
        ], [
            'penguji1.required' => 'Pilih Penguji 1 terlebih dahulu'
        ]);

        // ambil semua penguji untuk kelompok
        $penguji = Penguji::where('kelompok_id', $kelompok_id)->get();

        // Get session context
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');
        $tahun_ajaran_id = session('tahun_ajaran_id') ?? 1;

        // penguji 1
        if ($penguji1 = $penguji->first()) {
            $penguji1->user_id = $request->penguji1;
            $penguji1->save();
        } else {
            Penguji::create([
                'kelompok_id' => $kelompok_id,
                'user_id' => $request->penguji1
            ]);
        }

        // ✅ AUTO-CREATE DosenRole untuk Penguji 1 jika belum ada
        $existingRole = DosenRole::where('user_id', $request->penguji1)
            ->where('role_id', 2)
            ->where('prodi_id', $prodi_id)
            ->where('KPA_id', $KPA_id)
            ->where('TM_id', $TM_id)
            ->first();
        
        if (!$existingRole) {
            DosenRole::create([
                'user_id' => $request->penguji1,
                'role_id' => 2,
                'prodi_id' => $prodi_id,
                'KPA_id' => $KPA_id,
                'TM_id' => $TM_id,
                'tahun_ajaran_id' => $tahun_ajaran_id,
                'status' => 'Aktif'
            ]);
        }

        // penguji 2
        if ($request->penguji2) {
            if ($penguji2 = $penguji->skip(1)->first()) {
                $penguji2->user_id = $request->penguji2;
                $penguji2->save();
            } else {
                Penguji::create([
                    'kelompok_id' => $kelompok_id,
                    'user_id' => $request->penguji2
                ]);
            }

            // ✅ AUTO-CREATE DosenRole untuk Penguji 2 jika belum ada
            $existingRole2 = DosenRole::where('user_id', $request->penguji2)
                ->where('role_id', 4)
                ->where('prodi_id', $prodi_id)
                ->where('KPA_id', $KPA_id)
                ->where('TM_id', $TM_id)
                ->first();
            
            if (!$existingRole2) {
                DosenRole::create([
                    'user_id' => $request->penguji2,
                    'role_id' => 4,
                    'prodi_id' => $prodi_id,
                    'KPA_id' => $KPA_id,
                    'TM_id' => $TM_id,
                    'tahun_ajaran_id' => $tahun_ajaran_id,
                    'status' => 'Aktif'
                ]);
            }
        }

        return redirect()->route('penguji.index')
            ->with('success', 'Data penguji berhasil diperbarui');
    }
public function updatepenguji2(Request $request, $encryptedId)
{
$id = Crypt::decrypt($encryptedId);
$validated = $request->validate([
    'user_id' => 'required|integer',
    'kelompok_id' => 'required|integer|exists:kelompok,id',
], [
    "user_id.required" => "Pilih minimal satu dosen.",
    "kelompok_id.required" => "Kelompok wajib dipilih.",
    "kelompok_id.exists" => "Kelompok tidak valid.",
]);

// Ambil data pembimbing berdasarkan ID
$penguji = Penguji::findOrFail($id); // Gantilah ini sesuai nama model kamu

// Cek apakah dosen sudah jadi pembimbing di kelompok yang dimaksud
    $isAlreadyPembimbing = DB::table('pembimbing')
        ->where('user_id', $validated['user_id'])
        ->where('kelompok_id', $validated['kelompok_id'])
        ->exists();
         if ($isAlreadyPembimbing) {
        // Ambil nomor kelompok
        $kelompok = DB::table('kelompok')->where('id', $validated['kelompok_id'])->first();
        $nomorKelompok = $kelompok ? $kelompok->nomor_kelompok : $validated['kelompok_id'];

        return redirect()->back()->withErrors([
            'user_id' => "Dosen sudah menjadi pembimbing di kelompok nomor $nomorKelompok dan tidak bisa menjadi penguji!",
        ])->withInput();
    }

// Update pembimbing
$penguji->user_id = $request->user_id;
$penguji->kelompok_id = $request->kelompok_id;
$penguji->save();

return redirect()->route('penguji2.index')
    ->with('success', 'Data pembimbing berhasil diperbarui.');
}
    public function destroy($encryptedId)
    {
        try {
            $kelompok_id = Crypt::decrypt($encryptedId);

            // Ambil semua penguji untuk kelompok ini
            $pengujiList = Penguji::where('kelompok_id', $kelompok_id)->get();

            // ✅ Hapus DosenRole entries jika penguji tidak di-assign ke kelompok lain
            foreach ($pengujiList as $pg) {
                $masihAdaKelompok = Penguji::where('user_id', $pg->user_id)
                    ->where('kelompok_id', '!=', $kelompok_id)
                    ->exists();

                // Jika dosen tidak punya kelompok lain, hapus dari dosen_roles (untuk Penguji 1 & 2 di PA ini)
                if (!$masihAdaKelompok) {
                    $prodi_id = session('prodi_id');
                    $KPA_id = session('KPA_id');
                    $TM_id = session('TM_id');
                    
                    DosenRole::where('user_id', $pg->user_id)
                        ->whereIn('role_id', [2, 4]) // Penguji 1 atau 2
                        ->where('prodi_id', $prodi_id)
                        ->where('KPA_id', $KPA_id)
                        ->where('TM_id', $TM_id)
                        ->delete();
                }
            }

            // Hapus semua penguji untuk kelompok ini
            Penguji::where('kelompok_id', $kelompok_id)->delete();

            return redirect()->back()->with('success', 'Semua dosen penguji berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
