<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use Illuminate\Http\Request;
use App\Models\Kelompok;
use App\Models\pengumpulan_tugas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Exception;
use App\Models\PengajuanSeminar;
use App\Models\pembimbing;
use Illuminate\Support\Facades\Http;
class Artefak_Controller extends Controller
{
    public function Artefak(Request $request)
    {
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
            foreach($artefak as $artefakItem){
                if($artefakItem->tanggal_pengumpulan <=now() && $artefakItem->status !=='selesai'){
                    $artefakItem->status = 'selesai';
                    $artefakItem->save();
                }
              }
            foreach ($artefak as $item) {
                $deadline = Carbon::parse($item->tanggal_pengumpulan);
                $now = Carbon::now();
                $diffInSeconds = $now->diffInSeconds($deadline, false);
            
                $item->formatted_deadline = $deadline->format('d M Y - h:i A');
            
                if ($diffInSeconds > 0) {
                    // Masih ada waktu
                    if ($diffInSeconds >= 86400) { // lebih dari atau sama dengan 24 jam
                        $days = floor($diffInSeconds / 86400);
                        $item->time_remaining = "$days hari lagi";
                    } else {
                        $hours = floor($diffInSeconds / 3600);
                        $minutes = floor(($diffInSeconds % 3600) / 60);
                        $item->time_remaining = "{$hours} jam {$minutes} menit lagi";
                    }
                    $item->status_class = 'text-warning';
                } else {
                    // Sudah lewat deadline
                    $diffInSeconds = abs($diffInSeconds);
                    if ($diffInSeconds >= 86400) {
                        $days = floor($diffInSeconds / 86400);
                        $item->time_remaining = "Selesai $days hari yang lalu";
                    } else {
                        $hours = floor($diffInSeconds / 3600);
                        $minutes = floor(($diffInSeconds % 3600) / 60);
                        $item->time_remaining = "Selesai {$hours} jam {$minutes} menit yang lalu";
                    }
                    $item->status_class = 'text-warning';
                }
            }
            
// untuk pengajuan Seminar
            $kelompokId = session('kelompok_id');
            $status = pengumpulan_tugas::with(['Kelompok','tugas'])
            ->where('kelompok_id', $kelompokId)
            ->get();
            $statusByTugas = $status->keyBy('tugas_id');

            $pengajuanSeminars = PengajuanSeminar::where('kelompok_id', $kelompokId)
                    ->orderBy('created_at', 'desc')
                    ->with('files') // 
                    ->get();
                    
        return view('pages.Mahasiswa.Artefak.index', compact('artefak','statusByTugas','pengajuanSeminars','status'));
    }
    
