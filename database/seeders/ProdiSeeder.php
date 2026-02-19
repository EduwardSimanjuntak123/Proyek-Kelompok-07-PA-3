<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('prodi')->insert([
            [
                'id' => 4,
                'nama_prodi' => 'DIV Teknologi Rekayasa Perangkat Lunak',
                'maks_project' =>'3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            ['id' => 1,
                'nama_prodi' => 'DIII Teknologi Informasi',
                'maks_project' =>'2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nama_prodi' => 'DIII Teknologi Komputer',
                'maks_project' =>'2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
