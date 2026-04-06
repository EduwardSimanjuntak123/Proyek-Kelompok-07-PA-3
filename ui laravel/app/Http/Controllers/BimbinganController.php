<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Bimbingan;
use App\Models\DosenRole;
use App\Models\Kelompok;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Crypt;
use App\Models\KartuBimbingan;
use Mpdf\Mpdf;
use App\Models\KelompokMahasiswa;
use App\Models\pembimbing;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BimbinganController extends Controller
{
    public function index(Request $request){
        $kelompokId = session('kelompok_id');
        // dd($kelompokId);
        $bimbingan = Bimbingan::where ('kelompok_id',$kelompokId)->with('kelompok')->get();

        foreach ($bimbingan as $bimbinganItem) {
            if ($bimbinganItem->status === 'disetujui' && $bimbinganItem->rencana_selesai <= now()) {
                $bimbinganItem->status = 'selesai';
                $bimbinganItem->save();
            } elseif ($bimbinganItem->status === 'menunggu') {
                // Tetap menunggu, tidak perlu update
            }
        }
        

        return view('pages.Mahasiswa.Bimbingan.index',compact('bimbingan'));
    }
    
    public function create() {
        $kelompokId =  session('kelompok_id');
        $token = session('token');
        $ruangan = Ruangan::all();

        return view('pages.Mahasiswa.Bimbingan.create',compact('kelompokId','ruangan'));
        // dd($kelompokId);
    }

    // public function store(Request $request){
    //     $validated = $request->validate([
    //         'kelompok_id' => 'required||exists:kelompok,id',
    //         'ruangan_id' => 'required|exists:ruangan,id',
    //         'keperluan' => 'required|string|max:1000',
    //         'rencana_mulai' => 'required|date|after_or_equal:today',
    //         'rencana_selesai' => 'required|date|after_or_equal:today',
    //         'status' => 'required',
    //     ]);
    
    //         Bimbingan::create($validated);
    //         return redirect()->route('bimbingan.index')->with('success', 'Request Bimbingan  berhasil disimpan.');
    // }

    public function edit($encryptedId){
        try{
            $id = Crypt::decrypt($encryptedId);

            $bimbingan = Bimbingan::findOrFail($id);

            if(in_array($bimbingan->status,['selesai', 'disetujui','ditolak'])){
                // Tampilkan pesan kesalahan jika status masih Aktif
                return back()->withErrors([
                    'error' => 'Tidak dapat mengedit data Request Bimbingan.',
                ]);
            }
            
            // Ambil data ruangan untuk dropdown
            $ruangan = Ruangan::all();
            
            return view('pages.Mahasiswa.Bimbingan.edit', compact('bimbingan', 'ruangan'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menampilkan data: ' . $e->getMessage());
        }
    }
public function store(Request $request)
{
    $validated = $request->validate([
        'kelompok_id' => 'required|exists:kelompok,id',
        'ruangan_id' => 'required|exists:ruangan,id',
        'keperluan' => 'required|string|max:1000',
        'rencana_mulai' => [
            'required',
            'date',
            function ($attribute, $value, $fail) use ($request) {
                try {
                    $mulai = new \DateTime($value);
                    $selesai = new \DateTime($request->rencana_selesai);
                    $now = new \DateTime();

                    // Validasi minimal H-1
                    $diffFromNow = $now->diff($mulai);
                    if ($diffFromNow->days < 1 || $diffFromNow->invert == 1) {
                        $fail('Bimbingan harus diajukan minimal 1 hari sebelum tanggal pelaksanaan.');
                    }

                    // Validasi durasi
                    $diffInMinutes = ($mulai->diff($selesai)->h * 60) + $mulai->diff($selesai)->i;
                    if ($diffInMinutes > 120) {
                        $fail('Durasi bimbingan maksimal 2 jam.');
                    }
                    if ($diffInMinutes < 30) {
                        $fail('Durasi bimbingan minimal 30 menit.');
                    }

                    // Validasi waktu selesai harus setelah mulai
                    if ($selesai <= $mulai) {
                        $fail('Waktu selesai harus setelah waktu mulai.');
                    }

                    // Validasi waktu bimbingan antara 08:00 dan 18:00
                    $startTime = $mulai->format('H:i');
                    $endTime = $selesai->format('H:i');

                    if ($startTime < '08:00' || $endTime > '18:00') {
                        $fail('Waktu bimbingan hanya diperbolehkan antara pukul 08:00 hingga 18:00.');
                    }
                } catch (\Exception $e) {
                    $fail('Terjadi kesalahan dalam memproses tanggal/waktu.');
                }
            }
        ],
        'rencana_selesai' => 'required|date',
    ]);

    Bimbingan::create($validated);

    return redirect()->route('bimbingan.index')->with('success', 'Request Bimbingan berhasil disimpan.');
}


    public function update(Request $request, $encryptedId){
        try {
            DB::beginTransaction();
            
            $id = Crypt::decrypt($encryptedId);
            
            // Jika ini adalah update untuk kartu bimbingan
            if ($request->has('hasil_bimbingan')) {
                // Log untuk debugging
                Log::info('Updating hasil_bimbingan', [
                    'id' => $id,
                    'hasil_bimbingan' => $request->hasil_bimbingan
                ]);
                
                // Cek apakah kolom hasil_bimbingan ada di tabel request_bimbingan
                $hasColumn = DB::getSchemaBuilder()->hasColumn('request_bimbingan', 'hasil_bimbingan');
                Log::info('Has hasil_bimbingan column in request_bimbingan table: ' . ($hasColumn ? 'Yes' : 'No'));
                
                // Jika kolom tidak ada, tambahkan kolom
                if (!$hasColumn) {
                    Log::info('Adding hasil_bimbingan column to request_bimbingan table');
                    DB::statement('ALTER TABLE request_bimbingan ADD COLUMN hasil_bimbingan TEXT NULL');
                }
                
                $bimbingan = Bimbingan::findOrFail($id);
                
                // Update hasil_bimbingan di tabel request_bimbingan menggunakan query builder
                DB::table('request_bimbingan')
                    ->where('id', $id)
                    ->update(['hasil_bimbingan' => $request->hasil_bimbingan]);
                
                Log::info('Updated hasil_bimbingan in request_bimbingan table');
                
                // Cek apakah sudah ada kartu bimbingan untuk request ini
                $kartuBimbingan = KartuBimbingan::where('request_bimbingan_id', $id)->first();
                
                // Jika belum ada, buat baru
                if (!$kartuBimbingan) {
                    // Ambil data pembimbing
                    $pembimbing = Pembimbing::where('kelompok_id', $bimbingan->kelompok_id)->first();
                    
                    $kartuData = [
                        'request_bimbingan_id' => $id,
                        'pembimbing_id' => $pembimbing ? $pembimbing->id : null,
                        'kelompok_id' => $bimbingan->kelompok_id,
                        'tanggal_bimbingan' => now(),
                        'hasil_bimbingan' => $request->hasil_bimbingan,
                        'tanda_tangan_pembimbing' => $pembimbing ? $pembimbing->nama : 'N/A',
                    ];
                    
                    Log::info('Creating new KartuBimbingan', $kartuData);
                    
                    $newKartu = KartuBimbingan::create($kartuData);
                    Log::info('New KartuBimbingan created', ['id' => $newKartu->id ?? 'failed']);
                } else {
                    // Update kartu yang sudah ada menggunakan query builder
                    DB::table('kartu_bimbingan')
                        ->where('id', $kartuBimbingan->id)
                        ->update([
                            'hasil_bimbingan' => $request->hasil_bimbingan,
                            'tanggal_bimbingan' => now()
                        ]);
                    
                    Log::info('Updated existing KartuBimbingan', ['id' => $kartuBimbingan->id]);
                }
                
                DB::commit();
                
                // Redirect kembali ke halaman kartu bimbingan
                return redirect()->route('bimbingan.kartu', Crypt::encrypt($id))
                    ->with('success', 'Hasil bimbingan berhasil disimpan!');
            } else {
                // Ini adalah update untuk request bimbingan biasa
                // $validated = $request->validate([
                //     'ruangan_id' => 'required|exists:ruangan,id',
                //     'keperluan' => 'required|string|max:1000',
                //     'rencana_mulai' => 'required|date|after_or_equal:today',
                //     'rencana_selesai' => 'required|date|after_or_equal:today',
                // ]);

                $validated = $request->validate([
                        'ruangan_id' => 'required|exists:ruangan,id',
                        'keperluan' => 'required|string|max:1000',
                        'rencana_mulai' => [
                            'required',
                            'date',
                            function ($attribute, $value, $fail) use ($request) {
                                $mulai = new \DateTime($value);
                                $selesai = new \DateTime($request->rencana_selesai);
                                $now = new \DateTime();
                                $diff = $mulai->diff($selesai);
                                $diffFromNow = $now->diff($mulai);
                                
                                // Validasi harus diajukan H-1 (minimal 1 hari sebelum bimbingan)
                                if ($diffFromNow->days < 1 || $diffFromNow->invert == 1) {
                                    $fail('Bimbingan harus diajukan minimal 1 hari sebelum tanggal pelaksanaan.');
                                }
                                
                                // // Validasi durasi minimal 1 jam
                                // if ($diff->h < 1 && $diff->days == 0) {
                                //     $fail('Durasi bimbingan minimal 1 jam.');
                                // }
                                
                                // // Validasi durasi maksimal 2 jam
                                // if ($diff->h > 2 || $diff->days > 0) {
                                //     $fail('Durasi bimbingan maksimal 2 jam.');
                                // }

                                // Validasi waktu selesai harus setelah waktu mulai
                                // if($selesai <= $mulai){
                                //     $fail('Waktu selesai harus setelah waktu dimulai.');
                                // }
                            }
                        ],
                        // 'rencana_selesai' => 'required|date|after_or_equal:rencana_mulai',
                        'rencana_selesai' => [
                            'required',
                            'date',
                            'after:rencana_mulai',
                            function ($attribute, $value, $fail) use ($request) {
                                $start = \Carbon\Carbon::parse($request->rencana_mulai);
                                $end = \Carbon\Carbon::parse($value);
                                $diff = $start->diff($end);

                            if ($diff->h > 2 || $diff->days > 0) {
                                    $fail('Durasi bimbingan maksimal 2 jam.');
                                }
                            }
                        ],
                    ]);

                $bimbingan = Bimbingan::findOrFail($id);
                $bimbingan->update($validated);
                
                DB::commit();
                
                return redirect()->route('bimbingan.index')->with('success', 'Request bimbingan berhasil diperbarui!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error saving bimbingan data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function destroy ($id){
        try{
        $bimbingan = Bimbingan::find($id);
        if(in_array($bimbingan->status,['selesai', 'disetujui','ditolak'])){
            // Tampilkan pesan kesalahan jika status masih Aktif
 return back()->withErrors([
     'error' => 'Tidak dapat menghapus  data Request Bimbingan .',
 ]);
     }
        $bimbingan->delete();
        return redirect()->back()->with('success', 'Data Request Bimbingan berhasil dihapus.');
      } catch (Exception $e) {
          return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
      }
    }


    //untuk dosen pembimbing

    public function indexpembimbing(){
       
       $user_id = session('user_id');
       $prodi_ids = DosenRole::where('user_id', $user_id)
                          ->where('status', 'Aktif')
                          ->where('role_id', '3')
                          ->pluck('prodi_id');
        $TM_ids = DosenRole::where('user_id', $user_id)
                            ->where('status', 'Aktif')
                            ->where('role_id', '3')
                          ->pluck('TM_id');
        $KPA_ids = DosenRole::where('user_id', $user_id)
                          ->where('status', 'Aktif')
                          ->where('role_id', '3')
                          ->pluck('KPA_id');

        $bimbingan = Bimbingan::with(['prodi','kategoriPA'])
            ->whereHas('kelompok',function($query) use ($prodi_ids,$KPA_ids,$TM_ids){
            $query  ->whereIn('prodi_id', $prodi_ids)
                    ->whereIn('KPA_id', $KPA_ids)
                    ->whereIn('TM_id', $TM_ids);
        })->with('kelompok')->get();
        

        // Ambil data dosen dari API
        // $token = session('token');
        // $responseDosen = Http::withHeaders([
        //     'Authorization' => "Bearer $token"
        // ])->get(env('API_URL'). "library-api/dosen");
        
        // if ($responseDosen->successful()) {
        //     $dosen_list = $responseDosen->json()['data']['dosen'] ?? [];
        //     // Buat map user_id => nama
        //     $dosen_map = collect($dosen_list)->keyBy('user_id');
            
        //     $bimbingan->each(function ($item) use ($dosen_map) {
        //         $item->nama = $dosen_map[$item->user_id]['nama'] ?? 'N/A';
        //     });
        // } else {
        //     // Tangani jika API gagal
        //     $bimbingan->each(function ($item) {
        //         $item->nama = 'N/A'; // Tampilkan N/A jika API gagal
        //     });
        // }
        
        foreach($bimbingan as $bimbinganItem){
            if($bimbinganItem->rencana_selesai <=now() && $bimbinganItem->status !=='selesai'){
                $bimbinganItem->status = 'selesai';
                $bimbinganItem->save();
            }
        }
        return view('pages.Pembimbing.Bimbingan.index',compact('bimbingan'));
    }

    public function setuju($encryptedId){
        $id = Crypt::decrypt($encryptedId); 
       
        $bimbingan = Bimbingan::find($id);
        $userId = session('user_id');
        // dd($userId);
        $bimbingan->status = 'disetujui';
        $bimbingan->user_id = $userId;
        $bimbingan->save();
        return redirect()->back()->with('success', 'Bimbingan berhasil disetujui.');

    }
    public function tolak($encryptedId){
        $id = Crypt::decrypt($encryptedId); 
        $bimbingan = Bimbingan::find($id);
        $userId = session('user_id');
        // dd($userId);
        $bimbingan->status = 'ditolak';
        $bimbingan->user_id = $userId;
        $bimbingan->save();
        return redirect()->back()->with('success', 'Bimbingan berhasil ditolak.');

    }

    public function showKartuBimbingan($id)
    {
        try {
            // Ambil data bimbingan berdasarkan ID yang ter-enkripsi
            $decryptedId = Crypt::decrypt($id);
            $bimbingan = Bimbingan::findOrFail($decryptedId);
            
            // Log untuk debugging
            Log::info('Showing kartu bimbingan', [
                'id' => $decryptedId,
                'hasil_bimbingan' => $bimbingan->hasil_bimbingan
            ]);
            
            // Cek juga di tabel kartu_bimbingan
            $kartuBimbingan = KartuBimbingan::where('request_bimbingan_id', $decryptedId)->first();
            if ($kartuBimbingan) {
                Log::info('Found kartu_bimbingan record', [
                    'id' => $kartuBimbingan->id,
                    'hasil_bimbingan' => $kartuBimbingan->hasil_bimbingan
                ]);
                
                // Jika hasil_bimbingan kosong di Bimbingan tapi ada di KartuBimbingan
                if (empty($bimbingan->hasil_bimbingan) && !empty($kartuBimbingan->hasil_bimbingan)) {
                    $bimbingan->hasil_bimbingan = $kartuBimbingan->hasil_bimbingan;
                    Log::info('Using hasil_bimbingan from kartu_bimbingan');
                }
            }
            
            // Ambil data mahasiswa yang tergabung dalam kelompok ini
            $kelompokId = $bimbingan->kelompok_id;
            $mahasiswakelompoks = KelompokMahasiswa::where('kelompok_id', $kelompokId)->get();
         
            // Ambil Pembimbing yang sudah ditetapkan untuk kelompok ini
            $pembimbing = pembimbing::where('kelompok_id', $kelompokId)->first();
         
            // Jika pembimbing tidak ada, set default value
            if (!$pembimbing) {
                $pembimbing = new \stdClass();
                $pembimbing->nama = 'Pembimbing tidak tersedia';
            }
           
            // Ambil data mahasiswa dari API eksternal
            $token = session('token');
            $response = Http::withHeaders([
                'Authorization' => "Bearer $token"
            ])->get(env('API_URL') . "library-api/mahasiswa", [
                'limit' => 100
            ]);
        
            // Jika API berhasil, ambil data mahasiswa
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
                return $item;
            });
            
            // Ambil data dosen dari API untuk mendapatkan nama pembimbing
            $responseDosen = Http::withHeaders([
                'Authorization' => "Bearer $token"
            ])->get(env('API_URL'). "library-api/dosen");
            
            if ($responseDosen->successful()) {
                $dosen_list = $responseDosen->json()['data']['dosen'] ?? [];
                // Buat map user_id => nama
                $dosen_map = collect($dosen_list)->keyBy('user_id');
                
                // Jika bimbingan memiliki user_id (dosen pembimbing)
                if ($bimbingan->user_id && isset($dosen_map[$bimbingan->user_id])) {
                    $bimbingan->nama = $dosen_map[$bimbingan->user_id]['nama'] ?? 'N/A';
                } else {
                    $bimbingan->nama = $pembimbing->nama ?? 'N/A';
                }
            } else {
                // Jika API gagal, gunakan nama dari model Pembimbing
                $bimbingan->nama = $pembimbing->nama ?? 'N/A';
            }
        
            // Kirim data ke view
            return view('pages.Mahasiswa.Bimbingan.kartu', compact('bimbingan', 'mahasiswakelompoks', 'pembimbing'));
        } catch (Exception $e) {
            Log::error('Error showing kartu bimbingan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('bimbingan.index')->with('error', 'Gagal menampilkan kartu bimbingan: ' . $e->getMessage());
        }
    }
    
    public function exportToPdf($id)
    {
        try {
            // Ambil data berdasarkan ID yang ter-enkripsi
            $decryptedId = Crypt::decrypt($id);
            $bimbingan = Bimbingan::findOrFail($decryptedId);
            $kelompokId = $bimbingan->kelompok_id;
            
            // Log untuk debugging
            Log::info('Exporting PDF', [
                'id' => $decryptedId,
                'hasil_bimbingan' => $bimbingan->hasil_bimbingan
            ]);
            
            // Cek juga di tabel kartu_bimbingan
            $kartuBimbingan = KartuBimbingan::where('request_bimbingan_id', $decryptedId)->first();
            if ($kartuBimbingan) {
                Log::info('Found kartu_bimbingan record for PDF', [
                    'id' => $kartuBimbingan->id,
                    'hasil_bimbingan' => $kartuBimbingan->hasil_bimbingan
                ]);
                
                // Jika hasil_bimbingan kosong di Bimbingan tapi ada di KartuBimbingan
                if (empty($bimbingan->hasil_bimbingan) && !empty($kartuBimbingan->hasil_bimbingan)) {
                    $bimbingan->hasil_bimbingan = $kartuBimbingan->hasil_bimbingan;
                    Log::info('Using hasil_bimbingan from kartu_bimbingan for PDF');
                }
            }
        
            // Ambil anggota kelompok
            $mahasiswakelompoks = KelompokMahasiswa::where('kelompok_id', $kelompokId)->get();
        
            // Ambil pembimbing yang sesuai
            $pembimbing = Pembimbing::where('kelompok_id', $kelompokId)->first();
        
            // Jika pembimbing tidak ada, set default value
            if (!$pembimbing) {
                $pembimbing = new \stdClass();
                $pembimbing->nama = 'Pembimbing tidak tersedia';
            }
            
            // Ambil data mahasiswa dari API eksternal
            $token = session('token');
            $response = Http::withHeaders([
                'Authorization' => "Bearer $token"
            ])->get(env('API_URL') . "library-api/mahasiswa", [
                'limit' => 100
            ]);
        
            // Jika API berhasil, ambil data mahasiswa
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
                return $item;
            });
            
            // Ambil data dosen dari API untuk mendapatkan nama pembimbing
            $responseDosen = Http::withHeaders([
                'Authorization' => "Bearer $token"
            ])->get(env('API_URL'). "library-api/dosen");
            
            if ($responseDosen->successful()) {
                $dosen_list = $responseDosen->json()['data']['dosen'] ?? [];
                // Buat map user_id => nama
                $dosen_map = collect($dosen_list)->keyBy('user_id');
                
                // Jika bimbingan memiliki user_id (dosen pembimbing)
                if ($bimbingan->user_id && isset($dosen_map[$bimbingan->user_id])) {
                    $bimbingan->nama = $dosen_map[$bimbingan->user_id]['nama'] ?? 'N/A';
                } else {
                    $bimbingan->nama = $pembimbing->nama ?? 'N/A';
                }
            } else {
                // Jika API gagal, gunakan nama dari model Pembimbing
                $bimbingan->nama = $pembimbing->nama ?? 'N/A';
            }
        
            // Menyiapkan data untuk view PDF
            $data = [
                'bimbingan' => $bimbingan,
                'mahasiswakelompoks' => $mahasiswakelompoks,
                'pembimbing' => (object)[
                    'nama' => $bimbingan->nama // Gunakan nama pembimbing yang sudah diambil dari API
                ],
            ];
            
            // Log data yang akan dikirim ke PDF
            Log::info('PDF data prepared', [
                'hasil_bimbingan' => $bimbingan->hasil_bimbingan,
                'pembimbing' => $bimbingan->nama
            ]);
        
            // Load view dan generate PDF dengan mPDF
            $html = view('pages.Mahasiswa.Bimbingan.pdf', $data)->render();
        
            // Buat objek mPDF
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($html);
        
            // Kembalikan PDF sebagai file download
            return response()->stream(
                function () use ($mpdf) {
                    $mpdf->Output('kartu_bimbingan.pdf', 'D');
                },
                200,
                [
                    "Content-Type" => "application/pdf",
                    "Content-Disposition" => "attachment; filename=kartu_bimbingan.pdf"
                ]
            );
        } catch (Exception $e) {
            Log::error('Error exporting PDF', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('bimbingan.index')->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
}