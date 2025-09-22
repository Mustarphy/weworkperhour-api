<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartGuide extends Model
{
    protected $fillable = [
        'user_id',
        'guide_id',
        'selections',
        'version',
    ];

    protected $casts = [
        'selections' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
