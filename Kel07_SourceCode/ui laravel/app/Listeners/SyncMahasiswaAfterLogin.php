<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use App\Jobs\SyncMahasiswaJob;

class SyncMahasiswaAfterLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $token = session('token');

        Log::info('EVENT LOGIN TERPICU', [
            'user_id' => $event->user->id
        ]);

        if (!$token) {
            Log::warning('Login tanpa CIS token, sync mahasiswa dilewati', [
                'user_id' => $event->user->id
            ]);
            return;
        }

        // ⬅️ PENTING: kirim ke queue (NON blocking)
        SyncMahasiswaJob::dispatch($token, $event->user->id);

        Log::info('Job sync mahasiswa berhasil dikirim ke queue', [
            'user_id' => $event->user->id
        ]);
    }
}