<?php

namespace App\Jobs;

use App\Services\MahasiswaSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncMahasiswaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $token;
    protected int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $token, int $userId)
    {
        $this->token  = $token;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(MahasiswaSyncService $service): void
    {
        Log::info('JOB sync mahasiswa dimulai', [
            'user_id' => $this->userId
        ]);

        $total = $service->syncWithSession($this->token);

        Log::info('JOB sync mahasiswa selesai', [
            'user_id' => $this->userId,
            'total'   => $total
        ]);
    }

    /**
     * Jika job gagal
     */
    public function failed(\Throwable $e): void
    {
        Log::error('JOB sync mahasiswa GAGAL', [
            'user_id' => $this->userId,
            'error'   => $e->getMessage()
        ]);
    }
}