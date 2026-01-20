<?php

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
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function ($exceptions) {
        $exceptions->render(function (DecryptException $e, $request) {
            Log::error('DecryptException capturada', [
                'url' => $request->fullUrl(),
                'input' => $request->all(),
                'message' => $e->getMessage(),
            ]);

            dd(
                'DecryptException capturada',
                $e->getMessage(),
                $request->fullUrl()
            );
        });
    })->create();
    //->withExceptions(function (Exceptions $exceptions): void {
        //
    //})->create();
