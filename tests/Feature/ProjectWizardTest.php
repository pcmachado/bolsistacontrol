<?php

namespace Tests\Feature;

use App\Models\DocumentTemplate;
use App\Models\Institution;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectWizardTest extends TestCase
{
    use RefreshDatabase;

    public function test_wizard_step_one_creates_project_with_user_institution_and_unit(): void
    {
        $institution = Institution::factory()->create();
        $alternateInstitution = Institution::factory()->create();
        $unit = Unit::factory()->create(['institution_id' => $institution->id]);
        $user = User::factory()->create([
            'institution_id' => $institution->id,
        ]);
        $coordinatorRole = Role::firstOrCreate(['name' => 'coordenador_geral']);
        $user->assignRole($coordinatorRole);

        $response = $this->actingAs($user)
            ->post(route('admin.projects.store.step1'), [
                'name' => 'Projeto Teste',
                'unit_id' => $unit->id,
                'institution_id' => $alternateInstitution->id,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonth()->format('Y-m-d'),
            ]);

        $response->assertRedirect();

        $project = Project::first();

        $this->assertNotNull($project);
        $this->assertSame('Projeto Teste', $project->name);
        $this->assertSame($institution->id, $project->institution_id);
        $this->assertSame($unit->id, $project->unit_id);
        $this->assertSame(Project::STATUS_DRAFT, $project->status);
        $this->assertSame('step2', $project->wizard_step);
    }

    public function test_project_update_saves_report_header_footer_and_template(): void
    {
        $institution = Institution::factory()->create();
        $unit = Unit::factory()->create(['institution_id' => $institution->id]);
        $template = DocumentTemplate::create([
            'key' => 'project_report',
            'name' => 'Relatório de Projeto',
            'description' => 'Template de relatório',
            'body_html' => '<p>Corpo do relatório</p>',
            'active' => true,
        ]);
        $project = Project::factory()->create([
            'institution_id' => $institution->id,
            'unit_id' => $unit->id,
        ]);
        $user = User::factory()->create(['institution_id' => $institution->id]);
        $coordinatorRole = Role::firstOrCreate(['name' => 'coordenador_geral']);
        $user->assignRole($coordinatorRole);

        $response = $this->actingAs($user)
            ->put(route('admin.projects.update', $project), [
                'name' => 'Projeto Atualizado',
                'description' => 'Descrição atualizada',
                'unit_id' => $unit->id,
                'institution_id' => $institution->id,
                'monthly_report_template_id' => $template->id,
                'report_header_html' => '<header>Logo do Projeto</header>',
                'report_footer_html' => '<footer>Rodapé do Projeto</footer>',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonth()->format('Y-m-d'),
            ]);

        $response->assertRedirect(route('admin.projects.index'));

        $project->refresh();

        $this->assertSame('Projeto Atualizado', $project->name);
        $this->assertSame($template->id, $project->monthly_report_template_id);
        $this->assertSame('<header>Logo do Projeto</header>', $project->report_header_html);
        $this->assertSame('<footer>Rodapé do Projeto</footer>', $project->report_footer_html);
    }
}
