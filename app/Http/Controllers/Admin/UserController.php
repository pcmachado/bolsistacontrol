<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Unit;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\DataTables\UsersDataTable;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;

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
        return $dataTable->render('admin.users.index');
    }

    /**
     * Mostra o formulário para criação de um novo usuário.
     */
    public function create(): View
    {
        $units = Unit::all();
        $roles = Role::all();
        return view('admin.users.create', compact('units', 'roles'));
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
            'role' => 'required|string|exists:roles,name'
        ];

        // Se não for admin ou coordenador geral, exige unit_id
        if (!auth()->user()->hasRole(['admin', 'coordenador-geral'])) {
            $rules['unit_id'] = 'required|exists:units,id';
        } else {
            $rules['unit_id'] = 'nullable|exists:units,id';
        }

        $validated = $request->validate($rules);
        // Cria usuário via service
        $user = $this->userService->createUser($validated);

        return redirect()->route('admin.users.index')->with('success', 'Usuário criado com sucesso!');
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
        $units = Unit::all();
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'units', 'roles'));
    }

    /**
     * Atualiza o usuário no banco de dados.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $rules=[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|exists:roles,name'
        ];

        if (!auth()->user()->hasRole(['admin', 'coordenador-geral'])) {
            $rules['unit_id'] = 'required|exists:units,id';
        } else {
            $rules['unit_id'] = 'nullable|exists:units,id';
        }

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6|confirmed';
        }

        $validated = $request->validate($rules);


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
}
