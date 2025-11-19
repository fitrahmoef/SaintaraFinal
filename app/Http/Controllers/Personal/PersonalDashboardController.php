<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Services\FreeTokenService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PersonalDashboardController extends Controller
{
    protected FreeTokenService $freeTokenService;

    public function __construct(FreeTokenService $freeTokenService)
    {
        $this->freeTokenService = $freeTokenService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $customer = $user->customer;

        // Get latest test result with character type
        $latestResult = $user->latestTestResult();

        // Get token balance including free tokens
        if ($customer) {
            $tokenBalance = $this->freeTokenService->getTotalTokenBalance($customer);
        } else {
            $tokenBalance = [
                'free_tokens' => 0,
                'purchased_total' => 0,
                'purchased_used' => 0,
                'purchased_available' => 0,
                'total_available' => 0,
                'total_used' => 0,
            ];
        }

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
                'total' => $tokenBalance['purchased_total'] + $tokenBalance['free_tokens'],
                'used' => $tokenBalance['purchased_used'],
                'available' => $tokenBalance['total_available'],
                'free_tokens' => $tokenBalance['free_tokens'],
                'purchased_available' => $tokenBalance['purchased_available'],
            ],
        ]);
    }
}
