<?php

namespace App\Http\Controllers;

use App\Models\WwphJob;

class AdminJobController extends Controller
{
    public function index()
    {
        $jobs = WwphJob::latest()->get();

        return response()->json([
            'status' => 'success',
            'count' => $jobs->count(),
            'data' => $jobs,
        ]);
    }
}
