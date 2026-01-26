<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'currency',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

     /**
     * Safely credit wallet (only from approved payments)
     */
    public function credit(float $amount, string $source, int $sourceId)
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Credit amount must be positive');
        }

        $this->increment('balance', $amount);

        // Log the transaction for audit trail
        WalletTransaction::create([
            'wallet_id' => $this->id,
            'type' => 'credit',
            'amount' => $amount,
            'balance_after' => $this->fresh()->balance,
            'source' => $source, // e.g., 'employer_payment'
            'source_id' => $sourceId, // payment_id
            'description' => "Payment from employer approved",
        ]);

        return $this;
    }

    /**
     * Safely debit wallet (for withdrawals)
     */
    public function debit(float $amount, string $source, int $sourceId)
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Debit amount must be positive');
        }

        if ($this->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }

        $this->decrement('balance', $amount);

        // Log the transaction
        WalletTransaction::create([
            'wallet_id' => $this->id,
            'type' => 'debit',
            'amount' => $amount,
            'balance_after' => $this->fresh()->balance,
            'source' => $source, // e.g., 'withdrawal'
            'source_id' => $sourceId, // withdrawal_id
            'description' => "Withdrawal request",
        ]);

        return $this;
    }
}