<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Team;
use App\Models\TestResult;
use App\Models\Token;
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

        // Total tests this month
        $totalTestsThisMonth = TestResult::whereMonth('test_date', $currentMonth)
            ->whereYear('test_date', $currentYear)
            ->count();

        // Active agents (users with user_type 'personal' who have made purchases)
        $activeAgents = User::where('user_type', 'personal')
            ->has('tokens')
            ->count();

        // Count agendas by type
        $talkshowCount = Agenda::where('type', 'talkshow')
            ->where('status', '!=', 'cancelled')
            ->count();

        $webinarCount = Agenda::where('type', 'webinar')
            ->where('status', '!=', 'cancelled')
            ->count();

        // Test distribution (Personal, Institution, Gift)
        $personalTests = TestResult::where('test_type', 'personal')->count();
        $institutionTests = TestResult::where('test_type', 'instansi')->count();
        $giftTests = TestResult::where('test_type', 'gift')->count();

        // Token sales this month
        $tokenSalesThisMonth = Token::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('payment_status', 'paid')
            ->sum('price');

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
