<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AgentAnalyticsController extends Controller
{
    // Base URL API Python Agent
    private $agentApiUrl = 'http://localhost:8002';

    /**
     * Display agent analytics dashboard
     */
    public function dashboard()
    {
        try {
            $KPA_id = session('KPA_id');
            $userId = session('user_id');

            Log::info('[Analytics Dashboard] Loading for user: ' . $userId);

            // Fetch analytics data from Python API
            $mongoStatus = $this->getMongoDBStatus();
            Log::info('[Analytics Dashboard] MongoDB Status: ' . json_encode($mongoStatus));
            
            $userAnalytics = $this->getUserAnalytics();
            Log::info('[Analytics Dashboard] User Analytics: ' . json_encode($userAnalytics));
            
            $conversationHistory = $this->getConversationHistory(limit: 20);
            Log::info('[Analytics Dashboard] Conversation History: ' . json_encode($conversationHistory));
            
            $metrics = $this->getMetrics(days: 7);
            Log::info('[Analytics Dashboard] Metrics: ' . json_encode($metrics));
            
            $executionLogs = $this->getExecutionLogs(limit: 20);
            Log::info('[Analytics Dashboard] Execution Logs: ' . json_encode($executionLogs));

            // Summary statistics
            $statistics = [
                'total_conversations' => $userAnalytics['total_messages'] ?? $conversationHistory['total'] ?? 0,
                'total_actions' => $userAnalytics['total_actions'] ?? $executionLogs['total'] ?? 0,
                'avg_response_time' => $userAnalytics['avg_response_time_ms'] ?? $metrics['avg_response_time'] ?? 0,
                'success_rate' => $this->calculateSuccessRate($executionLogs),
                'mongodb_status' => $mongoStatus['connected'] ? 'connected' : 'disconnected',
            ];

            return view('pages.Koordinator.agent.agent-analytics', compact(
                'mongoStatus',
                'userAnalytics',
                'conversationHistory',
                'metrics',
                'executionLogs',
                'statistics'
            ));
        } catch (\Exception $e) {
            Log::error('Agent Analytics Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat data analytics: ' . $e->getMessage());
        }
    }

    /**
     * Get MongoDB connection status
     */
    private function getMongoDBStatus()
    {
        try {
            $response = Http::timeout(5)->get("{$this->agentApiUrl}/mongodb-status");
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'connected' => $data['mongodb_connected'] ?? false,
                    'status' => $data['status'] ?? 'unknown',
                    'database' => $data['database'] ?? 'N/A',
                    'service' => $data['service'] ?? 'mongodb-memory',
                    'collections' => $data['collections'] ?? []
                ];
            }
            return ['connected' => false, 'status' => 'error', 'error' => 'API tidak merespons'];
        } catch (\Exception $e) {
            Log::error('MongoDB Status Check Error: ' . $e->getMessage());
            return ['connected' => false, 'status' => 'error', 'error' => $e->getMessage()];
        }
    }

    /**
     * Get user analytics from MongoDB
     */
    private function getUserAnalytics()
    {
        try {
            $userId = session('user_id');
            Log::debug('[getUserAnalytics] Requesting for user: ' . $userId);
            
            $url = "{$this->agentApiUrl}/analytics/{$userId}";
            Log::debug('[getUserAnalytics] URL: ' . $url);
            
            $response = Http::timeout(5)->get($url);
            
            Log::debug('[getUserAnalytics] Response Status: ' . $response->status());
            
            if ($response->successful()) {
                $data = $response->json();
                Log::debug('[getUserAnalytics] Response Data: ' . json_encode($data));
                
                // Map API response to expected format
                return [
                    'total_messages' => $data['total_messages'] ?? 0,
                    'total_planner_actions' => $data['total_planner_actions'] ?? 0,
                    'total_executor_actions' => $data['total_executor_actions'] ?? 0,
                    'total_actions' => $data['total_actions'] ?? 0,
                    'last_activity' => $data['last_activity'] ?? null,
                    'avg_response_time_ms' => $data['avg_response_time_ms'] ?? 0,
                    'avg_quality_score' => $data['avg_quality_score'] ?? 0,
                    'metrics_count' => $data['metrics_count'] ?? [],
                ];
            }
            
            Log::warning('[getUserAnalytics] Non-successful response: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('User Analytics Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get conversation history from MongoDB
     */
    private function getConversationHistory($limit = 20)
    {
        try {
            $userId = session('user_id');
            Log::debug('[getConversationHistory] Requesting for user: ' . $userId);
            
            $url = "{$this->agentApiUrl}/long-term-history/{$userId}";
            $response = Http::timeout(5)->get($url, ['days' => 30, 'limit' => $limit]);

            Log::debug('[getConversationHistory] Response Status: ' . $response->status());

            if ($response->successful()) {
                $data = $response->json();
                Log::debug('[getConversationHistory] Raw response: ' . json_encode($data));
                
                // API returns data under 'history' key, not 'messages'
                $messages = $data['history'] ?? [];
                Log::debug('[getConversationHistory] Messages count: ' . count($messages));
                
                return [
                    'messages' => $messages,
                    'total' => count($messages),
                ];
            }
            
            Log::warning('[getConversationHistory] Non-successful response: ' . $response->body());
            return ['messages' => [], 'total' => 0];
        } catch (\Exception $e) {
            Log::error('Conversation History Error: ' . $e->getMessage());
            return ['messages' => [], 'total' => 0];
        }
    }

    /**
     * Get performance metrics from MongoDB
     */
    private function getMetrics($days = 7)
    {
        try {
            $userId = session('user_id');
            $response = Http::timeout(5)->get(
                "{$this->agentApiUrl}/metrics/{$userId}/response_time_ms",
                ['days' => $days]
            );

            if ($response->successful()) {
                $data = $response->json();
                
                // Handle both 'values' and 'metrics' keys from API
                $values = $data['values'] ?? $data['metrics'] ?? [];
                
                return [
                    'response_time' => $values,
                    'min' => $data['min'] ?? 0,
                    'max' => $data['max'] ?? 0,
                    'avg_response_time' => $data['avg'] ?? 0,
                    'count' => $data['count'] ?? count($values),
                    'sum' => $data['sum'] ?? 0,
                ];
            }
            return ['response_time' => [], 'min' => 0, 'max' => 0, 'avg_response_time' => 0, 'count' => 0, 'sum' => 0];
        } catch (\Exception $e) {
            Log::error('Metrics Error: ' . $e->getMessage());
            return ['response_time' => [], 'min' => 0, 'max' => 0, 'avg_response_time' => 0, 'count' => 0, 'sum' => 0];
        }
    }

    /**
     * Get execution logs from MongoDB
     */
    private function getExecutionLogs($limit = 20)
    {
        try {
            $userId = session('user_id');
            Log::debug('[getExecutionLogs] Requesting for user: ' . $userId);
            
            $url = "{$this->agentApiUrl}/execution-logs/{$userId}";
            $response = Http::timeout(5)->get($url, ['limit' => $limit]);

            Log::debug('[getExecutionLogs] Response Status: ' . $response->status());

            if ($response->successful()) {
                $data = $response->json();
                Log::debug('[getExecutionLogs] Raw response: ' . json_encode($data));
                
                $logs = $data['logs'] ?? [];
                Log::debug('[getExecutionLogs] Logs count: ' . count($logs));
                
                return [
                    'logs' => $logs,
                    'total' => count($logs),
                ];
            }
            
            Log::warning('[getExecutionLogs] Non-successful response: ' . $response->body());
            return ['logs' => [], 'total' => 0];
        } catch (\Exception $e) {
            Log::error('Execution Logs Error: ' . $e->getMessage());
            return ['logs' => [], 'total' => 0];
        }
    }

    /**
     * Calculate success rate from execution logs
     */
    private function calculateSuccessRate($logs)
    {
        if (empty($logs['logs']) || $logs['total'] === 0) {
            return 0;
        }

        $successful = 0;
        foreach ($logs['logs'] as $log) {
            if (isset($log['status']) && $log['status'] === 'success') {
                $successful++;
            }
        }

        return round(($successful / $logs['total']) * 100, 2);
    }

    /**
     * API endpoint: Get analytics data (for AJAX)
     */
    public function getAnalyticsData(Request $request)
    {
        try {
            $userId = session('user_id');
            $dataType = $request->query('type', 'analytics');

            $data = match ($dataType) {
                'analytics' => $this->getUserAnalytics(),
                'history' => $this->getConversationHistory(limit: $request->query('limit', 20)),
                'metrics' => $this->getMetrics(days: $request->query('days', 7)),
                'logs' => $this->getExecutionLogs(limit: $request->query('limit', 20)),
                default => [],
            };

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Get Analytics Data Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Refresh analytics (for manual refresh button)
     */
    public function refresh()
    {
        try {
            return redirect()->route('agent.analytics.dashboard')
                ->with('success', 'Data analytics berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Debug endpoint to check API connectivity
     */
    public function debug()
    {
        try {
            $userId = session('user_id');
            
            $debugInfo = [
                'user_id' => $userId,
                'api_url' => $this->agentApiUrl,
                'session_data' => [
                    'user_id' => session('user_id'),
                    'KPA_id' => session('KPA_id'),
                    'prodi_id' => session('prodi_id'),
                    'TM_id' => session('TM_id'),
                ],
                'api_endpoints' => [
                    'mongodb_status' => "{$this->agentApiUrl}/mongodb-status",
                    'analytics' => "{$this->agentApiUrl}/analytics/{$userId}",
                    'long_term_history' => "{$this->agentApiUrl}/long-term-history/{$userId}",
                    'metrics' => "{$this->agentApiUrl}/metrics/{$userId}/response_time_ms",
                    'execution_logs' => "{$this->agentApiUrl}/execution-logs/{$userId}",
                ]
            ];

            // Test each endpoint
            $debugInfo['api_responses'] = [];

            try {
                $response = Http::timeout(5)->get("{$this->agentApiUrl}/mongodb-status");
                $debugInfo['api_responses']['mongodb_status'] = [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ];
            } catch (\Exception $e) {
                $debugInfo['api_responses']['mongodb_status'] = ['error' => $e->getMessage()];
            }

            try {
                $response = Http::timeout(5)->get("{$this->agentApiUrl}/analytics/{$userId}");
                $debugInfo['api_responses']['analytics'] = [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ];
            } catch (\Exception $e) {
                $debugInfo['api_responses']['analytics'] = ['error' => $e->getMessage()];
            }

            try {
                $response = Http::timeout(5)->get("{$this->agentApiUrl}/long-term-history/{$userId}", ['days' => 30, 'limit' => 5]);
                $debugInfo['api_responses']['long_term_history'] = [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ];
            } catch (\Exception $e) {
                $debugInfo['api_responses']['long_term_history'] = ['error' => $e->getMessage()];
            }

            Log::info('[Analytics Debug] ' . json_encode($debugInfo));

            return response()->json($debugInfo, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
