<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AdminTokenAuth
{
    /**
     * Handle an incoming request and verify admin token
     */
    public function handle(Request $request, Closure $next)
    {
        // Get token from Authorization header
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'error' => 'No token provided',
            ], 401);
        }

        // Hash the token to compare with database
        $hashedToken = hash('sha256', $token);

        // Find user by token
        $user = User::where('admin_token', $hashedToken)
            ->where('admin_token_expires_at', '>', now())
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'error' => 'Invalid or expired token',
            ], 401);
        }

        // Check if user is admin
        if ($user->role !== 'admin' && $user->role !== 'super_admin') {
            Log::warning('Non-admin tried to access admin route', [
                'user_id' => $user->id,
                'role' => $user->role,
            ]);

            return response()->json([
                'status' => 'error',
                'error' => 'Unauthorized. Admin access only.',
            ], 403);
        }

        auth()->setUser($user);

        // Attach user to request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}