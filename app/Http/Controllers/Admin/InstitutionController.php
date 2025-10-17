<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;

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

        return redirect()->route('admin.institutions.index')
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

        return redirect()->route('admin.institutions.index')
                         ->with('success', 'Instituição atualizada com sucesso!');
    }

    public function destroy(Institution $institution)
    {
        $institution->delete();

        return redirect()->route('admin.institutions.index')
                         ->with('success', 'Instituição removida com sucesso!');
    }
}
