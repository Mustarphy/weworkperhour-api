<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletToken;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WalletController extends Controller
{
    /**
     * Get candidate wallet data with token
     */
    public function getWallet($userId): JsonResponse
    {
        $authUserId = auth()->id();
        
        // Verify user is accessing their own wallet
        if ((int)$userId !== (int)$authUserId) {
            return response()->json([
                'error' => 'Unauthorized',
            ], 403);
        }

        $wallet = Wallet::where('user_id', $authUserId)->first();
        
        if (!$wallet) {
            return response()->json([
                'error' => 'Wallet not found',
            ], 404);
        }

        $walletToken = WalletToken::getToken($authUserId);

        return response()->json([
            'balance' => $wallet->balance ?? 0,
            'wallet_token' => $walletToken ? $walletToken->wallet_token : null,
            'currency' => $wallet->currency ?? 'NGN',
        ]);
    }

    /**
     * Generate wallet token
     */
    public function generateToken(Request $request): JsonResponse
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return response()->json([
                'error' => 'User not authenticated',
            ], 401);
        }

        $walletToken = WalletToken::generateToken($userId);

        return response()->json([
            'wallet_token' => $walletToken->wallet_token,
            'message' => 'Wallet token generated successfully',
        ], 201);
    }
}
?>