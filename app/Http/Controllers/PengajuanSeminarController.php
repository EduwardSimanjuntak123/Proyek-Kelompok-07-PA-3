<?php 
namespace App\Http\Controllers;

use App\Models\PengajuanSeminar;
use App\Models\PengajuanSeminarFile;
use App\Models\Kelompok;
use App\Models\pembimbing;
use Illuminate\Http\Request;
use App\Models\Tugas;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PengajuanSeminarController extends Controller
{
    // Untuk Mahasiswa methods remain unchanged

    public function status_perizinan(){
        $kelompokId = session('kelompok_id');
        $pengajuanSeminar = PengajuanSeminar::with('kelompok','pembimbing')
        ->where('kelompok_id', $kelompokId)
        ->get();
        $token = session('token');
        $responseDosen = Http::withHeaders([
            'Authorization' =>"Bearer $token"
        ])->get(env('API_URL'). "library-api/dosen");
       
        if($responseDosen->successful()){
            $dosen_list =  $responseDosen->json()['data']['dosen'] ?? [];
        if(!$pengajuanSeminar){
            return view('pages.Mahasiswa.Artefak.pengajuan_seminar', compact('kelompok','artefak'))
            ->with('error','Belum ada request');
        }
            
        // Buat map berdasarkan user_id
        $dosen_map = collect($dosen_list)->keyBy('user_id');
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');
        
        $artefak = Tugas::with(['prodi', 'tahunMasuk', 'kategoripa'])
        ->where('prodi_id', $prodi_id)
        ->where('KPA_id', $KPA_id)
        ->where('TM_id', $TM_id)
        ->where('kategori_tugas','Artefak')
        ->orderBy('created_at', 'desc')
        ->get();
        // dd($pengajuanSeminar);
        return view('pages.Mahasiswa.Artefak.pengajuan_seminar', compact('pengajuanSeminar','dosen_map','artefak'));
      
    }
}   
public function index()
{
    $kelompokId = session('kelompok_id');
    $pengajuanSeminars = PengajuanSeminar::where('kelompok_id', $kelompokId)
        ->orderBy('created_at', 'desc')
        ->with('files') // Load the files relationship
        ->get();
        
    return view('pages.Mahasiswa.Pengajuan_Seminar.index', compact('pengajuanSeminars'));
}

    public function create()
    {
        $kelompokId = session('kelompok_id');
        $kelompok = Kelompok::findOrFail($kelompokId);
        $token = session('token');
        
        // Get the pembimbing associated with this kelompok
        $pembimbing = Pembimbing::where('kelompok_id', $kelompokId)->first();
        
        // If no pembimbing is found, create a default one for the form
        if (!$pembimbing) {
            // Create a default pembimbing object
            $pembimbing = new \stdClass();
            $pembimbing->id = 1;
            $pembimbing->nama = 'Default Pembimbing';
        }
        
        // Fetch dosen data from API to get the actual name of the pembimbing
        $responseDosen = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL'). "library-api/dosen");
        
        if ($responseDosen->successful()) {
            $dosen_list = $responseDosen->json()['data']['dosen'] ?? [];
            // Create map of user_id => nama
            $dosen_map = collect($dosen_list)->keyBy('user_id');
            
            // If pembimbing has user_id, update the nama property with the actual name from API
            if (isset($pembimbing->user_id) && isset($dosen_map[$pembimbing->user_id])) {
                $pembimbing->nama = $dosen_map[$pembimbing->user_id]['nama'] ?? $pembimbing->nama;
            }
        } else {
            // Handle API failure - keep existing name or set default
            if (!isset($pembimbing->nama)) {
                $pembimbing->nama = 'Nama Pembimbing Tidak Tersedia';
            }
        }
    
        return view('pages.Mahasiswa.Pengajuan_Seminar.create', compact('kelompok', 'pembimbing'));
    }


public function edit($id)
{
    try {
        $token = session('token');
        $id = Crypt::decrypt($id);
        $pengajuanSeminar = PengajuanSeminar::with('files')->findOrFail($id);
        
        // Pastikan pengajuan ini milik kelompok yang sedang login
        $kelompokId = session('kelompok_id');
        if ($pengajuanSeminar->kelompok_id != $kelompokId) {
            return redirect()->route('artefak.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit pengajuan ini.');
        }
        
        $kelompok = Kelompok::findOrFail($pengajuanSeminar->kelompok_id);
        $pembimbing = Pembimbing::findOrFail($pengajuanSeminar->pembimbing_id);

         $responseDosen = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get(env('API_URL'). "library-api/dosen");
        
        if ($responseDosen->successful()) {
            $dosen_list = $responseDosen->json()['data']['dosen'] ?? [];
            // Create map of user_id => nama
            $dosen_map = collect($dosen_list)->keyBy('user_id');
            
            // If pembimbing has user_id, update the nama property with the actual name from API
            if (isset($pembimbing->user_id) && isset($dosen_map[$pembimbing->user_id])) {
                $pembimbing->nama = $dosen_map[$pembimbing->user_id]['nama'] ?? $pembimbing->nama;
            }
        } else {
            // Handle API failure - keep existing name or set default
            if (!isset($pembimbing->nama)) {
                $pembimbing->nama = 'Nama Pembimbing Tidak Tersedia';
            }
        }
        // Jika status ditolak, kita bisa menampilkan catatan penolakan
        $catatan = $pengajuanSeminar->catatan;
        // dd($pembimbing);
        return view('pages.Mahasiswa.Pengajuan_Seminar.edit', compact('pengajuanSeminar', 'kelompok', 'pembimbing', 'catatan'));
    } catch (\Exception $e) {
        Log::error('Error editing pengajuan seminar', [
            'id' => $id ?? 'unknown',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->route('artefak.index')
            ->with('error', 'Terjadi kesalahan saat membuka form edit: ' . $e->getMessage());
    }
}


public function update(Request $request, $id)
{
    try {
        $id = Crypt::decrypt($id);
        $pengajuanSeminar = PengajuanSeminar::findOrFail($id);
        
        // Pastikan pengajuan ini milik kelompok yang sedang login
        $kelompokId = session('kelompok_id');
        if ($pengajuanSeminar->kelompok_id != $kelompokId) {
            return redirect()->route('PengajuanSeminar.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengupdate pengajuan ini.');
        }
        
        // Validasi request
        $validatedData = $request->validate([
            'files' => 'required|array|min:1|max:5',
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,docx|max:10240',
        ]);
        
        // Mulai transaksi database
        DB::beginTransaction();
        
        // Update status pengajuan menjadi menunggu lagi
        $pengajuanSeminar->update([
            'status' => 'menunggu',
            'catatan' => null // Hapus catatan penolakan sebelumnya
        ]);
        
        // Hapus file-file lama
        foreach ($pengajuanSeminar->files as $file) {
            // Hapus file dari storage
            Storage::disk('public')->delete($file->file_path);
            // Hapus record dari database
            $file->delete();
        }
        
        // Proses file-file baru
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filePath = $file->store('pengajuan-seminar-files', 'public');
                
                // Buat record untuk setiap file
                PengajuanSeminarFile::create([
                    'pengajuan_seminar_id' => $pengajuanSeminar->id,
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize()
                ]);
            }
        }
        
        // Commit transaksi
        DB::commit();
        
        return redirect()->route('artefak.index')
            ->with('success', 'Pengajuan seminar berhasil diperbarui dan dikirim kembali ke pembimbing!');
    } catch (\Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        DB::rollBack();
        
        Log::error('Error updating pengajuan seminar', [
            'id' => $id ?? 'unknown',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->back()
            ->with('error', 'Terjadi kesalahan saat memperbarui pengajuan: ' . $e->getMessage())
            ->withInput();
    }
}

    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'kelompok_id' => 'required|exists:kelompok,id',
            'pembimbing_id' => 'required|exists:pembimbing,id',
            'files' => 'required|array|min:1|max:5',
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,docx|max:10240',
        ]);
    
        try {
            // Start a database transaction
            DB::beginTransaction();
            
            // Get the kelompok and pembimbing details
            $kelompok = Kelompok::findOrFail($validatedData['kelompok_id']);
            $pembimbing = Pembimbing::findOrFail($validatedData['pembimbing_id']);
            
            // Log the submission details
            Log::info('Creating new seminar submission', [
                'kelompok_id' => $validatedData['kelompok_id'],
                'kelompok_prodi' => $kelompok->prodi_id ?? 'Unknown',
                'pembimbing_id' => $validatedData['pembimbing_id'],
                'pembimbing_prodi' => $pembimbing->prodi_id ?? 'Unknown',
                'files_count' => count($request->file('files'))
            ]);
            
            // Create the pengajuan seminar record
            $pengajuanSeminar = PengajuanSeminar::create([
                'kelompok_id' => $validatedData['kelompok_id'],
                'pembimbing_id' => $validatedData['pembimbing_id'],
                'status' => 'menunggu'
            ]);
            
            // Process each uploaded file
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filePath = $file->store('pengajuan-seminar-files', 'public');
                    
                    // Create a record for each file
                    PengajuanSeminarFile::create([
                        'pengajuan_seminar_id' => $pengajuanSeminar->id,
                        'file_path' => $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize()
                    ]);
                }
            }
            
            // Commit the transaction
            DB::commit();
            
            return redirect()->route('artefak.index')->with('success', 'Pengajuan seminar berhasil disimpan dan dikirim ke pembimbing!');
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();
            
            Log::error('Error creating pengajuan seminar', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan pengajuan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $decryptId = Crypt::decrypt($id); // Tambahkan ini
        $pengajuanSeminar = PengajuanSeminar::findOrFail($decryptId);
        $pengajuanSeminar->delete();

        return redirect()->back()->with('success', 'Data kelompok berhasil dihapus.');
    }

    // IMPROVED: indexPembimbing method to show submissions across different study programs
    public function indexPembimbing()
    {
        // Get all relevant session data
        $pembimbingId = session('pembimbing_id');
        $userId = session('user_id');
        
        // Log session values for debugging
        Log::info('Pembimbing session values', [
            'pembimbing_id' => $pembimbingId,
            'user_id' => $userId
        ]);
        
        // Initialize empty collection for submissions
        $pengajuanSeminars = collect();
        
        try {
            // APPROACH 1: Get submissions directly by pembimbing_id if available
            if ($pembimbingId) {
                $directSubmissions = PengajuanSeminar::where('pembimbing_id', $pembimbingId)
                    ->with(['kelompok', 'files','prodi','kategoriPA'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                if ($directSubmissions->isNotEmpty()) {
                    $pengajuanSeminars = $directSubmissions;
                    Log::info('Found submissions by pembimbing_id', [
                        'count' => $pengajuanSeminars->count()
                    ]);
                }
            }
            
            // APPROACH 2: If no results or no pembimbing_id, try with user_id
            if ($pengajuanSeminars->isEmpty() && $userId) {
                // Get all pembimbing records associated with this user_id
                $pembimbingRecords = pembimbing::where('user_id', $userId)->get();
                
                if ($pembimbingRecords->isNotEmpty()) {
                    // Get all pembimbing IDs for this user
                    $pembimbingIds = $pembimbingRecords->pluck('id')->toArray();
                    
                    // Get all kelompok IDs where this user is assigned as pembimbing
                    $kelompokIds = $pembimbingRecords->pluck('kelompok_id')->toArray();
                    
                    Log::info('Found pembimbing records for user', [
                        'user_id' => $userId,
                        'pembimbing_ids' => $pembimbingIds,
                        'kelompok_ids' => $kelompokIds
                    ]);
                    
                    // Get submissions by pembimbing_id OR kelompok_id
                    $userSubmissions = PengajuanSeminar::where(function($query) use ($pembimbingIds, $kelompokIds) {
                        $query->whereIn('pembimbing_id', $pembimbingIds)
                              ->orWhereIn('kelompok_id', $kelompokIds);
                    })
                    ->with(['kelompok', 'files','prodi','kategoriPA'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                    
                    if ($userSubmissions->isNotEmpty()) {
                        $pengajuanSeminars = $userSubmissions;
                        Log::info('Found submissions by user relationships', [
                            'count' => $pengajuanSeminars->count()
                        ]);
                    }
                }
            }
            
            // If we still have no results, try a fallback approach
            if ($pengajuanSeminars->isEmpty() && $userId) {
                // This is a fallback approach - check if there are any submissions
                // where the user_id matches in the pembimbing table
                $fallbackSubmissions = PengajuanSeminar::whereHas('pembimbing', function($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->with(['kelompok', 'files','prodi','kategoriPA'])
                ->orderBy('created_at', 'desc')
                ->get();
                
                if ($fallbackSubmissions->isNotEmpty()) {
                    $pengajuanSeminars = $fallbackSubmissions;
                    Log::info('Found submissions by fallback method', [
                        'count' => $pengajuanSeminars->count()
                    ]);
                }
            }
            
            // Log detailed information about the results
            if (!$pengajuanSeminars->isEmpty()) {
                Log::info('Final submission details', [
                    'count' => $pengajuanSeminars->count(),
                    'submissions' => $pengajuanSeminars->map(function($item) {
                        return [
                            'id' => $item->id,
                            'kelompok_id' => $item->kelompok_id,
                            'pembimbing_id' => $item->pembimbing_id,
                            'status' => $item->status,
                            'kelompok_prodi' => $item->kelompok ? $item->kelompok->prodi_id : 'Unknown',
                            'files_count' => $item->files->count()
                        ];
                    })
                ]);
            } else {
                Log::warning('No submissions found for this pembimbing', [
                    'pembimbing_id' => $pembimbingId,
                    'user_id' => $userId
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching pengajuan seminars', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        return view('pages.Pembimbing.Pengajuan_Seminar.index', compact('pengajuanSeminars'));
    }

    // IMPROVED: setujui method to handle cross-program approvals
    public function setujui(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
            $pengajuan = PengajuanSeminar::with(['kelompok', 'files'])->findOrFail($id);
            
            // Get session values
            $pembimbingId = session('pembimbing_id');
            $userId = session('user_id');
            
            // Check if this pembimbing is authorized to approve this submission
            $isAuthorized = false;
            
            // Check direct assignment by pembimbing_id
            if ($pengajuan->pembimbing_id == $pembimbingId) {
                $isAuthorized = true;
            }
            
            // If not directly assigned, check through user_id in pembimbing table
            if (!$isAuthorized && $userId) {
                // Get all pembimbing records for this user
                $pembimbingRecords = Pembimbing::where('user_id', $userId)->get();
                
                // Check if any of these records match the submission's pembimbing_id or kelompok_id
                foreach ($pembimbingRecords as $record) {
                    if ($record->id == $pengajuan->pembimbing_id || $record->kelompok_id == $pengajuan->kelompok_id) {
                        $isAuthorized = true;
                        break;
                    }
                }
            }
            
            // If still not authorized, check if the user is assigned to the kelompok directly
            if (!$isAuthorized && $userId) {
                $pembimbing = Pembimbing::where('user_id', $userId)
                    ->where('kelompok_id', $pengajuan->kelompok_id)
                    ->first();
                
                if ($pembimbing) {
                    $isAuthorized = true;
                }
            }
            
            if (!$isAuthorized) {
                return redirect()->back()->with('error', 'Anda tidak berhak menyetujui pengajuan ini karena bukan pembimbing yang ditugaskan.');
            }

            // Log for debugging
            Log::info('Approving submission', [
                'id' => $id,
                'pembimbing_id' => $pembimbingId,
                'user_id' => $userId,
                'kelompok_id' => $pengajuan->kelompok_id,
                'kelompok_prodi' => $pengajuan->kelompok ? $pengajuan->kelompok->prodi_id : 'Unknown'
            ]);

            $pengajuan->update([
                'status' => 'disetujui',
                'catatan' => null // Hapus catatan jika sebelumnya ada
            ]);

            return redirect()->route('PembimbingPengajuanSeminar.index')->with('success', 'Pengajuan seminar berhasil disetujui.');
        } catch (\Exception $e) {
            Log::error('Error approving submission', [
                'id' => $id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('PembimbingPengajuanSeminar.index')->with('error', 'Terjadi kesalahan saat menyetujui pengajuan: ' . $e->getMessage());
        }
    }

    // IMPROVED: tolak method to handle cross-program rejections
    public function tolak(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
            $pengajuan = PengajuanSeminar::with(['kelompok', 'files'])->findOrFail($id);
            
            // Get session values
            $pembimbingId = session('pembimbing_id');
            $userId = session('user_id');
            
            // Check if this pembimbing is authorized to reject this submission
            $isAuthorized = false;
            
            // Check direct assignment by pembimbing_id
            if ($pengajuan->pembimbing_id == $pembimbingId) {
                $isAuthorized = true;
            }
            
            // If not directly assigned, check through user_id in pembimbing table
            if (!$isAuthorized && $userId) {
                // Get all pembimbing records for this user
                $pembimbingRecords = Pembimbing::where('user_id', $userId)->get();
                
                // Check if any of these records match the submission's pembimbing_id or kelompok_id
                foreach ($pembimbingRecords as $record) {
                    if ($record->id == $pengajuan->pembimbing_id || $record->kelompok_id == $pengajuan->kelompok_id) {
                        $isAuthorized = true;
                        break;
                    }
                }
            }
            
            // If still not authorized, check if the user is assigned to the kelompok directly
            if (!$isAuthorized && $userId) {
                $pembimbing = Pembimbing::where('user_id', $userId)
                    ->where('kelompok_id', $pengajuan->kelompok_id)
                    ->first();
                
                if ($pembimbing) {
                    $isAuthorized = true;
                }
            }
            
            if (!$isAuthorized) {
                return redirect()->back()->with('error', 'Anda tidak berhak menolak pengajuan ini karena bukan pembimbing yang ditugaskan.');
            }

            $request->validate([
                'catatan' => 'required|string|max:255'
            ]);

            // Log for debugging
            Log::info('Rejecting submission', [
                'id' => $id,
                'pembimbing_id' => $pembimbingId,
                'user_id' => $userId,
                'kelompok_id' => $pengajuan->kelompok_id,
                'kelompok_prodi' => $pengajuan->kelompok ? $pengajuan->kelompok->prodi_id : 'Unknown',
                'catatan' => $request->catatan
            ]);

            $pengajuan->update([
                'status' => 'ditolak',
                'catatan' => $request->catatan
            ]);

            return redirect()->route('PembimbingPengajuanSeminar.index')->with('success', 'Pengajuan seminar berhasil ditolak dengan catatan.');
        } catch (\Exception $e) {
            Log::error('Error rejecting submission', [
                'id' => $id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('PembimbingPengajuanSeminar.index')->with('error', 'Terjadi kesalahan saat menolak pengajuan: ' . $e->getMessage());
        }
    }

    // Debug route remains unchanged
    public function debugPengajuanSeminar()
    {
        $pembimbingId = session('pembimbing_id', '1');
        $userId = session('user_id');
        $prodi_id = session('prodi_id');
        
        // Get pembimbing details
        $pembimbing = Pembimbing::find($pembimbingId);
        
        // Get all kelompok IDs where this user is assigned as pembimbing
        $kelompokIds = [];
        if ($userId) {
            $kelompokIds = Pembimbing::where('user_id', $userId)
                ->pluck('kelompok_id')
                ->toArray();
        }
        
        // Get submissions by different methods
        $directSubmissions = PengajuanSeminar::where('pembimbing_id', $pembimbingId)
            ->with(['kelompok', 'files'])
            ->get();
        
        $userSubmissions = [];
        if (!empty($kelompokIds)) {
            $userSubmissions = PengajuanSeminar::whereIn('kelompok_id', $kelompokIds)
                ->with(['kelompok', 'files'])
                ->get();
        }
        
        $prodiSubmissions = [];
        if ($prodi_id) {
            $prodiSubmissions = PengajuanSeminar::whereHas('kelompok', function($query) use ($prodi_id) {
                $query->where('prodi_id', $prodi_id);
            })->with(['kelompok', 'files'])->get();
        }
        
        // Get all submissions and pembimbings for system-wide check
        $allSubmissions = PengajuanSeminar::with(['kelompok', 'pembimbing', 'files'])->get();
        $allPembimbings = Pembimbing::all();
        
        $data = [
            'session_info' => [
                'pembimbing_id' => $pembimbingId,
                'user_id' => $userId,
                'prodi_id' => $prodi_id
            ],
            'pembimbing_info' => $pembimbing ? [
                'id' => $pembimbing->id,
                'user_id' => $pembimbing->user_id,
                'prodi_id' => $pembimbing->prodi_id,
                'kelompok_id' => $pembimbing->kelompok_id,
                'nama' => $pembimbing->nama
            ] : 'Not found',
            'kelompok_ids' => $kelompokIds,
            'direct_submissions' => [
                'count' => $directSubmissions->count(),
                'submissions' => $directSubmissions->map(function($item) {
                    return [
                        'id' => $item->id,
                        'kelompok_id' => $item->kelompok_id,
                        'pembimbing_id' => $item->pembimbing_id,
                        'status' => $item->status,
                        'kelompok_prodi' => $item->kelompok ? $item->kelompok->prodi_id : 'Unknown'
                    ];
                })
            ],
            'user_submissions' => [
                'count' => count($userSubmissions),
                'submissions' => collect($userSubmissions)->map(function($item) {
                    return [
                        'id' => $item->id,
                        'kelompok_id' => $item->kelompok_id,
                        'pembimbing_id' => $item->pembimbing_id,
                        'status' => $item->status,
                        'kelompok_prodi' => $item->kelompok ? $item->kelompok->prodi_id : 'Unknown'
                    ];
                })
            ],
            'prodi_submissions' => [
                'count' => count($prodiSubmissions),
                'submissions' => collect($prodiSubmissions)->map(function($item) {
                    return [
                        'id' => $item->id,
                        'kelompok_id' => $item->kelompok_id,
                        'pembimbing_id' => $item->pembimbing_id,
                        'status' => $item->status,
                        'kelompok_prodi' => $item->kelompok ? $item->kelompok->prodi_id : 'Unknown'
                    ];
                })
            ],
            'system_info' => [
                'total_submissions' => $allSubmissions->count(),
                'total_pembimbings' => $allPembimbings->count(),
                'all_pembimbing_ids' => $allPembimbings->pluck('id')->toArray(),
                'all_submission_pembimbing_ids' => $allSubmissions->pluck('pembimbing_id')->unique()->toArray()
            ]
        ];
        
        return response()->json($data);
    }
}