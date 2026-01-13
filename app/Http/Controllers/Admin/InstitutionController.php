<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class InstitutionController extends Controller
{
    public function index()
    {
        $institutions = Institution::paginate(10);
        return view('institutions.index', compact('institutions'));
    }

    public function create()
    {
        return view('institutions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'cnpj'    => 'nullable|string|max:18|unique:institutions,cnpj',
            'address' => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:100',
            'state'   => 'nullable|string|max:2',
        ]);

        Institution::create($validated);

        return redirect()->route('institutions.index')
                         ->with('success', 'Instituição criada com sucesso!');
    }

    public function show(Institution $institution)
    {
        return view('institutions.show', compact('institution'));
    }

    public function edit(Institution $institution)
    {
        return view('institutions.edit', compact('institution'));
    }

    public function update(Request $request, Institution $institution)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'cnpj'    => 'nullable|string|max:18|unique:institutions,cnpj,' . $institution->id,
            'address' => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:100',
            'state'   => 'nullable|string|max:2',
        ]);

        $institution->update($validated);

        return redirect()->route('institutions.index')
                         ->with('success', 'Instituição atualizada com sucesso!');
    }

    public function destroy(Institution $institution)
    {
        $institution->delete();

        return redirect()->route('institutions.index')
                         ->with('success', 'Instituição removida com sucesso!');
    }

    public function select(): View
    {
        $user = Auth::user();

        $institutions = $user->isAdmin()
            ? Institution::orderBy('name')->get(['institutions.id', 'institutions.name'])
            : $user->institutions()->orderBy('institutions.name')->get(['institutions.id', 'institutions.name']);

        return view('institutions.select', [
            'institutions' => $institutions,
            'active'       => session('institution_id'),
            'user'         => $user,
        ]);
    }

    public function set(Request $request): RedirectResponse
    {
        $request->validate([
            'institution_id' => 'required|exists:institutions,id',
        ]);

        $institutionId = (int) $request->input('institution_id');
        session(['institution_id' => $institutionId]);
        session()->save();

        $user = Auth::user();
        $route = $user->hasRole(['admin', 'coordenador_geral', 'coordenador_adjunto_geral', 'coordenador_adjunto'])
            ? 'admin.dashboard'
            : 'dashboard';

        return redirect()->route($route);
    }

    public function clear(): RedirectResponse
    {
        session()->forget('institution_id');
        return redirect()->route('institution.select');
    }
}
