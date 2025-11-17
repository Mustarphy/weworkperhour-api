<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            // Log incoming request for debugging
            \Log::info('JWT Middleware - Incoming request', [
                'url' => $request->url(),
                'has_auth_header' => $request->header('Authorization') !== null,
            ]);
            
            $user = JWTAuth::parseToken()->authenticate();
            
            \Log::info('JWT Middleware - User authenticated', [
                'user_id' => $user ? $user->id : null,
            ]);

            // This is required for Broadcast::auth() to work
            if ($user) {
                auth()->setUser($user);
                $request->setUserResolver(function () use ($user) {
                    return $user;
                });
            }

        } catch (Exception $e) {
            \Log::error('JWT Middleware - Exception', [
                'error' => $e->getMessage(),
                'type' => get_class($e),
                'url' => $request->url(),
            ]);
            
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['status' => 'Token is Invalid'], 401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['status' => 'Token is Expired'], 401);
            }else{
                return response()->json(['status' => 'Authorization Token not found'], 401);
            }
        }
        return $next($request);
    }
}