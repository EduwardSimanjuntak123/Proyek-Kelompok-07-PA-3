<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class ErrorLogController extends Controller
{
    /**
     * Log client-side errors from JavaScript
     */
    public function logError(Request $request)
    {
        try {
            $errorData = $request->all();
            
            // Log the error
            Log::channel('errors')->error('Client Error', [
                'type' => $errorData['type'] ?? 'unknown',
                'message' => $errorData['message'] ?? 'No message',
                'url' => $errorData['url'] ?? null,
                'timestamp' => $errorData['timestamp'] ?? null,
                'user_agent' => $errorData['userAgent'] ?? null,
                'user_id' => Auth::id(),
                'stack' => $errorData['stack'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Error logged successfully'
            ]);
        } catch (\Exception $e) {
            // Even if logging fails, don't break the client
            Log::error('Failed to log client error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to log error'
            ], 500);
        }
    }

    /**
     * Get error statistics (for admin dashboard)
     */
    public function getStatistics(Request $request)
{
    $logFile = storage_path('logs/errors.log');

    if (!File::exists($logFile)) {
        return response()->json([
            'total_errors' => 0,
            'errors_by_type' => [],
            'recent_errors' => []
        ]);
    }

    $lines = File::lines($logFile);

    $logs = collect(iterator_to_array($lines));

    return response()->json([
        'total_errors' => $logs->count(),
        'errors_by_type' => [],
        'recent_errors' => $logs->take(-10)->values()
    ]);
}
}
