<?php

namespace App\Http\Controllers;

use App\Models\SmartGuideContent;
use App\Models\UserSmartGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SmartGuideController extends Controller
{
    /**
     * Get the current user's SmartGuide selections
     */
    public function show(Request $request)
    {
        $userGuide = UserSmartGuide::where('user_id', $request->user()->id)->first();

        if (!$userGuide) {
            return response()->json([
                'status' => 'empty',
                'data' => null,
            ], 204);
        }

        return response()->json([
            'status' => 'success',
            'selections' => $userGuide->selections,
            'guideId' => $userGuide->guide_id,
            'completedModules' => $userGuide->completed_modules ?? [],
        ], 200);
    }

    /**
     * Create or update the user's SmartGuide
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'guideId' => ['required', 'string', 'max:120', 'regex:/^[a-z0-9-]+$/'],
            'selections' => ['required', 'array'],
            'selections.role' => ['required', 'string'],
            'selections.experience' => ['required', 'string'],
            'selections.goal' => ['nullable', 'string'],
            'selections.market' => ['nullable', 'string'],
            'selections.workStyle' => ['nullable', 'string'],
            'version' => ['nullable', 'string'],
        ]);

        // Check if guideId exists in smart_guides_content
        $exists = DB::table('smart_guides_content')
            ->where('guide_id', $payload['guideId'])
            ->exists();

        if (!$exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid guideId provided',
            ], 422);
        }

        $user = $request->user();

        DB::beginTransaction();
        try {
            // ✅ Use the correct model: UserSmartGuide
            $sg = UserSmartGuide::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'guide_id' => $payload['guideId'],
                    'selections' => $payload['selections'],
                    'version' => $payload['version'] ?? null,
                ]
            );

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $sg,
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('SmartGuide save failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save SmartGuide',
            ], 500);
        }
    }

    /**
     * Get full guide content for a guideId
     */
    public function showGuideContent($guideId)
    {
        $guide = SmartGuideContent::where('guide_id', $guideId)->first();

        if (!$guide) {
            return response()->json([
                'status' => 'error',
                'message' => 'Guide not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'guide' => $guide,
        ], 200);
    }

    /**
     * Update user's completed modules
     */
    public function updateProgress(Request $request, $guideId)
    {
        $payload = $request->validate([
            'completedModules' => ['required', 'array'],
        ]);

        $userGuide = UserSmartGuide::where('user_id', $request->user()->id)
            ->where('guide_id', $guideId)
            ->first();

        if (!$userGuide) {
            return response()->json([
                'status' => 'error',
                'message' => 'User guide not found',
            ], 404);
        }

        $userGuide->completed_modules = $payload['completedModules'];
        $userGuide->save();

        return response()->json([
            'status' => 'success',
            'completedModules' => $userGuide->completed_modules,
        ], 200);
    }
}
