<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Session\TokenMismatchException;

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
            'verified' => \App\Http\Middleware\WarnIfEmailIsUnverified::class,
            'email.verification.enabled' => \App\Http\Middleware\EnsureEmailVerificationIsEnabled::class,
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
    })
    ->withExceptions(function ($exceptions) {

        $exceptions->render(function (
            TokenMismatchException $e,
            $request
        ) {

            if ($request->expectsJson()) {

                return response()->json([
                    'message' => 'Sua sessão expirou. Atualize a página e tente novamente.'
                ], 419);
            }

            return redirect()
                ->back()
                ->withInput(
                    $request->except('password')
                )
                ->withErrors([
                    'session' =>
                        'Sua sessão expirou ou foi atualizada em outro acesso. Por favor, tente novamente.'
                ]);
        });
    })->create();
