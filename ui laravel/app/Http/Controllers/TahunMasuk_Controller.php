<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use App\Models\TahunMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class TahunMasuk_Controller extends Controller
{
    public function index(){
        $TahunMasuk = TahunMasuk::all();

        return view('pages.BAAK.TahunMasuk.index',compact('TahunMasuk'));
    }

    public function create(){
        return view('pages.BAAK.TahunMasuk.create');
    }

    public function store(Request $request){
        $validated = $request->validate([
            'Tahun_Masuk' => 'required|digits:4|integer|min:2000|max:' . date('Y').'|unique:tahun_masuk,Tahun_Masuk',
            'Status'  => 'required'
        ],
        [ 
        'Tahun_Masuk.required' => 'Tahun Ajaran wajib diisi.',
        'Tahun_Masuk.digits'   => 'Tahun Ajaran harus 4 digit.',
        'Tahun_Masuk.integer'  => 'Tahun Ajaran harus berupa angka.',
        'Tahun_Masuk.min'      => 'Tahun Ajaran minimal tahun 2000.',
        'Tahun_Masuk.max'      => 'Tahun Ajaran tidak boleh lebih dari tahun sekarang.',
        'Tahun_Masuk.unique'   => 'Tahun Ajaran sudah terdaftar.',

        ]);

         TahunMasuk::create($validated);
         return redirect()->route('TahunMasuk.index')->with('success', 'Pengumuman berhasil ditambahkan.');
        
    }

    public function edit($encryptedId){
        $id = Crypt::decrypt($encryptedId);

        $TahunMasuk =TahunMasuk::findOrFail($id);

        return view('pages.BAAK.TahunMasuk.edit',compact('TahunMasuk'));
    }

    public function update(Request $request, $encryptedId){
        
        $id = Crypt::decrypt($encryptedId);
    
        $TahunMasuk = TahunMasuk::findOrFail($id);

        $validated = $request->validate([
            'Tahun_Masuk' => 'required|digits:4|integer|min:2000|max:' . date('Y') .
                '|unique:tahun_masuk,Tahun_Masuk,' . $TahunMasuk->id,
            'Status' => 'required|in:Aktif,Tidak-Aktif',
        ], [
            'Tahun_Masuk.required' => 'Tahun Ajaran wajib diisi.',
            'Tahun_Masuk.digits' => 'Tahun Ajaran harus 4 digit.',
            'Tahun_Masuk.integer' => 'Tahun Ajaran harus berupa angka.',
            'Tahun_Masuk.min' => 'Tahun Ajaran minimal tahun 2000.',
            'Tahun_Masuk.max' => 'Tahun Ajaran tidak boleh lebih dari tahun sekarang.',
            'Tahun_Masuk.unique' => 'Tahun Ajaran sudah terdaftar.',
        ]);        
        $TahunMasuk->update($validated);
      
        return redirect()->route('TahunMasuk.index')->with('success', 'Tahun Ajaran berhasil diperbarui!');
    }
    
    public function destroy($id){
        $TahunMasuk = TahunMasuk::findOrFail($id);
        if($TahunMasuk->Status === 'Aktif') {
            return back()->withErrors([
                'error' => 'Tidak dapat menghapus Tugas yang sedang Berlangsung.',
            ]);   
    }
    $TahunMasuk->delete();
    return redirect()->back()->with('success', 'Data tugas berhasil dihapus.');

}
}