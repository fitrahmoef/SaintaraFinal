<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Team;
use App\Models\TestResult;
// use App\Models\Token; // DEPRECATED: Use TokenPurchase instead
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Get current month statistics
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Total tests this month (FIXED: changed test_date to tanggal_tes)
        $totalTestsThisMonth = TestResult::whereMonth('tanggal_tes', $currentMonth)
            ->whereYear('tanggal_tes', $currentYear)
            ->count();

        // Active agents (users with user_type 'personal' who have made token purchases)
        // FIXED: Changed from 'tokens' to 'customer.tokenPurchases'
        $activeAgents = User::where('user_type', 'personal')
            ->whereHas('customer.tokenPurchases')
            ->count();

        // Count agendas by type
        $talkshowCount = Agenda::where('type', 'talkshow')
            ->where('status', '!=', 'cancelled')
            ->count();

        $webinarCount = Agenda::where('type', 'webinar')
            ->where('status', '!=', 'cancelled')
            ->count();

        // Test distribution (Personal, Institution, Gift)
        // NOTE: test_type field needs to be added to test_results table via migration
        // For now, return 0 to prevent SQL errors
        $personalTests = 0; // TODO: Re-enable after migration: TestResult::where('test_type', 'personal')->count();
        $institutionTests = 0; // TODO: Re-enable after migration: TestResult::where('test_type', 'instansi')->count();
        $giftTests = 0; // TODO: Re-enable after migration: TestResult::where('test_type', 'gift')->count();

        // Token sales this month (FIXED: Use TokenPurchase via Transaction instead of old Token model)
        $tokenSalesThisMonth = \App\Models\Transaction::whereMonth('waktu_dibuat', $currentMonth)
            ->whereYear('waktu_dibuat', $currentYear)
            ->where('status_pembayaran', 'dibayar')
            ->sum('jumlah_bayar');

        // Team daily reports (last 3 team members who submitted reports)
        $teamReports = Team::where('status', 'active')
            ->latest()
            ->take(3)
            ->get();

        // Recent approval requests (using transactions for demo)
        $approvalRequests = Transaction::where('status', 'pending')
            ->latest()
            ->take(3)
            ->with(['user', 'team'])
            ->get();

        return Inertia::render('Admin/dashboard-admin', [
            'stats' => [
                'total_tests_this_month' => $totalTestsThisMonth,
                'active_agents' => $activeAgents,
                'talkshow_count' => $talkshowCount,
                'webinar_count' => $webinarCount,
            ],
            'testDistribution' => [
                'personal' => $personalTests,
                'institution' => $institutionTests,
                'gift' => $giftTests,
            ],
            'tokenSalesThisMonth' => number_format($tokenSalesThisMonth, 0, ',', '.'),
            'teamReports' => $teamReports,
            'approvalRequests' => $approvalRequests,
        ]);
    }
}
