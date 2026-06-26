<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\NilaiMatkulMahasiswa;
use Illuminate\Support\Facades\DB;

class NilaiMahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswa = Mahasiswa::all();
        $matkul = MataKuliah::all();

        foreach ($mahasiswa as $mhs) {

            foreach ($matkul as $mk) {

                $nilai = rand(60, 95);

                $huruf = match (true) {
                    $nilai >= 79.5 => 'A',
                    $nilai >= 72 => 'AB',
                    $nilai >= 64.5 => 'B',
                    $nilai >= 57 => 'BC',
                    $nilai >= 49.5 => 'C',
                    $nilai >= 34 => 'D',
                    default => 'E'
                };

                $bobot = match ($huruf) {
                    'A' => 4.0,
                    'AB' => 3.5,
                    'B' => 3.0,
                    'BC' => 2.5,
                    'C' => 2.0,
                    'D' => 1.0,
                    'E' => 0.0
                };

                DB::table('nilai_matkul_mahasiswa')->insert([
                    'mahasiswa_id' => $mhs->user_id,
                    'kode_mk' => $mk->kode_mk,
                    'nilai_angka' => $nilai,
                    'nilai_huruf' => $huruf,
                    'bobot_nilai' => $bobot,
                    'semester' => $mk->semester,
                    'tahun_ajaran' => $mk->tahun_ajaran,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

            }
        }
    }
}