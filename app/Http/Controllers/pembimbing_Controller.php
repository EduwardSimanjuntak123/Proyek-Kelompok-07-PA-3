<?php

namespace App\Http\Controllers;

use App\Models\DosenRole;
use App\Models\Kelompok;
use App\Models\pembimbing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Psy\TabCompletion\Matcher\FunctionDefaultParametersMatcher;

class pembimbing_Controller extends Controller
{
  public function index (){
    $token = session('token');
    $prodi_id = session('prodi_id');
    $KPA_id = session('KPA_id');
    $TM_id = session('TM_id');
    
    $pembimbing = pembimbing::with(['kelompok', 'dosenRoles.role'])
        ->whereHas('dosenRoles.role', function ($query) {
            $query->where('role_name', 'Pembimbing 1');
        })
        ->whereHas('kelompok', function ($query) use ($prodi_id, $KPA_id, $TM_id) {
            $query->where('prodi_id', $prodi_id)
                  ->where('KPA_id', $KPA_id)
                  ->where('TM_id', $TM_id);
        })
        ->get();
    
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
        $pembimbing->transform(function ($role) use ($dosen_map) {
            $role->nama = $dosen_map[$role->user_id]['nama'] ?? 'N/A';
            return $role;
        });
    } else {
        // Tangani jika API gagal
        $pembimbing->each(function ($role) {
            $role->nama = 'N/A'; // Tampilkan N/A jika API gagal
        });
    }
    

