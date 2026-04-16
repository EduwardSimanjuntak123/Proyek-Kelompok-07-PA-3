<?php

namespace App\Http\Controllers\Agent;
use App\Http\Controllers\Controller;
use App\Models\DosenRole;
use App\Models\Dosen;
use App\Models\Kelompok;
use App\Models\KelompokMahasiswa;
use App\Models\pembimbing as PembimbingModel;
use App\Models\Penguji as PengujiModel;
use Illuminate\Http\Request;
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

    private function getPrimaryDosenRole($userId)
    {
        return DosenRole::where('user_id', $userId)
            ->orderByDesc('status')
            ->orderByDesc('id')
            ->first();
    }

    private function parseNomorKelompokToInt($nomor)
    {
        if (is_null($nomor)) {
            return 0;
        }

        if (is_numeric($nomor)) {
            return (int) $nomor;
        }

        if (preg_match('/(\d+)/', (string) $nomor, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }

    private function resolveTahunAjaranIdForRole($role)
    {
        if ($role && !empty($role->tahun_ajaran_id)) {
            return (int) $role->tahun_ajaran_id;
        }

        $active = DB::table('tahun_ajaran')
            ->where('status', 'Aktif')
            ->orderByDesc('tahun_mulai')
            ->first();

        if ($active) {
            return (int) $active->id;
        }

        $latest = DB::table('tahun_ajaran')
            ->orderByDesc('tahun_mulai')
            ->first();

        return $latest ? (int) $latest->id : null;
    }

    public function callAgent(Request $request)
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
                "dosen_context" => $payload,
                "user_id" => $userId
            ];

            Log::info("[$traceId] Final Payload:", $finalPayload);
            Log::info("[$traceId] Sending request to AI Agent API...");

            $response = Http::timeout(600)
                ->post('http://127.0.0.1:8002/agent', $finalPayload);

            Log::info("[$traceId] Agent API Status:", ['status' => $response->status()]);
            // dd($response->body());
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

            Log::info("[$traceId] === END GENERATE AI ===");

            return response()->json([
                'success' => true,
                'result' => $data['result'] ?? '',
                'action' => $data['action'] ?? null,
                'grouping_payload' => $data['grouping_payload'] ?? null,
                'grouping_meta' => $data['grouping_meta'] ?? null,
                'pembimbing_payload' => $data['pembimbing_payload'] ?? null,
                'pembimbing_meta' => $data['pembimbing_meta'] ?? null,
                'penguji_payload' => $data['penguji_payload'] ?? null,
                'penguji_meta' => $data['penguji_meta'] ?? null,
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

    public function cekKelompok(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session user tidak ditemukan.'
                ], 401);
            }

            $role = $this->getPrimaryDosenRole($userId);
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role dosen tidak ditemukan.'
                ], 404);
            }

            $existingGroups = Kelompok::where('prodi_id', $role->prodi_id)
                ->where('KPA_id', $role->KPA_id)
                ->where('TM_id', $role->TM_id)
                ->orderBy('id')
                ->get(['id', 'nomor_kelompok', 'status']);

            return response()->json([
                'success' => true,
                'exists' => $existingGroups->isNotEmpty(),
                'total' => $existingGroups->count(),
                'groups' => $existingGroups,
                'context' => [
                    'prodi_id' => $role->prodi_id,
                    'kategori_pa_id' => $role->KPA_id,
                    'angkatan_id' => $role->TM_id,
                    'tahun_ajaran_id' => $this->resolveTahunAjaranIdForRole($role),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('cekKelompok error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek kelompok: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function saveGeneratedGroups(Request $request)
    {
        $request->validate([
            'grouping_payload' => 'required|array',
            'grouping_payload.groups' => 'required|array|min:1',
            'replace_existing' => 'nullable|boolean',
        ]);

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session user tidak ditemukan.'
                ], 401);
            }

            $role = $this->getPrimaryDosenRole($userId);
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role dosen tidak ditemukan.'
                ], 404);
            }

            $groupsPayload = $request->input('grouping_payload.groups', []);
            $replaceExisting = (bool) $request->boolean('replace_existing', false);

            $tahunAjaranId = $this->resolveTahunAjaranIdForRole($role);
            if (!$tahunAjaranId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tahun ajaran belum tersedia.'
                ], 422);
            }

            $existingQuery = Kelompok::where('prodi_id', $role->prodi_id)
                ->where('KPA_id', $role->KPA_id)
                ->where('TM_id', $role->TM_id);

            $existingIds = $existingQuery->pluck('id')->toArray();
            $existingCount = count($existingIds);

            $savedKelompok = 0;
            $savedMembers = 0;
            $deletedKelompok = 0;
            $deletedMembers = 0;
            $skippedExistingMembers = 0;

            DB::transaction(function () use (
                $groupsPayload,
                $role,
                $tahunAjaranId,
                $replaceExisting,
                $existingIds,
                &$savedKelompok,
                &$savedMembers,
                &$deletedKelompok,
                &$deletedMembers,
                &$skippedExistingMembers
            ) {
                if ($replaceExisting && !empty($existingIds)) {
                    $deletedMembers = KelompokMahasiswa::whereIn('kelompok_id', $existingIds)->delete();
                    $deletedKelompok = Kelompok::whereIn('id', $existingIds)->delete();
                }

                $occupiedUserIds = KelompokMahasiswa::pluck('user_id')
                    ->map(function ($val) {
                        return (int) $val;
                    })
                    ->toArray();
                $occupiedMap = array_fill_keys($occupiedUserIds, true);

                $maxNomor = Kelompok::where('prodi_id', $role->prodi_id)
                    ->where('KPA_id', $role->KPA_id)
                    ->where('TM_id', $role->TM_id)
                    ->pluck('nomor_kelompok')
                    ->map(function ($nomor) {
                        return $this->parseNomorKelompokToInt($nomor);
                    })
                    ->max();

                $nextNomor = ((int) $maxNomor) + 1;

                foreach ($groupsPayload as $group) {
                    $memberUserIds = collect($group['members'] ?? [])
                        ->pluck('user_id')
                        ->filter()
                        ->map(function ($val) {
                            return (int) $val;
                        })
                        ->unique()
                        ->values()
                        ->all();

                    $newUserIds = [];
                    foreach ($memberUserIds as $uid) {
                        if (!isset($occupiedMap[$uid])) {
                            $newUserIds[] = $uid;
                        } else {
                            $skippedExistingMembers++;
                        }
                    }

                    if (empty($newUserIds)) {
                        continue;
                    }

                    $kelompok = Kelompok::create([
                        'nomor_kelompok' => (string) $nextNomor,
                        'KPA_id' => $role->KPA_id,
                        'prodi_id' => $role->prodi_id,
                        'TM_id' => $role->TM_id,
                        'tahun_ajaran_id' => $tahunAjaranId,
                        'status' => 'Aktif',
                    ]);

                    $savedKelompok++;
                    $nextNomor++;

                    foreach ($newUserIds as $userIdMahasiswa) {
                        KelompokMahasiswa::create([
                            'user_id' => $userIdMahasiswa,
                            'kelompok_id' => $kelompok->id,
                        ]);
                        $occupiedMap[$userIdMahasiswa] = true;
                        $savedMembers++;
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Kelompok berhasil disimpan ke database.',
                'saved_kelompok' => $savedKelompok,
                'saved_members' => $savedMembers,
                'deleted_kelompok' => $deletedKelompok,
                'deleted_members' => $deletedMembers,
                'skipped_existing_members' => $skippedExistingMembers,
                'existing_count' => $existingCount,
            ]);
        } catch (\Exception $e) {
            Log::error('saveGeneratedGroups error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan kelompok: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function saveGeneratedPembimbing(Request $request)
    {
        $request->validate([
            'pembimbing_payload' => 'required|array',
            'pembimbing_payload.groups' => 'required|array|min:1',
            'replace_existing' => 'nullable|boolean',
        ]);

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session user tidak ditemukan.'
                ], 401);
            }

            $role = $this->getPrimaryDosenRole($userId);
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role dosen tidak ditemukan.'
                ], 404);
            }

            $groupsPayload = $request->input('pembimbing_payload.groups', []);
            $replaceExisting = (bool) $request->boolean('replace_existing', false);

            $contextKelompokIds = Kelompok::where('prodi_id', $role->prodi_id)
                ->where('KPA_id', $role->KPA_id)
                ->where('TM_id', $role->TM_id)
                ->pluck('id')
                ->toArray();

            if (empty($contextKelompokIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada kelompok pada konteks ini.'
                ], 422);
            }

            $existingCount = PembimbingModel::whereIn('kelompok_id', $contextKelompokIds)->count();
            if ($existingCount > 0 && !$replaceExisting) {
                return response()->json([
                    'success' => false,
                    'requires_confirmation' => true,
                    'message' => 'Pembimbing sudah ada. Konfirmasi untuk hapus assignment lama dan simpan hasil baru.',
                    'existing_count' => $existingCount,
                ], 409);
            }

            $savedAssignments = 0;
            $deletedAssignments = 0;
            $allowedKelompokIdSet = array_flip($contextKelompokIds);

            DB::transaction(function () use (
                $groupsPayload,
                $replaceExisting,
                $contextKelompokIds,
                $allowedKelompokIdSet,
                &$savedAssignments,
                &$deletedAssignments
            ) {
                if ($replaceExisting) {
                    $deletedAssignments = PembimbingModel::whereIn('kelompok_id', $contextKelompokIds)->delete();
                }

                foreach ($groupsPayload as $group) {
                    $kelompokId = (int) ($group['kelompok_id'] ?? 0);
                    if ($kelompokId <= 0 || !isset($allowedKelompokIdSet[$kelompokId])) {
                        continue;
                    }

                    $assignedInGroup = [];
                    foreach (($group['pembimbing'] ?? []) as $pb) {
                        $dosenUserId = (int) ($pb['user_id'] ?? 0);
                        if ($dosenUserId <= 0 || isset($assignedInGroup[$dosenUserId])) {
                            continue;
                        }

                        $assignedInGroup[$dosenUserId] = true;

                        PembimbingModel::firstOrCreate([
                            'kelompok_id' => $kelompokId,
                            'user_id' => $dosenUserId,
                        ]);

                        $savedAssignments++;
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Pembimbing berhasil disimpan ke database.',
                'saved_assignments' => $savedAssignments,
                'deleted_assignments' => $deletedAssignments,
            ]);
        } catch (\Exception $e) {
            Log::error('saveGeneratedPembimbing error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pembimbing: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function cekPembimbing(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session user tidak ditemukan.'
                ], 401);
            }

            $role = $this->getPrimaryDosenRole($userId);
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role dosen tidak ditemukan.'
                ], 404);
            }

            $contextKelompokIds = Kelompok::where('prodi_id', $role->prodi_id)
                ->where('KPA_id', $role->KPA_id)
                ->where('TM_id', $role->TM_id)
                ->pluck('id')
                ->toArray();

            if (empty($contextKelompokIds)) {
                return response()->json([
                    'success' => true,
                    'exists' => false,
                    'total' => 0,
                    'message' => 'Tidak ada kelompok pada konteks ini.'
                ]);
            }

            $existingCount = PembimbingModel::whereIn('kelompok_id', $contextKelompokIds)->count();

            return response()->json([
                'success' => true,
                'exists' => $existingCount > 0,
                'total' => $existingCount,
                'context_kelompok_total' => count($contextKelompokIds),
            ]);
        } catch (\Exception $e) {
            Log::error('cekPembimbing error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal cek pembimbing: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function saveGeneratedPenguji(Request $request)
    {
        $request->validate([
            'penguji_payload' => 'required|array',
            'penguji_payload.groups' => 'required|array|min:1',
            'replace_existing' => 'nullable|boolean',
        ]);

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session user tidak ditemukan.'
                ], 401);
            }

            $role = $this->getPrimaryDosenRole($userId);
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role dosen tidak ditemukan.'
                ], 404);
            }

            $groupsPayload = $request->input('penguji_payload.groups', []);
            $replaceExisting = (bool) $request->boolean('replace_existing', false);

            $contextKelompokIds = Kelompok::where('prodi_id', $role->prodi_id)
                ->where('KPA_id', $role->KPA_id)
                ->where('TM_id', $role->TM_id)
                ->pluck('id')
                ->toArray();

            if (empty($contextKelompokIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada kelompok pada konteks ini.'
                ], 422);
            }

            $existingCount = PengujiModel::whereIn('kelompok_id', $contextKelompokIds)->count();
            if ($existingCount > 0 && !$replaceExisting) {
                return response()->json([
                    'success' => false,
                    'requires_confirmation' => true,
                    'message' => 'Penguji sudah ada. Konfirmasi untuk hapus assignment lama dan simpan hasil baru.',
                    'existing_count' => $existingCount,
                ], 409);
            }

            $savedAssignments = 0;
            $deletedAssignments = 0;
            $skippedAsPembimbing = 0;
            $allowedKelompokIdSet = array_flip($contextKelompokIds);

            $pembimbingRows = PembimbingModel::whereIn('kelompok_id', $contextKelompokIds)
                ->get(['kelompok_id', 'user_id']);

            $pembimbingMap = [];
            foreach ($pembimbingRows as $row) {
                $gid = (int) $row->kelompok_id;
                $uid = (int) $row->user_id;
                if (!isset($pembimbingMap[$gid])) {
                    $pembimbingMap[$gid] = [];
                }
                $pembimbingMap[$gid][$uid] = true;
            }

            DB::transaction(function () use (
                $groupsPayload,
                $replaceExisting,
                $contextKelompokIds,
                $allowedKelompokIdSet,
                $pembimbingMap,
                &$savedAssignments,
                &$deletedAssignments,
                &$skippedAsPembimbing
            ) {
                if ($replaceExisting) {
                    $deletedAssignments = PengujiModel::whereIn('kelompok_id', $contextKelompokIds)->delete();
                }

                foreach ($groupsPayload as $group) {
                    $kelompokId = (int) ($group['kelompok_id'] ?? 0);
                    if ($kelompokId <= 0 || !isset($allowedKelompokIdSet[$kelompokId])) {
                        continue;
                    }

                    $assignedInGroup = [];
                    foreach (($group['penguji'] ?? []) as $pb) {
                        $dosenUserId = (int) ($pb['user_id'] ?? 0);
                        if ($dosenUserId <= 0 || isset($assignedInGroup[$dosenUserId])) {
                            continue;
                        }

                        if (isset($pembimbingMap[$kelompokId][$dosenUserId])) {
                            $skippedAsPembimbing++;
                            continue;
                        }

                        $assignedInGroup[$dosenUserId] = true;

                        PengujiModel::firstOrCreate([
                            'kelompok_id' => $kelompokId,
                            'user_id' => $dosenUserId,
                        ]);

                        $savedAssignments++;
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Penguji berhasil disimpan ke database.',
                'saved_assignments' => $savedAssignments,
                'deleted_assignments' => $deletedAssignments,
                'skipped_as_pembimbing' => $skippedAsPembimbing,
            ]);
        } catch (\Exception $e) {
            Log::error('saveGeneratedPenguji error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan penguji: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function cekPenguji(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session user tidak ditemukan.'
                ], 401);
            }

            $role = $this->getPrimaryDosenRole($userId);
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role dosen tidak ditemukan.'
                ], 404);
            }

            $contextKelompokIds = Kelompok::where('prodi_id', $role->prodi_id)
                ->where('KPA_id', $role->KPA_id)
                ->where('TM_id', $role->TM_id)
                ->pluck('id')
                ->toArray();

            if (empty($contextKelompokIds)) {
                return response()->json([
                    'success' => true,
                    'exists' => false,
                    'total' => 0,
                    'message' => 'Tidak ada kelompok pada konteks ini.'
                ]);
            }

            $existingCount = PengujiModel::whereIn('kelompok_id', $contextKelompokIds)->count();

            return response()->json([
                'success' => true,
                'exists' => $existingCount > 0,
                'total' => $existingCount,
                'context_kelompok_total' => count($contextKelompokIds),
            ]);
        } catch (\Exception $e) {
            Log::error('cekPenguji error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal cek penguji: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteForContext(Request $request)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session user tidak ditemukan.'
                ], 401);
            }

            $role = $this->getPrimaryDosenRole($userId);
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role dosen tidak ditemukan.'
                ], 404);
            }

            $existingIds = Kelompok::where('prodi_id', $role->prodi_id)
                ->where('KPA_id', $role->KPA_id)
                ->where('TM_id', $role->TM_id)
                ->pluck('id')
                ->toArray();

            $deletedMembers = 0;
            $deletedKelompok = 0;

            DB::transaction(function () use ($existingIds, &$deletedMembers, &$deletedKelompok) {
                if (!empty($existingIds)) {
                    $deletedMembers = KelompokMahasiswa::whereIn('kelompok_id', $existingIds)->delete();
                    $deletedKelompok = Kelompok::whereIn('id', $existingIds)->delete();
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Kelompok berhasil dihapus.',
                'deleted_kelompok' => $deletedKelompok,
                'deleted_members' => $deletedMembers,
            ]);
        } catch (\Exception $e) {
            Log::error('deleteForContext error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kelompok: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function downloadExcel(Request $request)
    {
        try {
            $filename = $request->input('filename');
            
            if (!$filename) {
                return response()->json([
                    'success' => false,
                    'message' => 'Filename tidak ditemukan'
                ], 400);
            }

            // Path ke file Excel yang sudah di-generate oleh agent_ai
            $filePath = base_path('../agent_ai/storage/outputs/' . basename($filename));

            // Validate file exists
            if (!file_exists($filePath)) {
                Log::warning('File not found for download', [
                    'requested_filename' => $filename,
                    'expected_path' => $filePath,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            // Return file download
            return response()->download($filePath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);

        } catch (\Exception $e) {
            Log::error('downloadExcel error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload file: ' . $e->getMessage(),
            ], 500);
        }
    }

  
}