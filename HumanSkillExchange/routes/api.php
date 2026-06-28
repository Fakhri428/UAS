<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ExchangeProgressController;
use App\Http\Controllers\Api\ExchangeRequestController;
use App\Http\Controllers\Api\ExchangeTypeController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\MentoringBookingController;
use App\Http\Controllers\Api\MentoringRoomController;
use App\Http\Controllers\Api\NeedController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReputationController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);

Route::get('/plans', [PlanController::class, 'index']);
Route::get('/exchange-types', [ExchangeTypeController::class, 'index']);
Route::get('/exchange_types', [ExchangeTypeController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthApiController::class, 'me']);
    Route::get('/me', [AuthApiController::class, 'me']);
    Route::post('/logout', [AuthApiController::class, 'logout']);

    // Subscription (penamaan internal /subscription + alias sesuai PRD §14.3)
    Route::get('/subscription', [PlanController::class, 'current']);
    Route::post('/subscription', [PlanController::class, 'subscribe']);
    Route::patch('/subscription', [PlanController::class, 'cancel']);
    Route::get('/my-plan', [PlanController::class, 'current']);
    Route::post('/subscribe', [PlanController::class, 'subscribe']);
    Route::patch('/subscribe/cancel', [PlanController::class, 'cancel']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'save']);
    Route::put('/profile', [ProfileController::class, 'save']);
    Route::get('/users/{id}/profile', [ProfileController::class, 'publicProfile'])->whereNumber('id');

    // Public per-user resources (PRD §14)
    Route::get('/users/{id}/reviews', [ReviewController::class, 'forUser'])->whereNumber('id');
    Route::get('/users/{id}/reputation', [ReputationController::class, 'forUser'])->whereNumber('id');
    Route::get('/users/{id}/portfolios', [PortfolioController::class, 'forUser'])->whereNumber('id');

    Route::get('/skills', [SkillController::class, 'index']);
    Route::get('/skills/{id}', [SkillController::class, 'show'])->whereNumber('id');
    Route::post('/skills', [SkillController::class, 'store']);
    Route::put('/skills', [SkillController::class, 'updateFromQuery']);
    Route::delete('/skills', [SkillController::class, 'destroyFromQuery']);
    Route::put('/skills/{id}', [SkillController::class, 'update'])->whereNumber('id');
    Route::delete('/skills/{id}', [SkillController::class, 'destroy'])->whereNumber('id');

    Route::get('/needs', [NeedController::class, 'index']);
    Route::get('/needs/{id}', [NeedController::class, 'show'])->whereNumber('id');
    Route::post('/needs', [NeedController::class, 'store']);
    Route::put('/needs', [NeedController::class, 'updateFromQuery']);
    Route::delete('/needs', [NeedController::class, 'destroyFromQuery']);
    Route::put('/needs/{id}', [NeedController::class, 'update'])->whereNumber('id');
    Route::delete('/needs/{id}', [NeedController::class, 'destroy'])->whereNumber('id');
    Route::get('/needs/{id}/matches', [MatchController::class, 'needMatches'])->whereNumber('id');

    Route::get('/offers', [OfferController::class, 'index']);
    Route::get('/offers/{id}', [OfferController::class, 'show'])->whereNumber('id');
    Route::post('/offers', [OfferController::class, 'store']);
    Route::put('/offers', [OfferController::class, 'updateFromQuery']);
    Route::delete('/offers', [OfferController::class, 'destroyFromQuery']);
    Route::put('/offers/{id}', [OfferController::class, 'update'])->whereNumber('id');
    Route::delete('/offers/{id}', [OfferController::class, 'destroy'])->whereNumber('id');
    Route::get('/offers/{id}/matches', [MatchController::class, 'offerMatches'])->whereNumber('id');

    Route::get('/portfolios', [PortfolioController::class, 'index']);
    Route::get('/portfolios/{id}', [PortfolioController::class, 'show'])->whereNumber('id');
    Route::post('/portfolios', [PortfolioController::class, 'store']);
    Route::put('/portfolios/{id}', [PortfolioController::class, 'update'])->whereNumber('id');
    Route::delete('/portfolios/{id}', [PortfolioController::class, 'destroy'])->whereNumber('id');

    Route::get('/matches', [MatchController::class, 'index']);

    foreach (['exchange-requests', 'exchange_requests'] as $uri) {
        Route::get("/{$uri}", [ExchangeRequestController::class, 'index']);
        Route::get("/{$uri}/{id}", [ExchangeRequestController::class, 'show'])->whereNumber('id');
        Route::post("/{$uri}", [ExchangeRequestController::class, 'store']);
        Route::patch("/{$uri}", [ExchangeRequestController::class, 'patchFromQuery']);
        Route::patch("/{$uri}/{id}", [ExchangeRequestController::class, 'patch'])->whereNumber('id');
        // Endpoint bernama sesuai PRD §14.9
        Route::patch("/{$uri}/{id}/status", [ExchangeRequestController::class, 'status'])->whereNumber('id');
        Route::patch("/{$uri}/{id}/complete", [ExchangeRequestController::class, 'markComplete'])->whereNumber('id');
        // Progress bersarang sesuai PRD §14.10
        Route::get("/{$uri}/{id}/progress", [ExchangeProgressController::class, 'indexForRequest'])->whereNumber('id');
        Route::post("/{$uri}/{id}/progress", [ExchangeProgressController::class, 'storeForRequest'])->whereNumber('id');
    }

    foreach (['exchange-progress', 'exchange_progress'] as $uri) {
        Route::get("/{$uri}", [ExchangeProgressController::class, 'index']);
        Route::post("/{$uri}", [ExchangeProgressController::class, 'store']);
        Route::put("/{$uri}", [ExchangeProgressController::class, 'updateFromQuery']);
        Route::delete("/{$uri}", [ExchangeProgressController::class, 'destroyFromQuery']);
        Route::put("/{$uri}/{id}", [ExchangeProgressController::class, 'update'])->whereNumber('id');
        Route::delete("/{$uri}/{id}", [ExchangeProgressController::class, 'destroy'])->whereNumber('id');
    }

    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::post('/reviews', [ReviewController::class, 'store']);

    // Mentoring rooms
    Route::get('/mentoring-rooms', [MentoringRoomController::class, 'index']);
    Route::get('/mentoring-rooms/{mentoringRoom}', [MentoringRoomController::class, 'show']);
    Route::post('/mentoring-rooms', [MentoringRoomController::class, 'store']);
    Route::put('/mentoring-rooms/{mentoringRoom}', [MentoringRoomController::class, 'update']);
    Route::delete('/mentoring-rooms/{mentoringRoom}', [MentoringRoomController::class, 'destroy']);
    Route::post('/mentoring-rooms/{mentoringRoom}/book', [MentoringBookingController::class, 'book']);

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::patch('/transactions/{transaction}/confirm', [TransactionController::class, 'confirm']);

    // Mentoring bookings
    Route::get('/mentoring-bookings', [MentoringBookingController::class, 'index']);
    Route::post('/mentoring-bookings', [MentoringBookingController::class, 'store']);
    Route::put('/mentoring-bookings/{mentoringBooking}', [MentoringBookingController::class, 'update']);

    Route::get('/reputation', [ReputationController::class, 'show']);

    // Admin API (PRD §14.15) — gating role admin di dalam controller
    Route::prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'users']);
        Route::get('/exchanges', [AdminController::class, 'exchanges']);
        Route::get('/reviews', [AdminController::class, 'reviews']);
        Route::get('/transactions', [AdminController::class, 'transactions']);
        Route::patch('/users/{id}/verify', [AdminController::class, 'verifyUser'])->whereNumber('id');
        Route::patch('/reviews/{id}/hide', [AdminController::class, 'hideReview'])->whereNumber('id');
    });
});
