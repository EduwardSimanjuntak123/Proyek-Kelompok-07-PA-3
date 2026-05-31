<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\Nilai_Bimbingan;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class NilaiBimbingan_Controller extends Controller
{
    // -------------------------------------------------------------------------
    // PRIVATE HELPERS — logika yang sama dipakai oleh pembimbing 1 & 2
    // -------------------------------------------------------------------------

    /**
     * Hitung Total nilai bimbingan dari komponen A1–A5.
     * Formula: 0.10×A1 + 0.10×A2 + 0.15×A3 + 0.40×A4 + 0.25×A5
     * Dipusatkan di sini agar jika formula berubah cukup edit 1 tempat.
     */
    private function hitungTotal(Request $request): float
    {
        return 0.10 * $request->A1
             + 0.10 * $request->A2
             + 0.15 * $request->A3
             + 0.40 * $request->A4
             + 0.25 * $request->A5;
    }

    /**
     * Aturan validasi input — sama untuk store maupun update semua role.
     */
    private function validasiRules(bool $includeUserPenilai = true): array
    {
        $rules = [
            'A1' => 'required|numeric|min:0|max:100',
            'A2' => 'required|numeric|min:0|max:100',
            'A3' => 'required|numeric|min:0|max:100',
            'A4' => 'required|numeric|min:0|max:100',
            'A5' => 'required|numeric|min:0|max:100',
        ];

        if ($includeUserPenilai) {
            $rules['user_id']    = 'required';
            $rules['penilai_id'] = 'required';
        }

        return $rules;
    }

    /**
     * Ambil data kelompok + mahasiswa dari API untuk index page.
     * Digunakan oleh indexpembimbing1 & indexpembimbing2.
     *
     * @param  int    $roleIdPenilai  role_id yang disimpan di nilai_bimbingan (3 atau 5)
     * @param  string $viewPath       path blade view yang dituju
     */
    private function indexPembimbing(int $roleIdPenilai, string $viewPath)
    {
        $token  = session('token');
        $userId = session('user_id');
        $roleId = session('role_id');

        // Ambil kelompok yang dibimbing oleh dosen ini
        $kelompoks = Kelompok::with(['pembimbing.dosenRoles', 'KelompokMahasiswa', 'nilais'])
            ->whereHas('pembimbing.dosenRoles', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();

        // Ambil data mahasiswa dari API
        $mahasiswa_map = collect();
        $response = Http::withHeaders(['Authorization' => "Bearer $token"])
            ->get(env('API_URL') . 'library-api/mahasiswa', ['limit' => 100]);

        if ($response->successful()) {
            $listMahasiswa = $response->json()['data']['mahasiswa'] ?? [];
            $mahasiswa_map = collect($listMahasiswa)->keyBy('user_id');
        }

        // Kumpulkan semua user_id anggota kelompok
        $user_ids = $kelompoks->flatMap(fn($k) => $k->KelompokMahasiswa->pluck('user_id'));

        // Ambil nilai bimbingan yang sudah diinput oleh penilai ini dengan role tertentu
        $nilaiBimbingan = Nilai_Bimbingan::whereIn('user_id', $user_ids)
            ->where('penilai_id', $userId)
            ->where('role_id', $roleIdPenilai)
            ->get()
            ->keyBy('user_id');

        // Pasang nama & nim dari API ke setiap mahasiswa
        $kelompoks->each(function ($kelompok) use ($mahasiswa_map, $nilaiBimbingan) {
            $kelompok->KelompokMahasiswa->each(function ($mhs) use ($mahasiswa_map, $nilaiBimbingan) {
                $data        = $mahasiswa_map->get($mhs->user_id);
                $mhs->nama   = $data['nama'] ?? 'N/A';
                $mhs->nim    = $data['nim']  ?? 'N/A';
                $mhs->nilai_individu = $nilaiBimbingan->get($mhs->user_id)->Total ?? null;
            });
        });

        return view($viewPath, [
            'kelompoks'      => $kelompoks,
            'nilaiindividu'  => $nilaiBimbingan,
        ]);
    }

    // -------------------------------------------------------------------------
    // PEMBIMBING 1  (role_id = 3)
    // -------------------------------------------------------------------------

    public function indexpembimbing1()
    {
        // PERBAIKAN: view path konsisten pakai huruf kapital 'Pembimbing'
        return $this->indexPembimbing(3, 'pages.Pembimbing.Nilai_Individu.indexp3');
    }

    public function storepembimbing1(Request $request)
    {
        $userId = session('user_id');
        $request->validate($this->validasiRules(true));

        Nilai_Bimbingan::create([
            'user_id'   => $request->user_id,
            'penilai_id' => $userId,
            'role_id'   => 3,
            'A1'        => $request->A1,
            'A2'        => $request->A2,
            'A3'        => $request->A3,
            'A4'        => $request->A4,
            'A5'        => $request->A5,
            'Total'     => $this->hitungTotal($request),
        ]);

        return redirect()->route('pembimbing1.NilaiBimbingan.index')
            ->with('success', 'Nilai berhasil disimpan.');
    }

    public function updatepembimbing1(Request $request, $id)
    {
        $request->validate($this->validasiRules(false));

        $nilai = Nilai_Bimbingan::findOrFail($id);
        $nilai->update([
            'A1'    => $request->A1,
            'A2'    => $request->A2,
            'A3'    => $request->A3,
            'A4'    => $request->A4,
            'A5'    => $request->A5,
            'Total' => $this->hitungTotal($request),
        ]);

        return redirect()->route('pembimbing1.NilaiBimbingan.index')
            ->with('success', 'Nilai berhasil diperbarui.');
    }

    public function destroypembimbing1($id)
    {
        Nilai_Bimbingan::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }

    // -------------------------------------------------------------------------
    // PEMBIMBING 2  (role_id = 5)
    // -------------------------------------------------------------------------

    public function indexpembimbing2()
    {
        return $this->indexPembimbing(5, 'pages.Pembimbing.Nilai_Individu.indexp3');
    }

    public function storepembimbing2(Request $request)
    {
        $userId = session('user_id');
        $request->validate($this->validasiRules(true));

        Nilai_Bimbingan::create([
            'user_id'    => $request->user_id,
            'penilai_id' => $userId,
            'role_id'    => 5,
            'A1'         => $request->A1,
            'A2'         => $request->A2,
            'A3'         => $request->A3,
            'A4'         => $request->A4,
            'A5'         => $request->A5,
            'Total'      => $this->hitungTotal($request),
        ]);

        return redirect()->route('pembimbing2.NilaiBimbingan.index')
            ->with('success', 'Nilai berhasil disimpan.');
    }

    public function updatepembimbing2(Request $request, $id)
    {
        $request->validate($this->validasiRules(false));

        $nilai = Nilai_Bimbingan::findOrFail($id);
        $nilai->update([
            'A1'    => $request->A1,
            'A2'    => $request->A2,
            'A3'    => $request->A3,
            'A4'    => $request->A4,
            'A5'    => $request->A5,
            'Total' => $this->hitungTotal($request),
        ]);

        // PERBAIKAN #1: redirect yang benar (sebelumnya ke 'pembimbing2.NilaiIndividu.index')
        return redirect()->route('pembimbing2.NilaiBimbingan.index')
            ->with('success', 'Nilai berhasil diperbarui.');
    }

    public function destroypembimbing2($id)
    {
        Nilai_Bimbingan::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }
}