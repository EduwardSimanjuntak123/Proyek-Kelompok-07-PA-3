<?php

namespace App\Http\Controllers\Agent;
use App\Http\Controllers\Controller;
use App\Models\DosenRole;
use App\Models\Dosen;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
  use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

    public function generate(\Illuminate\Http\Request $request)
    {
        $traceId = Str::uuid();

        try {
            Log::info("[$traceId] === START GENERATE AI ===");

            $userId = session('user_id');
            $prompt = $request->input('prompt');

            Log::info("[$traceId] User ID:", ['user_id' => $userId]);
            Log::info("[$traceId] Prompt:", ['prompt' => $prompt]);

            $roles = DosenRole::with('role', 'kategoriPA', 'tahunMasuk', 'prodi')
                ->where('user_id', $userId)
                ->get();

            Log::info("[$traceId] Roles Count:", ['count' => $roles->count()]);

            $payload = [];

            foreach ($roles as $r) {
                $item = [
                    "user_id" => $r->user_id,
                    "angkatan" => $r->TahunMasuk->id ?? null,
                    "prodi" => $r->prodi->nama_prodi ?? null,
                    "prodi_id" => $r->prodi->id ?? null,
                    "role" => $r->role->role_name ?? null,
                    "kategori_pa" => $r->kategoriPA->id ?? null
                ];

                $payload[] = $item;
                Log::info("[$traceId] Role Item:", $item);
            }

            $finalPayload = [
                "prompt" => $prompt,
                "dosen_context" => $payload
            ];

            Log::info("[$traceId] Final Payload:", $finalPayload);
            Log::info("[$traceId] Sending request to FastAPI...");

            $response = Http::timeout(600)
                ->post('http://127.0.0.1:8001/generate-kelompok', $finalPayload);

            Log::info("[$traceId] FastAPI Status:", ['status' => $response->status()]);

            if (!$response->successful()) {
                Log::error("[$traceId] FastAPI ERROR RESPONSE", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'result' => 'FastAPI tidak merespon'
                ]);
            }

            $data = $response->json();
            Log::info("[$traceId] FastAPI Response:", $data);

            // Extract rekomendasi dari response
            $recommendations = $data['recommendations'] ?? null;
            $groups = $data['groups'] ?? null;

            if ($recommendations) {
                session()->put("last_recommendations_$userId", $recommendations);
                session()->put("last_groups_$userId", $groups);

                Log::info("[$traceId] Recommendations stored in session", [
                    'actions' => count($recommendations['actions'] ?? []),
                    'constraints' => count($recommendations['constraints'] ?? []),
                    'instructions' => count($recommendations['instructions'] ?? [])
                ]);
            }

            Log::info("[$traceId] === END GENERATE AI ===");

            return response()->json([
                'success' => true,
                'result' => $data['result'] ?? '',
                'type' => $data['type'] ?? '',
                'data' => $data['data'] ?? '',  // Include formatted HTML table from executor
                'groups' => $groups,
                'recommendations' => $recommendations,
                'plan' => $data['plan'] ?? null,
                'message' => count($groups ?? []) . ' kelompok berhasil dibuat'
            ]);

        } catch (\Exception $e) {
            Log::error("[$traceId] EXCEPTION ERROR", [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'result' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Save kelompok hasil generate ke database
     * Dipanggil ketika user click "Simpan ke Database"
     */
    public function saveGroupsToDatabase(\Illuminate\Http\Request $request)
    {
        $traceId = \Illuminate\Support\Str::uuid();
        
        try {
            Log::info("[$traceId] === START SAVE GROUPS ===");
            
            $userId = session('user_id');
            $groups = $request->input('groups', []);
            $kategoriPaId = $request->input('kategori_pa_id');
            $prodiId = $request->input('prodi_id');
            $tahunMasukId = $request->input('tahun_masuk_id');
            
            Log::info("[$traceId] Save Groups Input", [
                'user_id' => $userId,
                'kelompok_count' => count($groups),
                'kategori_pa_id' => $kategoriPaId,
                'prodi_id' => $prodiId,
                'tahun_masuk_id' => $tahunMasukId
            ]);
            
            if (empty($groups)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada kelompok untuk disimpan'
                ], 400);
            }
            
            // ===== VALIDASI ROLE =====
            $role = DosenRole::with('kategoriPA')
                ->where('user_id', $userId)
                ->where('kategori_pa', $kategoriPaId)
                ->where('prodi_id', $prodiId)
                ->where('tahun_masuk', $tahunMasukId)
                ->first();
            
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk kategori PA ini'
                ], 403);
            }
            
            // ===== CREATE KELOMPOK RECORD =====
            $kelompok = DB::table('kelompok')->insertGetId([
                'KPA_id' => $role->kategoriPA->id,
                'prodi_id' => $prodiId,
                'TM_id' => $tahunMasukId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info("[$traceId] Kelompok record created", ['kelompok_id' => $kelompok]);
            
            // ===== INSERT ANGGOTA KELOMPOK =====
            $totalMembers = 0;
            foreach ($groups as $groupData) {
                $kelompokNum = $groupData['kelompok'] ?? 0;
                $members = $groupData['members'] ?? [];
                
                foreach ($members as $member) {
                    $nim = $member['nim'] ?? null;
                    
                    if (!$nim) {
                        continue;
                    }
                    
                    // Find mahasiswa by NIM
                    $mahasiswa = DB::table('mahasiswa')->where('nim', $nim)->first();
                    
                    if ($mahasiswa) {
                        DB::table('kelompok_mahasiswa')->insert([
                            'kelompok_id' => $kelompok,
                            'mahasiswa_id' => $mahasiswa->id,
                            'nomor_urut' => $kelompokNum,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        $totalMembers++;
                        
                        Log::info("[$traceId] Added member", [
                            'nim' => $nim,
                            'nama' => $member['nama'] ?? 'Unknown',
                            'kelompok_num' => $kelompokNum
                        ]);
                    } else {
                        Log::warning("[$traceId] Mahasiswa not found", ['nim' => $nim]);
                    }
                }
            }
            
            Log::info("[$traceId] === END SAVE GROUPS ===", ['total_members' => $totalMembers]);
            
            return response()->json([
                'success' => true,
                'message' => "Kelompok berhasil disimpan ({$totalMembers} anggota dalam " . count($groups) . " kelompok)",
                'kelompok_id' => $kelompok
            ]);
            
        } catch (\Exception $e) {
            Log::error("[$traceId] SAVE GROUPS ERROR", [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Handle constraint modifications (must_pair atau avoid_pair)
     * Dipanggil ketika user click pada constraint suggestion
     */
    public function applyConstraintModification(\Illuminate\Http\Request $request)
    {
        $traceId = \Illuminate\Support\Str::uuid();
        
        try {
            Log::info("[$traceId] === START CONSTRAINT MODIFICATION ===");
            
            $userId = session('user_id');
            $constraintType = $request->input('constraint_type');  // must_pair atau avoid_pair
            $personA = $request->input('person_a');
            $personB = $request->input('person_b');
            $instruction = $request->input('instruction');  // Pre-formatted instruction
            
            Log::info("[$traceId] Constraint Modification Input", [
                'user_id' => $userId,
                'constraint_type' => $constraintType,
                'person_a' => $personA,
                'person_b' => $personB,
                'instruction' => $instruction
            ]);
            
            // Get last groups dari session
            $lastGroups = session("last_groups_$userId");
            if (!$lastGroups) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada grouping sebelumnya. Buat kelompok terlebih dahulu.'
                ], 400);
            }
            
            // Build new instruction dengan constraint
            $newInstruction = "modifikasi kelompok: " . $instruction;
            
            Log::info("[$traceId] Sending regenerate request to FastAPI with constraint");
            
            // Get dosen context lagi untuk regenerate
            $roles = DosenRole::with('role', 'kategoriPA', 'tahunMasuk', 'prodi')
                ->where('user_id', $userId)
                ->get();
            
            $payload = [];
            foreach ($roles as $r) {
                $payload[] = [
                    "user_id" => $r->user_id,
                    "angkatan" => $r->TahunMasuk->id,
                    "prodi" => $r->prodi->nama_prodi,
                    "prodi_id" => $r->prodi->id,
                    "role" => $r->role->role_name,
                    "kategori_pa" => $r->kategoriPA->id
                ];
            }
            
            $finalPayload = [
                "prompt" => $newInstruction,
                "dosen_context" => $payload
            ];
            
            // Call FastAPI untuk regenerate dengan constraint
            $response = Http::timeout(600)
                ->post('http://127.0.0.1:8001/generate-kelompok', $finalPayload);
            
            if (!$response->successful()) {
                Log::error("[$traceId] FastAPI regenerate error", [
                    'status' => $response->status()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal regenerate kelompok dengan constraint'
                ], 500);
            }
            
            $regenerateResult = $response->json();
            $newGroups = $regenerateResult['groups'] ?? null;
            $newRecommendations = $regenerateResult['recommendations'] ?? null;
            
            // Store ke session
            if ($newGroups) {
                session()->put("last_groups_$userId", $newGroups);
                session()->put("last_recommendations_$userId", $newRecommendations);
            }
            
            Log::info("[$traceId] === END CONSTRAINT MODIFICATION ===", [
                'new_groups_count' => count($newGroups ?? [])
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Kelompok berhasil dimodifikasi sesuai constraint',
                'instruction_applied' => $instruction,
                'constraint_type' => $constraintType,
                'new_groups' => $newGroups,
                'new_recommendations' => $newRecommendations,
                'result_html' => $regenerateResult['result'] ?? ''
            ]);
            
        } catch (\Exception $e) {
            Log::error("[$traceId] CONSTRAINT MODIFICATION ERROR", [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
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