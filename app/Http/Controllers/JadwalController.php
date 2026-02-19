<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\Models\Jadwal;
use App\Models\Kelompok;
use App\Models\Prodi;
use App\Models\DosenRole;
use App\Models\kategoriPA;
use App\Models\TahunMasuk;
use App\Models\Ruangan;
use App\Models\Role;
use App\Models\PengajuanSeminar;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class JadwalController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userID = session('user_id');
            if (!$userID) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
            }
    
            $jadwal = Jadwal::with(['kelompok.pembimbing', 'kelompok.penguji'])
                ->where('user_id', $userID)
                ->orderBy('created_at', 'desc')
                ->get();
    
            $token = session('token');
            $responseDosen = Http::withHeaders([
                'Authorization' => "Bearer $token"
            ])->get(env('API_URL') . "library-api/dosen", ['limit' => 100]);
    
            $dosenData = $responseDosen->successful() ? $responseDosen->json()['data']['dosen'] : [];
    
            $dosenArray = [];
            foreach ($dosenData as $dosen) {
                $dosenArray[$dosen['user_id']] = $dosen['nama'];
            }
    
            foreach ($jadwal as $item) {
                // Set Nama Pembimbing
                if ($item->kelompok && $item->kelompok->pembimbing->isNotEmpty()) {
                    $pembimbingNames = [];
                    foreach ($item->kelompok->pembimbing as $pembimbing) {
                        $namaPembimbing = $dosenArray[$pembimbing->user_id] ?? 'Tidak Ditemukan';
                        $pembimbingNames[] = $namaPembimbing;
                    }
                    $item->pembimbing_nama = implode(', ', $pembimbingNames);
                } else {
                    $item->pembimbing_nama = '-';
                }
    
                // Set Nama Penguji
                if ($item->kelompok && $item->kelompok->penguji->isNotEmpty()) {
                    $pengujiNames = [];
                    foreach ($item->kelompok->penguji as $penguji) {
                        $namaPenguji = $dosenArray[$penguji->user_id] ?? 'Tidak Ditemukan';
                        $pengujiNames[] = $namaPenguji;
                    }
                    $item->penguji_nama = implode('<br>', $pengujiNames);
                } else {
                    $item->penguji_nama = '-';
                }
            }
    
            return view('pages.Koordinator.jadwal.index', compact('jadwal'));
    
        } catch (Exception $e) {
            Log::error('Error fetching jadwal: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengambil data jadwal');
        }
    }

    public function create()
    {
        try {
            $userID = session('user_id');
            $token = session('token');
            $KPA_id = session('KPA_id');
            $prodi_id = session('prodi_id');
            $TM_id = session('TM_id');
            $role_id = session('role_id');
    
            if (!$userID || !$token) {
                return redirect()->route('login')->with('error', 'Sesi telah berakhir');
            }
    
            // Only show groups that have approved seminar submissions
            $kelompok = Kelompok::where('KPA_id', $KPA_id)
                ->where('prodi_id', $prodi_id)
                ->where('TM_id', $TM_id)
                ->whereHas('pengajuanSeminar', function($query) {
                    $query->where('status', 'disetujui');
                })
                ->whereDoesntHave('jadwal') // Ensure no schedule exists yet
                ->with('pengajuanSeminar') // Eager load pengajuanSeminar
                ->get();
    
            $kategoriPA = kategoriPA::find($KPA_id);
            $prodi = Prodi::find($prodi_id);
            $tahunMasuk = TahunMasuk::find($TM_id);
            $ruangan = Ruangan::all();
    
            if ($kelompok->isEmpty()) {
                // Check if there are groups with unapproved submissions
                $hasUnapprovedGroups = Kelompok::where('KPA_id', $KPA_id)
                    ->where('prodi_id', $prodi_id)
                    ->where('TM_id', $TM_id)
                    ->whereHas('pengajuanSeminar', function($query) {
                        $query->where('status', '!=', 'disetujui');
                    })
                    ->exists();

                    // cek apakah  semua kelompok yang disetujui sudah mempunyai kelompok 

               // Cek apakah semua kelompok yang disetujui sudah memiliki jadwal
            $allApprovedHaveSchedules = Kelompok::where('KPA_id', $KPA_id)
                ->where('prodi_id', $prodi_id)
                ->where('TM_id', $TM_id)
                ->whereHas('pengajuanSeminar', function($query) {
                    $query->where('status', 'disetujui');
                })
                ->whereHas('jadwal')
                ->exists();
            
            if ($hasUnapprovedGroups) {
                $message = 'Pengajuan Seminar belum disetujui oleh dosen pembimbing.';
            } elseif ($allApprovedHaveSchedules) {
                $message = 'Semua kelompok dengan pengajuan seminar yang disetujui sudah memiliki jadwal.';
            } else {
                $message = 'Tidak ada kelompok yang tersedia.';
            }

            return redirect()->route('jadwal.index')
                ->with('warning', $message)
                ->with('showUnapprovedAlert', $hasUnapprovedGroups);
        }

        return view('pages.Koordinator.jadwal.create', compact('kelompok','kategoriPA','prodi','tahunMasuk', 'ruangan'));

    } catch (Exception $e) {
        Log::error('Error loading create form: ' . $e->getMessage());
        return back()->with('error', 'Gagal memuat form');
    }
}

    public function edit($id)
    {
        try {
            $id = Crypt::decrypt($id);
            $token = session('token');
            $KPA_id = session('KPA_id');
            $prodi_id = session('prodi_id');
            $TM_id = session('TM_id');

            $jadwal = Jadwal::findOrFail($id);
            $kelompok = Kelompok::where('KPA_id', $KPA_id)
                ->where('prodi_id', $prodi_id)
                ->where('TM_id', $TM_id)
                ->get();
            $ruangan = Ruangan::all();
            $responseDosen = Http::withHeaders([
                'Authorization' => "Bearer $token"
            ])->get(env('API_URL') . "library-api/dosen", ['limit' => 100]);

            $dosen = collect($responseDosen->json()['data']['dosen'] ?? []);

            $role = Role::all();

            return view('pages.Koordinator.jadwal.edit', compact('jadwal', 'kelompok', 'dosen', 'role','ruangan'));

        } catch (Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat form edit');
        }
    }
        public function store(Request $request)
        {
            try {
                $userID = session('user_id');
            if (!$userID) {
                return redirect()->route('login')->with('error', 'Sesi telah berakhir');
            }
                $validated = $request->validate([
                    'kelompok_id' => [
                        'required', 
                        function($attribute, $value, $fail) use($request) {
                            if (Jadwal::where('kelompok_id', $value)
                                ->where('KPA_id', $request->KPA_id)
                                ->where('prodi_id', $request->prodi_id)
                                ->where('TM_id', $request->TM_id)
                                ->exists()) {
                                $fail('Jadwal untuk kelompok ini sudah ada.');
                            }
                            
                            $hasApprovedSubmission = PengajuanSeminar::where('kelompok_id', $value)
                                ->where('status', 'disetujui')
                                ->exists();
        
                            if (!$hasApprovedSubmission) {
                                $fail('Kelompok ini belum memiliki pengajuan seminar yang disetujui.');
                            }
                        }
                    ],
                    // 'ruangan_id' => 'required|exists:ruangan,id',
                    // 'waktu_mulai' => 'required|date|after:now',
                    // 'waktu_selesai' => 'required|date|after:waktu_mulai',
                    'ruangan_id' => [
                        'required',
                        'exists:ruangan,id',
                            function ($attribute, $value, $fail) use ($request) {
                                $waktuMulai = $request->input('waktu_mulai');
                                $waktuSelesai = $request->input('waktu_selesai');
                                if (!$waktuMulai || !$waktuSelesai) return;
                                $bentrokRuangan = Jadwal::where('ruangan_id', $value)
                                    ->where(function ($query) use ($waktuMulai, $waktuSelesai) {
                                        $query->whereBetween('waktu_mulai', [$waktuMulai, $waktuSelesai])
                                            ->orWhereBetween('waktu_selesai', [$waktuMulai, $waktuSelesai])
                                            ->orWhere(function ($q) use ($waktuMulai, $waktuSelesai) {
                                                $q->where('waktu_mulai', '<=', $waktuMulai)
                                                ->where('waktu_selesai', '>=', $waktuSelesai);
                                            });
                                    })
                                    ->exists();
                                if ($bentrokRuangan) {
                                    $fail('Ruangan sudah terpakai pada waktu tersebut.');
                                }
                            }
                        ],
                        'waktu_mulai' => [
                            'required', 'date', 'after:now',
                            function($attribute, $value, $fail) use($request) {
                                // $waktuMulai = $value;
                                $waktuMulai = \Carbon\Carbon::parse($value);
                                $waktuSelesai = $request->waktu_selesai;

                                if(($waktuMulai->hour >= 0 && $waktuMulai->hour < 8 || $waktuMulai -> hour >= 17)){
                                    $fail("Waktu mulai hanya bisa di antara pukul 08.00 hingga 17.00");
                                    return;
                                }

                                if (!$waktuSelesai) return;

                                $bentrok = Jadwal::where('KPA_id', $request->KPA_id)
                                    ->where('prodi_id', $request->prodi_id)
                                    ->where('TM_id', $request->TM_id)
                                    ->where(function ($query) use ($waktuMulai, $waktuSelesai) {
                                        $query->whereBetween('waktu_mulai', [$waktuMulai, $waktuSelesai])
                                            ->orWhereBetween('waktu_selesai', [$waktuMulai, $waktuSelesai])
                                            ->orWhere(function ($q) use ($waktuMulai, $waktuSelesai) {
                                                $q->where('waktu_mulai', '<=', $waktuMulai)
                                                ->where('waktu_selesai', '>=', $waktuSelesai);
                                            });
                                    })
                                    ->exists();

                                if ($bentrok) {
                                    $fail("Sudah ada jadwal untuk waktu ini pada program dan tahun masuk yang sama.");
                                }
                            }
                        ],
                        'waktu_selesai' => [
                            'required', 'date', 'after:waktu_mulai',
                            function($attribute, $value, $fail) use ($request) {
                                $mulai = strtotime($request->waktu_mulai);
                                $selesai = strtotime($value);
                                $diffInMinutes = ($selesai - $mulai) / 60;

                                if ($diffInMinutes < 60) {
                                    $fail("Waktu selesai minimal harus 1 jam setelah waktu mulai.");
                                } elseif ($diffInMinutes > 120) {
                                    $fail("Waktu selesai maksimal hanya boleh 2 jam setelah waktu mulai.");
                                }
                            }
                        ],
                    'KPA_id' => 'required|exists:kategori_pa,id',
                    'prodi_id' => 'required|exists:prodi,id',
                    'TM_id' => 'required|exists:tahun_masuk,id',
                ]);
        

                $jadwal=Jadwal::create([
                    'kelompok_id' => $validated['kelompok_id'],
                    'ruangan_id' => $validated['ruangan_id'],
                    'waktu_mulai' => $validated['waktu_mulai'],
                    'waktu_selesai' => $validated['waktu_selesai'],
                    'user_id' => $userID,
                    'KPA_id' => $validated['KPA_id'],
                    'prodi_id' => $validated['prodi_id'],
                    'TM_id' => $validated['TM_id'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);    
               
        // Ambil device token dari mahasiswa kelompok tersebut
        $tokens = DB::table('device_token')
            ->join('kelompok_mahasiswa', 'device_token.user_id', '=', 'kelompok_mahasiswa.user_id')
            ->where('kelompok_mahasiswa.kelompok_id', $jadwal->kelompok_id)
            ->pluck('device_token.token_device')
            ->filter()
            ->values()
            ->toArray();

        // Kirim notifikasi jika token ada
        if (!empty($tokens)) {
            $body = [
                "message" => [
                    "tokens" => $tokens,
                    "notification" => [
                        "title" => "Jadwal Sidang ",
                        "body"  => "Jadwal Sidang telah dipublish "
                    ],
                    "data" => [
                        "screen" => "HomePage",
                        "waktu_mulai" => $jadwal->waktu_mulai,
                        "waktu_selesai" => $jadwal->waktu_selesai,
                        "notif_time" => Carbon::now()->toDateTimeString(),
                    ]
                ]
            ];

            $notifikasiResponse = Http::post('https://9bfd-114-10-85-135.ngrok-free.app/send-notification', $body);

            $result = json_decode($notifikasiResponse->getBody()->getContents(), true);
            // Debug hasil response notifikasi
            // dd($result);

            Log::debug('Payload notifikasi', ['message' => $body]);

            if ($notifikasiResponse->successful()) {
                Log::info('Notifikasi berhasil dikirim ke device token.', ['tokens' => $tokens, 'response' => $notifikasiResponse->json()]);
            } else {
                Log::error('Gagal mengirim notifikasi.', [
                    'status' => $notifikasiResponse->status(),
                    'body' => $notifikasiResponse->body(),
                ]);
            }
        }

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dibuat');

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Return error validasi ke form
        return back()->withErrors($e->validator)->withInput();
    } catch (Exception $e) {
        // Log error dan kembalikan pesan error ke user
        Log::error('Error creating jadwal: ' . $e->getMessage());
        return back()->with('error', 'Gagal membuat jadwal: ' . $e->getMessage())->withInput();
    }
}

    public function update(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);

            $validated = $request->validate([
                'kelompok_id' => 'required|exists:kelompok,id',
                'ruangan_id' => 'required|exists:ruangan,id',
                // 'waktu_mulai' => 'required|date|after:now',
                // 'waktu_selesai'=>'required|date|after:waktu_mulai'
                'waktu_mulai' => [
                        'required', 'date', 'after:now',
                        function($attribute, $value, $fail) use($request) {
                            // $waktuMulai = $value;
                            $waktuMulai = \Carbon\Carbon::parse($value);
                            $waktuSelesai = $request->waktu_selesai;

                            if(($waktuMulai->hour >= 0 && $waktuMulai->hour < 8 || $waktuMulai -> hour >= 17)){
                                    $fail("Waktu mulai hanya bisa di antara pukul 08.00 hingga 17.00");
                                    return;
                                }

                            if (!$waktuSelesai) return;

                            $bentrok = Jadwal::where('KPA_id', $request->KPA_id)
                                ->where('prodi_id', $request->prodi_id)
                                ->where('TM_id', $request->TM_id)
                                ->where(function ($query) use ($waktuMulai, $waktuSelesai) {
                                    $query->whereBetween('waktu_mulai', [$waktuMulai, $waktuSelesai])
                                        ->orWhereBetween('waktu_selesai', [$waktuMulai, $waktuSelesai])
                                        ->orWhere(function ($q) use ($waktuMulai, $waktuSelesai) {
                                            $q->where('waktu_mulai', '<=', $waktuMulai)
                                            ->where('waktu_selesai', '>=', $waktuSelesai);
                                        });
                                })
                                ->exists();

                            if ($bentrok) {
                                $fail("Sudah ada jadwal untuk waktu ini pada program dan tahun masuk yang sama.");
                            }
                        }
                    ],
                    'waktu_selesai' => [
                        'required', 'date', 'after:waktu_mulai',
                        function($attribute, $value, $fail) use ($request) {
                            $mulai = strtotime($request->waktu_mulai);
                            $selesai = strtotime($value);
                            $diffInMinutes = ($selesai - $mulai) / 60;

                            if ($diffInMinutes < 60) {
                                $fail("Waktu selesai minimal harus 1 jam setelah waktu mulai.");
                            } elseif ($diffInMinutes > 120) {
                                $fail("Waktu selesai maksimal hanya boleh 2 jam setelah waktu mulai.");
                            }
                        }
                    ],
            ]);

            $jadwal = Jadwal::findOrFail($id);
            $jadwal->update(array_merge($validated, [
                'user_id' => session('user_id'),
                'updated_at' => now()
            ]));

            return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbarui');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            Log::error('Error updating jadwal: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui jadwal')->withInput();
        }
    }

    public function show($id)
{
    try {
        $id = Crypt::decrypt($id);

        $jadwal = Jadwal::with(['prodi', 'tahunMasuk', 'ruangan','kategoriPA', 'kelompok.penguji', 'kelompok.pembimbing'])
            ->findOrFail($id);

        $token = session('token');
        $responseDosen = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL') . "library-api/dosen", ['limit' => 100]);

        $dosen = collect($responseDosen->json()['data']['dosen'] ?? []);

        $pembimbingNames = [];
        if ($jadwal->kelompok && $jadwal->kelompok->pembimbing) {
            foreach ($jadwal->kelompok->pembimbing as $pembimbing) {
                $namaPembimbing = $dosen->firstWhere('user_id', $pembimbing->user_id)['nama'] ?? 'Tidak Ditemukan';
                $pembimbingNames[] = $namaPembimbing;
            }
        }

        $pengujiNama = [];
        if ($jadwal->kelompok && $jadwal->kelompok->penguji) {
            foreach ($jadwal->kelompok->penguji as $penguji) {
                $nama = $dosen->firstWhere('user_id', $penguji->user_id)['nama'] ?? '-';
                $pengujiNama[] = $nama;
            }
        }

        return view('pages.Koordinator.jadwal.show', compact('jadwal', 'pengujiNama', 'pembimbingNames'));
    } catch (Exception $e) {
        Log::error('Error showing jadwal: ' . $e->getMessage());
        return back()->with('error', 'Gagal memuat detail jadwal.');
    }
}



    public function destroy($id)
    {
        try {
            $id = Crypt::decrypt($id);
            $jadwal = Jadwal::findOrFail($id);
            $jadwal->delete();

            return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus');

        } catch (Exception $e) {
            Log::error('Error deleting jadwal: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus jadwal');
        }
    }
}
