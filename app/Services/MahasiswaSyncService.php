<?php

namespace App\Services;

use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class MahasiswaSyncService
{
    /**
     * Sinkronisasi data mahasiswa fakultas VOKASI dari API CIS
     * Menggunakan token session user
     */
    public function syncWithSession(string $token): int
    {
        // 1️⃣ Panggil API CIS
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ])
        ->timeout(30)
        ->get(config('services.mahasiswa_api.url'));

        // 2️⃣ Validasi response
        if ($response->unauthorized()) {
            throw new \Exception('Session user tidak valid / token expired');
        }

        if (!$response->successful()) {
            throw new \Exception('Gagal mengambil data mahasiswa dari API CIS');
        }

        // 3️⃣ Ambil data mahasiswa
        $data = $response->json('data.mahasiswa');

        if (!is_array($data)) {
            Log::warning('Format data mahasiswa tidak valid', [
                'response' => $response->json()
            ]);
            return 0;
        }

        // 4️⃣ FILTER + NORMALISASI DATA
        $payload = collect($data)
            ->filter(function ($mhs) {
                // 🔑 FILTER FAKULTAS: HANYA VOKASI
                return isset($mhs['fakultas'])
                    && strtolower(trim($mhs['fakultas'])) === 'vokasi';
            })
            ->map(function ($mhs) {

                // ❌ Skip data tanpa NIM
                if (empty($mhs['nim'])) {
                    return null;
                }

                // 🧼 Normalisasi prodi_id
                $prodiId = $mhs['prodi_id'] ?? null;
                if ($prodiId === '-' || $prodiId === '') {
                    $prodiId = null;
                }

                // 🧼 Normalisasi angkatan
                $angkatan = is_numeric($mhs['angkatan'] ?? null)
                    ? (int) $mhs['angkatan']
                    : null;

                return [
                    'nim'         => (string) $mhs['nim'],
                    'dim_id'      => Arr::get($mhs, 'dim_id'),
                    'user_id'     => Arr::get($mhs, 'user_id'),
                    'user_name'   => Arr::get($mhs, 'user_name'),
                    'nama'        => trim(Arr::get($mhs, 'nama', '')),
                    'email'       => Arr::get($mhs, 'email'),
                    'prodi_id'    => $prodiId,
                    'prodi_name'  => Arr::get($mhs, 'prodi_name'),
                    'fakultas'    => 'Vokasi', // 🔒 dipastikan konsisten
                    'angkatan'    => $angkatan,
                    'status'      => Arr::get($mhs, 'status'),
                    'asrama'      => Arr::get($mhs, 'asrama'),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            })
            ->filter() // buang null
            ->values()
            ->toArray();

        if (empty($payload)) {
            Log::info('Tidak ada data mahasiswa fakultas Vokasi yang valid');
            return 0;
        }

        // 5️⃣ UPSERT KE DATABASE
        Mahasiswa::upsert(
            $payload,
            ['nim'], // unique key
            [
                'dim_id',
                'user_id',
                'user_name',
                'nama',
                'email',
                'prodi_id',
                'prodi_name',
                'fakultas',
                'angkatan',
                'status',
                'asrama',
                'updated_at',
            ]
        );

        Log::info('Sync mahasiswa fakultas Vokasi selesai', [
            'total' => count($payload)
        ]);

        return count($payload);
    }
}