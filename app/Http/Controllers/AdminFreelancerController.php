<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminFreelancerController extends Controller
{
    public function index()
    {
        $freelancers = User::where('role', 1)
            ->select('id', 'name', 'title', 'bio', 'avatar')
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'title' => $u->title,
                    'bio' => $u->bio,
                    'avatar' => $u->avatar
                        ? url($u->avatar)   // 👈 THIS IS THE KEY
                        : null,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $freelancers,
        ]);
    }
}
