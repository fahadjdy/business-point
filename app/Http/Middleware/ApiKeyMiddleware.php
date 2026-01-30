<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\ApiResponse;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('services.auth_api_key');
        
        // Check for API key in header first
        $providedKey = $request->header('X-Api-Key');
        
        // For export requests, also check query parameter
        if (!$providedKey && ($request->is('api/*/contacts/export') || $request->query('api_key'))) {
            $providedKey = $request->query('api_key') ?: $request->header('X-Api-Key');
        }

        if (!$providedKey || $providedKey !== $apiKey) {
            return ApiResponse::error('Unauthorized: Invalid or missing API Key', [], 401);
        }

        return $next($request);
    }
}
