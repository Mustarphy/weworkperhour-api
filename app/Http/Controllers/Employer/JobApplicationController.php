<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\WwphJob;
use Illuminate\Http\Request;
use App\Mail\ApplicantApprovedMail;
use App\Mail\ApplicantRejectedMail;
use Illuminate\Support\Facades\Mail;

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

    public function updateStatus(Request $request, $id)
{
    $employerId = auth()->user()->id;

    $application = JobApplication::where('id', $id)
        ->whereHas('job', function ($query) use ($employerId) {
            $query->where('company_id', $employerId);
        })
        ->first();

    if (!$application) {
        return response()->json([
            'status' => 'error',
            'message' => 'Application not found or unauthorized.'
        ], 404);
    }

    $status = $request->input('status');

    if ($status === 'approved') {
        $application->status = 'approved';
        $application->save();

        // Send approval email
        Mail::to($application->user->email)->send(
            new ApplicantApprovedMail($application->user, $application->job)
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Application approved and email sent.',
        ]);
    }

    if ($status === 'rejected') {

        Mail::to($application->user->email)->send(
            new ApplicantRejectedMail($application->user, $application->job)
        );

        $application->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Application rejected, deleted and email sent.',
        ]);
    }

    return response()->json([
        'status' => 'error',
        'message' => 'Invalid status value provided.'
    ], 400);
}

}
