<?php

namespace App\Services;

use App\Models\Dosen;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class DosenSyncService
{
    /**
     * Sinkronisasi data dosen fakultas VOKASI dari API CIS
     */
    public function syncWithSession(string $token): int
    {
        // 1️⃣ Panggil API CIS
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])
            ->timeout(30)
            ->get(config('services.dosen_api.url'));

        // 2️⃣ Validasi response
        if ($response->unauthorized()) {
            throw new \Exception('Token CIS tidak valid / expired');
        }

        if (!$response->successful()) {
            throw new \Exception('Gagal mengambil data dosen dari API CIS');
        }

        // 3️⃣ Ambil data dosen
        $data = $response->json('data.dosen');

        if (!is_array($data)) {
            Log::warning('Format data dosen tidak valid', [
                'response' => $response->json()
            ]);
            return 0;
        }

        // 4️⃣ FILTER + NORMALISASI DATA
        $payload = collect($data)

            // 🔑 FILTER DOSEN VOKASI (PRODI DIII)
            ->filter(function ($dsn) {

                $prodiId = $dsn['prodi_id'] ?? null;

                if (!is_numeric($prodiId)) {
                    return false;
                }

                return in_array((int) $prodiId, [4, 1, 3]);
            })

            ->map(function ($dsn) {

                if (empty($dsn['nama'])) {
                    return null;
                }

                // NORMALISASI prodi_id
                $prodiId = $dsn['prodi_id'] ?? null;
                if ($prodiId === '-' || $prodiId === '') {
                    $prodiId = null;
                }

                // NORMALISASI email
                $email = Arr::get($dsn, 'email');
                if ($email === '-' || $email === '') {
                    $email = null;
                }

                // NORMALISASI nip
                $nip = Arr::get($dsn, 'nip');
                if ($nip === '-') {
                    $nip = null;
                }

                return [
                    'pegawai_id' => Arr::get($dsn, 'pegawai_id'),
                    'dosen_id' => Arr::get($dsn, 'dosen_id'),
                    'nip' => $nip,
                    'nama' => trim(Arr::get($dsn, 'nama')),
                    'email' => $email,
                    'prodi_id' => $prodiId, // ← sudah aman
                    'prodi' => Arr::get($dsn, 'prodi'),
                    'jabatan_akademik' => Arr::get($dsn, 'jabatan_akademik'),
                    'jabatan_akademik_desc' => Arr::get($dsn, 'jabatan_akademik_desc'),
                    'jenjang_pendidikan' => Arr::get($dsn, 'jenjang_pendidikan'),
                    'nidn' => Arr::get($dsn, 'nidn'),
                    'user_id' => Arr::get($dsn, 'user_id'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })

            ->filter() // buang null
            ->values()
            ->toArray();

        if (empty($payload)) {
            Log::info('Tidak ada data dosen fakultas Vokasi yang valid');
            return 0;
        }

        // 5️⃣ UPSERT KE DATABASE
        Dosen::upsert(
            $payload,
            ['user_id'], // unique key
            [
                'pegawai_id',
                'dosen_id',
                'nip',
                'nama',
                'email',
                'prodi_id',
                'prodi',
                'jabatan_akademik',
                'jabatan_akademik_desc',
                'jenjang_pendidikan',
                'nidn',
                'updated_at'
            ]
        );

        Log::info('Sync dosen fakultas Vokasi selesai', [
            'total' => count($payload)
        ]);

        return count($payload);
    }
}