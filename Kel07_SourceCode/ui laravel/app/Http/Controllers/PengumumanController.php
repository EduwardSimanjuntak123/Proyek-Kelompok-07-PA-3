<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\DosenRole;
use App\Models\kategoriPA;
use App\Models\pengumpulan_tugas;
use App\Models\Prodi;
use App\Models\TahunAjaran;
use App\Models\TahunMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    // Menampilkan pengumuman berdasarkan prodi_id dari session
    public function index()
    {
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');

        // Mengambil pengumuman yang hanya terkait dengan prodi_id yang sesuai dan status 'aktif'
        $pengumuman = Pengumuman::
            where('prodi_id', $prodi_id)
            ->where('KPA_id', $KPA_id)
            ->where('TM_id', $TM_id)
            ->get();
        return view('pages.Koordinator.pengumuman.index', compact('pengumuman'));
    }

    //create pengumuman oleh koordinator
    public function create()
    {
        $user_id = session('user_id');
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');

        // Menampilkan form tambah pengumuman
        return view('pages.Koordinator.pengumuman.create',compact('user_id','prodi_id','KPA_id','TM_id'));
    }

    // Menyimpan pengumuman
    public function store(Request $request)
    {
        // Validasi input
       $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:1024',
            'file' => 'nullable|mimes:pdf,docx,jpg,jpeg,png|max:20480',
            'status' => 'required|in:aktif,non-aktif',
    
        ]);
        
         // Handle file upload if exists
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $filePath = $file->store('Pengumuman', 'public'); 
        $validated['file'] = $filePath;
    }
        $validated['tanggal_penulisan'] = now();
        $validated['user_id'] = session('user_id');
        $validated['KPA_id'] = $request->KPA_id;
        $validated['prodi_id'] = $request->prodi_id;
        $validated['TM_id'] = $request->TM_id;
        Pengumuman::create($validated);
        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    // Menampilkan form edit pengumuman
    public function edit($encryptedId)
    {
        try {
            // Decrypt the encrypted ID
            $id = Crypt::decrypt($encryptedId);
        $pengumuman = Pengumuman::findOrFail($id);
        return view('pages.Koordinator.pengumuman.edit', compact('pengumuman'));
    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
    }

    // // Menyimpan update data pengumuman
    public function update(Request $request, $encryptedId)
    {
        
    $id = Crypt::decrypt($encryptedId);

       $validated= $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'status' => 'required|in:aktif,non-aktif',
            'file' => 'nullable|file|mimes:pdf,docx,jpg,jpeg,png|max:2048',
        ]);

        $pengumuman = Pengumuman::findOrFail($id);
        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($pengumuman->file && Storage::disk('public')->exists($pengumuman->file)) {
                Storage::disk('public')->delete($pengumuman->file);
            }
        
            $file = $request->file('file');
            $filePath = $file->store('pengumuman', 'public');
            $validated['file'] = $filePath;
        }
        $pengumuman->update($validated);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui!');
    }


    // Menghapus data pengumuman
    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
    
        if ($pengumuman->status === 'aktif') {
            return back()->withErrors([
                'error' => 'Tidak dapat menghapus Tugas yang sedang Berlangsung.',
            ]);
        }
    
        // Hapus file dari storage jika ada
        if ($pengumuman->file && Storage::disk('public')->exists($pengumuman->file)) {
            Storage::disk('public')->delete($pengumuman->file);
        }
    
        $pengumuman->delete();
        return redirect()->back()->with('success', 'Data tugas berhasil dihapus.');
    }

    public function mahasiswaIndex()
    {    $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');
        $token = session('token');
        // Mengambil pengumuman yang hanya terkait dengan prodi_id yang sesuai dan status 'aktif'
        $pengumuman = Pengumuman::
             where('prodi_id', $prodi_id)
            ->where('KPA_id', $KPA_id)
            ->where('TM_id', $TM_id)
            ->where('status', 'aktif')
            ->get();

        $responseDosen = Http::withHeaders([
            'Authorization' =>"Bearer $token"
        ])->get(env('API_URL'). "library-api/dosen");
        if ($responseDosen->successful()) {
            $dosen_list = $responseDosen->json()['data']['dosen'] ?? [];
            // Buat map user_id => nama
            $dosen_map = collect($dosen_list)->keyBy('user_id');
            
            $pengumuman->each(function ($item) use ($dosen_map) {
                $item->nama = $dosen_map[$item->user_id]['nama'] ?? 'N/A';
            });
        } else {
            // Tangani jika API gagal
            $pengumuman->each(function ($item) {
                $item->nama = 'N/A'; // Tampilkan N/A jika API gagal
            });
        }
  
        return view('pages.Mahasiswa.Pengumuman.index', compact('pengumuman'));
    }

    public function showPengumuman($id)
{
    // Find the pengumuman by its ID
    $pengumuman = Pengumuman::findOrFail($id);
    $token = session('token');

    $responseDosen = Http::withHeaders([
        'Authorization' =>"Bearer $token"
    ])->get(env('API_URL'). "library-api/dosen");
    if ($responseDosen->successful()) {
        $dosen_list = $responseDosen->json()['data']['dosen'] ?? [];
        // Buat map user_id => nama
        $dosen_map = collect($dosen_list)->keyBy('user_id');
        
        $pengumuman->nama = $dosen_map[$pengumuman->user_id]['nama'] ?? 'N/A';
        
    } else {
        // Tangani jika API gagal
        $pengumuman->nama = 'N/A'; // Tampilkan N/A jika API gagal
        
    }
    
    // Return the view with the pengumuman data
    return view('pages.Mahasiswa.Pengumuman.show', compact('pengumuman'));
}
public function showPengumumanBAAK($id)
{
    // Find the pengumuman by its ID
    $pengumuman = Pengumuman::findOrFail($id);
    $token = session('token');

    $responseDosen = Http::withHeaders([
        'Authorization' =>"Bearer $token"
    ])->get(env('API_URL'). "library-api/dosen");
    if ($responseDosen->successful()) {
        $dosen_list = $responseDosen->json()['data']['dosen'] ?? [];
        // Buat map user_id => nama
        $dosen_map = collect($dosen_list)->keyBy('user_id');
        
        $pengumuman->nama = $dosen_map[$pengumuman->user_id]['nama'] ?? 'N/A';
        
    } else {
        // Tangani jika API gagal
        $pengumuman->nama = 'N/A'; // Tampilkan N/A jika API gagal
        
    }
    
    // Return the view with the pengumuman data
    return view('pages.BAAK.pengumuman.show', compact('pengumuman'));
}public function showPengumumanKoordinator($id)
{
    // Find the pengumuman by its ID
    $pengumuman = Pengumuman::findOrFail($id);
    $token = session('token');

    $responseDosen = Http::withHeaders([
        'Authorization' =>"Bearer $token"
    ])->get(env('API_URL'). "library-api/dosen");
    if ($responseDosen->successful()) {
        $dosen_list = $responseDosen->json()['data']['dosen'] ?? [];
        // Buat map user_id => nama
        $dosen_map = collect($dosen_list)->keyBy('user_id');
        
        $pengumuman->nama = $dosen_map[$pengumuman->user_id]['nama'] ?? 'N/A';
        
    } else {
        // Tangani jika API gagal
        $pengumuman->nama = 'N/A'; // Tampilkan N/A jika API gagal
        
    }

    // Return the view with the pengumuman data
    return view('pages.Koordinator.pengumuman.show', compact('pengumuman'));
}


