<?php

namespace App\Http\Controllers;

use App\Models\kategoriPA;
use App\Models\Kelompok;
use App\Models\Nilai_kelompok;
use Illuminate\Http\Request;

class NilaiKelompok_Controller extends Controller
{
    // untuk koordinator
    public function indexpenguji2()
{
    $userId = session('user_id');
    $prodi_id = session('prodi_id');
    $KPA_id = session('KPA_id');
    $TM_id = session('TM_id');

    $kelompok = Kelompok::with(['penguji','kategoriPA','prodi'])
    ->whereHas('penguji',function ($q) use ($userId){
        $q->where('user_id', $userId);
    })->get();

    // ambil nilai yang sudah ada
    $nilaiKelompok = Nilai_kelompok::whereIn('kelompok_id', $kelompok->pluck('id'))
    ->where('role_id', 4)
    ->where('user_id',$userId)->get()->keyBy('kelompok_id');
    return view('pages.Penguji.Nilai_Kelompok.indexp2', compact('kelompok', 'nilaiKelompok', 'userId'));
}
public function storepenguji2(Request $request)
{
    $userId = session('user_id');
    $request->validate([
        'kelompok_id' => 'required|exists:kelompok,id',
        'A11' => 'required|numeric|min:0|max:100',
        'A12' => 'required|numeric|min:0|max:100',
        'A13' => 'required|numeric|min:0|max:100',
        'A1_total' => 'numeric|min:0|max:100',
        'A21' => 'required|numeric|min:0|max:100',
        'A22' => 'required|numeric|min:0|max:100',
        'A23' => 'required|numeric|min:0|max:100',
        'A2_total' => 'numeric|min:0|max:100',
        'A_total' => 'numeric|min:0|max:55',
        'user_id' => 'required',
    ]);

    // Calculate totals
    $A1_total = (0.15 * $request->A11) + (0.05 * $request->A12) + (0.05 * $request->A13);
    $A2_total = (0.2 * $request->A21) + (0.05 * $request->A22) + (0.05 * $request->A23);
    $A_total = $A1_total + $A2_total;

    // Create new Nilai_kelompok entry
    Nilai_kelompok::create([
        'kelompok_id' => $request->kelompok_id,
        'A11' => $request->A11,
        'A12' => $request->A12,
        'A13' => $request->A13,
        'A1_total' => $A1_total,
        'A21' => $request->A21,
        'A22' => $request->A22,
        'A23' => $request->A23,
        'A2_total' => $A2_total,
        'A_total' => $A_total,
        'user_id' => $userId,
        'role_id' => 4,
    ]);

    // Redirect with success message
    return redirect()->route('penguji2.NilaiKelompok.index')->with('success', 'Nilai berhasil Disimpan');
}

public function updatepenguji2(Request $request, $id)
{
    $request->validate([
        'kelompok_id' => 'required|exists:kelompok,id',
        'A11' => 'required|numeric|min:0|max:100',
        'A12' => 'required|numeric|min:0|max:100',
        'A13' => 'required|numeric|min:0|max:100',
        'A1_total' => 'numeric|min:0|max:100',
        'A21' => 'required|numeric|min:0|max:100',
        'A22' => 'required|numeric|min:0|max:100',
        'A23' => 'required|numeric|min:0|max:100',
        'A2_total' => 'numeric|min:0|max:100',
        'A_total' => 'numeric|min:0|max:55',
        'user_id' => 'required',
    ]);

    // Calculate totals
    $A1_total = (0.15 * $request->A11) + (0.05 * $request->A12) + (0.05 * $request->A13);
    $A2_total = (0.2 * $request->A21) + (0.05 * $request->A22) + (0.05 * $request->A23);
    $A_total = $A1_total + $A2_total;

    // Find and update Nilai_kelompok entry
    $nilai = Nilai_kelompok::findOrFail($id);
    $nilai->update([
        'A11' => $request->A11,
        'A12' => $request->A12,
        'A13' => $request->A13,
        'A1_total' => $A1_total,
        'A21' => $request->A21,
        'A22' => $request->A22,
        'A23' => $request->A23,
        'A2_total' => $A2_total,
        'A_total' => $A_total,
        'user_id' => $request->user_id,
    ]);

    // Redirect with success message
    return redirect()->back()->with('success', 'Nilai berhasil diperbarui');
}

public function destroypenguji2($id)
{
    $nilai = Nilai_kelompok::findOrFail($id);
    $nilai->delete();

    return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
}

public function indexpenguji1(){
    $userId = session('user_id');
    $prodi_id = session('prodi_id');
    $KPA_id = session('KPA_id');
    $TM_id = session('TM_id');


    $kelompok = Kelompok::with(['penguji','kategoriPA','prodi'])
    ->whereHas('penguji', function ($q) use ($userId){
       $q->where('user_id',$userId);
    })->get();
    
    // ambil nilai yang sudah ada
    $nilaiKelompok = Nilai_kelompok::whereIn('kelompok_id', $kelompok->pluck('id'))
    ->where('role_id', 2)
    ->where('user_id',$userId)->get()->keyBy('kelompok_id');
    return view('pages.Penguji.Nilai_Kelompok.index', compact('kelompok', 'nilaiKelompok', 'userId'));

}
public function storepenguji1(Request $request){
    $userId = session('user_id');
    $request->validate([
        'kelompok_id' => 'required|exists:kelompok,id',
        'A11' => 'required|numeric|min:0|max:100',
        'A12' => 'required|numeric|min:0|max:100',
        'A13' => 'required|numeric|min:0|max:100',
        'A1_total' => 'numeric|min:0|max:100',
        'A21' => 'required|numeric|min:0|max:100',
        'A22' => 'required|numeric|min:0|max:100',
        'A23' => 'required|numeric|min:0|max:100',
        'A2_total' => 'numeric|min:0|max:100',
        'A_total' => 'numeric|min:0|max:55',
        'user_id' => 'required',
    ]);

    // Calculate totals
    $A1_total = (0.15 * $request->A11) + (0.05 * $request->A12) + (0.05 * $request->A13);
    $A2_total = (0.2 * $request->A21) + (0.05 * $request->A22) + (0.05 * $request->A23);
    $A_total = $A1_total + $A2_total;

    // Create new Nilai_kelompok entry
    Nilai_kelompok::create([
        'kelompok_id' => $request->kelompok_id,
        'A11' => $request->A11,
        'A12' => $request->A12,
        'A13' => $request->A13,
        'A1_total' => $A1_total,
        'A21' => $request->A21,
        'A22' => $request->A22,
        'A23' => $request->A23,
        'A2_total' => $A2_total,
        'A_total' => $A_total,
        'user_id' => $userId,
        'role_id' => 2,
    ]);

    // Redirect with success message
    return redirect()->route('penguji1.NilaiKelompok.index')->with('success', 'Nilai berhasil Disimpan');

}
public function updatepenguji1(Request $request, $id)
{
    $request->validate([
        'kelompok_id' => 'required|exists:kelompok,id',
        'A11' => 'required|numeric|min:0|max:100',
        'A12' => 'required|numeric|min:0|max:100',
        'A13' => 'required|numeric|min:0|max:100',
        'A1_total' => 'numeric|min:0|max:100',
        'A21' => 'required|numeric|min:0|max:100',
        'A22' => 'required|numeric|min:0|max:100',
        'A23' => 'required|numeric|min:0|max:100',
        'A2_total' => 'numeric|min:0|max:100',
        'A_total' => 'numeric|min:0|max:55',
        'user_id' => 'required',
    ]);

    // Calculate totals
    $A1_total = (0.15 * $request->A11) + (0.05 * $request->A12) + (0.05 * $request->A13);
    $A2_total = (0.2 * $request->A21) + (0.05 * $request->A22) + (0.05 * $request->A23);
    $A_total = $A1_total + $A2_total;

    // Find and update Nilai_kelompok entry
    $nilai = Nilai_kelompok::findOrFail($id);
    $nilai->update([
        'A11' => $request->A11,
        'A12' => $request->A12,
        'A13' => $request->A13,
        'A1_total' => $A1_total,
        'A21' => $request->A21,
        'A22' => $request->A22,
        'A23' => $request->A23,
        'A2_total' => $A2_total,
        'A_total' => $A_total,
        'user_id' => $request->user_id,
    ]);

    // Redirect with success message
    return redirect()->back()->with('success', 'Nilai berhasil diperbarui');
}

public function destroypenguji1($id)
{
    $nilai = Nilai_kelompok::findOrFail($id);
    $nilai->delete();

    return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
}

public function indexpembimbing1(){
    $userId = session('user_id');
    
    $kelompok = Kelompok::with(['pembimbing','kategoriPA','prodi'])
    ->whereHas('pembimbing', function ($q) use($userId){
        $q->where('user_id', $userId);
    })->get();

    $nilaiKelompok = Nilai_kelompok::whereIn('kelompok_id', $kelompok->pluck('id'))
    ->where('role_id', 3)
    ->where('user_id',$userId)->get()->keyBy('kelompok_id');
    // dd($kelompok);
    return view('pages.Pembimbing.Nilai_Kelompok.index', compact('kelompok', 'nilaiKelompok', 'userId'));

}
public function storepembimbing1(Request $request){
    $userId = session('user_id');

    $request->validate([
        'kelompok_id' => 'required|exists:kelompok,id',
        'A11' => 'required|numeric|min:0|max:100',
        'A12' => 'required|numeric|min:0|max:100',
        'A13' => 'required|numeric|min:0|max:100',
        'A1_total' => 'numeric|min:0|max:100',
        'A21' => 'required|numeric|min:0|max:100',
        'A22' => 'required|numeric|min:0|max:100',
        'A23' => 'required|numeric|min:0|max:100',
        'A2_total' => 'numeric|min:0|max:100',
        'A_total' => 'numeric|min:0|max:55',
        'user_id' => 'required',
    ]);

    // Calculate totals
    $A1_total = (0.15 * $request->A11) + (0.05 * $request->A12) + (0.05 * $request->A13);
    $A2_total = (0.2 * $request->A21) + (0.05 * $request->A22) + (0.05 * $request->A23);
    $A_total = $A1_total + $A2_total;

    // Create new Nilai_kelompok entry
    Nilai_kelompok::create([
        'kelompok_id' => $request->kelompok_id,
        'A11' => $request->A11,
        'A12' => $request->A12,
        'A13' => $request->A13,
        'A1_total' => $A1_total,
        'A21' => $request->A21,
        'A22' => $request->A22,
        'A23' => $request->A23,
        'A2_total' => $A2_total,
        'A_total' => $A_total,
        'user_id' => $userId,
        'role_id' => 3,
    ]);

    // Redirect with success message
    return redirect()->route('pembimbing1.NilaiKelompok.index')->with('success', 'Nilai berhasil Disimpan');
}
public function updatepembimbing1(Request $request, $id)
{
    $request->validate([
        'kelompok_id' => 'required|exists:kelompok,id',
        'A11' => 'required|numeric|min:0|max:100',
        'A12' => 'required|numeric|min:0|max:100',
        'A13' => 'required|numeric|min:0|max:100',
        'A1_total' => 'numeric|min:0|max:100',
        'A21' => 'required|numeric|min:0|max:100',
        'A22' => 'required|numeric|min:0|max:100',
        'A23' => 'required|numeric|min:0|max:100',
        'A2_total' => 'numeric|min:0|max:100',
        'A_total' => 'numeric|min:0|max:55',
        'user_id' => 'required',
    ]);

    // Calculate totals
    $A1_total = (0.15 * $request->A11) + (0.05 * $request->A12) + (0.05 * $request->A13);
    $A2_total = (0.2 * $request->A21) + (0.05 * $request->A22) + (0.05 * $request->A23);
    $A_total = $A1_total + $A2_total;

    // Find and update Nilai_kelompok entry
    $nilai = Nilai_kelompok::findOrFail($id);
    $nilai->update([
        'A11' => $request->A11,
        'A12' => $request->A12,
        'A13' => $request->A13,
        'A1_total' => $A1_total,
        'A21' => $request->A21,
        'A22' => $request->A22,
        'A23' => $request->A23,
        'A2_total' => $A2_total,
        'A_total' => $A_total,
        'user_id' => $request->user_id,
    ]);

    // Redirect with success message
    return redirect()->back()->with('success', 'Nilai berhasil diperbarui');
}

public function destroypembimbing1($id){

    $nilai =Nilai_kelompok::findOrFail($id);
    $nilai->delete();
  
    return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
}
// untuk pembimbing 2
public function indexpembimbing2(){
    $userId = session('user_id');
    
    $kelompok = Kelompok::with(['pembimbing','kategoriPA','prodi'])
    ->whereHas('pembimbing', function ($q) use($userId){
        $q->where('user_id', $userId);
    })->get();

    $nilaiKelompok = Nilai_kelompok::whereIn('kelompok_id', $kelompok->pluck('id'))
    ->where('role_id', 5)
    ->where('user_id',$userId)->get()->keyBy('kelompok_id');
    return view('pages.Pembimbing.Nilai_Kelompok.indexp2', compact('kelompok', 'nilaiKelompok', 'userId'));

}
public function storepembimbing2(Request $request){
    $userId = session('user_id');

    $request->validate([
        'kelompok_id' => 'required|exists:kelompok,id',
        'A11' => 'required|numeric|min:0|max:100',
        'A12' => 'required|numeric|min:0|max:100',
        'A13' => 'required|numeric|min:0|max:100',
        'A1_total' => 'numeric|min:0|max:100',
        'A21' => 'required|numeric|min:0|max:100',
        'A22' => 'required|numeric|min:0|max:100',
        'A23' => 'required|numeric|min:0|max:100',
        'A2_total' => 'numeric|min:0|max:100',
        'A_total' => 'numeric|min:0|max:55',
        'user_id' => 'required',
    ]);

    // Calculate totals
    $A1_total = (0.15 * $request->A11) + (0.05 * $request->A12) + (0.05 * $request->A13);
    $A2_total = (0.2 * $request->A21) + (0.05 * $request->A22) + (0.05 * $request->A23);
    $A_total = $A1_total + $A2_total;

    // Create new Nilai_kelompok entry
    Nilai_kelompok::create([
        'kelompok_id' => $request->kelompok_id,
        'A11' => $request->A11,
        'A12' => $request->A12,
        'A13' => $request->A13,
        'A1_total' => $A1_total,
        'A21' => $request->A21,
        'A22' => $request->A22,
        'A23' => $request->A23,
        'A2_total' => $A2_total,
        'A_total' => $A_total,
        'user_id' => $userId,
        'role_id' => 5,
    ]);

    // Redirect with success message
    return redirect()->route('pembimbing2.NilaiKelompok.index')->with('success', 'Nilai berhasil Disimpan');
}
public function updatepembimbing2(Request $request, $id)
{
    $request->validate([
        'kelompok_id' => 'required|exists:kelompok,id',
        'A11' => 'required|numeric|min:0|max:100',
        'A12' => 'required|numeric|min:0|max:100',
        'A13' => 'required|numeric|min:0|max:100',
        'A1_total' => 'numeric|min:0|max:100',
        'A21' => 'required|numeric|min:0|max:100',
        'A22' => 'required|numeric|min:0|max:100',
        'A23' => 'required|numeric|min:0|max:100',
        'A2_total' => 'numeric|min:0|max:100',
        'A_total' => 'numeric|min:0|max:55',
        'user_id' => 'required',
        'role_id' => 5,
    ]);

    // Calculate totals
    $A1_total = (0.15 * $request->A11) + (0.05 * $request->A12) + (0.05 * $request->A13);
    $A2_total = (0.2 * $request->A21) + (0.05 * $request->A22) + (0.05 * $request->A23);
    $A_total = $A1_total + $A2_total;

    // Find and update Nilai_kelompok entry
    $nilai = Nilai_kelompok::findOrFail($id);
    $nilai->update([
        'A11' => $request->A11,
        'A12' => $request->A12,
        'A13' => $request->A13,
        'A1_total' => $A1_total,
        'A21' => $request->A21,
        'A22' => $request->A22,
        'A23' => $request->A23,
        'A2_total' => $A2_total,
        'A_total' => $A_total,
        'user_id' => $request->user_id,
    ]);

    // Redirect with success message
    return redirect()->back()->with('success', 'Nilai berhasil diperbarui');
}

public function destroypembimbing2($id){

    $nilai =Nilai_kelompok::findOrFail($id);
    $nilai->delete();
  
    return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
}

}
