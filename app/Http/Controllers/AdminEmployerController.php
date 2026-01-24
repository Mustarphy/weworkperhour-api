<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminEmployerController extends Controller
{
    public function index()
    {
        // Fetch only users with role = 2 (employer) and select safe fields
        $employers = User::where('role', 2)
            ->select('id', 'name', 'email', 'avatar')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $employers ?: [],
        ]);
    }
}
