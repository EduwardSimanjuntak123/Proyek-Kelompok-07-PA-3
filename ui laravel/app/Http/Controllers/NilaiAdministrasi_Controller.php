<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\Nilai_Administrasi;
use App\Models\Nilai_Individu;
use Illuminate\Http\Request;

class NilaiAdministrasi_Controller extends Controller
{
    // ═══════════════════════════════════════════════
    //  INDEX
    // ═══════════════════════════════════════════════
    public function index()
    {
        $userId   = session('user_id');
        $prodi_id = session('prodi_id');
        $KPA_id   = session('KPA_id');
        $TM_id    = session('TM_id');

        $kelompok = Kelompok::with([
            'KelompokMahasiswa.mahasiswa'
        ])
        ->where('prodi_id', $prodi_id)
        ->where('KPA_id', $KPA_id)
        ->where('TM_id', $TM_id)
        ->get();

        $kelompokIds = $kelompok->pluck('id');

        // Nilai administrasi kelompok — key: kelompok_id
        $nilaiKelompok = Nilai_Administrasi::whereIn('kelompok_id', $kelompokIds)
            ->get()
            ->keyBy('kelompok_id');

        // Nilai individu (logbook) — key: "kelompok_id_user_id"
        $nilaiIndividu = Nilai_Individu::whereIn('kelompok_id', $kelompokIds)
            ->whereNotNull('D1')
            ->get()
            ->keyBy(fn($n) => $n->kelompok_id . '_' . $n->user_id);

        return view(
            'pages.Koordinator.Nilai_Administrasi.index',
            compact('kelompok', 'nilaiKelompok', 'nilaiIndividu', 'userId')
        );
    }

    // ═══════════════════════════════════════════════
    //  STORE NILAI KELOMPOK
    // ═══════════════════════════════════════════════
    public function store(Request $request)
    {
        $request->validate([
            'kelompok_id' => 'required|exists:kelompok,id',
            'C1'      => 'required|numeric|min:0|max:100',
            'C2'      => 'required|numeric|min:0|max:100',
            'C3'      => 'required|numeric|min:0|max:100',
            'C4'      => 'required|numeric|min:0|max:100',
            'C5'      => 'required|numeric|min:0|max:100',
            'Pameran' => 'required|numeric|min:0|max:100',
        ]);

        $cTotal = ($request->C1 + $request->C2 + $request->C3 + $request->C4 + $request->C5) / 5;

        Nilai_Administrasi::create([
            'kelompok_id'  => $request->kelompok_id,
            'user_id'      => session('user_id'),
            'C1'           => $request->C1,
            'C2'           => $request->C2,
            'C3'           => $request->C3,
            'C4'           => $request->C4,
            'C5'           => $request->C5,
            'Pameran'      => $request->Pameran,
            'C_total'      => $cTotal,
            'Administrasi' => $cTotal,
            'Total'        => $cTotal,
        ]);

        return back()->with('success', 'Nilai administrasi kelompok berhasil disimpan.');
    }

    // ═══════════════════════════════════════════════
    //  UPDATE NILAI KELOMPOK
    // ═══════════════════════════════════════════════
    public function update(Request $request, $id)
    {
        $request->validate([
            'kelompok_id' => 'required|exists:kelompok,id',
            'C1'      => 'required|numeric|min:0|max:100',
            'C2'      => 'required|numeric|min:0|max:100',
            'C3'      => 'required|numeric|min:0|max:100',
            'C4'      => 'required|numeric|min:0|max:100',
            'C5'      => 'required|numeric|min:0|max:100',
            'Pameran' => 'required|numeric|min:0|max:100',
        ]);

        $cTotal = ($request->C1 + $request->C2 + $request->C3 + $request->C4 + $request->C5) / 5;

        $nilai = Nilai_Administrasi::findOrFail($id);
        $nilai->update([
            'C1'           => $request->C1,
            'C2'           => $request->C2,
            'C3'           => $request->C3,
            'C4'           => $request->C4,
            'C5'           => $request->C5,
            'Pameran'      => $request->Pameran,
            'C_total'      => $cTotal,
            'Administrasi' => $cTotal,
            'Total'        => $cTotal,
        ]);

        return back()->with('success', 'Nilai administrasi kelompok berhasil diupdate.');
    }

