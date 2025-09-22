<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSmartGuide extends Model
{
    protected $table = 'user_smart_guides';
    protected $fillable = [
        'user_id',
        'guide_id',
        'selections',
        'completed_modules',
    ];

    protected $casts = [
        'selections' => 'array',
        'completed_modules' => 'array',
    ];
}
