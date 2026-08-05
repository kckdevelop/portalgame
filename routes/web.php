<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminGameController;
use App\Http\Controllers\GamePortalController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

// Public Portal Routes
Route::get('/', [GamePortalController::class, 'index'])->name('portal.index');
Route::get('/game/{slug}', [GamePortalController::class, 'play'])->name('portal.play');

// Admin Auth Routes
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.store');

// Protected Admin Panel Routes
Route::prefix('admin')->middleware(['auth', AdminMiddleware::class])->name('admin.')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('/', [AdminGameController::class, 'dashboard'])->name('dashboard');
    Route::get('/games', [AdminGameController::class, 'index'])->name('games.index');
    Route::get('/games/create', [AdminGameController::class, 'create'])->name('games.create');
    Route::post('/games', [AdminGameController::class, 'store'])->name('games.store');
    Route::get('/games/{game}/edit', [AdminGameController::class, 'edit'])->name('games.edit');
    Route::put('/games/{game}', [AdminGameController::class, 'update'])->name('games.update');
    Route::delete('/games/{game}', [AdminGameController::class, 'destroy'])->name('games.destroy');
    Route::patch('/games/{game}/toggle', [AdminGameController::class, 'toggleStatus'])->name('games.toggle');
});