    public function Revisi(Request $request)
    {
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');
    
        $artefak = Tugas::with(['prodi', 'tahunMasuk', 'kategoripa'])
            ->where('prodi_id', $prodi_id)
            ->where('KPA_id', $KPA_id)
            ->where('TM_id', $TM_id)
            ->where('kategori_tugas','Revisi')
            ->orderBy('created_at', 'desc')
            ->get();
            foreach($artefak as $artefakItem){
                if($artefakItem->tanggal_pengumpulan <=now() && $artefakItem->status !=='selesai'){
                    $artefakItem->status = 'selesai';
                    $artefakItem->save();
                }
              }
            foreach ($artefak as $item) {
                $deadline = Carbon::parse($item->tanggal_pengumpulan);
                $now = Carbon::now();
                $diffInSeconds = $now->diffInSeconds($deadline, false);
            
                $item->formatted_deadline = $deadline->format('d M Y - h:i A');
            
                if ($diffInSeconds > 0) {
                    // Masih ada waktu
                    if ($diffInSeconds >= 86400) { // lebih dari atau sama dengan 24 jam
                        $days = floor($diffInSeconds / 86400);
                        $item->time_remaining = "$days hari lagi";
                    } else {
                        $hours = floor($diffInSeconds / 3600);
                        $minutes = floor(($diffInSeconds % 3600) / 60);
                        $item->time_remaining = "{$hours} jam {$minutes} menit lagi";
                    }
                    $item->status_class = 'text-warning';
                } else {
                    // Sudah lewat deadline
                    $diffInSeconds = abs($diffInSeconds);
                    if ($diffInSeconds >= 86400) {
                        $days = floor($diffInSeconds / 86400);
                        $item->time_remaining = "Selesai $days hari yang lalu";
                    } else {
                        $hours = floor($diffInSeconds / 3600);
                        $minutes = floor(($diffInSeconds % 3600) / 60);
                        $item->time_remaining = "Selesai {$hours} jam {$minutes} menit yang lalu";
                    }
                    $item->status_class = 'text-success';
                }
            }

            $kelompokId = session('kelompok_id');
         
           
            $status = pengumpulan_tugas::with(['Kelompok','tugas'])
            ->where('kelompok_id', $kelompokId)
            ->get();
            $statusByTugas = $status->keyBy('tugas_id');
            $prodi_id = session('prodi_id');
            $KPA_id = session('KPA_id');
            $TM_id = session('TM_id');
            
            $artefak = Tugas::with(['prodi', 'tahunMasuk', 'kategoripa'])
            ->where('prodi_id', $prodi_id)
            ->where('KPA_id', $KPA_id)
            ->where('TM_id', $TM_id)
            ->where('kategori_tugas','Revisi')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pages.Mahasiswa.Artefak.revisi', compact('artefak','statusByTugas'));
    }

//   public function create( $encryptedId){
//         try {
//             $id = Crypt::decrypt($encryptedId);
//             $tugas = Tugas::findOrFail($id);
//             $idTugas = $tugas->id;
//             // dd($idTugas);
//             $deadline = Carbon::parse($tugas->tanggal_pengumpulan);
//             $now = Carbon::now();
//             $diffInSeconds = $now->diffInSeconds($deadline, false);
    
//             $tugas->formatted_deadline = $deadline->format('d M Y - h:i A');
    
//             if ($diffInSeconds > 0) {
//                 // Masih ada waktu
//                 if ($diffInSeconds >= 86400) {
//                     $days = floor($diffInSeconds / 86400);
//                     $tugas->time_remaining = "$days hari lagi";
//                 } else {
//                     $hours = floor($diffInSeconds / 3600);
//                     $minutes = floor(($diffInSeconds % 3600) / 60);
//                     $tugas->time_remaining = "{$hours} jam {$minutes} menit lagi";
//                 }
//                 $tugas->status_class = 'text-warning';
//             } else {
//                 // Sudah lewat deadline
//                 $diffInSeconds = abs($diffInSeconds);
//                 if ($diffInSeconds >= 86400) {
//                     $days = floor($diffInSeconds / 86400);
//                     $tugas->time_remaining = "Selesai $days hari yang lalu";
//                 } else {
//                     $hours = floor($diffInSeconds / 3600);
//                     $minutes = floor(($diffInSeconds % 3600) / 60);
//                     $tugas->time_remaining = "Selesai {$hours} jam {$minutes} menit yang lalu";
//                 }
//                 $tugas->status_class = 'text-success';
//             }
//             $kelompokId = session('kelompok_id');
            
//             //cek apakah kelompk sudah submit
//             $existingSubmission = pengumpulan_tugas::where('kelompok_id',$kelompokId)
//             ->where('tugas_id',$tugas->id)
//             ->first();
//             // dd($existingSubmission);
//             $hasSubmitted = $existingSubmission ? true : false;
//             $kelompokId = session('kelompok_id');
         
           
//             $status = pengumpulan_tugas::with(['Kelompok','tugas'])
//             ->where('kelompok_id', $kelompokId)
//             ->get();
//             $statusByTugas = $status->keyBy('tugas_id');
       

//             return view('pages.Mahasiswa.Artefak.create', compact('tugas','kelompokId','idTugas', 'hasSubmitted', 'existingSubmission','statusByTugas'));
//         } catch (Exception $e) {
//             return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
//         }
//     }
    public function create($encryptedId) {
    try {
        $id = Crypt::decrypt($encryptedId);
        $tugas = Tugas::findOrFail($id);
        $idTugas = $tugas->id;
        
        // Check if deadline has passed
        $deadline = Carbon::parse($tugas->tanggal_pengumpulan);
        $now = Carbon::now();
        $isDeadlinePassed = $now->isAfter($deadline);
        
        // Calculate time remaining for display
        $diffInSeconds = $now->diffInSeconds($deadline, false);
        $tugas->formatted_deadline = $deadline->format('d M Y - h:i A');
        
        if ($diffInSeconds > 0) {
            // Still time remaining
            if ($diffInSeconds >= 86400) {
                $days = floor($diffInSeconds / 86400);
                $tugas->time_remaining = "$days hari lagi";
            } else {
                $hours = floor($diffInSeconds / 3600);
                $minutes = floor(($diffInSeconds % 3600) / 60);
                $tugas->time_remaining = "{$hours} jam {$minutes} menit lagi";
            }
            $tugas->status_class = 'text-warning';
        } else {
            // Deadline has passed
            $diffInSeconds = abs($diffInSeconds);
            if ($diffInSeconds >= 86400) {
                $days = floor($diffInSeconds / 86400);
                $tugas->time_remaining = "Selesai $days hari yang lalu";
            } else {
                $hours = floor($diffInSeconds / 3600);
                $minutes = floor(($diffInSeconds % 3600) / 60);
                $tugas->time_remaining = "Selesai {$hours} jam {$minutes} menit yang lagi";
            }
            $tugas->status_class = 'text-success';
        }
        
        $kelompokId = session('kelompok_id');
        
        // Check if group has already submitted
        $existingSubmission = pengumpulan_tugas::where('kelompok_id', $kelompokId)
            ->where('tugas_id', $tugas->id)
            ->first();
            
        $hasSubmitted = $existingSubmission ? true : false;
        
        $status = pengumpulan_tugas::with(['Kelompok', 'tugas'])
            ->where('kelompok_id', $kelompokId)
            ->get();
        $statusByTugas = $status->keyBy('tugas_id');
        
        return view('pages.Mahasiswa.Artefak.create', compact(
            'tugas', 
            'kelompokId', 
            'idTugas', 
            'hasSubmitted', 
            'existingSubmission', 
            'statusByTugas', 
            'isDeadlinePassed'
        ));
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }
    
    public function submit(Request $request){
       
        $validated = $request->validate([
            'kelompok_id' => 'required|exists:kelompok,id',
            'tugas_id'    => 'required|exists:tugas,id',
            'file_path'   => 'required|mimes:pdf,docx,zip',
            'status'      => 'nullable|string',
        ]);
        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $path = $file->store('pengumpulan_tugas_files', 'public'); // Simpan di drive
            $validated['file_path'] = $path;
        }
    
        // Tambahkan waktu submit sekarang
        $validated['waktu_submit'] = now();
    
        // Simpan ke database
        pengumpulan_tugas::create($validated);
    
        return redirect()->route('artefak.index')->with('success', 'Data berhasil disimpan.');
        
    }

