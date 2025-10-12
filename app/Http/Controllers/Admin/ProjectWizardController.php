<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Instituition;
use App\Models\Unit;
use App\Models\Position;
use App\Models\ScholarshipHolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProjectWizardController extends Controller
{
    // Passo 1: Criar Projeto
    public function createStep1()
    {
        $instituitions = Instituition::all();
        $units = Unit::all();
        return view('admin.projects.wizard.step1', compact('instituitions', 'units'));
    }

    public function storeStep1(Request $request)
    {
        $project = Project::create($request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'instituition_id' => 'required|exists:instituitions,id',
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
        $holders = ScholarshipHolder::all();
        return view('admin.projects.wizard.step3', compact('project', 'positions', 'holders'));
    }

    public function storeStep3(Request $request, Project $project)
    {
        foreach ($request->scholarships as $sch) {
            $project->scholarshipHolders()->create([
                'scholarship_holder_id' => $sch['holder_id'],
                'project_position_id' => $sch['position_id'],
                //'start_date' => $sch['start_date'],
                //'end_date' => $sch['end_date'] ?? null,
                //'status' => 'active',
            ]);
        }

        return redirect()->route('admin.projects.create.step4', $project);
    }

    // Passo 4: Revisão
    public function createStep4(Project $project)
    {
        $project->load('positions', 'scholarships.scholarshipHolder');
        return view('admin.projects.wizard.step4', compact('project'));
    }

    public function finish(Project $project)
    {
        $project->update(['status' => 'active']);
        return redirect()->route('admin.projects.show', $project)->with('success', 'Projeto ativado com sucesso!');
    }
}
