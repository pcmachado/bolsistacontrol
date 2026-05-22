<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class IFRSAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('ifrs')->redirect();
    }

    public function callback()
    {
        $oauthUser = Socialite::driver('ifrs')->user();

        $user = User::where(
            'email',
            $oauthUser->email
        )->first();

        if (!$user) {

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Usuário não autorizado.'
                ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}