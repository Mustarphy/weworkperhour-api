<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected $secretKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('paystack.secret_key');
        $this->baseUrl = config('paystack.base_url');
    }

    /**
     * Verify payment with Paystack
     */
    public function verifyTransaction($reference)
    {
        try {
            Log::info('Verifying Paystack transaction', ['reference' => $reference]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying() // Disable SSL verification for development
            ->timeout(30)
            ->get("{$this->baseUrl}/transaction/verify/{$reference}");

            Log::info('Paystack API response status', ['status' => $response->status()]);

            if ($response->successful() && $response->json('status') === true) {
                Log::info('Transaction verified successfully', [
                    'reference' => $reference,
                    'amount' => $response->json('data.amount'),
                ]);

                return [
                    'success' => true,
                    'data' => $response->json('data'),
                ];
            }

            $message = $response->json('message', 'Verification failed');
            Log::error('Paystack verification failed', [
                'reference' => $reference,
                'message' => $message,
                'status' => $response->status(),
            ]);

            return [
                'success' => false,
                'error' => $message,
            ];
        } catch (\Exception $e) {
            Log::error('Error verifying payment with Paystack', [
                'reference' => $reference,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            return [
                'success' => false,
                'error' => 'Error verifying payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Initialize transaction
     */
    public function initializeTransaction($email, $amount, $reference)
    {
        try {
            Log::info('Initializing Paystack transaction', [
                'email' => $email,
                'amount' => $amount,
                'reference' => $reference,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying() // Disable SSL verification for development
            ->timeout(30)
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email' => $email,
                'amount' => $amount * 100, // Convert to kobo
                'reference' => $reference,
            ]);

            Log::info('Paystack initialize response', ['status' => $response->status()]);

            if ($response->successful() && $response->json('status') === true) {
                return [
                    'success' => true,
                    'data' => $response->json('data'),
                ];
            }

            $message = $response->json('message', 'Initialization failed');
            Log::error('Paystack initialization failed', [
                'reference' => $reference,
                'message' => $message,
            ]);

            return [
                'success' => false,
                'error' => $message,
            ];
        } catch (\Exception $e) {
            Log::error('Error initializing transaction with Paystack', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Error initializing transaction: ' . $e->getMessage(),
            ];
        }
    }
}
?>