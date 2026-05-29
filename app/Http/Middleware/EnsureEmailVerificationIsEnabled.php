<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerificationIsEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! SystemSetting::enabled('email_verification_enabled', false)) {
            return redirect()->route('dashboard', absolute: false);
        }

        return $next($request);
    }
}
