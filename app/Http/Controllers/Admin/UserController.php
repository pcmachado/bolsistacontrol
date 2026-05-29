<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\UsersDataTable;
use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Unit;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Support\RoleAccess;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Exibe a listagem de usuários com a Datatable.
     */
    public function index(UsersDataTable $dataTable)
    {
        $currentUser = auth()->user();
        $filters = request()->only([
            'filter_name',
            'filter_unit',
            'filter_role',
        ]);

        $unitsQuery = Unit::query()->orderBy('name');

        if ($currentUser?->isUnitScoped()) {
            $unitsQuery->whereIn('id', $currentUser->visibleUnitIds());
        } elseif ($currentUser && ! $currentUser->hasRole('superadmin') && $currentUser->activeInstitutionIds()->isNotEmpty()) {
            $unitsQuery->whereIn('institution_id', $currentUser->activeInstitutionIds());
        }

        $units = $unitsQuery->pluck('name', 'id');

        $roles = Role::query()
            ->orderBy('name')
            ->pluck('name', 'name');

        return $dataTable
            ->setFilters($filters)
            ->render('admin.users.index', compact('units', 'roles'));
    }

    /**
     * Mostra o formulário para criação de um novo usuário.
     */
    public function create(): View
    {
        $institutions = Institution::orderBy('name')->pluck('name', 'id');

        $selectedInstitution = $institutions->count() === 1
            ? $institutions->keys()->first()
            : null;

        $units = $selectedInstitution
            ? Unit::where('institution_id', $selectedInstitution)->pluck('name', 'id')
            : collect();

        $roles = Role::all();

        return view('admin.users.create', compact('units', 'roles', 'institutions', 'selectedInstitution'));
    }

    /**
     * Salva um novo usuário no banco de dados.
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|exists:roles,name',
            'institution_id' => 'required',
            'unit_id' => 'required',
        ];

        $rules['unit_id'] = 'nullable|exists:units,id';

        $validated = $request->validate($rules);

        if ($request->has('notify_user')) {
            $temporaryPassword = $this->generateTemporaryPassword();
            $validated['password'] = $temporaryPassword;
            $notify = true;
        } else {
            $notify = false;
        }

        $user = $this->userService->createUser($validated);

        if ($notify) {
            $user->notify(new \App\Notifications\UserCreatedNotification($validated['password']));
        }

        if (! $user->scholarshipHolder()->exists()) {
            return redirect()->route('admin.scholarship_holders.create', ['user_id' => $user->id])
                ->with('success', 'Usuário criado com sucesso! Agora complete os dados do bolsista.');
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function resendVerification(User $user): RedirectResponse
    {
        if ($user->hasVerifiedEmail()) {
            return back()->with('info', 'Este usuário já possui e-mail verificado.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'E-mail de verificação reenviado com sucesso.');
    }

    public function show(User $user): View
    {
        $user->load('unit', 'roles');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Exibe o formulário para edição de um usuário.
     */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $currentUser = Auth::user();
        $unitsQuery = Unit::query()->orderBy('name');

        if (! $currentUser->hasRole('superadmin') && $currentUser->activeInstitutionIds()->isNotEmpty()) {
            $unitsQuery->whereIn('institution_id', $currentUser->activeInstitutionIds());
        }

        $units = $unitsQuery->get();
        $roles = Role::query()
            ->whereIn('name', RoleAccess::assignableRoleNames($currentUser))
            ->orderBy('name')
            ->get();
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('admin.users.edit', compact('user', 'units', 'roles', 'userRoles'));
    }

    /**
     * Atualiza o usuário no banco de dados.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|string|exists:roles,name',
        ];

        if (! Auth::user()->hasRole(['admin', 'coordenador_geral'])) {
            $rules['unit_id'] = 'required|exists:units,id';
        } else {
            $rules['unit_id'] = 'nullable|exists:units,id';
        }

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6|confirmed';
        }

        $validated = $request->validate($rules);

        if (! RoleAccess::canAssignRole(Auth::user(), $validated['role'])) {
            abort(403, 'VocÃª nÃ£o pode atribuir este papel.');
        }

        $this->userService->updateUser($user, $validated);

        return redirect()->route('admin.users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove o usuário do banco de dados.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuário removido com sucesso!');
    }

    /**
     * Gera uma senha temporária segura.
     */
    private function generateTemporaryPassword(): string
    {
        return Str::random(12);
    }

    public function search(Request $request)
    {
        $term = $request->get('q');

        if (! auth()->check() || ! $term || strlen($term) < 2) {
            return response()->json([]);
        }

        $users = User::query()
            ->with('unit')
            ->when(! auth()->user()->hasRole('superadmin'), function ($query) {
                $institutionIds = auth()->user()->activeInstitutionIds();
                $unitIds = auth()->user()->visibleUnitIds();

                if ($institutionIds->isEmpty() && $unitIds->isEmpty()) {
                    return $query->whereRaw('1 = 0');
                }

                $query->where(function ($scoped) use ($institutionIds, $unitIds) {
                    if ($institutionIds->isNotEmpty()) {
                        $scoped->whereIn('institution_id', $institutionIds);
                    }

                    if ($unitIds->isNotEmpty()) {
                        if ($institutionIds->isNotEmpty()) {
                            $scoped->orWhereIn('unit_id', $unitIds);
                        } else {
                            $scoped->whereIn('unit_id', $unitIds);
                        }
                    }
                });
            })
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'email', 'unit_id']);

        return response()->json($users);
    }
}
