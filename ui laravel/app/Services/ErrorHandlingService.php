<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ErrorHandlingService
{
    const ERROR_TYPES = [
        'NETWORK' => 'network',
        'TIMEOUT' => 'timeout',
        'SERVER' => 'server',
        'DATABASE' => 'database',
        'VALIDATION' => 'validation',
        'AUTHENTICATION' => 'authentication',
        'AUTHORIZATION' => 'authorization',
        'NOT_FOUND' => 'not_found',
        'UNKNOWN' => 'unknown'
    ];

    /**
     * Handle and respond to errors
     */
    public static function handleError(
        $message,
        $type = 'general',
        $statusCode = 500,
        $data = null,
        $logLevel = 'error'
    ): JsonResponse {
        // Log the error
        Log::log($logLevel, $message, [
            'type' => $type,
            'data' => $data,
            'status_code' => $statusCode
        ]);

        return response()->json([
            'success' => false,
            'message' => $message,
            'error_type' => $type,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Handle database errors
     */
    public static function databaseError($exception): JsonResponse
    {
        Log::error('Database Error', [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ]);

        return self::handleError(
            'Database error. Please try again later.',
            self::ERROR_TYPES['DATABASE'],
            500
        );
    }

    /**
     * Handle validation errors
     */
    public static function validationError($errors): JsonResponse
    {
        return self::handleError(
            'Validation failed.',
            self::ERROR_TYPES['VALIDATION'],
            422,
            $errors
        );
    }

    /**
     * Handle network/API errors
     */
    public static function networkError($exception): JsonResponse
    {
        Log::error('Network Error', [
            'message' => $exception->getMessage(),
            'status' => $exception->getCode()
        ]);

        return self::handleError(
            'Unable to connect to external service. Please try again later.',
            self::ERROR_TYPES['NETWORK'],
            503
        );
    }

    /**
     * Handle authentication errors
     */
    public static function authenticationError(): JsonResponse
    {
        return self::handleError(
            'Authentication required. Please log in again.',
            self::ERROR_TYPES['AUTHENTICATION'],
            401
        );
    }

    /**
     * Handle authorization errors
     */
    public static function authorizationError(): JsonResponse
    {
        return self::handleError(
            'You do not have permission to perform this action.',
            self::ERROR_TYPES['AUTHORIZATION'],
            403
        );
    }

    /**
     * Handle not found errors
     */
    public static function notFoundError($resource = 'Resource'): JsonResponse
    {
        return self::handleError(
            "$resource not found.",
            self::ERROR_TYPES['NOT_FOUND'],
            404
        );
    }

    /**
     * Handle timeout errors
     */
    public static function timeoutError(): JsonResponse
    {
        return self::handleError(
            'Request timeout. Server did not respond in time.',
            self::ERROR_TYPES['TIMEOUT'],
            504
        );
    }

    /**
     * Handle generic server errors
     */
    public static function serverError($message = null): JsonResponse
    {
        return self::handleError(
            $message ?? 'An unexpected error occurred. Please try again later.',
            self::ERROR_TYPES['SERVER'],
            500
        );
    }

    /**
     * Log error to file
     */
    public static function logError($message, $context = [], $channel = 'errors')
    {
        Log::channel($channel)->error($message, $context);
    }

    /**
     * Get user-friendly error message
     */
    public static function getUserMessage($exceptionClass): string
    {
        $messages = [
            'Illuminate\Database\QueryException' => 'Database error. Please try again later.',
            'Illuminate\Validation\ValidationException' => 'Validation failed. Please check your input.',
            'Illuminate\Auth\AuthenticationException' => 'Authentication required.',
            'Illuminate\Auth\Access\AuthorizationException' => 'You are not authorized to perform this action.',
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException' => 'Resource not found.',
            'Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException' => 'Method not allowed.',
        ];

        return $messages[$exceptionClass] ?? 'An unexpected error occurred.';
    }
}
