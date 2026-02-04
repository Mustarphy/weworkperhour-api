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
        'work_status',       
        'work_approved_at',  
        'released_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'released_at' => 'datetime',
        'work_approved_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(EmployerPayment::class);
    }
}