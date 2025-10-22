<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function job()
{
    return $this->belongsTo(\App\Models\WwphJob::class, 'job_id');
}

public function user()
{
    return $this->belongsTo(\App\Models\User::class, 'user_id');
}


}