// public function pembimbingIndex()
//     {  
    
//         $token = session('token');
//         $user_id = session('user_id');
//         $role_ids = [3,5];
//        $prodi_ids = DosenRole::where('user_id', $user_id)
//                           ->where('status', 'Aktif')
//                           ->where('role_id', $role_ids)
//                           ->pluck('prodi_id');
//         $TM_ids = DosenRole::where('user_id', $user_id)
//                             ->where('status', 'Aktif')
//                             ->where('role_id', $role_ids)
//                           ->pluck('TM_id');
//         $KPA_ids = DosenRole::where('user_id', $user_id)
//                           ->where('status', 'Aktif')
//                           ->where('role_id', $role_ids)
//                           ->pluck('KPA_id');
//         $prodi_ids = $prodi_ids->unique();
//         $TM_ids = $TM_ids->unique();
//         $KPA_ids = $KPA_ids->unique();
//         // Mengambil pengumuman yang hanya terkait dengan prodi_id yang sesuai dan status 'aktif'
//         $pengumuman = Pengumuman::with(['prodi','kategoriPA'])
//             ->wherein('prodi_id', $prodi_ids)
//             ->wherein('KPA_id', $KPA_ids)
//             ->wherein('TM_id', $TM_ids)
//             ->where('status', 'aktif')
//             ->get();
// // 
//         $responseDosen = Http::withHeaders([
//             'Authorization' =>"Bearer $token"
//         ])->get(env('API_URL'). "library-api/dosen");
//         if ($responseDosen->successful()) {
//             $dosen_list = $responseDosen->json()['data']['dosen'] ?? [];
//             // Buat map user_id => nama
//             $dosen_map = collect($dosen_list)->keyBy('user_id');
            
