<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceBetRequest;
use App\Http\Resources\BetResource;
use App\Models\Bet;
use App\Models\Odd;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BetController extends Controller
{
    public function store(PlaceBetRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->status === 'suspended') {
            return response()->json(['message' => 'Your account is suspended.'], 403);
        }

        $stake = $request->stake;

        if ($user->balance < $stake) {
            return response()->json(['message' => 'Insufficient balance.'], 422);
        }

        $selections = $request->selections;
        $odds = Odd::whereIn('id', collect($selections)->pluck('odd_id'))
            ->where('is_active', true)
            ->get();

        if ($odds->count() !== count($selections)) {
            return response()->json(['message' => 'One or more selections are invalid or inactive.'], 422);
        }

        // Check all matches are upcoming or live
        $matchStatuses = $odds->load('market.match')->pluck('market.match.status')->unique();
        foreach ($matchStatuses as $status) {
            if (!in_array($status, ['upcoming', 'live'])) {
                return response()->json(['message' => 'One or more matches are no longer available for betting.'], 422);
            }
        }

        // Calculate potential win (multiply all odds for accumulator)
        $totalOdds = $odds->reduce(fn($carry, $odd) => $carry * $odd->value, 1);
        $potentialWin = round($stake * $totalOdds, 2);

        $bet = DB::transaction(function () use ($user, $stake, $potentialWin, $odds, $selections) {
            $bet = Bet::create([
                'user_id' => $user->id,
                'stake' => $stake,
                'potential_win' => $potentialWin,
                'status' => 'pending',
            ]);

            foreach ($selections as $selection) {
                $odd = $odds->firstWhere('id', $selection['odd_id']);
                $bet->items()->create([
                    'odd_id' => $odd->id,
                    'odd_value_at_placement' => $odd->value,
                ]);
            }

            $user->decrement('balance', $stake);
            $user->refresh();

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'bet',
                'amount' => -$stake,
                'balance_after' => $user->balance,
                'description' => "Bet #{$bet->id} placed",
            ]);

            return $bet;
        });

        $bet->load('items.odd.market');

        return response()->json([
            'message' => 'Bet placed successfully.',
            'bet' => new BetResource($bet),
        ], 201);
    }

    public function index(Request $request)
    {
        $bets = $request->user()
            ->bets()
            ->with('items.odd.market')
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return BetResource::collection($bets);
    }

    public function show(Request $request, Bet $bet): JsonResponse
    {
        if ($bet->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $bet->load('items.odd.market');

        return response()->json([
            'bet' => new BetResource($bet),
        ]);
    }
}
