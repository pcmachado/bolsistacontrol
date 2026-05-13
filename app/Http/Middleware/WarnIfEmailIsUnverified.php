<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WarnIfEmailIsUnverified
{
    /**
     * Keep routes accessible while warning authenticated users with unverified emails.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            $request->session()->flash(
                'warning',
                'Seu e-mail ainda não foi verificado. Você pode continuar usando o sistema, mas recomendamos confirmar o endereço cadastrado.'
            );
        }

        return $next($request);
    }
}
