<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\Nilai_Administrasi;
use Illuminate\Http\Request;

class NilaiAdministrasi_Controller extends Controller
{
    public function index(){
        $userId = session('user_id');
        $prodi_id = session('prodi_id');
        $KPA_id = session('KPA_id');
        $TM_id = session('TM_id');

        $kelompok = Kelompok::with('prodi','tahunMasuk','kategoriPA')
        ->where('prodi_id',$prodi_id)
        ->where('KPA_id', $KPA_id)
        ->where('TM_id', $TM_id)
        ->get();
// dd($kelompok);
        $kelompokIds = $kelompok->pluck('id'); // ambil id dari semua kelompok
        $nilaiAdministrasi = Nilai_Administrasi::whereIn('kelompok_id', $kelompokIds)->get()->keyBy('kelompok_id');

        return view('pages.Koordinator.Nilai_Administrasi.index', compact('kelompok', 'nilaiAdministrasi', 'userId'));
    }

    public function store(Request $request){
        $userId = session('user_id');
    $request->validate([
        'kelompok_id' => 'required|exists:kelompok,id',
        'Administrasi' => 'required|numeric|min:0|max:100',
        'Pameran' => 'required|numeric|min:0|max:100',
        'Total' => 'numeric|min:0|max:100',
        'user_id' => 'required',
    ]);

    // Calculate totals
    $Total = (0.1 * $request->Administrasi) + (0.05 * $request->Pameran);
   
    // Create new Nilai_kelompok entry
    Nilai_Administrasi::create([
        'kelompok_id' => $request->kelompok_id,
        'Administrasi' => $request->Administrasi,
        'Pameran' => $request->Pameran,
        'Total' => $Total,
        'user_id' => $userId,
    ]);

    // Redirect with success message
    return redirect()->route('koordinator.NilaiAdministrasi.index')->with('success', 'Nilai berhasil Disimpan');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'kelompok_id' => 'required|exists:kelompok,id',
            'Administrasi' => 'required|numeric|min:0|max:100',
            'Pameran' => 'required|numeric|min:0|max:100',
            'Total' => 'numeric|min:0|max:55',
            'user_id' => 'required',
        ]);
    
        // Calculate totals
        $Total = (0.1 * $request->Administrasi) + (0.05 * $request->Pameran);
   
        // Find and update Nilai_kelompok entry
        $nilai = Nilai_Administrasi::findOrFail($id);
        $nilai->update([
            'Administrasi' => $request->Administrasi,
            'Pameran' => $request->Pameran,
            'Total' => $Total,
            'user_id' => $request->user_id,
        ]);
    
        // Redirect with success message
        return redirect()->back()->with('success', 'Nilai berhasil diperbarui');
    }
    
    public function destroy($id)
    {
        $nilai = Nilai_Administrasi::findOrFail($id);
        $nilai->delete();
    
        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }
    
}
