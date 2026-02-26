<?php

namespace App\Http\Controllers;

use App\Models\tahunAjaran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TahunAJaran_Controller extends Controller
{
    public static function getTahunAjaranAktif()
{
    $now = Carbon::now();

    $tahunAjaranAktif = TahunAjaran::where('status', 'Aktif')->first();

    if ($tahunAjaranAktif) {

        if (
            $now->year > $tahunAjaranAktif->tahun_selesai ||
            ($now->year == $tahunAjaranAktif->tahun_selesai && $now->month >= 7)
        ) {

            $tahunAjaranAktif->update([
                'status' => 'Tidak-Aktif'
            ]);

            $tahunAjaranAktif = null;
        }
    }

    if (!$tahunAjaranAktif) {

        if ($now->month >= 7) {
            $tahunMulai = $now->year;
        } else {
            $tahunMulai = $now->year - 1;
        }

        $tahunAjaranAktif = TahunAjaran::create([
            'tahun_mulai'   => $tahunMulai,
            'tahun_selesai' => $tahunMulai + 1,
            'status'        => 'Aktif'
        ]);
    }

    return $tahunAjaranAktif;
}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    
}

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
{
    
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
   
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
   
}
}
