<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class Kategori_PASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategori_pa')->insert([
        [
            'kategori_pa' => 'PA-1',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'kategori_pa' => 'PA-2',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'kategori_pa' => 'PA-3',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
    }
}
