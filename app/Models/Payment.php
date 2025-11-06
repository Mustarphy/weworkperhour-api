<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_id',
        'reference',
        'status',
        'amount',
    ];

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }
}

