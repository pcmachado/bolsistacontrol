<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\DataTables\PositionsDataTable;
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
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('admin.positions.create');
    }

    /**
     * Salva um novo cargo no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => 'required|string|max:255|unique:positions,name',
        ];

        $validated = $request->validate($rules);

        $this->positionService->createPosition($validated);

        if ($request->ajax()) {
            return response()->json($position);
        }

        return redirect()->route('admin.positions.index')->with('success', 'Cargo cadastrado com sucesso!');
    }

    public function show(Position $position): View
    {
        return view('admin.positions.show', compact('position'));
    }

    /**
     * Exibe o formulário para editar um cargo existente.
     *
     * @param  \App\Models\Position  $position
     * @return \Illuminate\View\View
     */
    public function edit(Position $position): View
    {
        return view('admin.positions.edit', compact('position'));
    }

    /**
     * Atualiza um cargo no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Position $position): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $position->id,
        ]);

        $position->update($request->all());

        return redirect()->route('admin.positions.index')->with('success', 'Cargo atualizado com sucesso!');
    }

    /**
     * Remove um cargo do banco de dados.
     *
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Position $position): RedirectResponse
    {
        $position->delete();

        return redirect()->route('admin.positions.index')->with('success', 'Cargo removido com sucesso!');
    }
}