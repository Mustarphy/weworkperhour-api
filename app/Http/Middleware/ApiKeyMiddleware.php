<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
public function handle($request, Closure $next)
{
    $apiKey = $request->header('x-api-key');

   if ($request->header('x-api-key') !== env('ADMIN_API_KEY')) {
    return response()->json([
        'status' => 'error',
        'message' => 'Unauthorized',
    ], 401);
}


    return $next($request);
}


}
