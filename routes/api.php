<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Personal\TokenController;
use App\Http\Controllers\Personal\TestController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Personal User Routes (Protected)
Route::middleware(['auth', 'user.type:personal'])->prefix('personal')->name('personal.')->group(function () {
    // Token Management
    Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
    Route::get('/tokens/balance', [TokenController::class, 'balance'])->name('tokens.balance');
    Route::get('/tokens/packages', [TokenController::class, 'packages'])->name('tokens.packages');
    Route::post('/tokens/purchase', [TokenController::class, 'purchase'])->name('tokens.purchase');

    // Test Management
    Route::get('/tests', [TestController::class, 'index'])->name('tests.index');
    Route::get('/tests/{id}', [TestController::class, 'show'])->name('tests.show');
    Route::post('/tests/submit', [TestController::class, 'submit'])->name('tests.submit');

    // Test Results
    Route::get('/results', [TestController::class, 'results'])->name('results.index');
    Route::get('/results/{id}', [TestController::class, 'resultDetail'])->name('results.show');
});

// Admin Routes (Protected)
Route::middleware(['auth', 'user.type:admin'])->prefix('admin')->name('admin.')->group(function () {
    // User Management
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/stats', [UserManagementController::class, 'stats'])->name('users.stats');
    Route::get('/users/{id}', [UserManagementController::class, 'show'])->name('users.show');
    Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Dashboard
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'index'])->name('dashboard.stats');
});

// Instansi Routes (Protected)
Route::middleware(['auth', 'user.type:instansi'])->prefix('instansi')->name('instansi.')->group(function () {
    // Test submission for institutions
    Route::post('/tests/submit-batch', [TestController::class, 'submitBatch'])->name('tests.submit-batch');
});

// Public Routes
Route::prefix('public')->name('public.')->group(function () {
    // Public test packages (for display on landing page)
    Route::get('/packages', [TokenController::class, 'packages'])->name('packages');
});
