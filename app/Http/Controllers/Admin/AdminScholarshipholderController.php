<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminScholarshipHolderController extends Controller
{
    public function index(Request $request)
    {
        $query = ScholarshipHolder::with([
            'user',
            'unit',
            'projects',
            'projects.positions',
        ]);

        // 🔒 visibilidade
        $query = app(\App\Services\VisibilityService::class)
            ->apply($query, auth()->user(), 'admin');

        // 🔎 filtros
        if ($request->filled('project_id')) {
            $query->whereHas('projects', fn ($q) => $q->where('projects.id', $request->project_id)
            );
        }

        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->filled('position_id')) {
            $query->whereHas('projects', function ($q) use ($request) {
                $q->wherePivot('position_id', $request->position_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return view('admin.scholarship_holders.impersonate', [
            'holders' => $query->paginate(20),
            'projects' => Project::pluck('name', 'id'),
            'units' => Unit::pluck('name', 'id'),
            'positions' => Position::pluck('name', 'id'),
        ]);
    }

    public function start(User $user)
    {
        session(['impersonated_by' => auth()->id()]);

        Auth::login($user);

        return redirect('/dashboard');
    }

    public function stop()
    {
        $adminId = session('impersonated_by');

        Auth::loginUsingId($adminId);

        session()->forget('impersonated_by');

        return redirect()->route('admin.dashboard');
    }

    public function show(ScholarshipHolder $scholarshipHolder)
{
    $this->authorize('view', $scholarshipHolder);

    $scholarshipHolder->load([
        'user',
        'unit',
        'projects.positions',
    ]);

    return view('admin.scholarship_holders.show', compact('scholarshipHolder'));
}

public function edit(ScholarshipHolder $scholarshipHolder)
{
    $this->authorize('update', $scholarshipHolder);

    $scholarshipHolder->load([
        'projects.positions'
    ]);

    return view('admin.scholarship_holders.edit', [
        'holder' => $scholarshipHolder,
        'projects' => Project::pluck('name','id'),
        'units' => Unit::pluck('name','id'),
        'positions' => Position::pluck('name','id'),
    ]);
}

public function update(Request $request, ScholarshipHolder $scholarshipHolder)
{
    $this->authorize('update', $scholarshipHolder);

    $data = $request->validate([
        'unit_id' => 'nullable|exists:units,id',
        'status'  => 'required|in:active,inactive',
        // outros campos se quiser editar
    ]);

    $scholarshipHolder->update($data);

    // Se quiser atualizar posição no pivot:
    // $request->input('positions_by_project') = [project_id => position_id]
    if ($request->filled('positions_by_project')) {
        foreach ($request->positions_by_project as $projectId => $positionId) {
            $scholarshipHolder->projects()->updateExistingPivot($projectId, [
                'position_id' => $positionId
            ]);
        }
    }

    return back()->with('success', 'Bolsista atualizado.');
}
}
