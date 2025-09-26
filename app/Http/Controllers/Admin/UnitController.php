<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Unit;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Services\UnitService;
use App\DataTables\UnitsDataTable;

class UnitController extends Controller
{
    protected $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function index(UnitsDataTable $dataTable)
    {
        return $dataTable->render('admin.units.index');
    }

    public function create(): View
    {
        return view('admin.units.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $this->unitService->createUnit($request->all());

        return redirect()->route('admin.units.index')->with('success', 'Unidade cadastrada com sucesso!');
    }

    public function edit(Unit $unit): View
    {
        return view('admin.units.edit', compact('unit'));
    }  

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $unit->update($request->all());

        return redirect()->route('admin.units.index')->with('success', 'Unidade atualizada com sucesso!');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        $unit->delete();
        return redirect()->route('admin.units.index')->with('success', 'Unidade excluída com sucesso!');
    }
}
