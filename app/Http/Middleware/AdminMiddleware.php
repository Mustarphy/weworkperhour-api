<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check API key from header
        $apiKey = $request->header('x-api-key');
        
        if ($apiKey !== env('ADMIN_API_KEY', 'secret123')) {
            return response()->json([
                'status' => 'error',
                'error' => 'Unauthorized: Invalid API key',
            ], 401);
        }

        return $next($request);
    }
}