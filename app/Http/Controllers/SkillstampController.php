<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSkillstamp;

class SkillstampController extends Controller
{
    public function award(Request $request)
    {
        // Validate request
        $request->validate([
            'course_name' => 'required|string',
            'score' => 'required|integer',
            'total_questions' => 'required|integer', // Add total questions
        ]);

        $user = auth()->user();

        // Calculate dynamic pass mark (70%)
        $passMark = ceil(0.7 * $request->total_questions);

        if ($request->score < $passMark) {
            return response()->json([
                'passed' => false,
                'skillstamp_issued' => false
            ]);
        }

        // Prevent duplicates
        $existing = UserSkillstamp::where('user_id', $user->id)
            ->where('course_name', $request->course_name)
            ->first();

        if (!$existing) {
            UserSkillstamp::create([
                'user_id' => $user->id,
                'course_name' => $request->course_name,
                'score' => $request->score,
                'earned_at' => now()
            ]);
        }

        return response()->json([
            'passed' => true,
            'skillstamp_issued' => true,
            'skillstamp' => [
                'course_name' => $request->course_name,
                'score' => $request->score,
                'earned_at' => now()
            ]
        ]);
    }
}
