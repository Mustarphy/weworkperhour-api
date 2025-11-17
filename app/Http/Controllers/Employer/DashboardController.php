<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WwphJob;
use App\Models\JobApplication;
use App\Models\ProfileView;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->user();  // logged-in employer
        $employerId = $user->id;

        // 1. Active jobs
        $activeJobs = WwphJob::where('company_id', $employerId)
            ->where('status', 'active')
            ->count();

        // 2. New applicants today
        $newApplicants = JobApplication::whereHas('job', function ($q) use ($employerId) {
                $q->where('company_id', $employerId);
            })
            ->whereDate('created_at', today())
            ->count();

        // 3. Profile views
        $profileViews = ProfileView::where('employer_id', $employerId)->count();

        // 4. Engagement rate
        $engagementRate = $profileViews > 0
            ? round(($newApplicants / $profileViews) * 100, 1)
            : 0;

        // Return only the core stats for now
        return response()->json([
            "active_jobs"      => $activeJobs,
            "new_applicants"   => $newApplicants,
            "profile_views"    => $profileViews,
            "engagement_rate"  => $engagementRate
        ]);
    }
}
