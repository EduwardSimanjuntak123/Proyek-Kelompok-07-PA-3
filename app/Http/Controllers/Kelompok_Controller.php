<?php

namespace App\Http\Controllers;

use App\Models\DosenRole;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kelompok;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Models\kategoriPA;
use App\Models\Prodi;
use Exception;
use App\Models\TahunAjaran;
use App\Models\TahunMasuk;

class Kelompok_Controller extends Controller
{
    
    public function index(Request $request)
    {
          // Ambil data dari session
          $prodi_id = session('prodi_id');
          $KPA_id = session('KPA_id');
          $TM_id = session('TM_id');

        // Filter berdasarkan session
        $kelompok = Kelompok::with(['prodi', 'tahunMasuk', 'kategoripa'])
            ->where('prodi_id', $prodi_id)
            ->where('KPA_id', $KPA_id)
            ->where('TM_id', $TM_id)
            ->get();
    
        return view('pages.Koordinator.kelompok.index', compact('kelompok'));
    }
    
    
    public function create()
    {
        try {
            // Ambil data session, jika belum ada ambil dari database
            if (!session()->has('prodi_id') || !session()->has('KPA_id') || !session()->has('TM_id') || !session()->has('role_id')) {
                $user_id = session('user_id');
        
                // Ambil data role dosen yang aktif berdasarkan user_id
                $dosenRole = DosenRole::where('user_id', $user_id)
                                       ->where('status', 'Aktif')
                                       ->first();
                if ($dosenRole) {
                    // Simpan ke session
                    // session([
                    //     'prodi_id' => $dosenRole->prodi_id,
                    //     'KPA_id' => $dosenRole->KPA_id,
                    //     'TA_id' => $dosenRole->TA_id,
                    //     'role_id' => $dosenRole->role_id,
                    // ]);
                } else {
                    return redirect()->back()->with('error', 'Anda tidak memiliki data role yang aktif.');
                }
            }
            
            // Ambil data berdasarkan session
            $prodi = Prodi::find(session('prodi_id'));
            $kategoripa = KategoriPa::find(session('KPA_id'));
            $tahun_masuk = TahunMasuk::find(session('TM_id'));
    
            // Cek jika data terkait tidak ditemukan
            if (!$prodi || !$kategoripa || !$tahun_masuk) {
                return redirect()->back()->with('error', 'Data terkait tidak ditemukan.');
            }
        
            // Kirim data ke view
            return view('pages.Koordinator.kelompok.create', compact('prodi', 'kategoripa', 'tahun_masuk'));
    
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    
    public function store(Request $request)
    {
        // Validasi dasar
        $validated = $request->validate([
            'nomor_kelompok' => 'required|numeric',
            'prodi_id'       => 'required|exists:prodi,id',
            'KPA_id'         => 'required|exists:kategori_pa,id',
            'TM_id'          => 'required|exists:tahun_masuk,id',
            'status'         => 'required|in:Aktif,Tidak-Aktif',
        ]);
    
        // Validasi kombinasi unik dengan Eloquent langsung
        $exists = Kelompok::where('nomor_kelompok', $validated['nomor_kelompok'])
            ->where('prodi_id', $validated['prodi_id'])
            ->where('KPA_id', $validated['KPA_id'])
            ->where('TM_id', $validated['TM_id'])
            ->exists();
    
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['nomor_kelompok' => 'Nomor kelompok sudah digunakan untuk kombinasi Prodi, KPA, dan Tahun Ajaran ini.']);
        }
    
        // Simpan data jika tidak ada duplikat
        Kelompok::create($validated);
    
        return redirect()->route('kelompok.index')->with('success', 'Data berhasil disimpan.');
    }
    public function edit($encryptedId)
{
    try {
        // Dekripsi ID
        $id = Crypt::decrypt($encryptedId);
    
        // Ambil data kelompok beserta relasinya
        $kelompok = Kelompok::with(['prodi', 'tahunMasuk', 'kategoripa'])->findOrFail($id);
    
        // Validasi kecocokan session
        if (
            $kelompok->prodi_id != session('prodi_id') ||
            $kelompok->KPA_id != session('KPA_id') ||
            $kelompok->TM_id != session('TM_id')
        ) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit data ini.');
        }
    
        // Kirim data ke view edit
        return view('pages.Koordinator.kelompok.edit', compact('kelompok'));
    
    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Gagal menampilkan data: ' . $e->getMessage());
    }
}
public function update(Request $request, $encryptedId)
{
    // Dekripsi ID yang diterima
    $id = Crypt::decrypt($encryptedId);

    // Validasi input
    $request->validate([
        'nomor_kelompok' => 'required|string|max:255',  // Ganti numeric ke string jika nomor_kelompok berbentuk teks
        'status'         => 'required|in:Aktif,Tidak-Aktif',
        // Tambah validasi lain sesuai kebutuhan
    ]);
    
    try {
        // Ambil data kelompok berdasarkan ID
        $kelompok = Kelompok::findOrFail($id);
    
        // Pastikan hanya data sesuai session yang dapat diupdate
        if (
            $kelompok->prodi_id != session('prodi_id') ||
            $kelompok->KPA_id != session('KPA_id') ||
            $kelompok->TM_id != session('TM_id')
        ) {
            return redirect()->back()->with('error', 'Data tidak sesuai dengan session Anda.');
        }
    
        // Update data
        $kelompok->nomor_kelompok = $request->nomor_kelompok;
        $kelompok->status = $request->status;
        // Update field lainnya jika diperlukan
        $kelompok->save();
    
        return redirect()->route('kelompok.index')->with('success', 'Data kelompok berhasil diperbarui.');
    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
    }
}

    public function destroy($id)
    {
        try {
            $kelompok = Kelompok::findOrFail($id);
    
            // Opsional: pastikan kelompok yang dihapus sesuai dengan session
            if (
                $kelompok->prodi_id != session('prodi_id') ||
                $kelompok->KPA_id != session('KPA_id') ||
                $kelompok->TM_id != session('TM_id')
            ) {
                return redirect()->back()->with('error', 'Data tidak sesuai dengan session Anda.');
            }
            if ($kelompok->status === 'Aktif'){
                return back()->withErrors([
                    'error' => 'Tidak dapat menghapus Kelompok yang Aktif.',
                ]);
            }
    
            $kelompok->delete();
    
            return redirect()->back()->with('success', 'Data kelompok berhasil dihapus.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
   

}