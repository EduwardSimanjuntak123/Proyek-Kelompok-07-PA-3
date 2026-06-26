<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AgentApiService
{
    protected string $baseUrl;
    protected int $timeout;
    protected int $connectTimeout;
    protected int $retryTimes;
    protected int $retryDelay;
    protected string $endpoint;
    protected array $endpoints;

    public function __construct()
    {
        $this->endpoint = config('agent.endpoint', '/agent');
        $this->timeout = config('agent.timeout');
        $this->connectTimeout = config('agent.connect_timeout');
        $this->retryTimes = config('agent.retry_times');
        $this->retryDelay = config('agent.retry_delay');
        $this->endpoint = config('agent.endpoint');
        $this->endpoints = config('agent.endpoints');
    }

    /**
     * Send prompt to agent
     */
    public function sendPrompt(array $payload, string $traceId): array
    {
        try {
            $url = $this->baseUrl . $this->endpoint;
            
            Log::info("[$traceId] Sending request to Agent API", [
                'url' => $url,
                'user_id' => $payload['user_id'] ?? null,
                'prompt_preview' => substr($payload['prompt'] ?? '', 0, 100)
            ]);

            $response = Http::connectTimeout($this->connectTimeout)
                ->timeout($this->timeout)
                ->retry($this->retryTimes, $this->retryDelay)
                ->post($url, $payload);

            Log::info("[$traceId] Agent API Response Status", [
                'status' => $response->status(),
                'successful' => $response->successful()
            ]);

            if (!$response->successful()) {
                $friendlyMessage = $this->getFriendlyErrorMessage($response->status());
                
                Log::error("[$traceId] Agent API Error Response", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error_type' => 'agent_http_error',
                    'http_status' => $response->status(),
                    'result' => $friendlyMessage,
                    'trace_id' => $traceId
                ];
            }

            $data = $response->json();
            Log::info("[$traceId] Agent API Success Response", [
                'action' => $data['action'] ?? null,
                'success' => $data['success'] ?? false
            ]);

            return array_merge(['success' => true, 'trace_id' => $traceId], $data);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("[$traceId] Agent Connection Error", [
                'message' => $e->getMessage(),
                'url' => $this->baseUrl . $this->endpoint
            ]);

            return [
                'success' => false,
                'error_type' => 'agent_connection_error',
                'trace_id' => $traceId,
                'result' => 'Layanan agent tidak dapat dihubungi. Silakan coba lagi beberapa saat.',
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error("[$traceId] Agent Unexpected Error", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error_type' => 'agent_unexpected_error',
                'trace_id' => $traceId,
                'result' => 'Terjadi kesalahan saat memproses permintaan agent. Silakan coba lagi.',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get friendly error message based on HTTP status code
     */
    private function getFriendlyErrorMessage(int $statusCode): string
    {
        if ($statusCode >= 500) {
            return 'Layanan agent sedang tidak tersedia. Silakan coba lagi beberapa saat.';
        }
        
        if ($statusCode === 404) {
            return 'Layanan agent tidak ditemukan. Periksa konfigurasi integrasinya.';
        }
        
        return 'Layanan agent sedang bermasalah. Silakan coba lagi beberapa saat.';
    }

    /**
     * Check agent health
     */
    public function checkHealth(): array
    {
        try {
            $url = $this->baseUrl . $this->endpoints['health'];
            $response = Http::timeout(5)->get($url);
            
            if ($response->successful()) {
                return array_merge(['status' => 'ok'], $response->json());
            }
            
            return ['status' => 'error', 'message' => 'Health check failed'];
            
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Clear user session
     */
    public function clearSession(int $userId): array
    {
        try {
            $url = $this->baseUrl . $this->endpoints['clear_session'] . '/' . $userId;
            $response = Http::timeout(10)->post($url);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return ['success' => false, 'message' => 'Failed to clear session'];
            
        } catch (\Exception $e) {
            Log::error('Failed to clear session', ['user_id' => $userId, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get agent base URL
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get endpoint
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }
}