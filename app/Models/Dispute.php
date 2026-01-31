<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Dispute extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'priority',
        'status',
        'description',
        'dispute_category',
        'subject',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];
}

