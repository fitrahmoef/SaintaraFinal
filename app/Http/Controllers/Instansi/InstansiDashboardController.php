<?php

namespace App\Http\Controllers\Instansi;

use App\Http\Controllers\Controller;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InstansiDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Get institution name from user's latest test result
        $institutionName = $user->testResults()
            ->where('test_type', 'instansi')
            ->whereNotNull('institution_name')
            ->latest()
            ->value('institution_name');

        // Get all test results for this institution
        $testResults = TestResult::where('test_type', 'instansi')
            ->where('user_id', $user->id)
            ->with('characterType')
            ->latest()
            ->paginate(10);

        // Statistics
        $totalTests = TestResult::where('test_type', 'instansi')
            ->where('user_id', $user->id)
            ->count();

        // Get character distribution for institution tests
        $characterDistribution = TestResult::where('test_type', 'instansi')
            ->where('user_id', $user->id)
            ->with('characterType')
            ->get()
            ->groupBy('character_type_id')
            ->map(function ($group) {
                return [
                    'character_name' => $group->first()->characterType->name,
                    'count' => $group->count(),
                ];
            })
            ->values();

        return Inertia::render('Instansi/dashboard', [
            'institutionName' => $institutionName ?? 'Institution',
            'totalTests' => $totalTests,
            'testResults' => $testResults,
            'characterDistribution' => $characterDistribution,
        ]);
    }
}
