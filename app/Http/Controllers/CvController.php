<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CvController extends Controller
{
    public function generate(Request $request)
{
    try {
        $data = $request->all();

        // Log checks
        \Log::info('=== CV Generation Debug ===');
        \Log::info('experienceTitle: ' . ($data['experienceTitle'] ?? 'NULL'));
        \Log::info('All keys: ' . implode(', ', array_keys($data)));

        $user = auth()->user();
        $user->load('skillstamps');

        // Handle photo upload → convert to base64 for PDF
        if ($request->hasFile('photo')) {
            $photoFile = $request->file('photo');
            $imageData = base64_encode(file_get_contents($photoFile->getRealPath()));
            $mimeType = $photoFile->getMimeType();
            $data['photo'] = 'data:' . $mimeType . ';base64,' . $imageData;

            \Log::info('Photo processed: ' . $mimeType);
        }

        // pass user object to the view so skillstamps are available
        $data['user'] = $user;

        // convert skillstamp badge image to base64 for PDF embedding
        $badgePath = public_path('images/skillstamp-badge.png');
        if (file_exists($badgePath)) {
            $badgeData = base64_encode(file_get_contents($badgePath));
            $data['skillstampBadge'] = 'data:image/png;base64,' . $badgeData;
            \Log::info('Skillstamp badge embedded');
        } else {
            \Log::warning('Skillstamp badge image not found at:' . $badgePath);
        }

        // Set execution time for PDF generation
        set_time_limit(120);

        // Generate PDF with optimized options
        $options = [
            'isHTML5ParserEnabled' => true,
            'isRemoteEnabled' => false,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('cv.template', $data)
                ->setOptions($options)
                  ->setPaper('A4', 'portrait');

        // Build filename & path
        $userId = auth()->id();
        $fileName = 'smartcv_' . $userId . '_' . time() . '.pdf';
        $storagePath = 'public/smartcv/' . $fileName;

        // Ensure directory exists
        if (!\Storage::exists('public/smartcv')) {
            \Storage::makeDirectory('public/smartcv');
        }

        // Save PDF to storage
        \Storage::put($storagePath, $pdf->output());

        // Create public URL
        $publicUrl = asset('storage/smartcv/' . $fileName);

        //store directly in user profile
        $user = auth()->user();
        $user->smartcv = 'storage/smartcv/' . $fileName;
        $user->save();

        return response()->json([
            'status' => 'success',
            'cv_url' => asset('storage/smartcv/' . $fileName)
        ]);

    } catch (\Exception $e) {
        \Log::error('CV Generation Error: ' . $e->getMessage());
        \Log::error('Stack Trace: ' . $e->getTraceAsString());
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}

}