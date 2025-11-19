<?php

use App\Http\Middleware\AuditLogMiddleware;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckUserType;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Add audit logging to API routes for security tracking
        $middleware->api(append: [
            AuditLogMiddleware::class,
        ]);

        $middleware->alias([
            'user.type' => CheckUserType::class,
            'permission' => CheckPermission::class,
            'audit' => AuditLogMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Expire tokens, transactions, and sessions daily at midnight
        $schedule->command('tokens:expire')->daily()->at('00:00');

        // Optional: Run every hour for more frequent cleanup
        // $schedule->command('tokens:expire')->hourly();
    })
    ->create();
