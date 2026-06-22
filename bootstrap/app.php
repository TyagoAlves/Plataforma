<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__."/../routes/web.php",
        api: __DIR__."/../routes/api.php",
        commands: __DIR__."/../routes/console.php",
        health: "/up",
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: "*");
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is("api/*"),
        );

        $exceptions->reportable(function (\Throwable $e) {
            if (auth()->check()) {
                $user = auth()->user();
                $log = sprintf(
                    "[%s] User: %s (%s) URL: %s Message: %s\n",
                    now()->format("Y-m-d H:i:s"),
                    $user->id,
                    $user->name,
                    request()->fullUrl(),
                    $e->getMessage()
                );
                file_put_contents(storage_path("logs/bugs.txt"), $log, FILE_APPEND);
            }
        });
    })->create();
