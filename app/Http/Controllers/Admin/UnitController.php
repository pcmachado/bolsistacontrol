<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\UnitsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Unit;
use App\Services\UnitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
        $institution = $this->currentInstitution();

        return view('admin.units.create', compact('institution'));
    }

    public function store(Request $request): RedirectResponse
    {
        $institution = $this->currentInstitution();
        $validated = $this->validatedData($request);
        $validated['institution_id'] = $institution->id;

        $this->unitService->createUnit($validated);

        return redirect()->route('admin.units.index')->with('success', 'Unidade cadastrada com sucesso!');
    }

    public function show(Unit $unit): View
    {
        $this->ensureUnitBelongsToCurrentInstitution($unit);

        $unit->load('institution');

        return view('admin.units.show', compact('unit'));
    }

    public function edit(Unit $unit): View
    {
        $institution = $this->currentInstitution();
        $this->ensureUnitBelongsToCurrentInstitution($unit, $institution->id);

        return view('admin.units.edit', compact('unit', 'institution'));
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $institution = $this->currentInstitution();
        $this->ensureUnitBelongsToCurrentInstitution($unit, $institution->id);

        $validated = $this->validatedData($request);
        $validated['institution_id'] = $institution->id;

        $unit->update($validated);

        return redirect()->route('admin.units.index')->with('success', 'Unidade atualizada com sucesso!');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        $this->ensureUnitBelongsToCurrentInstitution($unit);

        $unit->delete();

        return redirect()->route('admin.units.index')->with('success', 'Unidade excluída com sucesso!');
    }

    protected function currentInstitution(): Institution
    {
        $institutionId = Auth::user()?->resolvedInstitutionId();

        abort_unless($institutionId, 403, 'Nenhum contexto institucional ativo foi encontrado.');

        return Institution::query()->findOrFail($institutionId);
    }

    protected function ensureUnitBelongsToCurrentInstitution(Unit $unit, ?int $institutionId = null): void
    {
        $institutionId ??= $this->currentInstitution()->id;

        abort_unless((int) $unit->institution_id === (int) $institutionId, 403);
    }

    protected function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'shortname' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'domain' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:255',
            'is_administrative' => 'nullable|boolean',
        ]);

        $validated['is_administrative'] = $request->boolean('is_administrative');

        return $validated;
    }
}
