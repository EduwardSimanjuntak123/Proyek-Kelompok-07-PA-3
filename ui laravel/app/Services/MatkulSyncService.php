<?php

namespace App\Services;

use App\Models\MataKuliah;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MatkulSyncService
{
    public function sync(string $token): int
    {
        $semesters = [1, 2];

        $allData = [];

        foreach ($semesters as $sem) {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])
            ->timeout(30)
            ->get(config('services.matkul_api.url'), [
                'prodi_id' => 4,
                'ta' => 2020,
                'sem_ta' => $sem
            ]);

            if (!$response->successful()) {
                throw new \Exception("Gagal mengambil data mata kuliah semester_ta $sem");
            }

            $data = $response->json('data');

            if (!is_array($data)) {
                continue;
            }

            foreach ($data as $mk) {

                $allData[] = [
                    'kuliah_id' => $mk['kuliah_id'],
                    'kode_mk' => $mk['kode_mk'],
                    'nama_matkul' => trim($mk['nama_matkul']),
                    'sks' => $mk['sks'],
                    'semester' => $mk['sem'],
                    'prodi_id' => 4,
                    'tahun_ajaran' => 2020,
                    'semester_ta' => $sem,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

            }
        }

        if (empty($allData)) {
            return 0;
        }

        MataKuliah::upsert(
            $allData,
            ['kuliah_id'],
            [
                'kode_mk',
                'nama_matkul',
                'sks',
                'semester',
                'updated_at'
            ]
        );

        Log::info('Sync mata kuliah selesai', [
            'total' => count($allData)
        ]);

        return count($allData);
    }
}