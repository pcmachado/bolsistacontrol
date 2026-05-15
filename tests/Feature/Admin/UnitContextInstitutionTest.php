<?php

namespace Tests\Feature\Admin;

use App\Models\Institution;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitContextInstitutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_unit_creation_uses_authenticated_users_institution_context(): void
    {
        $this->withoutMiddleware();

        $institution = $this->institution('Instituição do Usuário');
        $otherInstitution = $this->institution('Instituição Indevida');
        $user = User::factory()->create(['institution_id' => $institution->id]);

        $response = $this->actingAs($user)->post(route('admin.units.store'), [
            'name' => 'Unidade Contextual',
            'shortname' => 'UC',
            'city' => 'Recife',
            'address' => 'Rua do Contexto',
            'institution_id' => $otherInstitution->id,
        ]);

        $response->assertRedirect(route('admin.units.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('units', [
            'name' => 'Unidade Contextual',
            'institution_id' => $institution->id,
        ]);

        $this->assertDatabaseMissing('units', [
            'name' => 'Unidade Contextual',
            'institution_id' => $otherInstitution->id,
        ]);
    }


    public function test_unit_creation_uses_selected_admin_institution_context_when_available(): void
    {
        $this->withoutMiddleware();

        $userInstitution = $this->institution('Instituição do Usuário');
        $contextInstitution = $this->institution('Instituição do Contexto');
        $user = User::factory()->create(['institution_id' => $userInstitution->id]);
        $user->institutions()->attach($contextInstitution->id);

        $response = $this
            ->actingAs($user)
            ->withSession(['admin_institution_context' => $contextInstitution->id])
            ->post(route('admin.units.store'), [
                'name' => 'Unidade do Contexto Selecionado',
                'shortname' => 'UCS',
                'city' => 'Recife',
                'address' => 'Rua do Contexto Selecionado',
                'institution_id' => $userInstitution->id,
            ]);

        $response->assertRedirect(route('admin.units.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('units', [
            'name' => 'Unidade do Contexto Selecionado',
            'institution_id' => $contextInstitution->id,
        ]);

        $this->assertDatabaseMissing('units', [
            'name' => 'Unidade do Contexto Selecionado',
            'institution_id' => $userInstitution->id,
        ]);
    }

    public function test_unit_update_preserves_authenticated_users_institution_context(): void
    {
        $this->withoutMiddleware();

        $institution = $this->institution('Instituição do Usuário');
        $otherInstitution = $this->institution('Instituição Indevida');
        $user = User::factory()->create(['institution_id' => $institution->id]);
        $unit = Unit::factory()->create([
            'institution_id' => $institution->id,
            'name' => 'Unidade Original',
        ]);

        $response = $this->actingAs($user)->put(route('admin.units.update', $unit), [
            'name' => 'Unidade Atualizada',
            'shortname' => 'UA',
            'city' => 'Olinda',
            'address' => 'Rua Atualizada',
            'institution_id' => $otherInstitution->id,
        ]);

        $response->assertRedirect(route('admin.units.index'));
        $response->assertSessionHasNoErrors();

        $unit->refresh();

        $this->assertSame('Unidade Atualizada', $unit->name);
        $this->assertSame($institution->id, $unit->institution_id);
    }

    private function institution(string $name): Institution
    {
        return Institution::query()->create([
            'name' => $name,
            'shortname' => substr($name, 0, 10),
            'city' => 'Recife',
            'state' => 'PE',
        ]);
    }
}
