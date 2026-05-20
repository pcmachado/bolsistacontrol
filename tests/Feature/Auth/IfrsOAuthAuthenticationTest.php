<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IfrsOAuthAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.ifrs_login', [
            'enabled' => true,
            'client_id' => 'client-test',
            'client_secret' => 'secret-test',
            'redirect' => 'http://localhost/login/ifrs/callback',
            'authorize_url' => 'https://sso.ifrs.edu.br/oauth/authorize',
            'token_url' => 'https://sso.ifrs.edu.br/oauth/token',
            'userinfo_url' => 'https://sso.ifrs.edu.br/oauth/userinfo',
            'scopes' => 'openid profile email',
        ]);
    }

    public function test_redirect_to_ifrs_contains_required_query_params(): void
    {
        $response = $this->get(route('login.ifrs'));

        $response->assertRedirect();
        $location = $response->headers->get('Location');

        $this->assertStringContainsString('https://sso.ifrs.edu.br/oauth/authorize?', $location);
        $this->assertStringContainsString('client_id=client-test', $location);
        $this->assertStringContainsString('response_type=code', $location);
        $this->assertStringContainsString('scope=openid+profile+email', $location);
        $this->assertStringContainsString(urlencode('http://localhost/login/ifrs/callback'), $location);
    }

    public function test_callback_rejects_invalid_state(): void
    {
        $response = $this
            ->withSession(['ifrs_oauth_state' => 'expected-state'])
            ->get(route('login.ifrs.callback', ['state' => 'invalid-state', 'code' => 'code123']));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('oauth');
        $this->assertGuest();
    }

    public function test_callback_authenticates_existing_local_user(): void
    {
        $user = User::factory()->create(['email' => 'servidor@ifrs.edu.br']);

        Http::fake([
            'https://sso.ifrs.edu.br/oauth/token' => Http::response([
                'access_token' => 'token-abc',
                'token_type' => 'Bearer',
            ], 200),
            'https://sso.ifrs.edu.br/oauth/userinfo' => Http::response([
                'email' => 'SERVIDOR@ifrs.edu.br',
                'name' => 'Servidor IFRS',
            ], 200),
        ]);

        $response = $this
            ->withSession(['ifrs_oauth_state' => 'valid-state'])
            ->get(route('login.ifrs.callback', ['state' => 'valid-state', 'code' => 'code123']));

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_callback_blocks_user_without_local_registration(): void
    {
        Http::fake([
            'https://sso.ifrs.edu.br/oauth/token' => Http::response([
                'access_token' => 'token-abc',
            ], 200),
            'https://sso.ifrs.edu.br/oauth/userinfo' => Http::response([
                'email' => 'nao.cadastrado@ifrs.edu.br',
            ], 200),
        ]);

        $response = $this
            ->withSession(['ifrs_oauth_state' => 'valid-state'])
            ->get(route('login.ifrs.callback', ['state' => 'valid-state', 'code' => 'code123']));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('oauth');
        $this->assertGuest();
    }
}
