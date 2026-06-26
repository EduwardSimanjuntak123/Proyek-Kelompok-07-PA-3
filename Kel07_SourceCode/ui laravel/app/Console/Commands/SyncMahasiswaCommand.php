<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncMahasiswaCommand extends Command
{
    protected $signature = 'sync:mahasiswa';
    protected $description = 'Command dinonaktifkan karena sync butuh session user';

    public function handle()
    {
        $this->error(
            'Sync mahasiswa hanya bisa dilakukan setelah user login (berbasis session)'
        );
    }
}