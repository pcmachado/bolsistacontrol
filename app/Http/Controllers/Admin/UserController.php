<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\DataTables\UsersDataTable;
use App\Services\UserService;
use Yajra\DataTables\DataTables;

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
        return $dataTable->render('admin.users.users');
    }

    /**
     * Mostra o formulário para criação de um novo usuário.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Salva um novo usuário no banco de dados.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:usuario,coordenador_geral,coordenador_adjunto',
        ]);

        $this->userService->createUser($request->all());

        return redirect()->route('admin.users.index')->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Exibe o formulário para edição de um usuário.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Atualiza o usuário no banco de dados.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:bolsista,coordenador_geral,coordenador_adjunto',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

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

public function getData(Request $request)
{
    $usuarios = User::with('unit')->select(['id', 'name', 'email', 'unit_id', 'created_at']);

    return DataTables::of($usuarios)
        ->addColumn('unit', function ($user) {
            return $user->unit ? $user->unit->name : '-';
        })
        ->make(true);
}
}
