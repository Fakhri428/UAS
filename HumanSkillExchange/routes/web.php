<?php

use App\Http\Controllers\SkillExchangeController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');
Route::get('/home', [SkillExchangeController::class, 'index'])->name('home');
Route::get('/market', [SkillExchangeController::class, 'index'])->name('market');
Route::get('/explore', [SkillExchangeController::class, 'index']);
Route::get('/user/{user:name}', [SkillExchangeController::class, 'showKoukanId'])->name('user.show');
Route::get('/preview', [SkillExchangeController::class, 'index'])->name('preview');
Route::get('/users/{user}/profile', [SkillExchangeController::class, 'showProfile'])->name('users.profile');
Route::get('/offers/{offer}', [SkillExchangeController::class, 'showOffer'])->name('offers.show');
Route::get('/needs/{need}', [SkillExchangeController::class, 'showNeed'])->name('needs.show');
Route::get('/matches', [SkillExchangeController::class, 'matches'])->name('matches');

// Plan member
Route::get('/plans', [App\Http\Controllers\PlanController::class, 'index'])->name('plans.index');
// Midtrans webhook (publik, tanpa CSRF — lihat bootstrap/app.php)
Route::post('/plans/midtrans/callback', [App\Http\Controllers\PlanController::class, 'callback'])->name('plans.callback');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [SkillExchangeController::class, 'dashboard'])->name('dashboard');
    Route::get('/plans/{plan}/checkout', [App\Http\Controllers\PlanController::class, 'checkout'])->name('plans.checkout');
    Route::get('/plans/payment/finish', [App\Http\Controllers\PlanController::class, 'finish'])->name('plans.finish');
    Route::post('/profile/exchange', [SkillExchangeController::class, 'updateProfile'])->name('exchange.profile.update');
    Route::post('/skills', [SkillExchangeController::class, 'storeSkill'])->name('skills.store');
    Route::put('/skills/{skill}', [SkillExchangeController::class, 'updateSkill'])->name('skills.update');
    Route::delete('/skills/{skill}', [SkillExchangeController::class, 'destroySkill'])->name('skills.destroy');

    Route::post('/offers', [SkillExchangeController::class, 'storeOffer'])->name('offers.store');
    Route::put('/offers/{offer}', [SkillExchangeController::class, 'updateOffer'])->name('offers.update');
    Route::delete('/offers/{offer}', [SkillExchangeController::class, 'destroyOffer'])->name('offers.destroy');

    Route::post('/needs', [SkillExchangeController::class, 'storeNeed'])->name('needs.store');
    Route::put('/needs/{need}', [SkillExchangeController::class, 'updateNeed'])->name('needs.update');
    Route::delete('/needs/{need}', [SkillExchangeController::class, 'destroyNeed'])->name('needs.destroy');
    Route::post('/portfolios', [SkillExchangeController::class, 'storePortfolio'])->name('portfolios.store');
    Route::put('/portfolios/{portfolio}', [SkillExchangeController::class, 'updatePortfolio'])->name('portfolios.update');
    Route::delete('/portfolios/{portfolio}', [SkillExchangeController::class, 'destroyPortfolio'])->name('portfolios.destroy');
    Route::post('/offers/{offer}/request', [SkillExchangeController::class, 'requestOffer'])->name('offers.request');
    Route::post('/needs/{need}/request', [SkillExchangeController::class, 'requestNeed'])->name('needs.request');
    Route::patch('/exchange-requests/{exchangeRequest}', [SkillExchangeController::class, 'updateExchange'])->name('exchange-requests.update');
    
    // Admin
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'dashboard'])->middleware('can:admin')->name('admin.dashboard');
    Route::get('/admin/users', [App\Http\Controllers\AdminController::class, 'users'])->middleware('can:admin')->name('admin.users');
    Route::get('/admin/exchanges', [App\Http\Controllers\AdminController::class, 'exchanges'])->middleware('can:admin')->name('admin.exchanges');
    Route::get('/admin/reviews', [App\Http\Controllers\AdminController::class, 'reviews'])->middleware('can:admin')->name('admin.reviews');
    Route::get('/admin/transactions', [App\Http\Controllers\AdminController::class, 'transactions'])->middleware('can:admin')->name('admin.transactions');
    Route::post('/admin/users/{user}/verify', [App\Http\Controllers\AdminController::class, 'verifyUser'])->middleware('can:admin')->name('admin.users.verify');
    Route::post('/admin/reviews/{review}/hide', [App\Http\Controllers\AdminController::class, 'hideReview'])->middleware('can:admin')->name('admin.reviews.hide');
    Route::post('/admin/reviews/{review}/unhide', [App\Http\Controllers\AdminController::class, 'unhideReview'])->middleware('can:admin')->name('admin.reviews.unhide');

    // Mentoring booking via web (simple)
    Route::post('/mentoring-rooms', [App\Http\Controllers\Api\MentoringRoomController::class, 'store'])->name('mentoring-rooms.store');
    Route::delete('/mentoring-rooms/{mentoringRoom}', [App\Http\Controllers\Api\MentoringRoomController::class, 'destroy'])->name('mentoring-rooms.destroy');

    // Web booking endpoint for users
    Route::post('/mentoring-bookings', [App\Http\Controllers\Api\MentoringBookingController::class, 'store'])->name('mentoring-bookings.store');
    // Mentor actions: approve/decline bookings for rooms they own
    Route::post('/mentoring-bookings/{booking}/mentor-approve', [App\Http\Controllers\Api\MentoringBookingController::class, 'mentorApprove'])->name('mentoring-bookings.mentor.approve');
    Route::post('/mentoring-bookings/{booking}/mentor-decline', [App\Http\Controllers\Api\MentoringBookingController::class, 'mentorDecline'])->name('mentoring-bookings.mentor.decline');

    // Admin actions for bookings and transactions
    Route::post('/admin/bookings/{booking}/approve', [App\Http\Controllers\AdminController::class, 'approveBooking'])->middleware('can:admin')->name('admin.bookings.approve');
    Route::post('/admin/bookings/{booking}/decline', [App\Http\Controllers\AdminController::class, 'declineBooking'])->middleware('can:admin')->name('admin.bookings.decline');
    Route::post('/admin/transactions/{transaction}/complete', [App\Http\Controllers\AdminController::class, 'completeTransaction'])->middleware('can:admin')->name('admin.transactions.complete');

    // Exchange progress upload
    Route::post('/exchange-requests/{exchangeRequest}/progress', [SkillExchangeController::class, 'storeExchangeProgress'])->name('exchange-requests.progress.store');
    Route::delete('/exchange-progress/{progress}', [SkillExchangeController::class, 'deleteExchangeProgress'])->name('exchange-progress.destroy');

    // Review after exchange completed
    Route::post('/exchange-requests/{exchangeRequest}/review', [SkillExchangeController::class, 'storeReview'])->name('exchange-requests.review.store');

    // Chat
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}', [App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/messages', [App\Http\Controllers\ChatController::class, 'store'])->name('chat.messages.store');
    Route::get('/chat/with/{user}', [App\Http\Controllers\ChatController::class, 'createWithUser'])->name('chat.with');
    Route::post('/chat/{conversation}/progress', [App\Http\Controllers\ChatController::class, 'storeProgress'])->name('chat.progress.store');
    Route::post('/chat/{conversation}/exchange-action', [App\Http\Controllers\ChatController::class, 'exchangeAction'])->name('chat.exchange.action');

    // Mentoring Sessions
    Route::get('/mentoring/session/{booking}', [App\Http\Controllers\MentoringSessionController::class, 'show'])->name('mentoring.session.show');
    Route::post('/mentoring/session/{booking}/status', [App\Http\Controllers\MentoringSessionController::class, 'updateStatus'])->name('mentoring.session.update-status');
    Route::post('/mentoring/room/{room}/update', [App\Http\Controllers\MentoringSessionController::class, 'updateRoom'])->name('mentoring.room.update');
});
