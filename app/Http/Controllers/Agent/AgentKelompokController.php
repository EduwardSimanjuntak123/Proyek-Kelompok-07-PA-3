<?php

namespace App\Http\Controllers\Agent;
use App\Http\Controllers\Controller;
use App\Models\DosenRole;
use App\Models\Dosen;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AgentKelompokController extends Controller
{

    public function index()
    {
        $userId = session('user_id');
        // dd($userId);

        $roles = DosenRole::with('role', 'kategoriPA', 'TahunMasuk', 'Prodi')
            ->where('user_id', $userId)
            ->get()
            ->map(function ($item) {

                return [
                    'user_id' => $item->user_id,
                    'angkatan' => $item->TahunMasuk->Tahun_Masuk ?? '-',
                    'prodi' => $item->Prodi->nama_prodi ?? '-',
                    'role' => $item->role->role_name ?? '-',
                    'kategori_pa' => $item->kategoriPA->kategori_pa ?? '-'
                ];

            });
        $user = Dosen::where('user_id', $userId)->first();

        // dd($roles);

        return view('pages.Koordinator.agent.agent-kelompok', compact('roles', 'user'));
    }

    public function generate()
    {
        $userId = session('user_id');

        $roles = DosenRole::with('role', 'kategoriPA', 'tahunMasuk', 'prodi')
            ->where('user_id', $userId)
            ->get();

        $payload = [];

        foreach ($roles as $r) {

            $payload[] = [
                "user_id" => $r->user_id,
                "angkatan" => $r->TahunMasuk->id ?? null,
                "prodi" => $r->prodi->nama_prodi ?? null,
                "prodi_id" => $r->prodi->id ?? null,
                "role" => $r->role->role_name ?? null,
                "kategori_pa" => $r->kategoriPA->id ?? null
            ];
        }
        // dd($payload);

        $response = Http::timeout(600)
            ->post('http://127.0.0.1:8001/generate-kelompok', [
                "dosen_context" => $payload
            ]);

        if (!$response->successful()) {
            return redirect()->back()->with('error', 'FastAPI tidak merespon');
        }

        $data = $response->json();
        // dd($data);

        if (!isset($data['kelompok'])) {
            return redirect()->back()->with('error', 'Data kelompok tidak ditemukan');
        }

        return redirect()
            ->back()
            ->with('kelompok', $data['kelompok'])
            ->with('success', 'Kelompok berhasil dibuat');
    }

    public function cekKelompok()
    {

        $userId = session('user_id');

        $roles = DosenRole::with('kategoriPA', 'tahunMasuk', 'prodi')
            ->where('user_id', $userId)
            ->get();

        $exists = false;

        foreach ($roles as $r) {

            $cek = DB::table('kelompok')
                ->where('KPA_id', $r->kategoriPA->id)
                ->where('prodi_id', $r->prodi->id)
                ->where('TM_id', $r->tahunMasuk->id)
                ->exists();

            if ($cek) {
                $exists = true;
                break;
            }

        }

        return response()->json([
            'exists' => $exists
        ]);

    }

}