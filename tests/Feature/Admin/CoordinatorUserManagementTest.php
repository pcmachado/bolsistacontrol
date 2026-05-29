<?php

namespace Tests\Feature\Admin;

use App\Models\Institution;
use App\Models\Position;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CoordinatorUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_general_coordinator_can_edit_user_role_in_same_institution(): void
    {
        foreach (['coordenador_geral', 'coordenador_adjunto', 'bolsista'] as $role) {
            Role::create(['name' => $role]);
        }

        $institution = Institution::factory()->create();
        $unit = Unit::factory()->create(['institution_id' => $institution->id]);

        $coordinator = User::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $unit->id,
        ]);
        $coordinator->assignRole('coordenador_geral');

        $target = User::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $unit->id,
        ]);
        $target->assignRole('bolsista');

        $this->actingAs($coordinator)
            ->get(route('admin.users.edit', $target))
            ->assertOk();

        $this->actingAs($coordinator)
            ->put(route('admin.users.update', $target), [
                'name' => 'Usuario Atualizado',
                'email' => 'usuario-atualizado@example.com',
                'unit_id' => $unit->id,
                'role' => 'coordenador_adjunto',
            ])
            ->assertRedirect(route('admin.users.index'));

        $target->refresh();

        $this->assertSame('Usuario Atualizado', $target->name);
        $this->assertTrue($target->hasRole('coordenador_adjunto'));
    }

    public function test_general_adjunct_coordinator_can_edit_scholarship_holder_role_and_project_position(): void
    {
        foreach (['coordenador_adjunto_geral', 'supervisor', 'bolsista'] as $role) {
            Role::create(['name' => $role]);
        }

        $institution = Institution::factory()->create();
        $unit = Unit::factory()->create(['institution_id' => $institution->id]);

        $coordinator = User::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $unit->id,
        ]);
        $coordinator->assignRole('coordenador_adjunto_geral');

        $holderUser = User::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $unit->id,
        ]);
        $holderUser->assignRole('bolsista');

        $holder = ScholarshipHolder::factory()->create([
            'user_id' => $holderUser->id,
            'unit_id' => $unit->id,
            'cpf' => '11122233344',
        ]);

        $project = Project::factory()->create(['institution_id' => $institution->id]);
        $oldPosition = Position::factory()->create(['name' => 'Bolsista']);
        $newPosition = Position::factory()->create(['name' => 'Supervisor']);

        $project->positions()->attach($oldPosition->id, [
            'weekly_workload' => 20,
            'hourly_rate' => 10,
        ]);
        $project->positions()->attach($newPosition->id, [
            'weekly_workload' => 20,
            'hourly_rate' => 10,
        ]);
        $project->scholarshipHolders()->attach($holder->id, [
            'position_id' => $oldPosition->id,
            'weekly_workload' => 20,
            'status' => 'active',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
        ]);

        $this->assertSame($institution->id, $holder->unit?->institution_id);
        $this->assertTrue($coordinator->activeInstitutionIds()->contains($institution->id));
        $this->assertTrue($coordinator->can('update', $holderUser));
        $this->assertTrue($coordinator->can('update', $holder));
        $this->assertTrue(\App\Support\RoleAccess::canAssignRole($coordinator, 'supervisor'));

        $this->actingAs($coordinator)
            ->get(route('admin.scholarship_holders.edit', $holder))
            ->assertOk();

        $this->actingAs($coordinator)
            ->put(route('admin.scholarship_holders.update', $holder), [
                'name' => 'Bolsista Atualizado',
                'cpf' => '11122233344',
                'email' => 'bolsista-atualizado@example.com',
                'unit_id' => $unit->id,
                'start_date' => now()->toDateString(),
                'end_date' => null,
                'phone' => null,
                'bank' => null,
                'agency' => null,
                'account' => null,
                'pix_key' => null,
                'status' => 'active',
                'role' => 'supervisor',
                'positions_by_project' => [
                    $project->id => $newPosition->id,
                ],
            ])
            ->assertRedirect(route('admin.scholarship_holders.index'));

        $holderUser->refresh();
        $positionId = $project->scholarshipHolders()
            ->where('scholarship_holder_id', $holder->id)
            ->first()
            ->pivot
            ->position_id;

        $this->assertTrue($holderUser->hasRole('supervisor'));
        $this->assertSame($newPosition->id, $positionId);
    }
}
