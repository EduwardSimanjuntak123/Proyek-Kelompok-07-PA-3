<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
                'user_id' => auth()->id(),
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
        $this->authorize('viewAny', 'error');

        // Get error count by type in last 24 hours
        $errors = collect(
            Log::channel('errors')->getHandlers()[0]->getRecords()
        )->filter(function ($record) {
            return $record['datetime']->isAfter(now()->subDay());
        });

        return response()->json([
            'total_errors' => $errors->count(),
            'errors_by_type' => $errors->groupBy('type')->map->count(),
            'recent_errors' => $errors->take(10)->values()
        ]);
    }
}
