<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminWithdrawalController extends Controller
{
    /**
     * Get all withdrawal requests with pagination and filters
     */
    public function index(Request $request)
    {
        $query = Withdrawal::with('user', 'wallet');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search filter (user name, email, reference)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q2) use ($search) {
                    $q2->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        $withdrawals = $query->latest()->paginate(15);

        return response()->json($withdrawals);
    }

    /**
     * Approve withdrawal request
     * Admin confirms they've transferred the money via Paystack/bank
     */
    public function approve(Request $request)
    {
        $request->validate([
            'withdrawal_id' => 'required|integer|exists:withdrawals,id',
        ]);

        DB::beginTransaction();

        try {
            $withdrawal = Withdrawal::with('user', 'wallet')
                ->lockForUpdate()
                ->findOrFail($request->withdrawal_id);

            // Only pending withdrawals can be approved
            if ($withdrawal->status !== 'pending') {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'error' => 'Only pending withdrawals can be approved. Current status: ' . $withdrawal->status,
                ], 400);
            }

            // Update withdrawal status to approved
            $withdrawal->update([
                'status' => 'approved',
            ]);

            DB::commit();

            Log::info('Withdrawal approved by admin', [
                'withdrawal_id' => $withdrawal->id,
                'user_id' => $withdrawal->user_id,
                'amount' => $withdrawal->amount,
                'bank' => $withdrawal->bank_name,
                'account' => $withdrawal->account_number,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Withdrawal approved. Please transfer ₦' . number_format($withdrawal->amount, 2) . ' to the candidate.',
                'data' => [
                    'withdrawal_id' => $withdrawal->id,
                    'amount' => (float)$withdrawal->amount,
                    'bank_details' => [
                        'bank_name' => $withdrawal->bank_name,
                        'account_number' => $withdrawal->account_number,
                        'account_name' => $withdrawal->account_name,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error approving withdrawal', [
                'withdrawal_id' => $request->withdrawal_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'error' => 'Failed to approve withdrawal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject withdrawal request and return funds to wallet
     */
    public function reject(Request $request)
    {
        $request->validate([
            'withdrawal_id' => 'required|integer|exists:withdrawals,id',
            'reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $withdrawal = Withdrawal::with('user', 'wallet')
                ->lockForUpdate()
                ->findOrFail($request->withdrawal_id);

            // Only pending withdrawals can be rejected
            if ($withdrawal->status !== 'pending') {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'error' => 'Only pending withdrawals can be rejected',
                ], 400);
            }

            // Return funds to wallet
            $wallet = Wallet::lockForUpdate()->find($withdrawal->wallet_id);
            
            if ($wallet) {
                $wallet->increment('balance', (float)$withdrawal->amount);

                // Log transaction if WalletTransaction model exists
                if (class_exists(\App\Models\WalletTransaction::class)) {
                    \App\Models\WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'type' => 'credit',
                        'amount' => $withdrawal->amount,
                        'balance_after' => $wallet->fresh()->balance,
                        'source' => 'withdrawal_rejected',
                        'source_id' => $withdrawal->id,
                        'description' => 'Withdrawal rejected: ' . $request->reason,
                    ]);
                }
            }

            // Update withdrawal status
            $withdrawal->update([
                'status' => 'rejected',
                'admin_note' => $request->reason,
            ]);

            DB::commit();

            Log::info('Withdrawal rejected by admin', [
                'withdrawal_id' => $withdrawal->id,
                'user_id' => $withdrawal->user_id,
                'amount' => $withdrawal->amount,
                'reason' => $request->reason,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Withdrawal rejected. Funds returned to candidate wallet.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error rejecting withdrawal', [
                'withdrawal_id' => $request->withdrawal_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'error' => 'Failed to reject withdrawal: ' . $e->getMessage(),
            ], 500);
        }
    }
}