<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletToken extends Model
{
    protected $fillable = [
        'user_id',
        'wallet_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique wallet token
     */
    public static function generateToken($userId)
    {
        $token = self::createUniqueToken();
        
        // Check if user already has a token
        $existingToken = self::where('user_id', $userId)->first();
        
        if ($existingToken) {
            // Update existing token
            $existingToken->update(['wallet_token' => $token]);
            return $existingToken;
        }

        // Create new token
        return self::create([
            'user_id' => $userId,
            'wallet_token' => $token,
        ]);
    }

    /**
     * Create a unique token using secure random bytes
     */
    private static function createUniqueToken()
    {
        do {
            $token = 'WT_' . strtoupper(bin2hex(random_bytes(16)));
        } while (self::where('wallet_token', $token)->exists());

        return $token;
    }

    /**
     * Get token for a user
     */
    public static function getToken($userId)
    {
        return self::where('user_id', $userId)->first();
    }
}
?>