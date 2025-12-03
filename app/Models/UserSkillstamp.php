<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSkillstamp extends Model {
    protected $fillable = [
        'user_id',
        'course_name',
        'score',
        'earned_at'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}