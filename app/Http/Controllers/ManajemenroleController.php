<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Exception;
use App\Models\DosenRole;
use App\Models\kategoriPA;
use App\Models\Prodi;
use App\Models\Role;
use App\Models\TahunAjaran;
use App\Models\TahunMasuk;
use Illuminate\Support\Facades\Http;

class ManajemenroleController extends Controller
{

    
    public function index()
    {
        $token = session('token');
        
        // Ambil data dosen_roles dengan relasi prodi, role, tahun ajaran
        $dosenroles = DosenRole::with(['prodi', 'role', 'tahunMasuk','kategoripa'])->get();
        
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
            $dosenroles->transform(function ($role) use ($dosen_map) {
                $role->nama = $dosen_map[$role->user_id]['nama'] ?? 'N/A';
                return $role;
            });
        } else {
            // Tangani jika API gagal
            $dosenroles->each(function ($role) {
                $role->nama = 'N/A'; // Tampilkan N/A jika API gagal
            });
        }
    
        // Kembalikan view dengan data dosenroles
        return view('pages.BAAK.Kordinator.index', compact('dosenroles'));
    }
    
    public function create()
    {
        $token = session('token');
    
        // Ambil data dosen dari API eksternal
        $responseDosen = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL') . "library-api/dosen", ['limit' => 100]);
    
        $dosen = $responseDosen->successful() ? $responseDosen->json()['data']['dosen'] ?? [] : [];
    
        // Ambil data dari tabel menggunakan Eloquent
        $prodi = Prodi::all();
        $role = Role::all();
        $tahun_masuk = TahunMasuk::where('Status','Aktif')->get();
        $kategoripa =kategoriPA::all();
    
        return view('pages.BAAK.Kordinator.create', compact('dosen', 'prodi', 'role', 'tahun_masuk','kategoripa'));
    }
    public function store(Request $request)
    {
        // Validasi input umum
        $validated = $request->validate([
            'user_id'   => 'required|numeric',
            'role_id'   => 'required|exists:roles,id',
            'prodi_id'  => 'required|exists:prodi,id',
            'KPA_id'  => 'required|exists:kategori_pa,id',
            'TM_id'     => 'required|exists:tahun_masuk,id',
            'status'    => 'required|in:Aktif,Tidak-Aktif',
            'Tahun_Ajaran' =>'required',
              // Pastikan status benar
        ]);
    
        // Tentukan status, jika tidak ada gunakan 'Aktif' sebagai default
        $status = $validated['status'];
    
        // Ambil role_name berdasarkan role_id
        $role = Role::find($validated['role_id']);
        $rolesToCheck = ['penguji 1', 'pembimbing 1', 'penguji 2', 'pembimbing 2']; 
        if (in_array(strtolower($role->role_name), $rolesToCheck)) {
            if($validated['status'] === 'Aktif'){
                $existingDosen = DosenRole::where('user_id', $validated['user_id'])
                ->where('role_id',$validated['role_id'])
                ->where('prodi_id', $validated['prodi_id'])
                ->where('KPA_id', $validated['KPA_id'])
                ->where('TM_id', $validated['TM_id'])
                ->where('status','Aktif')
                ->exists();
                if ($existingDosen) {
                    return back()->withErrors([
                        'user_id' => 'Dosen ini sudah terdaftar sebagai ' . $role->role_name . ' untuk kombinasi Prodi, PA, dan Tahun Ajaran ini.',
                    ])->withInput();
                }
            }
        
        }
        if (strtolower($role->role_name) === 'koordinator') {

            // Jika status yang dikirim adalah Aktif, cek apakah sudah pernah jadi Koordinator Aktif
            if ($validated['status'] === 'Aktif') {
                $existingKoordinatorAktif = DosenRole::where('user_id', $validated['user_id'])
                    ->where('role_id', $validated['role_id'])
                    ->where('status', 'Aktif') // hanya cek yang Aktif
                    ->exists();
        
                if ($existingKoordinatorAktif) {
                    return back()->withErrors([
                        'user_id' => 'Dosen ini sudah pernah menjadi Koordinator Aktif dan tidak bisa menjadi Koordinator lagi.',
                    ])->withInput();
                }
            }
        
            // Validasi kombinasi yang sama tidak boleh ganda (terlepas dari status)
            $existingPerDosen = DosenRole::where('user_id', $validated['user_id'])
                ->where('prodi_id', $validated['prodi_id'])
                ->where('KPA_id', $validated['KPA_id'])
                ->where('TM_id', $validated['TM_id'])
                ->where('role_id', $validated['role_id'])
                ->first();
        
            if ($existingPerDosen) {
                return back()->withErrors([
                    'user_id' => 'Dosen ini sudah menjadi Koordinator di Prodi, PA, dan Tahun Ajaran tersebut.',
                ])->withInput();
            }
        
            // Validasi hanya satu Koordinator Aktif per kombinasi PA + Prodi + Tahun
            if ($validated['status'] === 'Aktif') {
                $existingGlobal = DosenRole::where('role_id', $validated['role_id'])
                    ->where('TM_id', $validated['TM_id'])
                    ->where('prodi_id', $validated['prodi_id'])
                    ->where('KPA_id', $validated['KPA_id'])
                    ->where('status', 'Aktif')
                    ->exists();
        
                if ($existingGlobal) {
                    return back()->withErrors([
                        'KPA_id' => 'Sudah ada Koordinator Aktif untuk PA ' . $validated['KPA_id'] . ' pada Tahun Ajaran ini.',
                    ])->withInput();
                }
            }
        }
        
    
        // Simpan data
        DosenRole::create([
            'user_id'   => $validated['user_id'],
            'role_id'   => $validated['role_id'],
            'prodi_id'  => $validated['prodi_id'],
            'KPA_id'  => $validated['KPA_id'],
            'TM_id'     => $validated['TM_id'],
            'Tahun_Ajaran'=>$validated['Tahun_Ajaran'],
            'status'    => $status,  // Pastikan status dikirimkan dengan benar
        ]);
    
        return redirect()->route('manajemen-role.index')->with('success', 'Data berhasil disimpan.');
    }    
    
    public function edit($id)
    {
        $token = session('token');
        $id = Crypt::decrypt($id);
    
        $dosenRole = DosenRole::findOrFail($id);
    
        // Ambil data dosen dari API eksternal
        $responseDosen = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL') . "library-api/dosen", ['limit' => 100]);
    
        $dosen = $responseDosen->successful() ? $responseDosen->json()['data']['dosen'] ?? [] : [];
    
        $prodi = Prodi::all();
        $role = Role::all();
        $tahun_masuk = TahunMasuk::all();
        $kategoripa =kategoriPA::all();
    
    
        return view('pages.BAAK.Kordinator.edit', compact('dosenRole', 'dosen', 'prodi', 'role', 'tahun_masuk','kategoripa'));
    }
    public function update(Request $request, $id)
{
    $id = Crypt::decrypt($id);
    
    // Validasi input umum
    $validated = $request->validate([
        'user_id'   => 'required|numeric',
        'role_id'   => 'required|exists:roles,id',
        'prodi_id'  => 'required|exists:prodi,id',
        'KPA_id'  => 'required|exists:kategori_pa,id',
        'TM_id'     => 'required|exists:tahun_masuk,id',
        'status'    => 'required|in:Aktif,Tidak-Aktif',
        'Tahun_Ajaran' => 'required'
    ]);
    
    // Ambil data dosen_role berdasarkan id yang didekripsi
    $dosenRole = DosenRole::findOrFail($id);
    
    // Ambil role berdasarkan role_id
    $role = Role::find($validated['role_id']);
    $rolesToCheck = ['penguji 1', 'pembimbing 1', 'penguji 2', 'pembimbing 2']; 
    if (in_array(strtolower($role->role_name), $rolesToCheck)) {
        if($validated['status'] === 'Aktif'){
            $existingDosen = DosenRole::where('user_id', $validated['user_id'])
            ->where('role_id',$validated['role_id'])
            ->where('prodi_id', $validated['prodi_id'])
            ->where('KPA_id', $validated['KPA_id'])
            ->where('TM_id', $validated['TM_id'])
            ->where('status','Aktif')
            ->exists();
            if ($existingDosen) {
                return back()->withErrors([
                    'user_id' => 'Dosen ini sudah terdaftar sebagai ' . $role->role_name . ' untuk kombinasi Prodi, PA, dan Tahun Ajaran ini.',
                ])->withInput();
            }
        }
    
    }
    // Jika role adalah Koordinator, lakukan validasi
    if (strtolower($role->role_name) === 'koordinator') {

    // Jika status ingin diubah menjadi "Aktif", baru lakukan semua validasi
    if ($validated['status'] === 'Aktif') {

        // Validasi: dosen tidak boleh punya entri koordinator untuk kombinasi ini
        $existingPerDosen = DosenRole::where('user_id', $validated['user_id'])
            ->where('prodi_id', $validated['prodi_id'])
            ->where('KPA_id', $validated['KPA_id'])
            ->where('TM_id', $validated['TM_id'])
            ->where('role_id', $validated['role_id'])
            ->where('id', '<>', $dosenRole->id) // Hindari konflik dengan data yang sedang diedit
            ->first();

        if ($existingPerDosen) {
            return back()->withErrors([
                'user_id' => 'Dosen ini sudah menjadi Koordinator di Prodi, PA, dan Tahun Ajaran tersebut.',
            ])->withInput();
        }

        // Validasi: hanya satu koordinator Aktif untuk kombinasi PA dan TM_id
        $existingGlobal = DosenRole::where('KPA_id', $validated['KPA_id'])
            ->where('TM_id', $validated['TM_id'])
            ->where('prodi_id', $validated['prodi_id'])
            ->where('role_id', $validated['role_id'])
            ->where('status', 'Aktif')
            ->where('id', '<>', $dosenRole->id) // hindari data yang sedang diedit
            ->exists();

        if ($existingGlobal) {
            return back()->withErrors([
                'KPA_id' => 'Sudah ada Koordinator Aktif untuk PA ' . $validated['KPA_id'] . ' pada Tahun Ajaran ini.',
            ])->withInput();
        }

        // Validasi: dosen tidak boleh pernah menjadi koordinator AKTIF sebelumnya (selain entri ini)
        $pernahKoordinatorAktif = DosenRole::where('user_id', $validated['user_id'])
            ->where('role_id', $validated['role_id'])
            ->where('status', 'Aktif')
            ->where('id', '<>', $dosenRole->id)
            ->exists();

        if ($pernahKoordinatorAktif) {
            return back()->withErrors([
                'user_id' => 'Dosen ini sudah menjadi Koordinator Aktif dan tidak bisa menjadi Koordinator lagi.',
            ])->withInput();
        }
    }
}


    // Lakukan pembaruan data dosenRole
    $dosenRole->update($validated);
    
    return redirect()->route('manajemen-role.index')->with('success', 'Data berhasil diperbarui.');
}

public function destroy($id)
{
    // Cari data dosenRole berdasarkan id
    $dosenRole = DosenRole::findOrFail($id);

    // Cek apakah status adalah 'Aktif'
    if ($dosenRole->status === 'Aktif') {
        // Tampilkan pesan kesalahan jika status masih Aktif
        return back()->withErrors([
            'error' => 'Tidak dapat menghapus data Dosen Role yang sedang aktif.',
        ]);
    }

    // Hapus data jika status adalah 'Tidak-Aktif'
    $dosenRole->delete();

    // Redirect ke halaman koordinator dengan pesan sukses
    return redirect()->route('manajemen-role.index')->with('success', 'Data berhasil dihapus.');
}  
}  