<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the secret key from .env
        $secretKey = config('app.api_secret_key');

        // Get the provided key from the request
        $providedKey = $request->header('X-API-KEY');

        // Validate header key
        if (!$providedKey || $providedKey !== $secretKey) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized Api Request'], 403);
        }

        return $next($request);
    }
}
