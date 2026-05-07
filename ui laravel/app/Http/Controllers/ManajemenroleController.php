<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Exception;
use App\Models\Dosen;
use App\Models\DosenRole;
use App\Models\kategoriPA;
use App\Models\Prodi;
use App\Models\Role;
use App\Models\TahunAjaran;
use App\Models\TahunMasuk;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Http\Controllers\TahunAJaran_Controller;

class ManajemenroleController extends Controller
{
    private function getDosenList(string|null $token): array
    {
        $localDosen = Dosen::select('user_id', 'nama')
            ->orderBy('nama')
            ->get()
            ->map(function ($item) {
                return [
                    'user_id' => $item->user_id,
                    'nama' => $item->nama,
                ];
            })
            ->toArray();

        try {
            $responseDosen = Http::withHeaders([
                'Authorization' => "Bearer $token"
            ])->get(env('API_URL') . "library-api/dosen", ['limit' => 1000]);

            if ($responseDosen->successful()) {
                $dosen = $responseDosen->json()['data']['dosen'] ?? [];

                if (!empty($dosen)) {
                    return collect($localDosen)
                        ->merge($dosen)
                        ->unique('user_id')
                        ->sortBy('nama')
                        ->values()
                        ->toArray();
                }
            }
        } catch (Exception $e) {
            Log::error('Error fetching dosen from API: ' . $e->getMessage());
        }

        return $localDosen;
    }

    private function getDosenName($userId, string|null $token): string
    {
        $dosen = Dosen::where('user_id', $userId)->first();

        if ($dosen && !empty($dosen->nama)) {
            return $dosen->nama;
        }

        $dosenApiMap = collect($this->getDosenList($token))->keyBy('user_id');

        return $dosenApiMap[$userId]['nama'] ?? 'N/A';
    }

    
    public function index()
    {
        $token = session('token');
        $filterRole = request('filter_role');
        
        // Ambil data dosen_roles dengan relasi prodi, role, tahun ajaran
        $dosenroles = DosenRole::with(['prodi', 'role', 'tahunMasuk','kategoripa','tahunAjaran'])
            ->when($filterRole, function ($query, $filterRole) {
                if ($filterRole === 'koordinator') {
                    $query->whereHas('role', function ($q) {
                        $q->where('role_name', 'Koordinator');
                    });
                } elseif ($filterRole === 'penguji') {
                    $query->whereHas('role', function ($q) {
                        $q->where('role_name', 'like', 'Penguji%');
                    });
                } elseif ($filterRole === 'pembimbing') {
                    $query->whereHas('role', function ($q) {
                        $q->where('role_name', 'like', 'Pembimbing%');
                    });
                }
            })
            ->get();
        
        $dosenMap = Dosen::whereIn('user_id', $dosenroles->pluck('user_id')->unique())
            ->get()
            ->keyBy('user_id');
        $dosenApiMap = collect($this->getDosenList($token))->keyBy('user_id');

        // Jika nama tidak ada, coba ambil dari Dosen table atau API
        $dosenroles->transform(function ($role) use ($dosenMap, $dosenApiMap) {
            if (empty($role->nama)) {
                // Coba dari Dosen table
                $dosen = $dosenMap->get($role->user_id);
                if ($dosen) {
                    $role->nama = $dosen->nama;
                } else {
                    $role->nama = $dosenApiMap[$role->user_id]['nama'] ?? 'N/A';
                }
            }
            return $role;
        });

        // Kembalikan view dengan data dosenroles
        return view('pages.BAAK.Kordinator.index', compact('dosenroles', 'filterRole'));
    }
    

public function create()
{
    $token = session('token');

    $dosen = $this->getDosenList($token);

    $prodi = Prodi::all();

    // HANYA KOORDINATOR
    $role = Role::where('role_name', 'Koordinator')->first();

    $tahun_masuk = TahunMasuk::where('Status', 'Aktif')->get();
    $kategoripa = kategoriPA::all();

    $tahunAjaranAktif = TahunAJaran_Controller::getTahunAjaranAktif();

    return view('pages.BAAK.Kordinator.create', compact(
        'dosen',
        'prodi',
        'role',
        'tahun_masuk',
        'kategoripa',
        'tahunAjaranAktif'
    ));
}

public function store(Request $request)
    {
        // dd($request);
        // Validasi input umum
        $validated = $request->validate([
            'user_id'   => 'required|numeric',
            'role_id'   => 'required|exists:roles,id',
            'prodi_id'  => 'required|exists:prodi,id',
            'KPA_id'  => 'required|exists:kategori_pa,id',
            'TM_id'     => 'required|exists:tahun_masuk,id',
            'tahun_ajaran_id'     => 'required',
            'status'    => 'required|in:Aktif,Tidak-Aktif'
              // Pastikan status benar
        ]);
    
        // Tentukan status, jika tidak ada gunakan 'Aktif' sebagai default
        $status = $validated['status'];
    
        // Ambil role_name berdasarkan role_id
        $role = Role::find($validated['role_id']);

if(strtolower($role->role_name) !== 'koordinator'){
    return back()->withErrors([
        'role_id' => 'BAAK hanya boleh menambahkan role Koordinator.'
    ]);
}
        $rolesToCheck = ['penguji 1', 'pembimbing 1', 'penguji 2', 'pembimbing 2']; 
        if (in_array(strtolower($role->role_name), $rolesToCheck)) {
            if($validated['status'] === 'Aktif'){
                $existingDosen = DosenRole::where('user_id', $validated['user_id'])
                ->where('role_id',$validated['role_id'])
                ->where('prodi_id', $validated['prodi_id'])
                ->where('KPA_id', $validated['KPA_id'])
                ->where('TM_id', $validated['TM_id'])
                ->where('tahun_ajaran_id', $validated['tahun_ajaran_id'])
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
                ->where('tahun_ajaran_id', $validated['tahun_ajaran_id'])

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
                    ->where('tahun_ajaran_id', $validated['tahun_ajaran_id'])

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
            'nama'      => $this->getDosenName($validated['user_id'], session('token')),
            'role_id'   => $validated['role_id'],
            'prodi_id'  => $validated['prodi_id'],
            'KPA_id'  => $validated['KPA_id'],
            // 'KPA_id'  => $validated['KPA_id'],
            'TM_id'     => $validated['TM_id'],
            'tahun_ajaran_id'=>$validated['tahun_ajaran_id'],
            'status'    => $status,  // Pastikan status dikirimkan dengan benar
        ]);
    
        return redirect()->route('manajemen-role.index')->with('success', 'Data berhasil disimpan.');
    }    
    
