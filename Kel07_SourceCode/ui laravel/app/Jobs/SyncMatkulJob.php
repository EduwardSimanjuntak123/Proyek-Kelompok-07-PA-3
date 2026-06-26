<?php

namespace App\Jobs;

use App\Services\MatkulSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncMatkulJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function handle(MatkulSyncService $service): void
    {
        Log::info('JOB sync mata kuliah dimulai');

        $total = $service->sync($this->token);

        Log::info('JOB sync mata kuliah selesai', [
            'total' => $total
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('JOB sync mata kuliah gagal', [
            'error' => $e->getMessage()
        ]);
    }
}