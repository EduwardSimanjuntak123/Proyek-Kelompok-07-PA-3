<?php

namespace App\Jobs;

use App\Services\DosenSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncDosenJob implements ShouldQueue
{
    use Queueable;

    protected $token;

    /**
     * Create a new job instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            app(DosenSyncService::class)
                ->syncWithSession($this->token);

        } catch (\Throwable $e) {

            Log::error('Gagal sync dosen', [
                'error' => $e->getMessage()
            ]);

        }
    }
}