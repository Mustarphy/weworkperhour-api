<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dispute;

class ReportController extends Controller
{
   public function index()
{
    $disputes = Dispute::latest()->get();

    $data = $disputes->map(function ($dispute) {
        return [
            'full_name' => $dispute->full_name,
            'email' => $dispute->email,
            'priority' => ucfirst($dispute->priority ?? 'low'),
            'status' => $dispute->status ?? 'Pending',
            'description' => $dispute->description,
        ];
    });

    return response()->json([
        'status' => true,
        'data' => $data
    ]);
}


}
