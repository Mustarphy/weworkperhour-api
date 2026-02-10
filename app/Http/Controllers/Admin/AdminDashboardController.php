<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WwphJob;
use App\Models\JobApplication; // ✅ FIXED
use App\Models\Payment;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalJobs = WwphJob::count();
        $totalApplications = JobApplication::count(); // ✅ FIXED
        $totalEmployers = User::where('role', '1')->count(); // ✅ FIXED
        $totalCandidates = User::where('role', '2')->count(); // ✅ FIXED

        $thisMonthRevenue = Payment::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        $lastMonthRevenue = Payment::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('amount');

        $recentPayments = Payment::with('user')->latest()->take(5)->get();

        return response()->json([
            "overview" => [
                "jobs" => $totalJobs,
                "applications" => $totalApplications,
                "employers" => $totalEmployers,
                "candidates" => $totalCandidates
            ],
            "revenue" => [
                "this_month" => $thisMonthRevenue,
                "last_month" => $lastMonthRevenue
            ],
            "recent_payments" => $recentPayments
        ]);
    }
}