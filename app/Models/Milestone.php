<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $fillable = [
        'payment_id',
        'title',
        'percentage',
        'amount',
        'status',
        'released_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'released_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(EmployerPayment::class);
    }
}