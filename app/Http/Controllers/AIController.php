<?php

namespace App\Http\Controllers;

use App\Models\TahunMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    public function generateGroups(Request $request)
    {
        Log::info("AIController generateGroups terpanggil");

        $groupSize = $request->input('group_size', 6);

        // 🔥 AMBIL LANGSUNG DARI FRONTEND
        $students = $request->input('mahasiswa');

          Log::info($students);

        if (empty($students)) {
            return response()->json([
                'error' => 'Data mahasiswa kosong dari frontend'
            ], 400);
        }

        // ambil hanya nama
        $names = collect($students)->pluck('nama')->toArray();

        Log::info("Kirim ke Python...");

        $responseAI = Http::timeout(20)->post(
            'http://127.0.0.1:8001/generate-groups',
            [
                'names' => $names,
                'group_size' => $groupSize
            ]
        );

        if ($responseAI->failed()) {
            return response()->json([
                'error' => 'AI Service Error',
                'detail' => $responseAI->body()
            ], 500);
        }

        return response()->json($responseAI->json());
    }
}