<?php

namespace Tests\Feature\Admin;

use App\Models\FundingSource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FundingSourceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_funding_source_can_be_created_from_admin_form(): void
    {
        $this->withoutMiddleware();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.funding-sources.store'), [
            'name' => 'Edital PROEX',
            'type' => 'internal',
            'total_amount' => 15000,
            'description' => 'Fomento institucional.',
        ]);

        $response->assertRedirect(route('admin.funding-sources.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('funding_sources', [
            'name' => 'Edital PROEX',
            'type' => 'internal',
            'total_amount' => 15000,
        ]);
    }

    public function test_wizard_step_five_can_create_funding_source_and_return_to_project(): void
    {
        $this->withoutMiddleware();

        $user = User::factory()->create();
        $project = Project::factory()->create(['wizard_step' => 'step5']);
        $redirectTo = route('admin.projects.create.step5', $project);

        $response = $this->actingAs($user)->post(route('admin.funding-sources.store'), [
            'name' => 'Fomento Emergencial',
            'type' => 'external',
            'redirect_to' => $redirectTo,
        ]);

        $response->assertRedirect($redirectTo);
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('funding_sources', [
            'name' => 'Fomento Emergencial',
            'type' => 'external',
        ]);
    }

    public function test_project_wizard_step_five_attaches_selected_funding_source(): void
    {
        $this->withoutMiddleware();

        $user = User::factory()->create();
        $project = Project::factory()->create(['wizard_step' => 'step5']);
        $fundingSource = FundingSource::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.projects.store.step5', $project), [
            'fundings' => [
                $fundingSource->id => [
                    'selected' => '1',
                    'funding_source_id' => (string) $fundingSource->id,
                    'allocated_amount' => '2500.50',
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.projects.review', $project));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('project_funding_source', [
            'project_id' => $project->id,
            'funding_source_id' => $fundingSource->id,
            'allocated_amount' => 2500.50,
            'status' => 'active',
        ]);
        $this->assertSame('review', $project->fresh()->wizard_step);
    }
}
