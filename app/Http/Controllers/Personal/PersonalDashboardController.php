<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PersonalDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Get latest test result with character type
        $latestResult = $user->latestTestResult();

        // Get user's token balance
        $tokens = $user->tokens()
            ->where('payment_status', 'paid')
            ->get();

        $totalTokens = $tokens->sum('token_amount');
        $usedTokens = $tokens->sum('tokens_used');
        $availableTokens = $totalTokens - $usedTokens;

        return Inertia::render('Personal/dashboard-personal', [
            'latestResult' => $latestResult ? [
                'id' => $latestResult->id,
                'character_type_name' => $latestResult->characterType->name,
                'character_type_code' => $latestResult->characterType->code,
                'description' => $latestResult->characterType->description,
                'strengths' => $latestResult->characterType->strengths,
                'challenges' => $latestResult->characterType->challenges,
                'communication_style' => $latestResult->characterType->communication_style,
            ] : null,
            'tokenBalance' => [
                'total' => $totalTokens,
                'used' => $usedTokens,
                'available' => $availableTokens,
            ],
        ]);
    }
}
