<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OAuth\IFRSOAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IFRSAuthController extends Controller
{
    public function __construct(
        private readonly IFRSOAuthService $oauth
    ) {}

    public function redirect()
    {
        return redirect()->away(
            $this->oauth->getRedirectUrl()
        );
    }

    public function callback(Request $request)
    {

        // if (
        //     !$request->state ||
        //     $request->state !== session('oauth_state')
        // ) {
        //     return redirect()
        //         ->route('login')
        //         ->withErrors([
        //             'oauth' => 'Falha de segurança OAuth.'
        //         ]);
        // }

        if ($request->error) {

            return redirect()
                ->route('login')
                ->withErrors([
                    'oauth' => $request->error_description
                ]);
        }

        $tokenData = $this->oauth->getAccessToken(
            $request->code
        );

        $oauthUser = $this->oauth->getUser(
            $tokenData['access_token']
        );

        $email = $oauthUser['email'] ?? null;

        if (!$email) {

            return redirect()
                ->route('login')
                ->withErrors([
                    'oauth' => 'O IFRS não retornou um e-mail válido.'
                ]);
        }

        $user = User::where(
            'email',
            $email
        )->first();

        if (!$user) {

            return redirect()
                ->route('login')
                ->withErrors([
                    'oauth' => 'Usuário não autorizado no sistema.'
                ]);
        }

        Auth::login($user, true);

        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }
}