<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\Nilai_Individu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NilaiIndividu_Controller extends Controller
{
    // -------------------------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------------------------

    /**
     * Filter kelompok berdasarkan slot urutan penguji.
     * slot 0 = Penguji 1, slot 1 = Penguji 2.
     */
    private function filterKelompokByPengujiSlot($kelompok, string $userId, int $slot)
    {
        return $kelompok->filter(function ($item) use ($userId, $slot) {
            $pengujiUrut = $item->penguji->sortBy('id')->values();
            $pengujiSlot = $pengujiUrut->get($slot);

            return $pengujiSlot && (string) $pengujiSlot->user_id === (string) $userId;
        })->values();
    }

    /**
     * Filter kelompok berdasarkan slot urutan pembimbing.
     * slot 0 = Pembimbing 1 (id terkecil), slot 1 = Pembimbing 2 (id terbesar).
     *
     * Catatan: jika tabel pembimbing punya kolom 'urutan' atau 'tipe',
     * ganti sortBy('id') dengan sortBy('urutan') atau filter by tipe.
     */
    private function filterKelompokByPembimbingSlot($kelompok, string $userId, int $slot)
    {
        return $kelompok->filter(function ($item) use ($userId, $slot) {
            $pembimbingUrut = $item->pembimbing->sortBy('id')->values();
            $pembimbingSlot = $pembimbingUrut->get($slot);

            return $pembimbingSlot && (string) $pembimbingSlot->user_id === (string) $userId;
        })->values();
    }

    /**
     * Hitung komponen dan total nilai individu dari B11–B31.
     */
    private function hitungKomponenB(Request $request): array
    {
        $B1_total = 0.02 * ($request->B11 + $request->B12 + $request->B13 + $request->B14 + $request->B15);
        $B2_total = 0.02 * ($request->B21 + $request->B22 + $request->B23 + $request->B24 + $request->B25);
        $B3_total = 0.25 * $request->B31;

        return [
            'B1_total' => $B1_total,
            'B2_total' => $B2_total,
            'B3_total' => $B3_total,
            'B_total'  => $B1_total + $B2_total + $B3_total,
        ];
    }

    /**
     * Aturan validasi input nilai individu.
     */
    private function validasiRules(bool $includeUserPenilai = true): array
    {
        $rules = [
            'B11' => 'required|numeric|min:0|max:100',
            'B12' => 'required|numeric|min:0|max:100',
            'B13' => 'required|numeric|min:0|max:100',
            'B14' => 'required|numeric|min:0|max:100',
            'B15' => 'required|numeric|min:0|max:100',
            'B21' => 'required|numeric|min:0|max:100',
            'B22' => 'required|numeric|min:0|max:100',
            'B23' => 'required|numeric|min:0|max:100',
            'B24' => 'required|numeric|min:0|max:100',
            'B25' => 'required|numeric|min:0|max:100',
            'B31' => 'required|numeric|min:0|max:100',
        ];

        if ($includeUserPenilai) {
            $rules['user_id']    = 'required';
            $rules['penilai_id'] = 'required';
        }

        return $rules;
    }

    /**
     * Ambil data untuk halaman index nilai individu (pembimbing).
     * Hanya menampilkan kelompok di mana dosen ini berada di slot $slot
     * (0 = Pembimbing 1, 1 = Pembimbing 2).
     *
     * @param int    $roleIdPenilai  role_id yang disimpan di nilai_individu (3 atau 5)
     * @param int    $slot           0 = Pembimbing 1, 1 = Pembimbing 2
     * @param string $viewPath       path blade view
     */
    private function indexPembimbing(int $roleIdPenilai, int $slot, string $viewPath)
    {
        $token  = session('token');
        $userId = session('user_id');

        // Ambil semua kelompok di mana dosen ini terdaftar sebagai pembimbing (apapun slot-nya)
        $kelompoks = Kelompok::with([
                'pembimbing' => fn($q) => $q->orderBy('id'), // urutkan agar slot konsisten
                'KelompokMahasiswa',
                'nilais',
                'kategoriPA',
                'prodi',
            ])
            ->whereHas('pembimbing', fn($q) => $q->where('user_id', $userId))
            ->get();

        // Filter hanya kelompok di mana dosen ini berada di slot yang sesuai
        $kelompoks = $this->filterKelompokByPembimbingSlot($kelompoks, $userId, $slot);

        $mahasiswa_map = $this->fetchMahasiswaFromApi($token);

        $user_ids = $kelompoks->flatMap(fn($k) => $k->KelompokMahasiswa->pluck('user_id'));

        $nilaiindividu = Nilai_Individu::whereIn('user_id', $user_ids)
            ->where('penilai_id', $userId)
            ->where('role_id', $roleIdPenilai)
            ->get()
            ->keyBy('user_id');

        $this->pasangDataMahasiswa($kelompoks, $mahasiswa_map, $nilaiindividu);

        return view($viewPath, compact('kelompoks', 'nilaiindividu'));
    }

    /**
     * Ambil data untuk halaman index nilai individu (penguji).
     */
    private function indexPenguji(int $roleIdPenilai, int $slot, string $viewPath)
    {
        $token  = session('token');
        $userId = session('user_id');

        $kelompoks = Kelompok::with([
                'penguji' => fn($q) => $q->orderBy('id'),
                'KelompokMahasiswa',
                'nilais',
                'kategoriPA',
                'prodi',
            ])
            ->whereHas('penguji', fn($q) => $q->where('user_id', $userId))
            ->get();

        $kelompoks = $this->filterKelompokByPengujiSlot($kelompoks, $userId, $slot);

        $mahasiswa_map = $this->fetchMahasiswaFromApi($token);

        $user_ids = $kelompoks->flatMap(fn($k) => $k->KelompokMahasiswa->pluck('user_id'));

        $nilaiindividu = Nilai_Individu::whereIn('user_id', $user_ids)
            ->where('penilai_id', $userId)
            ->where('role_id', $roleIdPenilai)
            ->get()
            ->keyBy('user_id');

        $this->pasangDataMahasiswa($kelompoks, $mahasiswa_map, $nilaiindividu);

        return view($viewPath, compact('kelompoks', 'nilaiindividu'));
    }

    /**
     * Simpan nilai individu baru.
     */
    private function storeNilai(Request $request, int $roleId, string $redirectRoute)
    {
        $userId = session('user_id');
        $request->validate($this->validasiRules(true));

        $komponen = $this->hitungKomponenB($request);

        Nilai_Individu::create([
            'user_id'    => $request->user_id,
            'penilai_id' => $userId,
            'role_id'    => $roleId,
            'B11'        => $request->B11,
            'B12'        => $request->B12,
            'B13'        => $request->B13,
            'B14'        => $request->B14,
            'B15'        => $request->B15,
            'B1_total'   => $komponen['B1_total'],
            'B21'        => $request->B21,
            'B22'        => $request->B22,
            'B23'        => $request->B23,
            'B24'        => $request->B24,
            'B25'        => $request->B25,
            'B2_total'   => $komponen['B2_total'],
            'B31'        => $request->B31,
            'B3_total'   => $komponen['B3_total'],
            'B_total'    => $komponen['B_total'],
        ]);

        return redirect()->route($redirectRoute)->with('success', 'Nilai berhasil disimpan.');
    }

    /**
     * Perbarui nilai individu.
     */
    private function updateNilai(Request $request, $id, string $redirectRoute)
    {
        $request->validate($this->validasiRules(false));

        $komponen = $this->hitungKomponenB($request);

        $nilai = Nilai_Individu::findOrFail($id);
        $nilai->update([
            'B11'      => $request->B11,
            'B12'      => $request->B12,
            'B13'      => $request->B13,
            'B14'      => $request->B14,
            'B15'      => $request->B15,
            'B1_total' => $komponen['B1_total'],
            'B21'      => $request->B21,
            'B22'      => $request->B22,
            'B23'      => $request->B23,
            'B24'      => $request->B24,
            'B25'      => $request->B25,
            'B2_total' => $komponen['B2_total'],
            'B31'      => $request->B31,
            'B3_total' => $komponen['B3_total'],
            'B_total'  => $komponen['B_total'],
        ]);

        return redirect()->route($redirectRoute)->with('success', 'Nilai berhasil diperbarui.');
    }

    /**
     * Ambil daftar mahasiswa dari API.
     */
    private function fetchMahasiswaFromApi(string $token): \Illuminate\Support\Collection
    {
        $response = Http::withHeaders(['Authorization' => "Bearer $token"])
            ->get(env('API_URL') . 'library-api/mahasiswa', ['limit' => 100]);

        if ($response->successful()) {
            $list = $response->json()['data']['mahasiswa'] ?? [];
            return collect($list)->keyBy('user_id');
        }

        return collect();
    }

    /**
     * Pasang nama & nim dari API ke setiap mahasiswa dalam koleksi kelompok.
     */
    private function pasangDataMahasiswa($kelompoks, $mahasiswa_map, $nilaiindividu): void
    {
        $kelompoks->each(function ($kelompok) use ($mahasiswa_map, $nilaiindividu) {
            $kelompok->KelompokMahasiswa->each(function ($mhs) use ($mahasiswa_map, $nilaiindividu) {
                $data                = $mahasiswa_map->get($mhs->user_id);
                $mhs->nama           = $data['nama'] ?? 'N/A';
                $mhs->nim            = $data['nim']  ?? 'N/A';
                $mhs->nilai_individu = $nilaiindividu->get($mhs->user_id)->B_total ?? null;
            });
        });
    }

    // =========================================================================
    // PEMBIMBING 1  (role_id = 3, slot ke-0)
    // =========================================================================

    public function indexpembimbing1()
    {
        return $this->indexPembimbing(3, 0, 'pages.Pembimbing.Nilai_Individu.index');
    }

    public function storepembimbing1(Request $request)
    {
        return $this->storeNilai($request, 3, 'pembimbing1.NilaiIndividu.index');
    }

    public function updatepembimbing1(Request $request, $id)
    {
        return $this->updateNilai($request, $id, 'pembimbing1.NilaiIndividu.index');
    }

    public function destroypembimbing1($id)
    {
        Nilai_Individu::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }

    // =========================================================================
    // PEMBIMBING 2  (role_id = 5, slot ke-1)
    // =========================================================================

    public function indexpembimbing2()
    {
        return $this->indexPembimbing(5, 1, 'pages.Pembimbing.Nilai_Individu.indexp2');
    }

    public function storepembimbing2(Request $request)
    {
        return $this->storeNilai($request, 5, 'pembimbing2.NilaiIndividu.index');
    }

    public function updatepembimbing2(Request $request, $id)
    {
        return $this->updateNilai($request, $id, 'pembimbing2.NilaiIndividu.index');
    }

    public function destroypembimbing2($id)
    {
        Nilai_Individu::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }

    // =========================================================================
    // PENGUJI 1  (role_id = 2, slot ke-0)
    // =========================================================================

    public function indexpenguji1()
    {
        return $this->indexPenguji(2, 0, 'pages.Penguji.Nilai_Individu.index');
    }

    public function storepenguji1(Request $request)
    {
        return $this->storeNilai($request, 2, 'penguji1.NilaiIndividu.index');
    }

    public function updatepenguji1(Request $request, $id)
    {
        return $this->updateNilai($request, $id, 'penguji1.NilaiIndividu.index');
    }

    public function destroypenguji1($id)
    {
        Nilai_Individu::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }

    // =========================================================================
    // PENGUJI 2  (role_id = 4, slot ke-1)
    // =========================================================================

    public function indexpenguji2()
    {
        return $this->indexPenguji(4, 1, 'pages.Penguji.Nilai_Individu.indexp2');
    }

    public function storepenguji2(Request $request)
    {
        return $this->storeNilai($request, 4, 'penguji2.NilaiIndividu.index');
    }

    public function updatepenguji2(Request $request, $id)
    {
        return $this->updateNilai($request, $id, 'penguji2.NilaiIndividu.index');
    }

    public function destroypenguji2($id)
    {
        Nilai_Individu::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }
}