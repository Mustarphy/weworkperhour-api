<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WwphJob extends Model
{
    use HasFactory;

    protected $table = 'wwph_jobs';

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'requirements',
        'work_type',
        'job_type',
        'category',
        'salary',
        'budget',
        'experience',
        'job_cover',
        'skills',
        'city',
        'state',
        'country',
        'naration',
        'job_role',
        'location', 
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(User::class, 'company_id', 'id');
    }

    public function worktype()
    {
        return $this->belongsTo(WorkType::class, 'work_type');
    }

    public function jobtype()
    {
        return $this->belongsTo(JobType::class, 'job_type');
    }

    public function departments()
    {
        return $this->hasMany(JobDepartment::class, 'wwph_job_id')
            ->with('department');
    }

    public function applicants()
    {
        return $this->hasMany(JobApplication::class, 'job_id');
    }
}
