<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\institution;
use App\Models\Unit;
use App\Models\Position;
use App\Models\ScholarshipHolder;
use App\Models\Course;
use App\Models\FundingSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProjectWizardController extends Controller
{
    // Passo 1: Criar Projeto
    public function createStep1()
    {
        $institutions = institution::all();
        $units = Unit::all();
        return view('admin.projects.wizard.step1', compact('institutions', 'units'));
    }

    public function storeStep1(Request $request)
    {
        $project = Project::create($request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'institution_id' => 'required|exists:institutions,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]));

        return redirect()->route('admin.projects.create.step2', $project);
    }

    // Passo 2: Definir Cargos
    public function createStep2(Project $project)
    {
        $positions = Position::all();
        return view('admin.projects.wizard.step2', compact('project', 'positions'));
    }

    public function storeStep2(Request $request, Project $project)
    {
        // Garante que veio um array de cargos
        $positions = $request->input('positions', []);

        // Vincula múltiplos cargos de uma vez
        $project->positions()->sync($positions);

        return redirect()->route('admin.projects.create.step3', $project);
    }

    // Passo 3: Vincular Bolsistas
    public function createStep3(Project $project)
    {
        $positions = $project->positions;
        $scholarshipHolders = ScholarshipHolder::all();
        return view('admin.projects.wizard.step3', compact('project', 'positions', 'scholarshipHolders'));
    }

    public function storeStep3(Request $request, Project $project)
    {
        $data = $request->input('scholarships', []);

        $syncData = [];
        foreach ($data as $item) {
            $syncData[$item['scholarship_holder_id']] = [
                'position_id'   => $item['position_id'],
                'start_date'    => $item['start_date'] ?? null,
                'end_date'      => $item['end_date'] ?? null,
                'weekly_workload' => $item['weekly_workload'] ?? null,
                'assignments'   => $item['assignments'] ?? null,
                'hourly_rate'   => $item['hourly_rate'] ?? null,
                'status'        => $item['status'] ?? null
            ];
        }

        $project->scholarshipHolders()->sync($syncData);

        return redirect()->route('admin.projects.create.step4', $project)->with('success', 'Bolsistas vinculados com sucesso!');
    }

    // Passo 4: Associar Cursos
    public function createStep4(Project $project)
    {
        $courses = Course::all();
        return view('admin.projects.wizard.step4', compact('project', 'courses'));
    }

    public function storeStep4(Request $request, Project $project)
    {
        $data = $request->input('courses', []);

        $syncData = [];
        foreach ($data as $item) {
            if (!empty($item['course_id'])) {
                $syncData[$item['course_id']] = [
                    'semester' => $item['semester'] ?? null,
                    'year'     => $item['year'] ?? null,
                    'active'   => isset($item['active']) ? (bool)$item['active'] : false,
                    'start_date' => $item['start_date'] ?? null,
                    'end_date'   => $item['end_date'] ?? null,
                    'capacity'   => $item['capacity'] ?? null,
                    'status'     => $item['status'] ?? null
                ];
            }
        }

        $project->courses()->sync($syncData);

        return redirect()->route('admin.projects.create.step5', $project)
                        ->with('success', 'Cursos vinculados ao projeto com sucesso!');
    }
    
    public function createStep5(Project $project)
    {
        // Aqui você pode carregar dados necessários para o Step 5
        // Exemplo: fontes de fomento já cadastradas
        $fundingSources = FundingSource::all();

        return view('admin.projects.wizard.step5', compact('project', 'fundingSources'));
    }

    public function storeStep5(Request $request, Project $project)
    {
        $validated = $request->validate([
            'fundings' => 'required|array',
            'fundings.*.funding_source_id' => 'required|exists:funding_sources,id',
            'fundings.*.amount' => 'required|numeric|min:0',
        ]);

        $syncData = [];
        foreach ($validated['fundings'] as $funding) {
            $syncData[$funding['funding_source_id']] = [
                'amount' => $funding['amount'],
            ];
        }

        $project->fundingSources()->syncWithoutDetaching($syncData);

        return redirect()
            ->route('admin.projects.review', $project->id)
            ->with('success', 'Fontes de fomento adicionadas com sucesso!');
    }

    public function review(Project $project)
    {
        // Carrega todas as relações necessárias
        $project->load([
            'positions',
            'scholarshipHolders',
            'courses',
            'fundingSources'
        ]);

        return view('admin.projects.wizard.review', compact('project'));
    }

    public function finalize(Project $project)
    {
        // Aqui você pode marcar o projeto como "completo"
        $project->update(['status' => 'completed']);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Projeto finalizado com sucesso!');
    }

}
