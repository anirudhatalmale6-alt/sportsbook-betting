<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        $user->refresh();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful. Please verify your email.',
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (!Auth::attempt([$loginField => $request->login, 'password' => $request->password])) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $user = Auth::user();

        if ($user->status === 'suspended') {
            Auth::logout();
            return response()->json(['message' => 'Your account has been suspended.'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        $token = $request->user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}
