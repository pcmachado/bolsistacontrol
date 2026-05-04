<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\PositionsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Services\PositionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PositionController extends Controller
{
    protected $positionService;

    public function __construct(PositionService $positionService)
    {
        $this->positionService = $positionService;
    }

    public function index(PositionsDataTable $dataTable)
    {
        return $dataTable->render('admin.positions.index');
    }

    /**
     * Exibe o formulário para criar um novo cargo.
     */
    public function create(): View
    {
        return view('admin.positions.create');
    }

    /**
     * Salva um novo cargo no banco de dados.
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => 'required|string|max:255|unique:positions,name',
            'description' => 'nullable|string',
            'is_teacher' => 'required|boolean',
        ];

        $validated = $request->validate($rules);

        $this->positionService->createPosition($validated);

        return redirect()->route('admin.positions.index')->with('success', 'Cargo cadastrado com sucesso!');
    }

    public function show(Position $position): View
    {
        return view('admin.positions.show', compact('position'));
    }

    /**
     * Exibe o formulário para editar um cargo existente.
     */
    public function edit(Position $position): View
    {
        return view('admin.positions.edit', compact('position'));
    }

    /**
     * Atualiza um cargo no banco de dados.
     */
    public function update(Request $request, Position $position): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,'.$position->id,
            'description' => 'nullable|string',
            'is_teacher' => 'required|boolean',
        ]);

        $position->update($request->all());

        return redirect()->route('admin.positions.index')->with('success', 'Cargo atualizado com sucesso!');
    }

    /**
     * Remove um cargo do banco de dados.
     */
    public function destroy(Position $position): RedirectResponse
    {
        $position->delete();

        return redirect()->route('admin.positions.index')->with('success', 'Cargo removido com sucesso!');
    }
}
