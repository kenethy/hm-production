<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log request details
        Log::info('DEBUG_REQUEST_START', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => $this->sanitizePayload($request->all()),
            'headers' => $this->sanitizeHeaders($request->headers->all()),
        ]);

        // Process the request
        $response = $next($request);

        // Log response details
        Log::info('DEBUG_REQUEST_END', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status' => $response->getStatusCode(),
            'duration' => microtime(true) - LARAVEL_START,
        ]);

        return $response;
    }

    /**
     * Sanitize the request payload to remove sensitive information.
     */
    private function sanitizePayload(array $payload): array
    {
        $sanitized = $payload;

        // Remove sensitive fields
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'api_key'];
        foreach ($sensitiveFields as $field) {
            if (isset($sanitized[$field])) {
                $sanitized[$field] = '[REDACTED]';
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize the request headers to remove sensitive information.
     */
    private function sanitizeHeaders(array $headers): array
    {
        $sanitized = $headers;

        // Remove sensitive headers
        $sensitiveHeaders = ['authorization', 'cookie', 'x-csrf-token'];
        foreach ($sensitiveHeaders as $header) {
            if (isset($sanitized[$header])) {
                $sanitized[$header] = ['[REDACTED]'];
            }
        }

        return $sanitized;
    }
}
