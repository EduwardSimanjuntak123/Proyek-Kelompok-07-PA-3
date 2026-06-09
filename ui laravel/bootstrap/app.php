<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'role'=> \App\Http\Middleware\RoleMiddleware::class,
            'auth.api' => \App\Http\Middleware\AuthApiMiddleware::class,
            'dosen_roles' => \App\Http\Middleware\CheckDosenMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'send-wa',
        ]);

    }) 
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle validation exceptions
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors(),
                'error_type' => 'validation'
            ], 422);
        });

        // Handle database connection errors
        $exceptions->render(function (\Illuminate\Database\QueryException $e) {
            Log::error('Database Error:', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql() ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Database connection error. Please try again later.',
                'error_type' => 'database',
            ], 500);
        });

        // Handle HTTP request exceptions
        $exceptions->render(function (\Illuminate\Http\Client\RequestException $e) {
            Log::error('External API Error:', [
                'message' => $e->getMessage(),
                'status' => $e->response?->status() ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to external service. Please try again later.',
                'error_type' => 'network',
            ], 503);
        });

        // Handle generic exceptions
        $exceptions->render(function (\Exception $e) {
            Log::error('Application Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error_type' => 'general',
            ], 500);
        });
    })->create();