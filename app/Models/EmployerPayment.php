<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployerPayment extends Model
{
    protected $fillable = [
        'employer_id',
        'candidate_id',
        'amount',
        'type',
        'employer_pays_total',
        'platform_fee',
        'freelancer_receives',
        'platform_commission',
        'platform_vat',
        'status',
        'work_status',
        'work_approved_at',
        'reference',
        'wallet_token',
        'payment_method',
        'paid_at',
    ];

    protected $casts = [
        // 'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'work_approved_at' => 'datetime',
    ];

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function candidate()
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class, 'payment_id');
    }
}