   public function edit($id)
{
    $token = session('token');
    $id = Crypt::decrypt($id);

    $dosenRole = DosenRole::findOrFail($id);

    $dosen = $this->getDosenList($token);

    $prodi = Prodi::all();
    $tahun_masuk = TahunMasuk::all();
    $kategoripa = kategoriPA::all();

    return view('pages.BAAK.Kordinator.edit', compact(
        'dosenRole',
        'dosen',
        'prodi',
        'tahun_masuk',
        'kategoripa'
    ));
}
   public function update(Request $request, $id)
{
    $id = Crypt::decrypt($id);

    $validated = $request->validate([
        'user_id'   => 'required|numeric',
        'role_id'   => 'required|exists:roles,id',
        'prodi_id'  => 'required|exists:prodi,id',
        'KPA_id'    => 'required|exists:kategori_pa,id',
        'TM_id'     => 'required|exists:tahun_masuk,id',
        'status'    => 'required|in:Aktif,Tidak-Aktif',
    ]);

    $dosenRole = DosenRole::findOrFail($id);
    $role = Role::find($validated['role_id']);

    $rolesToCheck = ['penguji 1', 'pembimbing 1', 'penguji 2', 'pembimbing 2'];

    if (in_array(strtolower($role->role_name), $rolesToCheck)) {

        if ($validated['status'] === 'Aktif') {

            $existingDosen = DosenRole::where('user_id', $validated['user_id'])
                ->where('role_id', $validated['role_id'])
                ->where('prodi_id', $validated['prodi_id'])
                ->where('KPA_id', $validated['KPA_id'])
                ->where('TM_id', $validated['TM_id'])
                ->where('status', 'Aktif')
                ->where('id', '<>', $dosenRole->id)
                ->exists();

            if ($existingDosen) {
                return back()->withErrors([
                    'user_id' => 'Dosen ini sudah terdaftar sebagai ' . $role->role_name . ' untuk kombinasi Prodi, PA, dan Tahun Masuk ini.',
                ])->withInput();
            }
        }
    }

    // VALIDASI KHUSUS KOORDINATOR
    if (strtolower($role->role_name) === 'koordinator') {

        if ($validated['status'] === 'Aktif') {

            // Cek per dosen
            $existingPerDosen = DosenRole::where('user_id', $validated['user_id'])
                ->where('prodi_id', $validated['prodi_id'])
                ->where('KPA_id', $validated['KPA_id'])
                ->where('TM_id', $validated['TM_id'])
                ->where('role_id', $validated['role_id'])
                ->where('id', '<>', $dosenRole->id)
                ->first();

            if ($existingPerDosen) {
                return back()->withErrors([
                    'user_id' => 'Dosen ini sudah menjadi Koordinator pada kombinasi tersebut.',
                ])->withInput();
            }

            // Cek global (hanya satu aktif)
            $existingGlobal = DosenRole::where('KPA_id', $validated['KPA_id'])
                ->where('TM_id', $validated['TM_id'])
                ->where('prodi_id', $validated['prodi_id'])
                ->where('role_id', $validated['role_id'])
                ->where('status', 'Aktif')
                ->where('id', '<>', $dosenRole->id)
                ->exists();

            if ($existingGlobal) {
                return back()->withErrors([
                    'KPA_id' => 'Sudah ada Koordinator Aktif untuk kombinasi ini.',
                ])->withInput();
            }

            // Cek pernah aktif sebelumnya
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

    $validated['nama'] = $this->getDosenName($validated['user_id'], session('token'));

    $dosenRole->update($validated);

    return redirect()->route('manajemen-role.index')
        ->with('success', 'Data berhasil diperbarui.');
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
