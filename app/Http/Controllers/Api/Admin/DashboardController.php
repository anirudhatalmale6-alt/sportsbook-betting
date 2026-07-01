<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\GameMatch;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'stats' => [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'suspended_users' => User::where('status', 'suspended')->count(),
                'total_bets' => Bet::count(),
                'pending_bets' => Bet::where('status', 'pending')->count(),
                'total_stake' => Bet::sum('stake'),
                'total_payouts' => Bet::where('status', 'won')->sum('potential_win'),
                'live_matches' => GameMatch::where('status', 'live')->count(),
                'upcoming_matches' => GameMatch::where('status', 'upcoming')->count(),
                'total_balance_held' => User::sum('balance'),
            ],
        ]);
    }
}
