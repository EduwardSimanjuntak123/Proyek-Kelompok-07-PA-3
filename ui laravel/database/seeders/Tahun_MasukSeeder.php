<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Tahun_MasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tahun_masuk')->insert([
          
            [
                'Tahun_Masuk' => '2019',
                'created_at' => now(),
                'updated_at' => now(),
                'Status' => 'Aktif',
            ]
        ]);
    }
}
