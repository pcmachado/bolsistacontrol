<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            $request->session()->flash(
                'warning',
                'Seu e-mail ainda não foi verificado. Você pode continuar usando o sistema, mas recomendamos confirmar o endereço cadastrado.'
            );
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function handleIfrsCallback(Request $request): RedirectResponse
    {
        $config = config('services.ifrs_login');

        if (($request->input('state') !== $request->session()->pull('ifrs_oauth_state')) || ! $request->filled('code')) {
            return redirect()->route('login')->withErrors([
                'oauth' => 'Falha de validação na autenticação externa. Tente novamente.',
            ]);
        }

        try {
            $tokenResponse = Http::asForm()->post($config['token_url'], [
                'grant_type' => 'authorization_code',
                'client_id' => $config['client_id'],
                'client_secret' => $config['client_secret'],
                'redirect_uri' => $config['redirect'],
                'code' => $request->input('code'),
            ])->throw();

            $accessToken = $tokenResponse->json('access_token');

            $profileResponse = Http::withToken($accessToken)
                ->acceptJson()
                ->get($config['userinfo_url'])
                ->throw();
        } catch (RequestException $exception) {
            report($exception);

            return redirect()->route('login')->withErrors([
                'oauth' => 'Não foi possível autenticar com a conta IFRS no momento.',
            ]);
        }

        $externalEmail = Str::lower((string) $profileResponse->json('email'));

        if (! $externalEmail) {
            return redirect()->route('login')->withErrors([
                'oauth' => 'A conta externa não retornou um e-mail válido.',
            ]);
        }

        $user = \App\Models\User::query()->whereRaw('LOWER(email) = ?', [$externalEmail])->first();

        if (! $user) {
            return redirect()->route('login')->withErrors([
                'oauth' => 'Usuário autenticado no IFRS, porém sem cadastro ativo neste sistema.',
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }
}
