<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    /**
     * Admin login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            // Find user by email
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Invalid credentials',
                ], 401);
            }

            // Check if user is admin
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                Log::warning('Non-admin user attempted admin login', [
                    'email' => $request->email,
                    'role' => $user->role,
                ]);

                return response()->json([
                    'status' => 'error',
                    'error' => 'Unauthorized. Admin access only.',
                ], 403);
            }

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Invalid credentials',
                ], 401);
            }

            // Generate admin token (simple token - you can use Sanctum or JWT)
            $token = Str::random(60);

            // Store token in database (you'll need to add admin_token column to users table)
            $user->update([
                'admin_token' => hash('sha256', $token),
                'admin_token_expires_at' => now()->addDays(7),
            ]);

            Log::info('Admin logged in successfully', [
                'admin_id' => $user->id,
                'email' => $user->email,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'token' => $token,
                    'admin' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Admin login error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'error' => 'Login failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Admin logout
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user) {
                $user->update([
                    'admin_token' => null,
                    'admin_token_expires_at' => null,
                ]);

                Log::info('Admin logged out', [
                    'admin_id' => $user->id,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Admin logout error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'error' => 'Logout failed',
            ], 500);
        }
    }

    /**
     * Get current admin user
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user || ($user->role !== 'admin' && $user->role !== 'super_admin')) {
            return response()->json([
                'status' => 'error',
                'error' => 'Unauthorized',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }
}