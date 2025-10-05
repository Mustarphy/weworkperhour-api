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

            \Log::info('=== CV Generation Debug ===');
            \Log::info('experienceTitle: ' . ($data['experienceTitle'] ?? 'NULL'));
            \Log::info('All keys: ' . implode(', ', array_keys($data)));

            // Handle photo upload and convert to base64
            if ($request->hasFile('photo')) {
                $photoFile = $request->file('photo');
                
                // Get the image content and convert to base64
                $imageData = base64_encode(file_get_contents($photoFile->getRealPath()));
                $mimeType = $photoFile->getMimeType();
                
                // Create base64 data URL
                $data['photo'] = 'data:' . $mimeType . ';base64,' . $imageData;
                
                \Log::info('Photo processed: ' . $mimeType);
            }

            $pdf = Pdf::loadView('cv.template', $data)
                      ->setPaper('A4', 'portrait');

            return $pdf->download('SmartCV.pdf');
        } catch (\Exception $e) {
            \Log::error('CV Generation Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}