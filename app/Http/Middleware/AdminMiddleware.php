<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
   public function handle($request, Closure $next)
{
    $user = auth()->user();

    if (!$user || $user->role !== 'admin') {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    return $next($request);
}

}