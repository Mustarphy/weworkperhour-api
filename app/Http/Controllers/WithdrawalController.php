<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    /**
     * Get candidate's withdrawal history
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $withdrawals = Withdrawal::where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return response()->json($withdrawals);
    }

    /**
     * Submit withdrawal request
     * ONLY allows withdrawal from employer-approved work (escrow) or approved milestones
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:500|max:10000000',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|min:10|max:10',
            'account_name' => 'required|string|max:255',
        ]);

        $user = auth()->user();

        DB::beginTransaction();

        try {
            // Get wallet
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

            if (!$wallet) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'error' => 'Wallet not found. Please contact support.',
                ], 404);
            }

            // SECURITY CHECK 1: Check total wallet balance
            if ($wallet->balance < $request->amount) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'error' => 'Insufficient wallet balance.',
                    'data' => [
                        'requested' => (float)$request->amount,
                        'available' => (float)$wallet->balance,
                    ],
                ], 400);
            }

            // SECURITY CHECK 2: Calculate approved balance
            // Include both escrow payments and individual milestone approvals
            
            // 1. Escrow payments that are completed and work approved
            $approvedEscrowPayments = \App\Models\EmployerPayment::where('candidate_id', $user->id)
                ->where('status', 'completed')
                ->where('type', 'escrow')
                ->where('work_status', 'approved')
                ->get();

            $escrowBalance = $approvedEscrowPayments->sum('amount');

            // 2. Individual milestone approvals
            $approvedMilestones = \App\Models\Milestone::whereHas('payment', function($query) use ($user) {
                    $query->where('candidate_id', $user->id)
                          ->where('status', 'completed'); // Payment must be completed by admin
                })
                ->where('work_status', 'approved') // Milestone must be approved by employer
                ->get();

            $milestoneBalance = $approvedMilestones->sum('amount');

            // Total approved balance
            $approvedBalance = $escrowBalance + $milestoneBalance;

            // Calculate how much has already been withdrawn from approved balance
            $withdrawnFromApproved = Withdrawal::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'approved'])
                ->sum('amount');

            // Available for withdrawal = approved balance - already withdrawn
            $availableForWithdrawal = $approvedBalance - $withdrawnFromApproved;

            Log::info('Withdrawal validation', [
                'user_id' => $user->id,
                'wallet_balance' => $wallet->balance,
                'escrow_approved' => $escrowBalance,
                'milestones_approved' => $milestoneBalance,
                'total_approved_balance' => $approvedBalance,
                'withdrawn_amount' => $withdrawnFromApproved,
                'available_for_withdrawal' => $availableForWithdrawal,
                'requested_amount' => $request->amount,
            ]);

            if ($availableForWithdrawal < $request->amount) {
                DB::rollBack();

                // Count pending items
                $pendingEscrow = \App\Models\EmployerPayment::where('candidate_id', $user->id)
                    ->where('status', 'completed')
                    ->where('type', 'escrow')
                    ->where('work_status', 'pending')
                    ->count();

                $pendingMilestones = \App\Models\Milestone::whereHas('payment', function($query) use ($user) {
                        $query->where('candidate_id', $user->id)
                              ->where('status', 'completed');
                    })
                    ->where('work_status', 'pending')
                    ->count();

                $totalPending = $pendingEscrow + $pendingMilestones;

                return response()->json([
                    'status' => 'error',
                    'error' => 'You can only withdraw from employer-approved work.',
                    'data' => [
                        'requested_amount' => (float)$request->amount,
                        'available_for_withdrawal' => (float)$availableForWithdrawal,
                        'escrow_approved' => (float)$escrowBalance,
                        'milestones_approved' => (float)$milestoneBalance,
                        'total_approved' => (float)$approvedBalance,
                        'already_withdrawn' => (float)$withdrawnFromApproved,
                        'pending_approvals' => $totalPending,
                        'message' => $totalPending > 0 
                            ? "You have {$totalPending} item(s) pending employer approval."
                            : 'No approved payments or milestones available for withdrawal.',
                    ],
                ], 400);
            }

            // All checks passed - create withdrawal request
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'amount' => $request->amount,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'status' => 'pending',
                'reference' => 'WD_' . strtoupper(Str::random(12)),
            ]);

            // Deduct from wallet (reserve the funds)
            $wallet->decrement('balance', $request->amount);

            // Log transaction for audit trail
            if (class_exists(\App\Models\WalletTransaction::class)) {
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'debit',
                    'amount' => $request->amount,
                    'balance_after' => $wallet->fresh()->balance,
                    'source' => 'withdrawal',
                    'source_id' => $withdrawal->id,
                    'description' => 'Withdrawal request: ' . $withdrawal->reference,
                ]);
            }

            DB::commit();

            Log::info('Withdrawal request created successfully', [
                'withdrawal_id' => $withdrawal->id,
                'user_id' => $user->id,
                'amount' => $request->amount,
                'reference' => $withdrawal->reference,
                'new_wallet_balance' => $wallet->fresh()->balance,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Withdrawal request submitted successfully. Pending admin approval.',
                'data' => [
                    'reference' => $withdrawal->reference,
                    'amount' => (float)$withdrawal->amount,
                    'new_balance' => (float)$wallet->fresh()->balance,
                    'bank_name' => $withdrawal->bank_name,
                    'account_number' => $withdrawal->account_number,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating withdrawal', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'error' => 'Failed to process withdrawal request. Please try again.',
            ], 500);
        }
    }

    /**
     * Get candidate's available withdrawal balance
     */
    public function getAvailableBalance(Request $request)
    {
        $user = auth()->user();

        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet) {
            return response()->json([
                'wallet_balance' => 0,
                'approved_balance' => 0,
                'available_for_withdrawal' => 0,
            ]);
        }

        // Calculate approved escrow payments
        $approvedEscrowBalance = \App\Models\EmployerPayment::where('candidate_id', $user->id)
            ->where('status', 'completed')
            ->where('type', 'escrow')
            ->where('work_status', 'approved')
            ->sum('amount');

        // Calculate approved milestones
        $approvedMilestoneBalance = \App\Models\Milestone::whereHas('payment', function($query) use ($user) {
                $query->where('candidate_id', $user->id)
                      ->where('status', 'completed');
            })
            ->where('work_status', 'approved')
            ->sum('amount');

        // Total approved
        $approvedBalance = $approvedEscrowBalance + $approvedMilestoneBalance;

        // Calculate already withdrawn/pending withdrawal
        $withdrawnAmount = Withdrawal::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->sum('amount');

        // Available = approved - withdrawn
        $availableForWithdrawal = $approvedBalance - $withdrawnAmount;

        return response()->json([
            'wallet_balance' => (float)$wallet->balance,
            'escrow_approved' => (float)$approvedEscrowBalance,
            'milestones_approved' => (float)$approvedMilestoneBalance,
            'total_approved' => (float)$approvedBalance,
            'withdrawn_or_pending' => (float)$withdrawnAmount,
            'available_for_withdrawal' => (float)$availableForWithdrawal,
        ]);
    }

    /**
     * Cancel pending withdrawal (candidate can cancel before admin processes)
     */
    public function cancel($id)
    {
        $user = auth()->user();

        DB::beginTransaction();

        try {
            $withdrawal = Withdrawal::where('user_id', $user->id)
                ->where('id', $id)
                ->lockForUpdate()
                ->first();

            if (!$withdrawal) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Withdrawal not found',
                ], 404);
            }

            if ($withdrawal->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Can only cancel pending withdrawals',
                ], 400);
            }

            // Return funds to wallet
            $wallet = Wallet::find($withdrawal->wallet_id);
            $wallet->increment('balance', $withdrawal->amount);

            // Update withdrawal status
            $withdrawal->update(['status' => 'rejected', 'admin_note' => 'Cancelled by user']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Withdrawal cancelled. Funds returned to wallet.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'error' => 'Failed to cancel withdrawal',
            ], 500);
        }
    }
}