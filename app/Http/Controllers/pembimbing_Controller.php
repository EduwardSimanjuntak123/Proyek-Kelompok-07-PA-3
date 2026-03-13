<?php

namespace App\Http\Controllers;

use App\Models\DosenRole;
use App\Models\Kelompok;
use App\Models\pembimbing;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Mpdf\Tag\P;
use Psy\TabCompletion\Matcher\FunctionDefaultParametersMatcher;

class pembimbing_Controller extends Controller
{
    public function index()
    {
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');

        // Ambil kelompok yang sudah digenerate
        $kelompok = Kelompok::with('pembimbing')
            ->where('prodi_id', $prodi_id)
            ->where('KPA_id', $KPA_id)
            ->where('TM_id', $TM_id)
            ->get();

        // Ambil data dosen dari tabel dosen
        $dosen = Dosen::get()->keyBy('user_id');

        // Tambahkan nama dosen ke pembimbing
        $kelompok->each(function ($item) use ($dosen) {

            $item->pembimbing->each(function ($pb) use ($dosen) {

                $pb->nama = $dosen[$pb->user_id]->nama ?? '-';

            });

        });

        return view('pages.Koordinator.pembimbing.index', compact('kelompok'));
    }
    public function indexpembimbing2()
    {
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


        return view('pages.Koordinator.pembimbing.indexp2', compact('pembimbing'));
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
        $dosen = Dosen::where('prodi_id', 4)->get(); // filter prodi_id sesuai kebutuhan

        $dosenFinal = $dosen->map(function ($d) use ($dosenApiMap) {
            $apiData = $dosenApiMap->get($d->user_id);

            return [
                'user_id' => $d->user_id,
                'nama' => $apiData['nama'] ?? $d->nama ?? 'Nama Tidak Diketahui',
            ];
        });

        return view('pages.Koordinator.pembimbing.create', [
            'dosen' => $dosenFinal,
            'kelompok_id' => $kelompok_id
        ]);
    }
    public function createpembimbing2()
    {
        $token = session('token');

        //  Ambil data dosen dari API eksternal
        $responseDosen = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL') . "library-api/dosen", ['limit' => 100]);

        $dosenApiMap = collect();
        // Buat map user_id => nama
        if ($responseDosen->successful()) {
            $dosenlist = $responseDosen->json()['data']['dosen'] ?? [];
            $dosenApiMap = collect($dosenlist)->keyBy('user_id');
        }
        //ambil data dosen berdasarkan session
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');

        $dosen = DosenRole::with(['prodi', 'tahunMasuk', 'kategoripa', 'role'])
            ->where('prodi_id', $prodi_id)
            ->where('KPA_id', $KPA_id)
            ->where('TM_id', $TM_id)
            ->where('role_id', '5')
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
        return view('pages.Koordinator.pembimbing.createp2', [
            'dosen' => $dosenFinal,
            'kelompok' => $kelompokbelummasuk,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelompok_id' => 'required',
            'pembimbing1' => 'required',
            'pembimbing2' => 'nullable|different:pembimbing1'
        ]);

        pembimbing::create([
            'user_id' => $request->pembimbing1,
            'kelompok_id' => $request->kelompok_id
        ]);

        if ($request->pembimbing2) {

            pembimbing::create([
                'user_id' => $request->pembimbing2,
                'kelompok_id' => $request->kelompok_id
            ]);

        }

        return redirect()->route('pembimbing.index')
            ->with('success', 'Pembimbing berhasil disimpan');
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
            // AMBIL PEMBIMBING KELOMPOK
            // ======================
            $pembimbing = pembimbing::where('kelompok_id', $kelompok_id)->get();

            $pembimbing1 = $pembimbing->first();
            $pembimbing2 = $pembimbing->skip(1)->first();

            // ======================
            // AMBIL NAMA DOSEN DARI TABEL DOSEN
            // ======================
            $dosenPembimbing1 = $pembimbing1 ? Dosen::where('user_id', $pembimbing1->user_id)->first() : null;
            $dosenPembimbing2 = $pembimbing2 ? Dosen::where('user_id', $pembimbing2->user_id)->first() : null;

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

            return view('pages.Koordinator.pembimbing.edit', [
                'dosen' => $dosenlist,
                'pembimbing1' => $pembimbing1,
                'pembimbing2' => $pembimbing2,
                'dosenPembimbing1' => $dosenPembimbing1,
                'dosenPembimbing2' => $dosenPembimbing2,
                'kelompok' => $kelompok,
                'kelompok_id' => $kelompok_id
            ]);

        } catch (\Exception $e) {
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
                ->where('role_id', '5')
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
            $kelompokIdsudahpunyapembimbing = DB::table('pembimbing')
                ->join('dosen_roles', 'pembimbing.user_id', '=', 'dosen_roles.user_id')
                ->join('roles', 'dosen_roles.role_id', '=', 'roles.id')
                ->where('roles.role_name', 'pembimbing 2')
                ->pluck('kelompok_id')->toArray();
            $kelompokbelummasuk = $Kelompok->filter(function ($klmpk) use ($kelompokIdsudahpunyapembimbing) {
                return !in_array($klmpk['id'], $kelompokIdsudahpunyapembimbing);
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
    $kelompok_id = Crypt::decrypt($encryptedId);

    $request->validate([
        'pembimbing1' => 'required',
        'pembimbing2' => 'nullable'
    ], [
        'pembimbing1.required' => 'Pilih Pembimbing 1 terlebih dahulu'
    ]);

    // ambil semua pembimbing untuk kelompok
    $pembimbing = pembimbing::where('kelompok_id', $kelompok_id)->get();

    // pembimbing 1
    if ($pembimbing1 = $pembimbing->first()) {
        $pembimbing1->user_id = $request->pembimbing1;
        $pembimbing1->save();
    } else {
        pembimbing::create([
            'kelompok_id' => $kelompok_id,
            'user_id' => $request->pembimbing1
        ]);
    }

    // pembimbing 2
    if ($request->pembimbing2) {
        if ($pembimbing2 = $pembimbing->skip(1)->first()) {
            $pembimbing2->user_id = $request->pembimbing2;
            $pembimbing2->save();
        } else {
            pembimbing::create([
                'kelompok_id' => $kelompok_id,
                'user_id' => $request->pembimbing2
            ]);
        }
    }

    return redirect()->route('pembimbing.index')
        ->with('success', 'Data pembimbing berhasil diperbarui');
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

    public function destroy($encryptedId)
{
    try {
        $kelompok_id = Crypt::decrypt($encryptedId);

        // Hapus semua pembimbing untuk kelompok ini
        pembimbing::where('kelompok_id', $kelompok_id)->delete();

        return redirect()->back()->with('success', 'Semua dosen pembimbing berhasil dihapus.');
    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Gagal menghapus pembimbing: ' . $e->getMessage());
    }
}
}
