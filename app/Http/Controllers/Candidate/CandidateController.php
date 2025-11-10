<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\ResumeResource;
use App\Models\User;
use App\Models\Resume;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    public function show($id)
    {
        $user = User::where('id', $id)->where('role', 1)->first();
        if (!$user) {
            return errorResponse('Candidate not found', [], 404);
        }

        $resume = Resume::where('user_id', $id)->first();

        $data = [
            'user' => new UserResource($user),
            'resume' => $resume ? new ResumeResource($resume) : null,
        ];

        return okResponse('Candidate profile fetched successfully', $data);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        if ($user->role != 1) {
            return errorResponse('Unauthorized', [], 403);
        }

        $request->validate([
            'qualification' => 'nullable|string',
            'gender' => 'nullable|string',
            'expected_salary' => 'nullable|numeric',
            'experience' => 'nullable|string',
            'overview' => 'nullable|string',
            'skills' => 'nullable|string',
        ]);

        $user->update($request->only(['qualification', 'gender', 'expected_salary']));

        $resume = Resume::where('user_id', $user->id)->first();
        if ($resume) {
            $resume->update($request->only(['experience', 'overview', 'skills']));
        }

        return okResponse('Profile updated successfully');
    }
}
