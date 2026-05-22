<?php

namespace App\Services\OAuth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class IFRSOAuthService
{
    public function getRedirectUrl(): string
    {
        $state = Str::random(40);

        session([
            'oauth_state' => $state
        ]);

        $query = http_build_query([
            'client_id' => config('services.ifrs.client_id'),

            'redirect_uri' => config('services.ifrs.redirect'),

            'response_type' => 'code',

            'scope' => 'read-menu read-meus-dados view-unidades',

            'state' => $state,
        ]);

        return config('services.ifrs.base_url')
            . '/oauth/authorize?' . $query;
    }

    public function getAccessToken(string $code): array
    {
        $response = Http::asForm()->post(
            config('services.ifrs.base_url') . '/oauth/token',
            [
                'grant_type' => 'authorization_code',

                'client_id' => config('services.ifrs.client_id'),

                'client_secret' => config('services.ifrs.client_secret'),

                'redirect_uri' => config('services.ifrs.redirect'),

                'code' => $code,
            ]
        );

        if ($response->failed()) {
            throw new \Exception(
                'Erro ao obter token OAuth IFRS.'
            );
        }

        return $response->json();
    }

    // public function getUser(string $token): array
    // {
    //     $response = Http::withToken($token)
    //         ->get(
    //             config('services.ifrs.base_url') . '/api/user'
    //         );

    //     if ($response->failed()) {
    //         throw new \Exception(
    //             'Erro ao obter usuário IFRS.'
    //         );
    //     }

    //     return $response->json();
    // }

    public function getUser(string $token): array
    {
        $endpointUsuario = config(
            'services.ifrs.user_endpoint'
        );

        $client = Http::acceptJson();

        if (app()->isLocal()) {
            $client->withoutVerifying();
        }

        $response = $client
            ->withToken($token)
            ->get($endpointUsuario);

        if ($response->failed()) {

            // dd([
            //     'status' => $response->status(),
            //     'body' => $response->body(),
            //     'endpoint' => $endpointUsuario,
            // ]);

            throw new \Exception(
                'Falha ao obter usuário IFRS.'
            );
        }

        return $response->json();
    }
}