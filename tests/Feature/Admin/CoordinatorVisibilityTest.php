<?php

namespace Tests\Feature\Admin;

use App\Models\Institution;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CoordinatorVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_general_coordinator_sees_all_units_in_their_institution_even_when_linked_to_one_unit(): void
    {
        Role::firstOrCreate(['name' => 'coordenador_geral']);

        $institution = Institution::factory()->create();
        $linkedUnit = Unit::factory()->create(['institution_id' => $institution->id]);
        $otherUnit = Unit::factory()->create(['institution_id' => $institution->id]);
        $outsideUnit = Unit::factory()->create();

        $coordinator = User::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $linkedUnit->id,
        ]);
        $coordinator->assignRole('coordenador_geral');

        $this->assertEqualsCanonicalizing(
            [$linkedUnit->id, $otherUnit->id],
            $coordinator->visibleUnitIds()->all()
        );
        $this->assertFalse($coordinator->visibleUnitIds()->contains($outsideUnit->id));
    }

    public function test_general_adjunct_coordinator_sees_all_units_but_adjunct_coordinator_stays_limited_to_linked_unit(): void
    {
        foreach (['coordenador_adjunto_geral', 'coordenador_adjunto'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $institution = Institution::factory()->create();
        $linkedUnit = Unit::factory()->create(['institution_id' => $institution->id]);
        $otherUnit = Unit::factory()->create(['institution_id' => $institution->id]);

        $generalAdjunct = User::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $linkedUnit->id,
        ]);
        $generalAdjunct->assignRole('coordenador_adjunto_geral');

        $unitAdjunct = User::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $linkedUnit->id,
        ]);
        $unitAdjunct->assignRole('coordenador_adjunto');

        $this->assertEqualsCanonicalizing(
            [$linkedUnit->id, $otherUnit->id],
            $generalAdjunct->visibleUnitIds()->all()
        );
        $this->assertEquals([$linkedUnit->id], $unitAdjunct->visibleUnitIds()->all());
    }
}
