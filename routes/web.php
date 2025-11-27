<?php

use App\Http\Controllers\BannerController;
use App\Http\Controllers\CallController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Protected routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/banned', [UserController::class, 'banned'])->name('users.banned');
    Route::get('/users/{uid}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{uid}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');

    // Calls
    Route::get('/calls', [CallController::class, 'index'])->name('calls.index');

    // Leaderboard
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

    // Banners
    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::patch('/banners/{id}', [BannerController::class, 'update'])->name('banners.update');
    Route::patch('/banners/{id}/toggle', [BannerController::class, 'toggleStatus'])->name('banners.toggle');
    Route::delete('/banners/{id}', [BannerController::class, 'destroy'])->name('banners.destroy');

    // App Config
    Route::get('/config', [ConfigController::class, 'index'])->name('config.index');
    Route::patch('/config', [ConfigController::class, 'update'])->name('config.update');
    Route::patch('/config/turn', [ConfigController::class, 'updateTurn'])->name('config.updateTurn');

    // Call Topics
    Route::get('/topics', [TopicController::class, 'index'])->name('topics.index');
    Route::post('/topics', [TopicController::class, 'store'])->name('topics.store');
    Route::patch('/topics/{id}', [TopicController::class, 'update'])->name('topics.update');
    Route::delete('/topics/{id}', [TopicController::class, 'destroy'])->name('topics.destroy');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::patch('/reports/{id}/resolve', [ReportController::class, 'resolve'])->name('reports.resolve');
    Route::post('/reports/bulk', [ReportController::class, 'bulkAction'])->name('reports.bulk');
});

require __DIR__.'/settings.php';