//             $pengumuman->each(function ($item) use ($dosen_map) {
//                 $item->nama = $dosen_map[$item->user_id]['nama'] ?? 'N/A';
//             });
//         } else {
//             // Tangani jika API gagal
//             $pengumuman->each(function ($item) {
//                 $item->nama = 'N/A'; // Tampilkan N/A jika API gagal
//             });
//         }
            
//         return view('pages.Pembimbing.Pengumuman.index', compact('pengumuman'));
//     }

    public function showPengumumanpembimbing($id)
{
    // Find the pengumuman by its ID
    $pengumuman = Pengumuman::findOrFail($id);
    $token = session('token');

    $responseDosen = Http::withHeaders([
        'Authorization' =>"Bearer $token"
    ])->get(env('API_URL'). "library-api/dosen");
    if ($responseDosen->successful()) {
        $dosen_list = $responseDosen->json()['data']['dosen'] ?? [];
        // Buat map user_id => nama
        $dosen_map = collect($dosen_list)->keyBy('user_id');
        
        $pengumuman->nama = $dosen_map[$pengumuman->user_id]['nama'] ?? 'N/A';
        
    } else {
        // Tangani jika API gagal
        $pengumuman->nama = 'N/A'; // Tampilkan N/A jika API gagal
        
    }
    
    // Return the view with the pengumuman data
    return view('pages.Pembimbing.Pengumuman.show', compact('pengumuman'));
}


    // untuk BAAK
    public function staffpengumuman(){
        $pengumuman = Pengumuman::all();

        return view('pages.BAAK.pengumuman.index', compact('pengumuman'));
    }

 // create pengumuman oleh staff
    public function createpengumuman(){
        $user_id = session('user_id');
        $prodi =  Prodi::all();
        $TM = TahunMasuk::all();
        $KPA = kategoriPA::all();

        return view('pages.BAAK.pengumuman.create',compact('user_id','TM','prodi','KPA'));
        
    }

    public function storepengumuman(Request $request)
    {
        // Validasi input
       $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:1024',
            'file' => 'nullable|mimes:pdf,docx,jpg,jpeg,png|max:20480',
            'status' => 'required|in:aktif,non-aktif',
    
        ]);
        
         // Handle file upload if exists
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $filePath = $file->store('Pengumuman', 'public'); 
        $validated['file'] = $filePath;
    }
        $validated['tanggal_penulisan'] = now();
        $validated['user_id'] = session('user_id');
        $validated['KPA_id'] = $request->KPA_id;
        $validated['prodi_id'] = $request->prodi_id;
        $validated['TM_id'] = $request->TM_id;
        Pengumuman::create($validated);
        return redirect()->route('pengumuman.BAAK.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }


    public function editpengumuman($encryptedId){
        try {
            // Decrypt the encrypted ID
            $id = Crypt::decrypt($encryptedId);
        $pengumuman = Pengumuman::findOrFail($id);

        return view('pages.BAAK.pengumuman.edit', compact('pengumuman'));
    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
    }
    public function updatepengumuman(Request $request, $encryptedId)
    {
        
    $id = Crypt::decrypt($encryptedId);

       $validated= $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'status' => 'required|in:aktif,non-aktif',
            'file' => 'nullable|file|mimes:pdf,docx,jpg,jpeg,png|max:2048',
        ]);

        $pengumuman = Pengumuman::findOrFail($id);
        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($pengumuman->file && Storage::disk('public')->exists($pengumuman->file)) {
                Storage::disk('public')->delete($pengumuman->file);
            }
        
            $file = $request->file('file');
            $filePath = $file->store('pengumuman', 'public');
            $validated['file'] = $filePath;
        }
        $pengumuman->update($validated);

        return redirect()->route('pengumuman.BAAK.index')->with('success', 'Pengumuman berhasil diperbarui!');
    }


    public function destroypengumuman($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
    
        if ($pengumuman->status === 'aktif') {
            return back()->withErrors([
                'error' => 'Tidak dapat menghapus Tugas yang sedang Berlangsung.',
            ]);
        }
    
        // Hapus file dari storage jika ada
        if ($pengumuman->file && Storage::disk('public')->exists($pengumuman->file)) {
            Storage::disk('public')->delete($pengumuman->file);
        }
    
        $pengumuman->delete();
        return redirect()->route('pengumuman.BAAK.index')->with('success', 'Data tugas berhasil dihapus.');
    }

