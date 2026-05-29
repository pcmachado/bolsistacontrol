<?php

namespace Tests\Feature\Seeds;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SeedFixesTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_users_have_scholarship_holders(): void
    {
        $this->artisan('db:seed');

        $roles = ['professor', 'coordenador_adjunto_geral', 'apoio_administrativo', 'supervisor', 'bolsista'];

        foreach ($roles as $role) {
            $users = User::role($role)->get();

            foreach ($users as $user) {
                $this->assertNotNull(
                    $user->scholarshipHolder,
                    "O usuário {$user->email} com papel {$role} deve ter um ScholarshipHolder."
                );
            }
        }
    }

    public function test_admin_users_do_not_have_scholarship_holders(): void
    {
        $this->artisan('db:seed');

        $roles = ['admin', 'superadmin'];

        foreach ($roles as $role) {
            $users = User::role($role)->get();

            foreach ($users as $user) {
                $this->assertNull(
                    $user->scholarshipHolder,
                    "O usuário {$user->email} com papel {$role} não deve ter um ScholarshipHolder."
                );
            }
        }
    }

    public function test_bolsista_users_are_created_with_scholarship_holders(): void
    {
        $this->artisan('db:seed');

        $user = User::role('bolsista')->first();

        $this->assertNotNull($user, 'Deve existir ao menos um usuário com papel bolsista.');
        $this->assertNotNull($user->scholarshipHolder, 'O bolsista deve ter um registro de ScholarshipHolder.');
    }

    public function test_impersonated_user_can_stop_impersonation(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'bolsista']);

        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $admin->assignRole('admin');

        $student = User::factory()->create(['email' => 'student@example.com']);
        $student->assignRole('bolsista');

        $this->actingAs($student)
            ->withSession(['impersonated_by' => $admin->id])
            ->post(route('admin.impersonate.stop'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_superadmin_can_start_impersonation_without_losing_origin(): void
    {
        Role::create(['name' => 'superadmin']);
        Role::create(['name' => 'bolsista']);

        $admin = User::factory()->create(['email' => 'superadmin@example.com']);
        $admin->assignRole('superadmin');

        $student = User::factory()->create(['email' => 'student-start@example.com']);
        $student->assignRole('bolsista');

        $this->actingAs($admin)
            ->post(route('admin.impersonate', $student))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('impersonated_by', $admin->id);

        $this->assertAuthenticatedAs($student);
    }

    public function test_receipt_verification_page_is_public(): void
    {
        $this->get(route('payments.verify.form'))
            ->assertOk();
    }
}
