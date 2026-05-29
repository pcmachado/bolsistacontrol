<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\SystemSetting;
use App\Models\ScholarshipHolder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_dashboard_resolver_sends_roles_to_their_home_surfaces(): void
    {
        foreach (['superadmin', 'admin', 'professor', 'bolsista'] as $role) {
            Role::create(['name' => $role]);
        }

        $superadmin = User::factory()->create();
        $superadmin->assignRole('superadmin');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $professor = User::factory()->create();
        $professor->assignRole('professor');

        $bolsista = User::factory()->create();
        $bolsista->assignRole('bolsista');
        ScholarshipHolder::factory()->create(['user_id' => $bolsista->id]);

        $this->actingAs($superadmin)
            ->get(route('dashboard'))
            ->assertRedirect(route('superadmin.dashboard', absolute: false));

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertRedirect(route('admin.dashboard', absolute: false));

        $this->actingAs($professor)
            ->get(route('dashboard'))
            ->assertRedirect(route('teacher.dashboard', absolute: false));

        $this->actingAs($bolsista)
            ->get(route('dashboard'))
            ->assertRedirect(route('holder.dashboard', absolute: false));
    }

    public function test_unverified_users_can_authenticate_and_receive_warning(): void
    {
        SystemSetting::set('email_verification_enabled', true);

        $user = User::factory()->unverified()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $response->assertSessionHas('warning');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
