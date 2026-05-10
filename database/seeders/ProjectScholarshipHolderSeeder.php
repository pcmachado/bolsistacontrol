<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\Position;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectScholarshipHolderSeeder extends Seeder
{
    public function run(): void
    {
        $holders = ScholarshipHolder::with('unit')->get();
        $positions = Position::all();
        $teacherPosition = Position::where('name', 'Professor')->first();

        if ($holders->isEmpty() || $positions->isEmpty()) {
            $this->command?->warn('ProjectScholarshipHolderSeeder: bolsistas/cargos ausentes.');

            return;
        }

        foreach ($holders as $holder) {
            $institutionId = $holder->unit?->institution_id;

            $project = $institutionId
                ? Project::where('institution_id', $institutionId)->orderBy('id')->first()
                : Project::orderBy('id')->first();

            if (! $project) {
                continue;
            }

            $role = $holder->user?->getRoleNames()->first();
            $position = $this->positionForRole($role, $positions, $teacherPosition);

            $this->attachHolderToProject($project, $holder, $position);
        }

        $this->attachIfrsGeneralAdjunctCoordinatorToAllProjects();
    }

    protected function attachIfrsGeneralAdjunctCoordinatorToAllProjects(): void
    {
        $institution = Institution::where('acronym', 'ifrs')->first();
        $user = User::where('email', 'cag@ifrs.example.com')->first();
        $holder = $user?->scholarshipHolder;
        $position = Position::where('name', 'Coordenador Adjunto Geral')->first();

        if (! $institution || ! $holder || ! $position) {
            $this->command?->warn('ProjectScholarshipHolderSeeder: vínculo do CAG IFRS não pôde ser criado.');

            return;
        }

        Project::where('institution_id', $institution->id)
            ->orderBy('id')
            ->get()
            ->each(fn (Project $project) => $this->attachHolderToProject($project, $holder, $position));
    }

    protected function positionForRole(?string $role, $positions, ?Position $teacherPosition): Position
    {
        $positionName = match ($role) {
            'coordenador_geral' => 'Coordenador Geral',
            'coordenador_adjunto_geral' => 'Coordenador Adjunto Geral',
            'coordenador_adjunto' => 'Coordenador Adjunto',
            'supervisor' => 'Supervisor',
            'professor' => 'Professor',
            'apoio_administrativo' => 'Apoio Administrativo',
            default => 'Bolsista',
        };

        return $positions->firstWhere('name', $positionName)
            ?? $teacherPosition
            ?? $positions->first();
    }

    protected function attachHolderToProject(Project $project, ScholarshipHolder $holder, Position $position): void
    {
        $project->scholarshipHolders()->syncWithoutDetaching([
            $holder->id => [
                'position_id' => $position->id,
                'weekly_workload' => 20,
                'edital_portaria' => 'Portaria XYZ/2026',
                'start_date' => now()->startOfYear()->toDateString(),
                'end_date' => null,
                'status' => 'active',
            ],
        ]);
    }
}
