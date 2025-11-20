<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TestResult;
use App\Models\TokenUsage;
use App\Models\TokenPurchase;
use App\Models\Transaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserMonitoringController extends Controller
{
    /**
     * Get detailed user information with all activity
     */
    public function show($id)
    {
        $user = User::with([
            'customer',
            'adminInstansi',
            'parentInstitution',
            'testResults.test',
            'testResults.characterType',
            'transactions',
            'activityLogs'
        ])->findOrFail($id);

        // Get user statistics
        $stats = $this->getUserStatistics($user);

        // Get recent activities
        $recentTests = $user->testResults()
            ->with(['test', 'characterType'])
            ->latest()
            ->limit(10)
            ->get();

        $recentTransactions = $user->transactions()
            ->latest()
            ->limit(10)
            ->get();

        $recentActivities = $user->activityLogs()
            ->latest()
            ->limit(20)
            ->get();

        // Get token usage history
        $tokenPurchases = TokenPurchase::where('user_id', $user->id)
            ->orWhere(function ($query) use ($user) {
                if ($user->customer) {
                    $query->where('customer_id', $user->customer->id);
                }
            })
            ->with('package')
            ->latest()
            ->limit(10)
            ->get();

        $tokenUsage = TokenUsage::where('user_id', $user->id)
            ->with('test')
            ->latest()
            ->limit(10)
            ->get();

        // Get monthly activity trends
        $monthlyTests = $this->getMonthlyTestTrend($user->id);
        $monthlyTokenUsage = $this->getMonthlyTokenUsageTrend($user->id);

        return response()->json([
            'user' => $user,
            'statistics' => $stats,
            'recent_tests' => $recentTests,
            'recent_transactions' => $recentTransactions,
            'recent_activities' => $recentActivities,
            'token_purchases' => $tokenPurchases,
            'token_usage' => $tokenUsage,
            'monthly_test_trend' => $monthlyTests,
            'monthly_token_usage_trend' => $monthlyTokenUsage,
        ]);
    }

    /**
     * Get user statistics
     */
    private function getUserStatistics(User $user)
    {
        $totalTests = $user->testResults()->count();
        $completedTests = $user->testResults()
            ->whereNotNull('character_type_id')
            ->count();

        $totalTokensPurchased = TokenPurchase::where('user_id', $user->id)
            ->where('payment_status', 'paid')
            ->sum('jumlah_token');

        $totalTokensUsed = TokenUsage::where('user_id', $user->id)
            ->sum('jumlah_token');

        $currentTokenBalance = $user->customer->saldo_token ?? 0;

        $totalSpent = $user->transactions()
            ->where('status', 'paid')
            ->sum('amount');

        $lastLoginAt = $user->activityLogs()
            ->where('action', 'login')
            ->latest()
            ->value('created_at');

        $lastTestAt = $user->testResults()
            ->latest()
            ->value('created_at');

        // Get most common character type
        $mostCommonCharacter = $user->testResults()
            ->whereNotNull('character_type_id')
            ->selectRaw('character_type_id, COUNT(*) as count')
            ->groupBy('character_type_id')
            ->orderByDesc('count')
            ->with('characterType')
            ->first();

        return [
            'total_tests' => $totalTests,
            'completed_tests' => $completedTests,
            'incomplete_tests' => $totalTests - $completedTests,
            'total_tokens_purchased' => $totalTokensPurchased,
            'total_tokens_used' => $totalTokensUsed,
            'current_token_balance' => $currentTokenBalance,
            'total_spent' => $totalSpent,
            'total_transactions' => $user->transactions()->count(),
            'last_login_at' => $lastLoginAt,
            'last_test_at' => $lastTestAt,
            'most_common_character' => $mostCommonCharacter,
            'account_age_days' => $user->created_at->diffInDays(now()),
        ];
    }

    /**
     * Get monthly test completion trend
     */
    private function getMonthlyTestTrend($userId)
    {
        return TestResult::where('user_id', $userId)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
    }

    /**
     * Get monthly token usage trend
     */
    private function getMonthlyTokenUsageTrend($userId)
    {
        return TokenUsage::where('user_id', $userId)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(jumlah_token) as total')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
    }

    /**
     * Get user activity timeline
     */
    public function getActivityTimeline($id, Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $type = $request->get('type'); // test, transaction, activity, token

        $user = User::findOrFail($id);

        // Collect all activities
        $activities = collect();

        if (!$type || $type === 'test') {
            $tests = $user->testResults()
                ->with(['test', 'characterType'])
                ->get()
                ->map(function ($test) {
                    return [
                        'type' => 'test',
                        'action' => 'Test Completed',
                        'description' => $test->test->judul ?? 'Unknown Test',
                        'character_type' => $test->characterType->nama_karakter ?? null,
                        'timestamp' => $test->created_at,
                        'data' => $test,
                    ];
                });
            $activities = $activities->concat($tests);
        }

        if (!$type || $type === 'transaction') {
            $transactions = $user->transactions()
                ->get()
                ->map(function ($transaction) {
                    return [
                        'type' => 'transaction',
                        'action' => 'Transaction ' . ucfirst($transaction->status),
                        'description' => 'Amount: Rp ' . number_format($transaction->amount, 0, ',', '.'),
                        'status' => $transaction->status,
                        'timestamp' => $transaction->created_at,
                        'data' => $transaction,
                    ];
                });
            $activities = $activities->concat($transactions);
        }

        if (!$type || $type === 'token') {
            $tokenPurchases = TokenPurchase::where('user_id', $user->id)
                ->get()
                ->map(function ($purchase) {
                    return [
                        'type' => 'token_purchase',
                        'action' => 'Token Purchase',
                        'description' => $purchase->jumlah_token . ' tokens purchased',
                        'status' => $purchase->payment_status,
                        'timestamp' => $purchase->created_at,
                        'data' => $purchase,
                    ];
                });
            $activities = $activities->concat($tokenPurchases);

            $tokenUsages = TokenUsage::where('user_id', $user->id)
                ->with('test')
                ->get()
                ->map(function ($usage) {
                    return [
                        'type' => 'token_usage',
                        'action' => 'Token Used',
                        'description' => $usage->jumlah_token . ' tokens for ' . ($usage->test->judul ?? 'test'),
                        'timestamp' => $usage->created_at,
                        'data' => $usage,
                    ];
                });
            $activities = $activities->concat($tokenUsages);
        }

        if (!$type || $type === 'activity') {
            $activityLogs = $user->activityLogs()
                ->get()
                ->map(function ($log) {
                    return [
                        'type' => 'activity',
                        'action' => ucfirst($log->action),
                        'description' => $log->description ?? '',
                        'timestamp' => $log->created_at,
                        'data' => $log,
                    ];
                });
            $activities = $activities->concat($activityLogs);
        }

        // Sort by timestamp descending
        $activities = $activities->sortByDesc('timestamp')->values();

        // Manual pagination
        $total = $activities->count();
        $page = $request->get('page', 1);
        $activities = $activities->slice(($page - 1) * $perPage, $perPage)->values();

        return response()->json([
            'data' => $activities,
            'current_page' => (int) $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
        ]);
    }

    /**
     * Get user test performance analytics
     */
    public function getTestPerformance($id)
    {
        $user = User::findOrFail($id);

        // Character type distribution
        $characterDistribution = $user->testResults()
            ->whereNotNull('character_type_id')
            ->selectRaw('character_type_id, COUNT(*) as count')
            ->groupBy('character_type_id')
            ->with('characterType')
            ->get();

        // Test completion rate over time
        $completionByMonth = TestResult::where('user_id', $id)
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total,
                        SUM(CASE WHEN character_type_id IS NOT NULL THEN 1 ELSE 0 END) as completed')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Average test duration (if tracked)
        // Most taken tests
        $mostTakenTests = $user->testResults()
            ->selectRaw('test_id, COUNT(*) as count')
            ->groupBy('test_id')
            ->with('test')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return response()->json([
            'character_distribution' => $characterDistribution,
            'completion_by_month' => $completionByMonth,
            'most_taken_tests' => $mostTakenTests,
        ]);
    }

    /**
     * Update user notes (admin can add notes about user)
     */
    public function updateNotes(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // If user has customer profile, update notes there
        if ($user->customer) {
            $user->customer->update([
                'notes' => $request->notes,
            ]);
        }

        // If user is institution, update in admin_instansi
        if ($user->adminInstansi) {
            $user->adminInstansi->update([
                'catatan' => $request->notes,
            ]);
        }

        return response()->json([
            'message' => 'Notes updated successfully',
        ]);
    }

    /**
     * Add tokens to user (admin action)
     */
    public function addTokens(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'reason' => 'nullable|string',
        ]);

        $user = User::findOrFail($id);

        if (!$user->customer) {
            return response()->json([
                'message' => 'User does not have a customer profile',
            ], 400);
        }

        $user->customer->increment('saldo_token', $request->amount);

        // Log this action
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'token_added_by_admin',
            'description' => "Admin added {$request->amount} tokens. Reason: " . ($request->reason ?? 'N/A'),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Tokens added successfully',
            'new_balance' => $user->customer->saldo_token,
        ]);
    }

    /**
     * Deduct tokens from user (admin action)
     */
    public function deductTokens(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'reason' => 'nullable|string',
        ]);

        $user = User::findOrFail($id);

        if (!$user->customer) {
            return response()->json([
                'message' => 'User does not have a customer profile',
            ], 400);
        }

        if ($user->customer->saldo_token < $request->amount) {
            return response()->json([
                'message' => 'Insufficient token balance',
            ], 400);
        }

        $user->customer->decrement('saldo_token', $request->amount);

        // Log this action
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'token_deducted_by_admin',
            'description' => "Admin deducted {$request->amount} tokens. Reason: " . ($request->reason ?? 'N/A'),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Tokens deducted successfully',
            'new_balance' => $user->customer->saldo_token,
        ]);
    }
}
