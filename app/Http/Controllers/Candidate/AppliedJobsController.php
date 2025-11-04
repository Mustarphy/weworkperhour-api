<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class AppliedJobsController extends Controller
{
    /**
     * Fetch all jobs the authenticated candidate applied to
     */
    public function index()
    {
        $userId = auth()->id(); // current candidate user id

        $applications = JobApplication::with([
                'job' => function ($query) {
                    $query->select('id', 'title', 'slug', 'company_id', 'location', 'salary', 'job_type', 'work_type');
                },
                'job.company' => function ($query) {
                    $query->select('id', 'name', 'avatar');
                }
            ])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $applications->map(function ($app) {
            return [
                'id' => $app->id,
                'title' => $app->job->title ?? 'Untitled Job',
                'slug' => $app->job->slug ?? '',
                'company' => $app->job->company ?? null,
                'location' => $app->job->location ?? null,
                'salary' => $app->job->salary ?? null,
                'job_type' => $app->job->job_type ?? null,
                'work_type' => $app->job->work_type ?? null,
                'status' => $app->status ?? 'pending',
                'date_applied' => $app->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Fetched applied jobs successfully.',
            'data' => $data,
        ]);
    }
}
