<?php

namespace App\Http\Controllers\Agent;
use App\Http\Controllers\Controller;
use App\Models\DosenRole;
use App\Models\Dosen;
use Illuminate\Support\Facades\Log;
use App\Models\Kelompok;
use App\Models\Pembimbing;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class AgentPengujiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    $userId = session('user_id');

    $roles = DosenRole::with('role', 'kategoriPA', 'TahunMasuk', 'Prodi', 'TahunAjaran')
        ->where('user_id', $userId)
        ->get()
        ->map(function ($item) {
            return [
                'user_id' => $item->user_id,
                'angkatan' => $item->TahunMasuk->Tahun_Masuk ?? '-',
                'prodi' => $item->Prodi->nama_prodi ?? '-',
                'role' => $item->role->role_name ?? '-',
                'kategori_pa' => $item->kategoriPA->kategori_pa ?? '-',
                'KPA_id' => $item->KPA_id,
                'prodi_id' => $item->prodi_id,
                'TM_id' => $item->TM_id,
                'tahun_ajaran_id' => $item->tahun_ajaran_id, // pastikan relasi TahunAjaran ada
            ];
        });

    $user = Dosen::where('user_id', $userId)->first();

    return view('pages.Koordinator.agent.agent-penguji', compact('roles', 'user'));
}
   public function generate(Request $request)
    {
        try {
            $request->validate([
                'KPA_id' => 'required|integer',
                'prodi_id' => 'required|integer',
                'TM_id' => 'required|integer',
                'tahun_ajaran_id' => 'required|integer',
            ]);

            // 🔹 Cek kelompok tanpa pembimbing
            $kelompokTanpaPembimbing = Kelompok::where('KPA_id', $request->KPA_id)
                ->where('prodi_id', $request->prodi_id)
                ->where('TM_id', $request->TM_id)
                ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
                ->doesntHave('pembimbing')
                ->count();

            if ($kelompokTanpaPembimbing > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Ada $kelompokTanpaPembimbing kelompok yang belum memiliki pembimbing."
                ], 400);
            }

            // 🔹 Semua lengkap → lanjut generate penguji
            // Logic generate penguji disini
            // $this->generatePenguji($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Semua kelompok sudah memiliki pembimbing. Proses generate penguji dimulai.'
            ]);

        } catch (\Throwable $e) {
            // 🔹 Log error untuk debug
            Log::error("Generate penguji error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi error di server: ' . $e->getMessage()
            ], 500);
        }
    }


}
