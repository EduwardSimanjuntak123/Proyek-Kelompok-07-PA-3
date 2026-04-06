<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\Models\DosenRole;
// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\Role;
use App\Models\Tugas;
use App\Models\Prodi;
use App\Models\TahunAjaran;
use App\Models\kategoriPA;
use App\Models\TahunMasuk;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Storage;


class TugasController extends Controller
{
    public function index(Request $request){
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');
        $user_id = session('user_id');

      $tugas = tugas::with(['prodi', 'tahunMasuk', 'kategoripa'])
      ->where('prodi_id', $prodi_id)
      ->where('KPA_id', $KPA_id)
      ->where('TM_id', $TM_id)
      ->where('user_id',$user_id)
      ->get();

      foreach($tugas as $tugasItem){
        if($tugasItem->tanggal_pengumpulan <=now() && $tugasItem->status !=='selesai'){
            $tugasItem->status = 'selesai';
            $tugasItem->save();
        }
      }
        return view('pages.Koordinator.tugas.index', compact('tugas'));
    }
   
    public function create()
    {
        try{
              // Ambil data session, jika belum ada ambil dari database
            if (!session()->has('prodi_id') || !session()->has('KPA_id') || !session()->has('TM_id') || !session()->has('role_id')) {
                $user_id = session('user_id');
        
                // Ambil data role dosen yang aktif berdasarkan user_id
                $dosenRole = DosenRole::where('user_id', $user_id)
                                       ->where('status', 'Aktif')
                                       ->first();
         }
        $prodi = Prodi::find(session('prodi_id'));
        $kategoripa = KategoriPa::find(session('KPA_id'));
        $tahun_masuk = TahunMasuk::find(session('TM_id'));
        $user_id = session('user_id');
         
        // Cek jika data terkait tidak ditemukan
        if (!$prodi || !$kategoripa || !$tahun_masuk) {
            return redirect()->back()->with('error', 'Data terkait tidak ditemukan.');
        }
        return view('pages.Koordinator.tugas.create',compact('prodi', 'kategoripa', 'tahun_masuk','user_id'));
    

    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
    
}

public function store(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required|numeric',
        'Judul_Tugas' => 'required|string|max:500',
        'Deskripsi_Tugas' => 'required|string|max:1000',
        'prodi_id'       => 'required|exists:prodi,id',
        'KPA_id'         => 'required|exists:kategori_pa,id',
        'TM_id'          => 'required|exists:tahun_masuk,id',
        'tanggal_pengumpulan' => 'required|date|after_or_equal:today',
        'file' => 'nullable|mimes:pdf,docx,jpg,jpeg,png|max:10240', 
        'status' => 'required',
        'kategori_tugas' => 'required',
    ]);

    // Handle file upload if exists
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $filePath = $file->store('tugas_files', 'public'); // Save file to 'tugas_files' directory in the public disk
        $validated['file'] = $filePath;
    }
    // if ($request->hasFile('file')) {
    // $uploadedFileUrl = Cloudinary::upload($request->file('file')->getRealPath(), [
    //     'folder' => 'tugas_files',
    //     'resource_type' => 'auto'
    // ])->getSecurePath();

    // $validated['file'] = $uploadedFileUrl;
// }

    Tugas::create($validated);
    return redirect()->route('koordinator.tugas.index')->with('success', 'Tugas berhasil disimpan.');
}

public function edit($encryptedId)
{
    try {
        // Decrypt the encrypted ID
        $id = Crypt::decrypt($encryptedId);
        $tugas = Tugas::findOrFail($id);

        // Retrieve session data or fetch from database
        if (!session()->has('prodi_id') || !session()->has('KPA_id') || !session()->has('TM_id') || !session()->has('role_id')) {
            $user_id = session('user_id');

            $dosenRole = DosenRole::where('user_id', $user_id)
                                   ->where('status', 'Aktif')
                                   ->first();

            if (!$dosenRole) {
                return redirect()->back()->with('error', 'Role dosen tidak ditemukan.');
            }
        } else {
            $user_id = session('user_id');
        }

        $prodi = Prodi::find(session('prodi_id'));
        $kategoripa = KategoriPa::find(session('KPA_id'));
        $tahun_masuk = TahunMasuk::find(session('TM_id'));

        if (!$prodi || !$kategoripa || !$tahun_masuk) {
            return redirect()->back()->with('error', 'Data terkait tidak ditemukan.');
        }

        return view('pages.Koordinator.tugas.edit', compact('prodi', 'kategoripa', 'tahun_masuk', 'user_id', 'tugas'));

    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

public function update(Request $request, $encryptedId)
{
    $id = Crypt::decrypt($encryptedId);

    $validated = $request->validate([
        'user_id' => 'required|numeric',
        'Judul_Tugas' => 'required|string|max:500',
        'Deskripsi_Tugas' => 'required|string|max:1000',
        'prodi_id'       => 'required|exists:prodi,id',
        'KPA_id'         => 'required|exists:kategori_pa,id',
        'TM_id'          => 'required|exists:tahun_masuk,id',
        'tanggal_pengumpulan' => 'required|date|after_or_equal:today',
        'file' => 'nullable|mimes:pdf,docx,jpg,jpeg,png|max:10240', 
        'status' => 'required',
       
    ]);

    $tugas = Tugas::findOrFail($id);

    // Handle file upload if exists
    if ($request->hasFile('file')) {
        // Hapus file lama jika ada
        if ($tugas->file && Storage::disk('public')->exists($tugas->file)) {
            Storage::disk('public')->delete($tugas->file);
        }
    
        $file = $request->file('file');
        $filePath = $file->store('tugas_files', 'public');
        $validated['file'] = $filePath;
    }
    // Update the tugas attributes
    $tugas->update($validated);
    
    return redirect()->route('koordinator.tugas.index')->with('success', 'Tugas berhasil diperbarui!');
}

    

public function destroy($id)
{
    $tugas = Tugas::findOrFail($id);

    if ($tugas->status === 'berlangsung') {
        return back()->withErrors([
            'error' => 'Tidak dapat menghapus Tugas yang sedang Berlangsung.',
        ]);
    }

    // Hapus file dari storage jika ada
    if ($tugas->file && Storage::disk('public')->exists($tugas->file)) {
        Storage::disk('public')->delete($tugas->file);
    }

    $tugas->delete();
    return redirect()->back()->with('success', 'Data tugas berhasil dihapus.');
}
    

    public function show($id){
        $tugas = Tugas::findOrFail($id);
        return view('pages.Koordinator.tugas.show', compact('tugas'));
    }

}
