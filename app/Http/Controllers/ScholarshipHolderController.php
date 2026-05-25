<?php

namespace App\Http\Controllers;

use App\DataTables\ScholarshipHoldersDataTable;
use App\Models\Position;
use App\Models\ScholarshipHolder;
use App\Models\Unit;
use App\Models\User;
use App\Services\ScholarshipHolderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class ScholarshipHolderController extends Controller
{
    protected ScholarshipHolderService $scholarshipHolderService;

    public function __construct(ScholarshipHolderService $scholarshipHolderService)
    {
        $this->scholarshipHolderService = $scholarshipHolderService;
    }

    public function index(ScholarshipHoldersDataTable $dataTable)
    {
        $currentUser = auth()->user();
        $filters = request()->only([
            'filter_name',
            'filter_unit',
            'filter_position',
        ]);

        $unitsQuery = Unit::query()->orderBy('name');

        if ($currentUser?->isUnitScoped()) {
            $unitsQuery->whereIn('id', $currentUser->visibleUnitIds());
        } elseif ($currentUser && ! $currentUser->hasRole('superadmin') && $currentUser->activeInstitutionIds()->isNotEmpty()) {
            $unitsQuery->whereIn('institution_id', $currentUser->activeInstitutionIds());
        }

        $units = $unitsQuery->pluck('name', 'id');

        $positions = Position::query()
            ->orderBy('name')
            ->pluck('name', 'id');

        return $dataTable
            ->setFilters($filters)
            ->render('admin.scholarship_holders.index', compact('units', 'positions'));
    }

    public function create(Request $request): View
    {
        $user = null;
        $units = collect();

        // Se a tela foi aberta via redirecionamento com um ID de usuário:
        if ($request->has('user_id')) {
            $user = User::find($request->user_id);

            if ($user && $user->institution_id) {
                $units = Unit::where('institution_id', $user->institution_id)->pluck('name', 'id');
            }
        }

        // Se for criar do zero, traz as unidades padrão
        if ($units->isEmpty()) {
            $units = Unit::pluck('name', 'id');
        }

        $roles = Role::pluck('name', 'id');

        return view('admin.scholarship_holders.create', compact('units', 'user', 'roles'));
    }

    public function store(Request $request)
    {
        // 1. Validação
        $rules = [
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|unique:scholarship_holders,cpf|max:14',
            'email' => ['required', 'email', 'unique:scholarship_holders,email'],
            'unit_id' => 'required|exists:units,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'position' => 'required|string|max:50',
            'phone' => 'nullable|string|max:15',
            'bank' => 'nullable|string',
            'agency' => 'nullable|string',
            'account' => 'nullable|string',
            'pix_key' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ];

        if (! $request->filled('user_id')) {
            $rules['email'][] = 'unique:users,email';
        }

        $validatedData = $request->validate($rules);
        DB::beginTransaction();

        try {
            // Pega o ID do usuário do form (se veio pelo autocomplete)
            $userId = $request->user_id;

            if (! empty($userId)) {
                $user = User::findOrFail($userId);

                $validatedData['email'] = $user->email;
            } else {
                $user = User::firstWhere('email', $validatedData['email']);

                if (! $user) {
                    $user = User::create([
                        'name' => $validatedData['name'],
                        'email' => $validatedData['email'],
                        'password' => Hash::make(
                            preg_replace('/[^0-9]/', '', $validatedData['cpf'])
                        ),
                    ]);

                    $user->assignRole('bolsista');
                }

                $userId = $user->id;
            }

            $scholarshipHolderData = array_merge($validatedData, [
                'user_id' => $userId,
            ]);

            $this->scholarshipHolderService->create($scholarshipHolderData);

            DB::commit();

            return redirect()->route('admin.scholarship_holders.index')->with('success', 'Bolsista e Usuário associado cadastrados com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Erro ao cadastrar bolsista e usuário: '.$e->getMessage());
        }
    }

    /**
     * Exibe o formulário para editar um bolsista.
     */
    public function edit(ScholarshipHolder $scholarshipHolder): View
    {
        $loggedUserInstId = auth()->user()->institution_id;

        $bolsistaInstId = $scholarshipHolder->user->institution_id ?? null;

        $user = $scholarshipHolder->user;

        $institutionsIds = array_filter([$loggedUserInstId, $bolsistaInstId]);

        $units = Unit::where('institution_id', $institutionsIds)->pluck('name', 'id');

        $unitActive = $scholarshipHolder->unit;

        $roles = Role::whereNotIn('name', ['superAdmin','admin'])->pluck('name', 'id');

        return view('admin.scholarship_holders.edit', compact('scholarshipHolder', 'units', 'unitActive', 'user', 'roles'));
    }

    public function show(ScholarshipHolder $scholarshipHolder): View
    {
        $scholarshipHolder->load('unit', 'user', 'projects.positions');

        return view('admin.scholarship_holders.show', compact('scholarshipHolder'));
    }

    /**
     * Atualiza um bolsista no banco de dados.
     */
    public function update(Request $request, ScholarshipHolder $scholarshipHolder): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => ['required', 'string', 'max:14', Rule::unique('scholarship_holders')->ignore($scholarshipHolder->id)],
            'email' => [
                'required',
                'email',
                Rule::unique('scholarship_holders')->ignore($scholarshipHolder->id),
                Rule::unique('users', 'email')->ignore($scholarshipHolder->user_id),
            ],
            'unit_id' => 'required|exists:units,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'phone' => 'nullable|string|max:15',
            'bank' => 'nullable|string',
            'agency' => 'nullable|string',
            'account' => 'nullable|string',
            'pix_key' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $scholarshipHolder->update($validated);

        if ($scholarshipHolder->user) {
            $scholarshipHolder->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'unit_id' => $validated['unit_id'],
            ]);
        }

        return redirect()->route('admin.scholarship_holders.index')->with('success', 'Bolsista atualizado com sucesso!');
    }

    /**
     * Remove um bolsista do banco de dados.
     */
    public function destroy(ScholarshipHolder $scholarshipHolder): RedirectResponse
    {
        // Deleta o usuário associado para evitar dados órfãos
        $scholarshipHolder->user()->delete();
        $scholarshipHolder->delete();

        return redirect()->route('admin.scholarship_holders.index')->with('success', 'Bolsista removido com sucesso!');
    }

    public function search(Request $request)
    {
        $term = $request->get('q');

        $query = ScholarshipHolder::query()
            ->with('user');

        $query = app(\App\Services\VisibilityService::class)
            ->apply($query, auth()->user(), 'admin');

        if ($term) {
            $query->where(function ($searchQuery) use ($term) {
                $searchQuery->whereHas('user', function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                })->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('cpf', 'like', "%{$term}%");
            });
        }

        if (! $request->boolean('include_inactive')) {
            $query->where('status', 'active');
        }

        $holders = $query
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $holders->map(fn ($holder) => [
                'id' => $holder->id,
                'text' => $holder->user->name ?? $holder->name,
            ]),
        ]);
    }
}
