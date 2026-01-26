<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    /**
     * Get candidate wallet info
     */
    public function getWallet($userId)
    {
        $user = auth()->user();

        // Security: User can only view their own wallet
        if ($user->id != $userId) {
            return response()->json([
                'error' => 'Unauthorized access',
            ], 403);
        }

        $wallet = Wallet::where('user_id', $userId)->first();
        $walletToken = WalletToken::where('user_id', $userId)->first();

        return response()->json([
            'balance' => $wallet ? (float)$wallet->balance : 0.00,
            'currency' => $wallet ? $wallet->currency : 'NGN',
            'wallet_token' => $walletToken ? $walletToken->wallet_token : null,
        ]);
    }

    /**
     * Generate wallet token for candidate
     */
    public function generateToken(Request $request)
    {
        $user = auth()->user();

        // Generate or regenerate token
        $token = 'WT_' . strtoupper(Str::random(16));

        WalletToken::updateOrCreate(
            ['user_id' => $user->id],
            ['wallet_token' => $token]
        );

        return response()->json([
            'status' => 'success',
            'wallet_token' => $token,
        ]);
    }
}