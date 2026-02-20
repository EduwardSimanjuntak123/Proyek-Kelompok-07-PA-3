<?php

namespace App\Http\Controllers;

use App\Models\tahunAjaran;
use Illuminate\Http\Request;

class TahunAJaran_Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahunAjaran = TahunAjaran::all();

        return view('pages.BAAK.TahunAjaran.index', compact('tahunAjaran'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    return view('pages.BAAK.TahunAjaran.create');
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{

    $data = $request->validate([
        'tahun_mulai' => 'required|integer',
        'tahun_selesai' => 'required|integer',
        'status' => 'required'
    ]);

    tahunAjaran::create($data);

    return redirect()->route('TahunAjaran.index')
        ->with('success','Data berhasil ditambahkan');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
{
    // dd("sdhs");
    $tahunAjaran = tahunAjaran::findOrFail($id);
    return view('pages.BAAK.TahunAjaran.edit', compact('tahunAjaran'));
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $data = $request->validate([
        'tahun_mulai' => 'required|integer|min:2000|max:2100',
        'tahun_selesai' => 'required|integer|gte:tahun_mulai|max:2100',
        'status' => 'required'
    ]);

    $tahunAjaran = tahunAjaran::findOrFail($id);
    $tahunAjaran->update($data);

    return redirect()->route('TahunAjaran.index')
        ->with('success','Data berhasil diupdate');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $tahunAjaran = tahunAjaran::findOrFail($id);
    $tahunAjaran->delete();

    return redirect()->route('TahunAjaran.index')
        ->with('success', 'Data berhasil dihapus');
}
}
