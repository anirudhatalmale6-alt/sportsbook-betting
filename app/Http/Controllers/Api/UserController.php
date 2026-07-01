<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => new UserResource($user),
        ]);
    }

    public function balance(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'balance' => $user->balance,
            'transactions' => TransactionResource::collection(
                $user->transactions()->orderByDesc('created_at')->limit(20)->get()
            ),
        ]);
    }
}
