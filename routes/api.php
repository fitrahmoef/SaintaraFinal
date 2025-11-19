<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Personal\TokenController;
use App\Http\Controllers\Personal\TestController;
use App\Http\Controllers\Personal\CertificateController;
use App\Http\Controllers\Personal\PersonalProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\TransactionManagementController;
use App\Http\Controllers\Admin\PackageManagementController;
use App\Http\Controllers\Admin\TestManagementController;
use App\Http\Controllers\Admin\QuestionManagementController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Instansi\InstansiDashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;

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

// Notification Routes (All authenticated users)
Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    Route::delete('/read/clear', [NotificationController::class, 'clearRead'])->name('clear-read');
});

// Personal User Routes (Protected)
Route::middleware(['auth', 'user.type:personal'])->prefix('personal')->name('personal.')->group(function () {
    // Token Management
    Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
    Route::get('/tokens/balance', [TokenController::class, 'balance'])->name('tokens.balance');
    Route::get('/tokens/packages', [TokenController::class, 'packages'])->name('tokens.packages');
    // CRITICAL: Rate limit token purchase to prevent abuse (5 purchases per hour)
    Route::post('/tokens/purchase', [TokenController::class, 'purchase'])
        ->middleware('throttle:5,60')
        ->name('tokens.purchase');

    // Test Management
    Route::get('/tests', [TestController::class, 'index'])->name('tests.index');
    Route::get('/tests/{id}', [TestController::class, 'show'])->name('tests.show');
    // Rate limit test submission to prevent spam (10 per hour)
    Route::post('/tests/submit', [TestController::class, 'submit'])
        ->middleware('throttle:10,60')
        ->name('tests.submit');

    // Test Session Management
    Route::post('/tests/session/start', [TestController::class, 'startSession'])
        ->middleware('throttle:20,60')
        ->name('tests.session.start');
    Route::post('/tests/session/save-progress', [TestController::class, 'saveProgress'])
        ->middleware('throttle:60,60')
        ->name('tests.session.save-progress');
    Route::post('/tests/session/submit', [TestController::class, 'submitSession'])
        ->middleware('throttle:10,60')
        ->name('tests.session.submit');
    Route::post('/tests/session/abandon', [TestController::class, 'abandonSession'])->name('tests.session.abandon');
    Route::get('/tests/session/status', [TestController::class, 'getSession'])->name('tests.session.status');

    // Test Results
    Route::get('/results', [TestController::class, 'results'])->name('results.index');
    Route::get('/results/{id}', [TestController::class, 'resultDetail'])->name('results.show');

    // Certificates
    Route::get('/certificates/{id}/download', [CertificateController::class, 'download'])->name('certificates.download');
    Route::get('/certificates/{id}/view', [CertificateController::class, 'view'])->name('certificates.view');
    Route::get('/results/{id}/download', [CertificateController::class, 'downloadTestResult'])->name('results.download');

    // Profile Management
    Route::get('/profile', [PersonalProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [PersonalProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [PersonalProfileController::class, 'uploadPhoto'])->name('profile.photo.upload');
    Route::delete('/profile/photo', [PersonalProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
});

// Admin Routes (Protected)
Route::middleware(['auth', 'user.type:admin'])->prefix('admin')->name('admin.')->group(function () {
    // User Management
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    // Rate limit user creation to prevent abuse
    Route::post('/users', [UserManagementController::class, 'store'])
        ->middleware('throttle:30,60')
        ->name('users.store');
    Route::get('/users/stats', [UserManagementController::class, 'stats'])->name('users.stats');
    Route::get('/users/export', [UserManagementController::class, 'export'])->name('users.export');
    Route::post('/users/bulk-delete', [UserManagementController::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::post('/users/bulk-update-type', [UserManagementController::class, 'bulkUpdateType'])->name('users.bulk-update-type');
    Route::get('/users/{id}', [UserManagementController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/details', [UserManagementController::class, 'details'])->name('users.details');
    Route::get('/users/{id}/activity', [UserManagementController::class, 'activitySummary'])->name('users.activity');
    Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Dashboard
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'index'])->name('dashboard.stats');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/stats', [AuditLogController::class, 'stats'])->name('audit-logs.stats');
    Route::get('/audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
    Route::get('/audit-logs/actions', [AuditLogController::class, 'actions'])->name('audit-logs.actions');
    Route::get('/audit-logs/modules', [AuditLogController::class, 'modules'])->name('audit-logs.modules');
    Route::get('/audit-logs/{id}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    Route::get('/audit-logs/user/{userId}', [AuditLogController::class, 'userActivity'])->name('audit-logs.user-activity');

    // Transaction Management
    Route::get('/transactions', [TransactionManagementController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/stats', [TransactionManagementController::class, 'stats'])->name('transactions.stats');
    Route::get('/transactions/export', [TransactionManagementController::class, 'export'])->name('transactions.export');
    Route::get('/transactions/{id}', [TransactionManagementController::class, 'show'])->name('transactions.show');
    Route::put('/transactions/{id}/status', [TransactionManagementController::class, 'updateStatus'])->name('transactions.update-status');

    // Package Management
    Route::get('/packages', [PackageManagementController::class, 'index'])->name('packages.index');
    Route::post('/packages', [PackageManagementController::class, 'store'])->name('packages.store');
    Route::get('/packages/{id}', [PackageManagementController::class, 'show'])->name('packages.show');
    Route::put('/packages/{id}', [PackageManagementController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{id}', [PackageManagementController::class, 'destroy'])->name('packages.destroy');
    Route::put('/packages/{id}/toggle-status', [PackageManagementController::class, 'toggleStatus'])->name('packages.toggle-status');

    // Test Management
    Route::get('/tests', [TestManagementController::class, 'index'])->name('tests.index');
    Route::post('/tests', [TestManagementController::class, 'store'])->name('tests.store');
    Route::get('/tests/{id}', [TestManagementController::class, 'show'])->name('tests.show');
    Route::put('/tests/{id}', [TestManagementController::class, 'update'])->name('tests.update');
    Route::delete('/tests/{id}', [TestManagementController::class, 'destroy'])->name('tests.destroy');
    Route::put('/tests/{id}/toggle-status', [TestManagementController::class, 'toggleStatus'])->name('tests.toggle-status');
    Route::post('/tests/{id}/duplicate', [TestManagementController::class, 'duplicate'])->name('tests.duplicate');

    // Question Management
    Route::get('/tests/{testId}/questions', [QuestionManagementController::class, 'index'])->name('questions.index');
    Route::post('/tests/{testId}/questions', [QuestionManagementController::class, 'store'])->name('questions.store');
    Route::post('/tests/{testId}/questions/bulk', [QuestionManagementController::class, 'bulkStore'])->name('questions.bulk-store');
    Route::get('/questions/{id}', [QuestionManagementController::class, 'show'])->name('questions.show');
    Route::put('/questions/{id}', [QuestionManagementController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{id}', [QuestionManagementController::class, 'destroy'])->name('questions.destroy');
    Route::post('/questions/reorder', [QuestionManagementController::class, 'reorder'])->name('questions.reorder');
});

// Instansi Routes (Protected)
Route::middleware(['auth', 'user.type:instansi'])->prefix('instansi')->name('instansi.')->group(function () {
    // Dashboard
    Route::get('/dashboard/stats', [InstansiDashboardController::class, 'index'])->name('dashboard.stats');
    Route::get('/dashboard/employees', [InstansiDashboardController::class, 'employees'])->name('dashboard.employees');
    Route::get('/dashboard/test-results', [InstansiDashboardController::class, 'testResults'])->name('dashboard.test-results');

    // Employee Management
    Route::post('/employees/bulk-upload', [InstansiDashboardController::class, 'bulkUpload'])->name('employees.bulk-upload');
    Route::get('/employees/template', [InstansiDashboardController::class, 'downloadTemplate'])->name('employees.template');

    // Test submission for institutions
    Route::post('/tests/submit-batch', [TestController::class, 'submitBatch'])->name('tests.submit-batch');
});

// Payment Routes (Public - for Midtrans callback)
Route::prefix('payment')->name('payment.')->group(function () {
    // Midtrans notification callback (MUST BE PUBLIC but rate limited)
    // CRITICAL: Rate limit to prevent webhook abuse (100 per minute per IP)
    Route::post('/notification', [PaymentController::class, 'handleNotification'])
        ->middleware('throttle:100,1')
        ->name('notification');
});

// Payment Routes (Protected)
Route::middleware(['auth'])->prefix('payment')->name('payment.')->group(function () {
    // Get transaction status
    Route::get('/status', [PaymentController::class, 'getTransactionStatus'])->name('status');

    // Cancel transaction
    Route::post('/cancel', [PaymentController::class, 'cancelTransaction'])->name('cancel');
});

// Public Routes
Route::prefix('public')->name('public.')->group(function () {
    // Public test packages (for display on landing page)
    Route::get('/packages', [TokenController::class, 'packages'])->name('packages');

    // Certificate verification (public)
    Route::get('/certificates/verify/{nomor_sertifikat}', [CertificateController::class, 'verify'])->name('certificates.verify');
});