    // ═══════════════════════════════════════════════
    //  DESTROY NILAI KELOMPOK
    // ═══════════════════════════════════════════════
    public function destroy($id)
    {
        $nilai = Nilai_Administrasi::findOrFail($id);
        $nilai->delete();

        return back()->with('success', 'Nilai administrasi kelompok berhasil dihapus.');
    }

    // ═══════════════════════════════════════════════
//  STORE INDIVIDU — updateOrCreate agar tidak duplicate
// ═══════════════════════════════════════════════
public function storeIndividu(Request $request)
{
    $request->validate([
        'kelompok_id' => 'required|exists:kelompok,id',
        'user_id'     => 'required',
        'D1'          => 'required|numeric|min:0|max:100',
    ]);

    // Ambil nilai administrasi kelompok
    $nilaiKelompok = Nilai_Administrasi::where(
        'kelompok_id',
        $request->kelompok_id
    )->firstOrFail();

    // Hitung nilai administrasi akhir
    $administrasi = (
        ($nilaiKelompok->C_total + (float)$request->D1) / 2
    ) * 0.10;

    $nilaiKelompok->update([
    'Administrasi' => $administrasi,
]);
    // Simpan logbook mahasiswa
    Nilai_Individu::updateOrCreate(
        [
            'kelompok_id' => $request->kelompok_id,
            'user_id'     => $request->user_id,
        ],
        [
            'penilai_id' => session('user_id'),
            'role_id'    => session('role_id') ?? 1,

            'B11' => 0,
            'B12' => 0,
            'B13' => 0,
            'B14' => 0,
            'B15' => 0,

            'B21' => 0,
            'B22' => 0,
            'B23' => 0,
            'B24' => 0,
            'B25' => 0,

            'B31' => 0,

            'D1' => (float)$request->D1,
        ]
    );

    // Update nilai administrasi akhir
    $nilaiKelompok->update([
        'Administrasi' => $administrasi,
    ]);

    return back()->with(
        'success',
        'Nilai logbook berhasil disimpan.'
    );
}

// ═══════════════════════════════════════════════
//  UPDATE INDIVIDU — pakai updateOrCreate juga
// ═══════════════════════════════════════════════
public function updateIndividu(Request $request, $id)
{
    $request->validate([
        'kelompok_id' => 'required|exists:kelompok,id',
        'user_id'     => 'required',
        'D1'          => 'required|numeric|min:0|max:100',
    ]);

    $nilai = Nilai_Individu::findOrFail($id);

    $nilai->update([
        'D1' => (float)$request->D1,
    ]);

    // Ambil nilai kelompok
    $nilaiKelompok = Nilai_Administrasi::where(
        'kelompok_id',
        $request->kelompok_id
    )->firstOrFail();

    // Hitung ulang administrasi
    $administrasi = (
        ($nilaiKelompok->C_total + (float)$request->D1) / 2
    ) * 0.10;

    $nilaiKelompok->update([
        'Administrasi' => $administrasi,
    ]);

    return back()->with(
        'success',
        'Nilai logbook berhasil diupdate.'
    );
}

// ═══════════════════════════════════════════════
//  DESTROY INDIVIDU — set null saja, jangan delete
// ═══════════════════════════════════════════════
public function destroyIndividu($id)
{
    $nilai = Nilai_Individu::findOrFail($id);

    $nilaiKelompok = Nilai_Administrasi::where(
        'kelompok_id',
        $nilai->kelompok_id
    )->first();

    if ($nilaiKelompok) {
        $nilaiKelompok->update([
            'Administrasi' => null,
        ]);
    }

    $nilai->delete();

    return back()->with(
        'success',
        'Nilai logbook berhasil dihapus.'
    );
}
}