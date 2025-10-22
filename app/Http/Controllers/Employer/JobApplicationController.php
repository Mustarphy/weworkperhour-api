<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\WwphJob;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    // Get all job applications for the employer’s jobs
    public function index()
    {
        $employerId = auth()->user()->id;

        $applications = JobApplication::with(['job', 'user'])
            ->whereHas('job', function ($query) use ($employerId) {
                $query->where('company_id', $employerId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Fetched job applications.',
            'data' => $applications,
        ]);
    }

    // Optionally: View one applicant
    public function show($id)
    {
        $employerId = auth()->user()->id;

        $application = JobApplication::with(['job', 'user'])
            ->where('id', $id)
            ->whereHas('job', function ($query) use ($employerId) {
                $query->where('company_id', $employerId);
            })
            ->first();

        if (!$application) {
            return response()->json([
                'status' => 'error',
                'message' => 'Application not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $application,
        ]);
    }
}
