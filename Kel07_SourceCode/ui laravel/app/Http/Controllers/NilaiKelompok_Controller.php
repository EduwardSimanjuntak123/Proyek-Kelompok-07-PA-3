<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\Nilai_kelompok;
use Illuminate\Http\Request;

class NilaiKelompok_Controller extends Controller
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
     * Hitung komponen A1_total, A2_total, dan A_total dari input request.
     */
    private function hitungKomponenA(Request $request): array
    {
        $A1_total = (0.15 * $request->A11) + (0.05 * $request->A12) + (0.05 * $request->A13);
        $A2_total = (0.20 * $request->A21) + (0.05 * $request->A22) + (0.05 * $request->A23);

        return [
            'A1_total' => $A1_total,
            'A2_total' => $A2_total,
            'A_total'  => $A1_total + $A2_total,
        ];
    }

    /**
     * Aturan validasi input nilai kelompok.
     */
    private function validasiRules(): array
    {
        return [
            'kelompok_id' => 'required|exists:kelompok,id',
            'A11'         => 'required|numeric|min:0|max:100',
            'A12'         => 'required|numeric|min:0|max:100',
            'A13'         => 'required|numeric|min:0|max:100',
            'A21'         => 'required|numeric|min:0|max:100',
            'A22'         => 'required|numeric|min:0|max:100',
            'A23'         => 'required|numeric|min:0|max:100',
            'user_id'     => 'required',
        ];
    }

    /**
     * Ambil data kelompok dan nilai untuk halaman index penguji.
     */
    private function indexPenguji(int $roleIdPenilai, int $slot, string $viewPath)
    {
        $userId = session('user_id');

        $kelompok = Kelompok::with([
                'penguji' => fn($q) => $q->orderBy('id'),
                'kategoriPA',
                'prodi',
            ])
            ->whereHas('penguji', fn($q) => $q->where('user_id', $userId))
            ->get();

        $kelompok = $this->filterKelompokByPengujiSlot($kelompok, $userId, $slot);

        $nilaiKelompok = Nilai_kelompok::whereIn('kelompok_id', $kelompok->pluck('id'))
            ->where('role_id', $roleIdPenilai)
            ->where('user_id', $userId)
            ->get()
            ->keyBy('kelompok_id');

        return view($viewPath, compact('kelompok', 'nilaiKelompok', 'userId'));
    }

    /**
     * Ambil data kelompok dan nilai untuk halaman index pembimbing.
     * Hanya menampilkan kelompok di mana dosen ini berada di slot $slot.
     *
     * @param int    $roleIdPenilai  role_id (3 = Pembimbing 1, 5 = Pembimbing 2)
     * @param int    $slot           0 = Pembimbing 1, 1 = Pembimbing 2
     * @param string $viewPath       path blade view
     */
    private function indexPembimbing(int $roleIdPenilai, int $slot, string $viewPath)
    {
        $userId = session('user_id');

        // Ambil semua kelompok di mana dosen ini terdaftar sebagai pembimbing
        $kelompok = Kelompok::with([
                'pembimbing' => fn($q) => $q->orderBy('id'), // urut agar slot konsisten
                'kategoriPA',
                'prodi',
            ])
            ->whereHas('pembimbing', fn($q) => $q->where('user_id', $userId))
            ->get();

        // Filter hanya kelompok di mana dosen ini ada di slot yang sesuai
        $kelompok = $this->filterKelompokByPembimbingSlot($kelompok, $userId, $slot);

        $nilaiKelompok = Nilai_kelompok::whereIn('kelompok_id', $kelompok->pluck('id'))
            ->where('role_id', $roleIdPenilai)
            ->where('user_id', $userId)
            ->get()
            ->keyBy('kelompok_id');

        return view($viewPath, compact('kelompok', 'nilaiKelompok', 'userId'));
    }

    /**
     * Simpan nilai kelompok baru.
     */
    private function storeNilai(Request $request, int $roleId, string $redirectRoute)
    {
        $userId = session('user_id');
        $request->validate($this->validasiRules());

        $komponen = $this->hitungKomponenA($request);

        Nilai_kelompok::create([
            'kelompok_id' => $request->kelompok_id,
            'A11'         => $request->A11,
            'A12'         => $request->A12,
            'A13'         => $request->A13,
            'A1_total'    => $komponen['A1_total'],
            'A21'         => $request->A21,
            'A22'         => $request->A22,
            'A23'         => $request->A23,
            'A2_total'    => $komponen['A2_total'],
            'A_total'     => $komponen['A_total'],
            'user_id'     => $userId,
            'role_id'     => $roleId,
        ]);

        return redirect()->route($redirectRoute)->with('success', 'Nilai berhasil disimpan.');
    }

    /**
     * Perbarui nilai kelompok.
     */
    private function updateNilai(Request $request, $id, string $redirectRoute)
    {
        $request->validate($this->validasiRules());

        $komponen = $this->hitungKomponenA($request);

        $nilai = Nilai_kelompok::findOrFail($id);
        $nilai->update([
            'A11'      => $request->A11,
            'A12'      => $request->A12,
            'A13'      => $request->A13,
            'A1_total' => $komponen['A1_total'],
            'A21'      => $request->A21,
            'A22'      => $request->A22,
            'A23'      => $request->A23,
            'A2_total' => $komponen['A2_total'],
            'A_total'  => $komponen['A_total'],
            'user_id'  => $request->user_id,
        ]);

        return redirect()->back()->with('success', 'Nilai berhasil diperbarui.');
    }

    // =========================================================================
    // PENGUJI 1  (role_id = 2, slot ke-0)
    // =========================================================================

    public function indexpenguji1()
    {
        return $this->indexPenguji(2, 0, 'pages.Penguji.Nilai_Kelompok.index');
    }

    public function storepenguji1(Request $request)
    {
        return $this->storeNilai($request, 2, 'penguji1.NilaiKelompok.index');
    }

    public function updatepenguji1(Request $request, $id)
    {
        return $this->updateNilai($request, $id, 'penguji1.NilaiKelompok.index');
    }

    public function destroypenguji1($id)
    {
        Nilai_kelompok::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }

    // =========================================================================
    // PENGUJI 2  (role_id = 4, slot ke-1)
    // =========================================================================

    public function indexpenguji2()
    {
        return $this->indexPenguji(4, 1, 'pages.Penguji.Nilai_Kelompok.indexp2');
    }

    public function storepenguji2(Request $request)
    {
        return $this->storeNilai($request, 4, 'penguji2.NilaiKelompok.index');
    }

    public function updatepenguji2(Request $request, $id)
    {
        return $this->updateNilai($request, $id, 'penguji2.NilaiKelompok.index');
    }

    public function destroypenguji2($id)
    {
        Nilai_kelompok::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }

    // =========================================================================
    // PEMBIMBING 1  (role_id = 3, slot ke-0)
    // =========================================================================

    public function indexpembimbing1()
    {
        return $this->indexPembimbing(3, 0, 'pages.Pembimbing.Nilai_Kelompok.index');
    }

    public function storepembimbing1(Request $request)
    {
        return $this->storeNilai($request, 3, 'pembimbing1.NilaiKelompok.index');
    }

    public function updatepembimbing1(Request $request, $id)
    {
        return $this->updateNilai($request, $id, 'pembimbing1.NilaiKelompok.index');
    }

    public function destroypembimbing1($id)
    {
        Nilai_kelompok::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }

    // =========================================================================
    // PEMBIMBING 2  (role_id = 5, slot ke-1)
    // =========================================================================

    public function indexpembimbing2()
    {
        return $this->indexPembimbing(5, 1, 'pages.Pembimbing.Nilai_Kelompok.indexp2');
    }

    public function storepembimbing2(Request $request)
    {
        return $this->storeNilai($request, 5, 'pembimbing2.NilaiKelompok.index');
    }

    public function updatepembimbing2(Request $request, $id)
    {
        return $this->updateNilai($request, $id, 'pembimbing2.NilaiKelompok.index');
    }

    public function destroypembimbing2($id)
    {
        Nilai_kelompok::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }
}