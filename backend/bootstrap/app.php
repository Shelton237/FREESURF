<?php

use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\GenerateMonthlyInvoices;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withCommands([
        GenerateMonthlyInvoices::class,
    ])
    ->withSchedule(function (Schedule $schedule): void {
        // Génération mensuelle des factures (le 1er à 01:00)
        $schedule->command('invoices:generate-monthly')
            ->monthlyOn(1, '01:00')
            ->withoutOverlapping();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
