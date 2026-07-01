<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BetController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\OddsController;
use App\Http\Controllers\Api\SportController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\LeagueController as AdminLeagueController;
use App\Http\Controllers\Api\Admin\MatchController as AdminMatchController;
use App\Http\Controllers\Api\Admin\SportController as AdminSportController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/sports', [SportController::class, 'index']);
Route::get('/sports/{sport}', [SportController::class, 'show']);

Route::get('/matches', [MatchController::class, 'index']);
Route::get('/matches/live', [MatchController::class, 'live']);
Route::get('/matches/upcoming', [MatchController::class, 'upcoming']);
Route::get('/matches/featured', [MatchController::class, 'featured']);
Route::get('/matches/{id}', [MatchController::class, 'show']);

// Odds API - public
Route::get('/odds/sports', [OddsController::class, 'getSports']);
Route::get('/odds/events/{eventId}', [OddsController::class, 'getEventOdds']);

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

    // Email verification
    Route::get('/email/verify/{id}/{hash}', function (Request $request) {
        $user = \App\Models\User::findOrFail($request->route('id'));

        if (!hash_equals(sha1($user->getEmailForVerification()), $request->route('hash'))) {
            return response()->json(['message' => 'Invalid verification link.'], 403);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return response()->json(['message' => 'Email verified successfully.']);
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/resend', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent.']);
    })->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated Users)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Betting
    Route::post('/bets', [BetController::class, 'store']);
    Route::get('/bets', [BetController::class, 'index']);
    Route::get('/bets/{bet}', [BetController::class, 'show']);

    // User Profile
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::get('/user/balance', [UserController::class, 'balance']);
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'stats']);

    // User Management
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/{user}', [AdminUserController::class, 'show']);
    Route::post('/users/{user}/balance', [AdminUserController::class, 'updateBalance']);
    Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend']);

    // Match Management
    Route::post('/matches', [AdminMatchController::class, 'store']);
    Route::put('/matches/{id}', [AdminMatchController::class, 'update']);
    Route::put('/matches/{id}/odds', [AdminMatchController::class, 'updateOdds']);

    // Sport Management
    Route::apiResource('/sports', AdminSportController::class);

    // League Management
    Route::apiResource('/leagues', AdminLeagueController::class);

    // Odds API - admin actions
    Route::post('/odds/refresh', [OddsController::class, 'refreshOdds']);
});
