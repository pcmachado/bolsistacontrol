<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        if (! $request->user()->hasVerifiedEmail()) {
            $request->session()->flash(
                'warning',
                'Seu e-mail ainda não foi verificado. Você pode continuar usando o sistema, mas recomendamos confirmar o endereço cadastrado.'
            );
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
