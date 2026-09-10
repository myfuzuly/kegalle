<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([\App\Providers\AppServiceProvider::class])
    ->withRouting(web: __DIR__.'/../routes/web.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: ['g-return', 'api/chat/*/send', 'api/chat/unread']);
    $middleware->web(append: [
        \App\Http\Middleware\SecurityHeaders::class,
        \App\Http\Middleware\CheckMaintenance::class,
        \App\Http\Middleware\HandleInertiaRequests::class,
    ]);
    $middleware->alias([
        'verified.custom' => \App\Http\Middleware\EnsureEmailIsVerifiedCustom::class,
        'is_admin'        => \App\Http\Middleware\EnsureIsAdmin::class,
        'account.active'  => \App\Http\Middleware\EnsureAccountIsActive::class,
        'cache.page'      => \App\Http\Middleware\CachePublicResponse::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
        });

        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            return redirect()->back()->withInput($request->except('password', 'password_confirmation'))->withErrors(['email' => 'Your session expired. Please try again.']);
        });

        if (app()->bound('sentry') && config('sentry.dsn')) {
            $exceptions->reportable(function (\Throwable $e) {
                \Sentry\Laravel\Integration::captureUnhandledException($e);
            });
        }
    })
    ->create();
