<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'overview',
        'skills',
        'intro',
        'resume',
        'resume_title',
        'experience',
    ];
    public function resumeFiles() {
        return $this->hasMany(ResumeFile::class);
    }
    public function Educations() {
        return $this->hasMany(Education::class);
    }
    public function Portfolio() {
        return $this->hasMany(Portfolio::class);
    }
}