    return view('pages.Koordinator.pembimbing.index',compact('pembimbing'));
  }
  public function indexpembimbing2(){
    $token = session('token');
    $prodi_id = session('prodi_id');
    $KPA_id = session('KPA_id');
    $TM_id = session('TM_id');
    
    $pembimbing = pembimbing::with(['kelompok', 'dosenRoles.role'])
        ->whereHas('dosenRoles.role', function ($query) {
            $query->where('role_name', 'Pembimbing 2');
        })
        ->whereHas('kelompok', function ($query) use ($prodi_id, $KPA_id, $TM_id) {
            $query->where('prodi_id', $prodi_id)
                  ->where('KPA_id', $KPA_id)
                  ->where('TM_id', $TM_id);
        })
        ->get();
    
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
        $pembimbing->transform(function ($role) use ($dosen_map) {
            $role->nama = $dosen_map[$role->user_id]['nama'] ?? 'N/A';
            return $role;
        });
    } else {
        // Tangani jika API gagal
        $pembimbing->each(function ($role) {
            $role->nama = 'N/A'; // Tampilkan N/A jika API gagal
        });
    }
    

    return view('pages.Koordinator.pembimbing.indexp2',compact('pembimbing'));
  }

  public function create(){
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
          ->where('role_id','3')
          ->whereHas('role', function ($query) {
            $query->where('id', '3');
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

    $Kelompok = Kelompok::with(['prodi', 'tahunMasuk', 'kategoripa'])
    ->where('prodi_id', $prodi_id)
    ->where('KPA_id', $KPA_id)
    ->where('TM_id', $TM_id)
    ->get();
    $kelompokIdsudahpunyapembimbing = DB ::table('pembimbing')
    ->join('dosen_roles', 'pembimbing.user_id','=','dosen_roles.user_id')
    ->join('roles','dosen_roles.role_id', '=', 'roles.id' )
    ->where('roles.id', '=','3')
    ->pluck('kelompok_id')
    ->toArray();
    $kelompokbelummasuk =  $Kelompok->filter(function($klmpk)use($kelompokIdsudahpunyapembimbing){
        return !in_array($klmpk['id'],$kelompokIdsudahpunyapembimbing);
    })->values();
    return view('pages.Koordinator.pembimbing.create',[
      'dosen' => $dosenFinal,
      'kelompok' => $kelompokbelummasuk,
    ]);
}
public function createpembimbing2(){
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
         ->where('role_id','5')
        ->whereHas('role', function ($query) {
          $query->where('id', '5');
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
$kelompokIdSudahPunyaP2 = DB::table('pembimbing')
->join('dosen_roles', 'pembimbing.user_id', '=', 'dosen_roles.user_id')
->join('roles', 'dosen_roles.role_id', '=', 'roles.id')
->where('roles.id', '5')
->pluck('pembimbing.kelompok_id')
->toArray();

// Filter hanya kelompok yang BELUM punya pembimbing 2
$kelompokbelummasuk = $Kelompok->filter(function ($klmpk) use ($kelompokIdSudahPunyaP2) {
return !in_array($klmpk->id, $kelompokIdSudahPunyaP2);
})->values();


// dd($dosenApiMap);
  return view('pages.Koordinator.pembimbing.createp2',[
    'dosen' => $dosenFinal,
    'kelompok' => $kelompokbelummasuk,
  ]);
}

  public function store(Request $request){
      $validated = $request->validate([
        'user_id'   => 'required|numeric',
        'kelompok_id'  => 'required|array',
        'kelompok_id.*' => 'exists:kelompok,id',
      ]);

      foreach ($validated['kelompok_id'] as $kelompokId) {
        pembimbing::create([
            'user_id' => $validated['user_id'],
            'kelompok_id' => $kelompokId,
        ]);
      }
      return redirect()->route('pembimbing.index')->with('succes', 'Data Berhasil disimpan');
  }
  public function storepembimbing2(Request $request){
    $validated = $request->validate([
      'user_id'   => 'required|numeric',
      'kelompok_id'  => 'required|array',
      'kelompok_id.*' => 'exists:kelompok,id',
    ]);

    foreach ($validated['kelompok_id'] as $kelompokId) {
      pembimbing::create([
          'user_id' => $validated['user_id'],
          'kelompok_id' => $kelompokId,
      ]);
    }
    return redirect()->route('pembimbing2.index')->with('succes', 'Data Berhasil disimpan');
}
  public function edit($encryptedId)
{
    try {
        // Dekripsi ID dan ambil token
        $token = session('token');
        $id = Crypt::decrypt($encryptedId);

        $pembimbing = pembimbing::findOrFail($id);

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
             ->where('role_id','3')
            // ->whereHas('role', function ($query) {
            //     $query->where('role_name', 'pembimbing 1');
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
    $kelompokIdsudahpunyapembimbing = DB ::table('pembimbing')
    ->join('dosen_roles','pembimbing.user_id', '=' , 'dosen_roles.user_id')
    ->join('roles', 'dosen_roles.role_id', '=', 'roles.id')
    ->where('roles.role_name', '=', 'pembimbing 1')
    ->pluck('kelompok_id')->toArray();
    $kelompokbelummasuk =  $Kelompok->filter(function($klmpk)use($kelompokIdsudahpunyapembimbing){
        return !in_array($klmpk['id'],$kelompokIdsudahpunyapembimbing);
    })->values();

        // Kirim ke view
        return view('pages.Koordinator.pembimbing.edit', [
          'dosen' => $dosenFinal,
          'Kelompok' => $kelompokbelummasuk,
          'pembimbing' => $pembimbing
      ]);

    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Gagal menampilkan data: ' . $e->getMessage());
    }
}
public function editpembimbing2($encryptedId)
{
    try {
        // Dekripsi ID dan ambil token
        $token = session('token');
        $id = Crypt::decrypt($encryptedId);

        $pembimbing = pembimbing::findOrFail($id);

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
             ->where('role_id','5')
            // ->whereHas('role', function ($query) {
            //     $query->where('role_name', 'pembimbing 2');
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
    $kelompokIdsudahpunyapembimbing = DB ::table('pembimbing')
    ->join('dosen_roles', 'pembimbing.user_id', '=', 'dosen_roles.user_id')
    ->join('roles', 'dosen_roles.role_id', '=', 'roles.id')
    ->where('roles.role_name', 'pembimbing 2')
    ->pluck('kelompok_id')->toArray();
    $kelompokbelummasuk =  $Kelompok->filter(function($klmpk)use($kelompokIdsudahpunyapembimbing){
        return !in_array($klmpk['id'],$kelompokIdsudahpunyapembimbing);
    })->values();

        // Kirim ke view
        return view('pages.Koordinator.pembimbing.editp2', [
          'dosen' => $dosenFinal,
          'Kelompok' => $kelompokbelummasuk,
          'pembimbing' => $pembimbing
      ]);

    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Gagal menampilkan data: ' . $e->getMessage());
    }
}
public function update(Request $request, $encryptedId)
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
    $pembimbing = Pembimbing::findOrFail($id); // Gantilah ini sesuai nama model kamu

    // Cek apakah kelompok sudah dibimbing oleh dosen lain
    // $sudahAda = Pembimbing::where('kelompok_id', $request->kelompok_id)
    //     ->where('id', '!=', $id)
    //     ->exists();

    // if ($sudahAda) {
    //     return back()->withErrors(['kelompok_id' => 'Kelompok sudah dibimbing oleh dosen lain.'])->withInput();
    // }

    // Update pembimbing
    $pembimbing->user_id = $request->user_id;
    $pembimbing->kelompok_id = $request->kelompok_id;
    $pembimbing->save();

    return redirect()->route('pembimbing.index')
        ->with('success', 'Data pembimbing berhasil diperbarui.');
}
public function updatepembimbing2(Request $request, $encryptedId)
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
    $pembimbing = Pembimbing::findOrFail($id); // Gantilah ini sesuai nama model kamu

    // Cek apakah kelompok sudah dibimbing oleh dosen lain
    // $sudahAda = Pembimbing::where('kelompok_id', $request->kelompok_id)
    //     ->where('id', '!=', $id)
    //     ->exists();

    // if ($sudahAda) {
    //     return back()->withErrors(['kelompok_id' => 'Kelompok sudah dibimbing oleh dosen lain.'])->withInput();
    // }

    // Update pembimbing
    $pembimbing->user_id = $request->user_id;
    $pembimbing->kelompok_id = $request->kelompok_id;
    $pembimbing->save();

    return redirect()->route('pembimbing2.index')
        ->with('success', 'Data pembimbing berhasil diperbarui.');
}

  public Function destroy ($id){
      try {

        $pembimbing =  pembimbing::findOrfail($id);

        $pembimbing->delete();
      return redirect()->back()->with('success', 'Data kelompok berhasil dihapus.');
    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
    }
  }
}
   