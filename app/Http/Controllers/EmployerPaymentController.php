<?php

namespace App\Http\Controllers;

use App\Models\WalletToken;
use App\Models\EmployerPayment;
use App\Models\Milestone;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EmployerPaymentController extends Controller
{
    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    /**
     * Validate candidate wallet token
     */
    public function validateToken(Request $request)
    {
        $request->validate([
            'wallet_token' => 'required|string|max:255',
        ]);

        $walletToken = WalletToken::where('wallet_token', $request->wallet_token)
            ->first();

        if (!$walletToken) {
            return response()->json([
                'status' => 'error',
                'error' => 'Invalid wallet token',
            ], 404);
        }

        $candidate = $walletToken->user;

        return response()->json([
            'status' => 'success',
            'user_id' => $walletToken->user_id,
            'candidate_name' => $candidate->first_name . ' ' . $candidate->last_name,
            'email' => $candidate->email,
        ]);
    }

    /**
     * Create escrow payment
     */
    public function fundWallet(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100|max:999999999',
            'wallet_token' => 'required|string|max:255',
            'user_id' => 'required|integer|exists:users,id',
            'type' => 'required|in:escrow,milestone',
        ]);

        $employer = auth()->user();
        $reference = 'ESCROW_' . strtoupper(Str::random(12));

        // Create payment record
        $payment = EmployerPayment::create([
            'employer_id' => $employer->id,
            'candidate_id' => $request->user_id,
            'amount' => $request->amount,
            'type' => $request->type,
            'status' => 'pending',
            'reference' => $reference,
            'wallet_token' => $request->wallet_token,
            'payment_method' => 'paystack',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'reference' => $reference,
                'payment_id' => $payment->id,
            ],
        ]);
    }

    /**
     * Create milestone-based payment
     */
    public function createMilestones(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100|max:999999999',
            'user_id' => 'required|integer|exists:users,id',
            'wallet_token' => 'required|string|max:255',
            'steps' => 'required|array|min:1|max:10',
            'steps.*.title' => 'required|string|max:255',
            'steps.*.percent' => 'required|numeric|min:1|max:100',
        ]);

        // Validate total percentage
        $totalPercent = collect($request->steps)->sum('percent');
        if ($totalPercent != 100) {
            return response()->json([
                'status' => 'error',
                'error' => 'Milestones must total exactly 100%',
            ], 422);
        }

        $employer = auth()->user();
        $reference = 'MILESTONE_' . strtoupper(Str::random(12));

        // Create parent payment record
        $payment = EmployerPayment::create([
            'employer_id' => $employer->id,
            'candidate_id' => $request->user_id,
            'amount' => $request->amount,
            'type' => 'milestone',
            'status' => 'pending',
            'reference' => $reference,
            'wallet_token' => $request->wallet_token,
            'payment_method' => 'paystack',
        ]);

        // Create milestone records
        foreach ($request->steps as $step) {
            Milestone::create([
                'payment_id' => $payment->id,
                'title' => $step['title'],
                'percentage' => $step['percent'],
                'amount' => ($request->amount * $step['percent']) / 100,
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'reference' => $reference,
                'payment_id' => $payment->id,
            ],
        ]);
    }

    /**
     * Verify payment after Paystack callback
     */
    public function verifyPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'reference' => 'required|string|max:255',
                'payment_id' => 'required|integer',
            ]);

            Log::info('Verify payment request:', $validated);

            $payment = EmployerPayment::find($validated['payment_id']);

            if (!$payment) {
                Log::error('Payment not found', ['payment_id' => $validated['payment_id']]);
                return response()->json([
                    'status' => 'error',
                    'error' => 'Payment record not found',
                ], 404);
            }

            Log::info('Found payment:', [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'reference' => $payment->reference,
            ]);

            // Verify with Paystack
            $verification = $this->paystackService->verifyTransaction($validated['reference']);

            Log::info('Paystack verification response:', $verification);

            if (!$verification['success']) {
                Log::error('Paystack verification failed', ['error' => $verification['error']]);
                return response()->json([
                    'status' => 'error',
                    'error' => $verification['error'],
                ], 400);
            }

            // Verify amount matches (convert from kobo to NGN)
            $paystackAmount = $verification['data']['amount'] / 100;
            $paymentAmount = (int)$payment->amount;
            $paystackAmountInt = (int)$paystackAmount;

            Log::info('Amount verification:', [
                'paystack_amount' => $paystackAmount,
                'paystack_amount_int' => $paystackAmountInt,
                'payment_amount' => $paymentAmount,
            ]);

            if ($paystackAmountInt !== $paymentAmount) {
                Log::error('Amount mismatch', [
                    'paystack' => $paystackAmountInt,
                    'payment' => $paymentAmount,
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'error' => 'Amount mismatch in payment verification',
                ], 400);
            }

            // Update payment status
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            Log::info('Payment verified and updated successfully', [
                'payment_id' => $payment->id,
                'reference' => $validated['reference'],
                'amount' => $payment->amount,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment verified and confirmed',
                'data' => [
                    'payment_id' => $payment->id,
                    'amount' => $payment->amount,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in verifyPayment', $e->errors());
            return response()->json([
                'status' => 'error',
                'error' => 'Validation failed',
                'details' => $e->errors(),
            ], 400);
        } catch (\Exception $e) {
            Log::error('Error in verifyPayment', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'status' => 'error',
                'error' => 'Error verifying payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get employer payments
     */
    public function getPayments()
    {
        $employer = auth()->user();
        $payments = EmployerPayment::where('employer_id', $employer->id)
            ->with('candidate', 'milestones')
            ->latest()
            ->paginate(15);

        return response()->json($payments);
    }
}
?>