    public function edit ($encryptedId){
        try {

            $id = Crypt::decrypt($encryptedId);
            $artefak = pengumpulan_tugas::findOrFail($id);
            return view('pages.Mahasiswa.Artefak.edit', compact('artefak'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function update(Request $request, $encryptedId){
        $id = Crypt::decrypt($encryptedId);
        $validated = $request->validate([
            'file_path'   => 'required',
        ]);

        $artefak = pengumpulan_tugas::findOrFail($id);
        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $path = $file->store('pengumpulan_tugas_files', 'public'); // Simpan di storage/app/public/pengumpulan_tugas_files
            $validated['file_path'] = $path;
        }
        // Update the tugas attributes
        $artefak->update($validated);
        
        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil diperbarui!');
    
    }
    public function show( $encryptedId){
        try {
            $id = Crypt::decrypt($encryptedId);
            $tugas = Tugas::findOrFail($id);
            $idTugas = $tugas->id;
            // dd($idTugas);
            $deadline = Carbon::parse($tugas->tanggal_pengumpulan);
            $now = Carbon::now();
            $diffInSeconds = $now->diffInSeconds($deadline, false);
    
            $tugas->formatted_deadline = $deadline->format('d M Y - h:i A');
    
            if ($diffInSeconds > 0) {
                // Masih ada waktu
                if ($diffInSeconds >= 86400) {
                    $days = floor($diffInSeconds / 86400);
                    $tugas->time_remaining = "$days hari lagi";
                } else {
                    $hours = floor($diffInSeconds / 3600);
                    $minutes = floor(($diffInSeconds % 3600) / 60);
                    $tugas->time_remaining = "{$hours} jam {$minutes} menit lagi";
                }
                $tugas->status_class = 'text-warning';
            } else {
                // Sudah lewat deadline
                $diffInSeconds = abs($diffInSeconds);
                if ($diffInSeconds >= 86400) {
                    $days = floor($diffInSeconds / 86400);
                    $tugas->time_remaining = "Selesai $days hari yang lalu";
                } else {
                    $hours = floor($diffInSeconds / 3600);
                    $minutes = floor(($diffInSeconds % 3600) / 60);
                    $tugas->time_remaining = "Selesai {$hours} jam {$minutes} menit yang lalu";
                }
                $tugas->status_class = 'text-success';
            }
            $kelompokId = session('kelompok_id');
            
            //cek apakah kelompk sudah submit
            $existingSubmission = pengumpulan_tugas::where('kelompok_id',$kelompokId)
            ->where('tugas_id',$tugas->id)
            ->first();
            // dd($existingSubmission);
            $hasSubmitted = $existingSubmission ? true : false;
            $kelompokId = session('kelompok_id');
         
           
            $status = pengumpulan_tugas::with(['Kelompok','tugas'])
            ->where('kelompok_id', $kelompokId)
            ->get();
            $statusByTugas = $status->keyBy('tugas_id');
       
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
            return view('pages.Mahasiswa.Artefak.show', compact('tugas','kelompokId','idTugas', 'hasSubmitted', 'existingSubmission','statusByTugas','artefak'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function editFeedback($id)
    {
        $pengumpulan = pengumpulan_tugas::with(['kelompok', 'tugas'])->findOrFail($id);
        return view('pages.feedback.create', compact('pengumpulan'));
    }

    public function updateFeedback(Request $request, $id)
    {
        $request->validate([
            'feedback' => 'required|string|max:1000',
        ]);

        $pengumpulan = pengumpulan_tugas::findOrFail($id);
        $pengumpulan->feedback = $request->feedback;
        $pengumpulan->save();

        return redirect()->route('artefak.index.koordinator', $pengumpulan->tugas_id)->with('success', 'Feedback berhasil disimpan.');
    }
    // public function editFeedbackPembimbing($id)
    // {
    //     $pengumpulan = pengumpulan_tugas::with(['kelompok', 'tugas'])->findOrFail($id);
    //     return view('pages.feedback.create_pembimbing', compact('pengumpulan'));
    // }
    public function updateFeedbackPembimbing(Request $request, $id)
    {
        $request->validate([
            'feedback_pembimbing' => 'required|string|max:1000',
        ]);

        $pengumpulan = pengumpulan_tugas::findOrFail($id);
        $pengumpulan->feedback_pembimbing = $request->feedback_pembimbing;
        $pengumpulan->save();

        return redirect()->route('artefak.index.koordinator', $pengumpulan->tugas_id)->with('success', 'Feedback pembimbing berhasil disimpan.');
    }

    public function index_koordinator($id){
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');
    
        $artefak = pengumpulan_tugas::with(['kelompok', 'tugas'])
            ->where('tugas_id', $id) // ambil berdasarkan tugas_id = $id
            ->whereHas('tugas', function ($query) use ($prodi_id, $KPA_id, $TM_id) {
                $query->where('prodi_id', $prodi_id)
                      ->where('KPA_id', $KPA_id)
                      ->where('TM_id', $TM_id);
            })
            ->get();
            // foreach($artefak as $artefakItem){
            //     if($artefakItem->tanggal_pengumpulan <=now() && $artefakItem->status !=='selesai'){
            //         $artefakItem->status = 'selesai';
            //         $artefakItem->save();
            //     }
             // }
        return view('pages.Koordinator.tugas.show_submission', compact('artefak'));
    }   

    public function Mahasiswatugas(Request $request)
    {
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');
    
        $tugas = Tugas::with(['prodi', 'tahunMasuk', 'kategoripa'])
            ->where('prodi_id', $prodi_id)
            ->where('KPA_id', $KPA_id)
            ->where('TM_id', $TM_id)
            ->where('kategori_tugas','Tugas')
            ->orderBy('created_at', 'desc')
            ->get();
            foreach($tugas as $tugasItem){
                if($tugasItem->tanggal_pengumpulan <=now() && $tugasItem->status !=='selesai'){
                    $tugasItem->status = 'selesai';
                    $tugasItem->save();
                }
              }
            foreach ($tugas as $item) {
                $deadline = Carbon::parse($item->tanggal_pengumpulan);
                $now = Carbon::now();
                $diffInSeconds = $now->diffInSeconds($deadline, false);
            
                $item->formatted_deadline = $deadline->format('d M Y - h:i A');
            
                if ($diffInSeconds > 0) {
                    // Masih ada waktu
                    if ($diffInSeconds >= 86400) { // lebih dari atau sama dengan 24 jam
                        $days = floor($diffInSeconds / 86400);
                        $item->time_remaining = "$days hari lagi";
                    } else {
                        $hours = floor($diffInSeconds / 3600);
                        $minutes = floor(($diffInSeconds % 3600) / 60);
                        $item->time_remaining = "{$hours} jam {$minutes} menit lagi";
                    }
                    $item->status_class = 'text-warning';
                } else {
                    // Sudah lewat deadline
                    $diffInSeconds = abs($diffInSeconds);
                    if ($diffInSeconds >= 86400) {
                        $days = floor($diffInSeconds / 86400);
                        $item->time_remaining = "Selesai $days hari yang lalu";
                    } else {
                        $hours = floor($diffInSeconds / 3600);
                        $minutes = floor(($diffInSeconds % 3600) / 60);
                        $item->time_remaining = "Selesai {$hours} jam {$minutes} menit yang lalu";
                    }
                    $item->status_class = 'text-danger';
                }
            }

            $kelompokId = session('kelompok_id');
         
           
            $status = pengumpulan_tugas::with(['Kelompok','tugas'])
            ->where('kelompok_id', $kelompokId)
            ->get();
            $statusByTugas = $status->keyBy('tugas_id');

        return view('pages.Mahasiswa.Tugas.index', compact('tugas','statusByTugas'));
    }

    public function Mahasiswacreate($encryptedId){
        try {
            $id = Crypt::decrypt($encryptedId);
            $tugas = Tugas::findOrFail($id);
            $idTugas = $tugas->id;
            // dd($idTugas);
            $deadline = Carbon::parse($tugas->tanggal_pengumpulan);
            $now = Carbon::now();
            $diffInSeconds = $now->diffInSeconds($deadline, false);
    
            $tugas->formatted_deadline = $deadline->format('d M Y - h:i A');
    
            if ($diffInSeconds > 0) {
                // Masih ada waktu
                if ($diffInSeconds >= 86400) {
                    $days = floor($diffInSeconds / 86400);
                    $tugas->time_remaining = "$days hari lagi";
                } else {
                    $hours = floor($diffInSeconds / 3600);
                    $minutes = floor(($diffInSeconds % 3600) / 60);
                    $tugas->time_remaining = "{$hours} jam {$minutes} menit lagi";
                }
                $tugas->status_class = 'text-warning';
            } else {
                // Sudah lewat deadline
                $diffInSeconds = abs($diffInSeconds);
                if ($diffInSeconds >= 86400) {
                    $days = floor($diffInSeconds / 86400);
                    $tugas->time_remaining = "Selesai $days hari yang lalu";
                } else {
                    $hours = floor($diffInSeconds / 3600);
                    $minutes = floor(($diffInSeconds % 3600) / 60);
                    $tugas->time_remaining = "Selesai {$hours} jam {$minutes} menit yang lalu";
                }
                $tugas->status_class = 'text-success';
            }
            $kelompokId = session('kelompok_id');
            
            //cek apakah kelompk sudah submit
            $existingSubmission = pengumpulan_tugas::where('kelompok_id',$kelompokId)
            ->where('tugas_id',$tugas->id)
            ->first();
            // dd($existingSubmission);
            $hasSubmitted = $existingSubmission ? true : false;
            $kelompokId = session('kelompok_id');
         
           
            $status = pengumpulan_tugas::with(['Kelompok','tugas'])
            ->where('kelompok_id', $kelompokId)
            ->get();
            $statusByTugas = $status->keyBy('tugas_id');
       

            return view('pages.Mahasiswa.Tugas.show', compact('tugas','kelompokId','idTugas', 'hasSubmitted', 'existingSubmission','statusByTugas'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function Mahasiswasubmit(Request $request){
       
        $validated = $request->validate([
            'kelompok_id' => 'required|exists:kelompok,id',
            'tugas_id'    => 'required|exists:tugas,id',
            'file_path'   => 'required|mimes:pdf,docx,zip',
            'status'      => 'nullable|string',
        ]);
        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $path = $file->store('pengumpulan_tugas_files', 'public'); // Simpan di storage/app/public/pengumpulan_tugas_files
            $validated['file_path'] = $path;
        }
    
        // Tambahkan waktu submit sekarang
        $validated['waktu_submit'] = now();
    
        // Simpan ke database
        pengumpulan_tugas::create($validated);
    
        return redirect()->route('Mahasiswa.tugas.index')->with('success', 'Data berhasil disimpan.');
        
    }

    public function Mahasiswaedit ($encryptedId){
        try {

            $id = Crypt::decrypt($encryptedId);
            $artefak = pengumpulan_tugas::findOrFail($id);
            return view('pages.Mahasiswa.Tugas.edit', compact('artefak'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function Mahasiswaupdate(Request $request, $encryptedId){
        $id = Crypt::decrypt($encryptedId);
        $validated = $request->validate([
            'file_path'   => 'required',
        ]);

        $artefak = pengumpulan_tugas::findOrFail($id);
        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $path = $file->store('pengumpulan_tugas_files', 'public'); // Simpan di storage/app/public/pengumpulan_tugas_files
            $validated['file_path'] = $path;
        }
        // Update the tugas attributes
        $artefak->update($validated);
        
        return redirect()->route('Mahasiswa.tugas.index')->with('success', 'Tugas berhasil diperbarui!');
    
    }
    
}
