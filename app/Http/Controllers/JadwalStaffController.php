<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Jadwal;
use App\Models\Kelompok;
use App\Models\Prodi;
use App\Models\DosenRole;
use App\Models\kategoriPA;
use App\Models\TahunMasuk;
use App\Models\Role;
use App\Models\Ruangan;
use App\Models\PengajuanSeminar;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JadwalStaffController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userID = session('user_id');
            if (!$userID) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
            }

            $jadwal = Jadwal::with(['kelompok', 'prodi', 'tahunMasuk', 'kategoriPA', 'ruangan'])
                // ->where('user_id', $userID)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('pages.BAAK.jadwal.index', compact('jadwal'));
        } catch (Exception $e) {
            Log::error('Error fetching jadwal: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengambil data jadwal');
        }
    }
    
public function getKelompok(Request $request)
{
    $validated = $request->validate([
        'prodi_id' => 'required|integer|exists:prodi,id',
        'KPA_id' => 'required|integer|exists:kategori_pa,id',
        'TM_id' => 'required|integer|exists:tahun_masuk,id'
    ]);

    try {
        // Hanya kembalikan kelompok yang memiliki pengajuan seminar disetujui dan belum memiliki jadwal
        $kelompok = Kelompok::where('prodi_id', $validated['prodi_id'])
            ->where('KPA_id', $validated['KPA_id'])
            ->where('TM_id', $validated['TM_id'])
            ->whereHas('pengajuanSeminar', function($query) {
                $query->where('status', 'disetujui');
            })
            ->whereDoesntHave('jadwal') // Pastikan belum ada jadwal
            ->select('id', 'nomor_kelompok as text')
            ->get();

        if ($kelompok->isEmpty()) {
            // Cek apakah ada kelompok dengan pengajuan yang belum disetujui
            $hasUnapprovedGroups = Kelompok::where('prodi_id', $validated['prodi_id'])
                ->where('KPA_id', $validated['KPA_id'])
                ->where('TM_id', $validated['TM_id'])
                ->whereHas('pengajuanSeminar', function($query) {
                    $query->where('status', '!=', 'disetujui');
                })
                ->exists();
            
            // Cek apakah semua kelompok yang disetujui sudah memiliki jadwal
            $allApprovedHaveSchedules = Kelompok::where('prodi_id', $validated['prodi_id'])
                ->where('KPA_id', $validated['KPA_id'])
                ->where('TM_id', $validated['TM_id'])
                ->whereHas('pengajuanSeminar', function($query) {
                    $query->where('status', 'disetujui');
                })
                ->whereHas('jadwal')
                ->exists();
            
            if ($hasUnapprovedGroups) {
                return response()->json([
                    'error' => 'Tidak ada kelompok dengan pengajuan seminar yang disetujui dosen Pembimbing. Pengajuan Seminar harus disetujui dosen Pembimbing.'
                ], 422);
            } elseif ($allApprovedHaveSchedules) {
                return response()->json([
                    'error' => 'Semua kelompok dengan pengajuan seminar yang disetujui sudah memiliki jadwal.'
                ], 422);
            } else {
                // Kondisi ketika tidak ada kelompok sama sekali    
                return response()->json([
                    'error' => 'Tidak ada kelompok yang tersedia.'
                ], 422);
            }
        }

        return response()->json($kelompok);

    } catch (Exception $e) {
        Log::error('Error in getKelompok: '.$e->getMessage());
        return response()->json(['error' => 'Gagal mengambil data kelompok'], 500);
    }
}
    
    public function create(){
        try{
            $userID = session('user_id');
            $token = session('token');

            if (!$userID || !$token) {
                return redirect()->route('login')->with('error', 'Sesi telah berakhir');
            }
            
            $kategori_pa = kategoriPA::all();
            $prodi = Prodi::all();
            $tahun_masuk = TahunMasuk::all();
            $ruangan = Ruangan::all();
            
            return view('pages.BAAK.jadwal.create', compact('kategori_pa', 'prodi', 'tahun_masuk', 'ruangan'));
        } catch (Exception $e) {
            Log::error('Error loading create form: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat form');
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
                'exists:kelompok,id',
                function ($attribute, $value, $fail) use ($request) {
                    // Cek apakah jadwal sudah ada untuk kelompok ini
                    if (Jadwal::where('kelompok_id', $value)->exists()) {
                        $fail("Jadwal untuk kelompok ini sudah ada.");
                    }

                    // Cek apakah pengajuan seminar sudah disetujui
                    $approved = Kelompok::where('id', $value)
                        ->whereHas('pengajuanSeminar', fn($q) => $q->where('status', 'disetujui'))
                        ->exists();

                    if (!$approved) {
                        $fail("Pengajuan seminar untuk kelompok ini belum disetujui oleh dosen pembimbing.");
                    }
                }
            ],
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
                'required',
                'date',
                'after:now',
                function ($attribute, $value, $fail) use ($request) {
                    $waktuMulai = Carbon::parse($value);
                    $waktuSelesai = Carbon::parse($request->waktu_selesai);

                    if (!$request->waktu_selesai) return;

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
                'required',
                'date',
                'after:waktu_mulai',
                function ($attribute, $value, $fail) use ($request) {
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
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $jadwal = new Jadwal([
            'kelompok_id'   => $validated['kelompok_id'],
            'ruangan_id'    => $validated['ruangan_id'],
            'waktu_mulai'   => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'],
            'user_id'       => $userID,
            'KPA_id'        => $validated['KPA_id'],
            'prodi_id'      => $validated['prodi_id'],
            'TM_id'         => $validated['TM_id'],
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        if ($request->hasFile('attachment')) {
            $jadwal->attachment_path = $this->storeAttachment($request->file('attachment'));
        }

        $jadwal->save();

        // Ambil device token mahasiswa kelompok
        $tokens = DB::table('device_token')
            ->join('kelompok_mahasiswa', 'device_token.user_id', '=', 'kelompok_mahasiswa.user_id')
            ->where('kelompok_mahasiswa.kelompok_id', $jadwal->kelompok_id)
            ->pluck('device_token.token_device')
            ->filter()
            ->values()
            ->toArray();

        // Kirim notifikasi
        if (!empty($tokens)) {
            $body = [
                "message" => [
                    "tokens" => $tokens,
                    "notification" => [
                        "title" => "Jadwal Sidang",
                        "body"  => "Jadwal Sidang telah dipublish"
                    ],
                    "data" => [
                        "screen" => "HomePage",
                        "waktu_mulai" => $jadwal->waktu_mulai,
                        "waktu_selesai" => $jadwal->waktu_selesai,
                        "notif_time" => Carbon::now()->toDateTimeString(),
                    ]
                ]
            ];

            $response = Http::post('https://9bfd-114-10-85-135.ngrok-free.app/send-notification', $body);

            Log::debug('Payload notifikasi', ['message' => $body]);

            if ($response->successful()) {
                Log::info('Notifikasi berhasil dikirim.', ['tokens' => $tokens, 'response' => $response->json()]);
            } else {
                Log::error('Gagal mengirim notifikasi.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        }

        return redirect()->route('baak.jadwal.index')->with('success', 'Jadwal berhasil dibuat');
    } catch (\Illuminate\Validation\ValidationException $e) {
        return back()->withErrors($e->validator)->withInput();
    } catch (\Exception $e) {
        Log::error('Error storing jadwal: ' . $e->getMessage());
        return back()->with('error', 'Gagal menyimpan jadwal: ' . $e->getMessage())->withInput();
    }
}

    public function storeAttachment($file)
    {
        try {
            // Generate a unique filename
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Store the file in the 'jadwal_attachments' directory within the public disk
            $path = $file->storeAs('jadwal_attachments', $fileName, 'public');
            
            // Return the path to be stored in the database
            return $path;
            
        } catch (Exception $e) {
            Log::error('Error storing attachment: ' . $e->getMessage());
            throw new Exception('Gagal menyimpan lampiran: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $id = Crypt::decrypt($id);
            $userID = session('user_id');
            $token = session('token');

            if (!$userID || !$token) {
                return redirect()->route('login')->with('error', 'Sesi telah berakhir');
            }

            $jadwal = Jadwal::findOrFail($id);
            $kategori_pa = KategoriPA::all();
            $prodi = Prodi::all();
            $tahun_masuk = TahunMasuk::all();
            $kelompok = Kelompok::all();
            $ruangan = Ruangan::all();

            return view('pages.BAAK.jadwal.edit', compact('jadwal', 'kategori_pa', 'prodi', 'tahun_masuk', 'kelompok', 'ruangan'));
        } catch (Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat form edit');
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);

            $validated = $request->validate([
                'kelompok_id' => 'required|exists:kelompok,id',
                'ruangan_id' => 'required|exists:ruangan,id',
                    'waktu_mulai' => [
                        'required', 'date', 'after:now',
                        function($attribute, $value, $fail) use($request) {
                            // $waktuMulai = $value;
                            $waktuMulai = \Carbon\Carbon::parse($value);
                            $waktuSelesai = $request->waktu_selesai;

                            if(($waktuMulai->hour >= 0 && $waktuMulai->hour < 8 || $waktuMulai -> hour >= 17)){
                                    $fail("Waktu mulai hanya bisa di antara pukul 08.00 hingga 16.00");
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
                'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            ]);

            $jadwal = Jadwal::findOrFail($id);
            
            // Handle attachment if present
            if ($request->hasFile('attachment')) {
                // Delete old attachment if exists
                if ($jadwal->attachment_path) {
                    Storage::disk('public')->delete($jadwal->attachment_path);
                }
                
                $validated['attachment_path'] = $this->storeAttachment($request->file('attachment'));
            }

            $jadwal->update(array_merge($validated, [
                'updated_at' => now()
            ]));

            return redirect()->route('baak.jadwal.index')->with('success', 'Jadwal berhasil diperbarui');
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

            // Ambil jadwal + relasi kelompok -> penguji dan pembimbing
            $jadwal = Jadwal::with(['kelompok.penguji', 'kelompok.pembimbing', 'prodi', 'tahunMasuk', 'kategoriPA', 'ruangan'])
                ->findOrFail($id);

            $token = session('token');
            $response = Http::withHeaders([
                'Authorization' => "Bearer $token"
            ])->get(env('API_URL') . 'library-api/dosen', ['limit' => 100]);

            $dosenArray = [];
            if ($response->successful()) {
                $dosenData = $response->json('data.dosen') ?? [];
                foreach ($dosenData as $dosen) {
                    $dosenArray[$dosen['user_id']] = $dosen['nama'];
                }
            }

            // Ambil semua nama penguji
            $pengujiNama = [];
            if ($jadwal->kelompok && $jadwal->kelompok->penguji) {
                foreach ($jadwal->kelompok->penguji as $penguji) {
                    $pengujiNama[] = $dosenArray[$penguji->user_id] ?? 'Nama tidak ditemukan';
                }
            }

            // Ambil semua nama pembimbing
            $pembimbingNames = [];
            if ($jadwal->kelompok && $jadwal->kelompok->pembimbing) {
                foreach ($jadwal->kelompok->pembimbing as $pembimbing) {
                    $pembimbingNames[] = $dosenArray[$pembimbing->user_id] ?? 'Nama tidak ditemukan';
                }
            }

            return view('pages.BAAK.jadwal.show', compact('jadwal', 'pengujiNama', 'pembimbingNames'));

        } catch (\Exception $e) {
            Log::error('Error loading jadwal detail: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat detail jadwal');
        }
    }

    public function destroy($id){
        try{
            $id = Crypt::decrypt($id);
            $jadwal = Jadwal::findOrFail($id);
            
            // // Delete attachment if exists
            // if ($jadwal->attachment_path) {
            //     Storage::disk('public')->delete($jadwal->attachment_path);
            // }
            
            $jadwal->delete();

            return redirect()->route('baak.jadwal.index')
                -> with('success', 'Jadwal berhasil dihapus');
        } catch (Exception $e) {
            Log::error('Error deleting jadwal: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus jadwal');
        }
    }
}
