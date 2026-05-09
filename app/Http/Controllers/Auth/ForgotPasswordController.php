<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.passwords.email');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        \Log::info('Password reset requested', [
            'email' => $request->email,
            'ip' => $request->ip(),
        ]);

        Password::sendResetLink(
            $request->only('email')
        );

        // Send the password reset link
        $this->sendResetLinkEmail($request);

        return back()->with('status', 'Enviaremos um link para redefinição de senha.');
    }
}
