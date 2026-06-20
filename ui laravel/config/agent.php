<?php
// config/agent.php

return [
    'base_url' => env('AGENT_API_URL', 'http://187.77.121.239:8002'),

    'timeout' => env('AGENT_API_TIMEOUT', 600),

    'connect_timeout' => env('AGENT_API_CONNECT_TIMEOUT', 5),

    'retry_times' => env('AGENT_API_RETRY_TIMES', 2),

    'retry_delay' => env('AGENT_API_RETRY_DELAY', 500),

    // ✅ TAMBAHKAN INI (default endpoint utama)
    'endpoint' => env('AGENT_API_ENDPOINT', '/agent'),

    'endpoints' => [
        'agent' => '/agent',
        'health' => '/health',
        'health_detailed' => '/health/detailed',
        'clear_session' => '/clear-session',
        'debug_session' => '/debug/session',
        'conversation_history' => '/conversation-history',
        'analytics' => '/analytics',
    ],
];