// public function pengujiIndex()
//     {   
//         $token = session('token');
//         $user_id = session('user_id');
//         $role_ids = [2,4];
//        $prodi_ids = DosenRole::where('user_id', $user_id)
//                           ->where('status', 'Aktif')
//                           ->where('role_id', $role_ids)
//                           ->pluck('prodi_id');
//         $TM_ids = DosenRole::where('user_id', $user_id)
//                             ->where('status', 'Aktif')
//                             ->where('role_id', $role_ids)
//                           ->pluck('TM_id');
//         $KPA_ids = DosenRole::where('user_id', $user_id)
//                           ->where('status', 'Aktif')
//                           ->where('role_id', $role_ids)
//                           ->pluck('KPA_id');
//         $prodi_ids = $prodi_ids->unique();
//         $TM_ids = $TM_ids->unique();
//         $KPA_ids = $KPA_ids->unique();
//         // Mengambil pengumuman yang hanya terkait dengan prodi_id yang sesuai dan status 'aktif'
//         $pengumuman = Pengumuman::with(['prodi','kategoriPA'])
//             ->wherein('prodi_id', $prodi_ids)
//             ->wherein('KPA_id', $KPA_ids)
//             ->wherein('TM_id', $TM_ids)
//             ->where('status', 'aktif')
//             ->get();
// // 
//         $responseDosen = Http::withHeaders([
//             'Authorization' =>"Bearer $token"
//         ])->get(env('API_URL'). "library-api/dosen");
//         if ($responseDosen->successful()) {
//             $dosen_list = $responseDosen->json()['data']['dosen'] ?? [];
//             // Buat map user_id => nama
//             $dosen_map = collect($dosen_list)->keyBy('user_id');
            
//             $pengumuman->each(function ($item) use ($dosen_map) {
//                 $item->nama = $dosen_map[$item->user_id]['nama'] ?? 'N/A';
//             });
//         } else {
//             // Tangani jika API gagal
//             $pengumuman->each(function ($item) {
//                 $item->nama = 'N/A'; // Tampilkan N/A jika API gagal
//             });
//         }
//         return view('pages.Penguji.dashboard', compact('pengumuman'));
//     }

    public function showPengumumanpenguji($id)
{
    // Find the pengumuman by its ID
    $pengumuman = Pengumuman::findOrFail($id);
    $token = session('token');

    $responseDosen = Http::withHeaders([
        'Authorization' =>"Bearer $token"
    ])->get(env('API_URL'). "library-api/dosen");
    if ($responseDosen->successful()) {
        $dosen_list = $responseDosen->json()['data']['dosen'] ?? [];
        // Buat map user_id => nama
        $dosen_map = collect($dosen_list)->keyBy('user_id');
        
        $pengumuman->nama = $dosen_map[$pengumuman->user_id]['nama'] ?? 'N/A';
        
    } else {
        // Tangani jika API gagal
        $pengumuman->nama = 'N/A'; // Tampilkan N/A jika API gagal
        
    }
    
    // Return the view with the pengumuman data
    return view('pages.Penguji.Pengumuman.show', compact('pengumuman'));
}

};
