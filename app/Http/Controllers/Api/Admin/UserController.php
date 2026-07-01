<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBalanceRequest;
use App\Http\Resources\UserResource;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->withCount('bets')
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return UserResource::collection($users);
    }

    public function show(User $user): JsonResponse
    {
        $user->loadCount('bets');
        $user->load(['transactions' => fn($q) => $q->orderByDesc('created_at')->limit(20)]);

        return response()->json([
            'user' => new UserResource($user),
            'recent_transactions' => $user->transactions,
        ]);
    }

    public function updateBalance(UpdateBalanceRequest $request, User $user): JsonResponse
    {
        $amount = $request->amount;
        $type = $request->type;

        if ($type === 'withdrawal' && $user->balance < $amount) {
            return response()->json(['message' => 'User has insufficient balance.'], 422);
        }

        DB::transaction(function () use ($user, $amount, $type, $request) {
            if ($type === 'deposit') {
                $user->increment('balance', $amount);
            } else {
                $user->decrement('balance', $amount);
            }

            $user->refresh();

            Transaction::create([
                'user_id' => $user->id,
                'type' => $type,
                'amount' => $type === 'deposit' ? $amount : -$amount,
                'balance_after' => $user->balance,
                'description' => $request->description ?? "Admin {$type}",
                'admin_id' => $request->user()->id,
            ]);
        });

        return response()->json([
            'message' => 'Balance updated successfully.',
            'user' => new UserResource($user->fresh()),
        ]);
    }

    public function suspend(User $user): JsonResponse
    {
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Cannot suspend an admin user.'], 422);
        }

        $newStatus = $user->status === 'suspended' ? 'active' : 'suspended';
        $user->update(['status' => $newStatus]);

        return response()->json([
            'message' => "User {$newStatus} successfully.",
            'user' => new UserResource($user),
        ]);
    }
}
