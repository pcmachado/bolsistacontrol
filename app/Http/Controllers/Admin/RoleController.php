<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Helper para definir hierarquia no controller também,
     * para filtrar a query. O ideal seria um Service, mas aqui funciona bem.
     */
    private function getRoleHierarchy()
    {
        return [
            'superadmin' => 100,
            'admin' => 90,
            'coordenador_geral' => 70,
            'coordenador_adjunto_geral' => 60,
            'coordenador_adjunto' => 30,
            'bolsista' => 10,
        ];
    }

    public function index()
    {
        $user = Auth::user();

        // Descobre o peso do usuário atual
        $hierarchy = $this->getRoleHierarchy();
        $currentUserWeight = $user->roles->map(fn ($r) => $hierarchy[$r->name] ?? 0)->max() ?? 0;

        // Query Base
        $query = Role::with('permissions')->orderBy('id', 'asc');

        // Se NÃO for Admin (peso < 90), filtramos a lista
        // Mostramos apenas roles que o usuário tem poder para gerenciar (peso menor)
        // OU roles de mesmo nível (apenas para visualização, sem edição)
        if ($currentUserWeight < 90) {
            // Pega nomes das roles que tem peso maior ou igual ao usuário atual
            // para EXCLUIR da lista (ou seja, ele não vê seus chefes)
            $rolesAbove = collect($hierarchy)
                ->filter(fn ($weight) => $weight >= $currentUserWeight)
                ->keys()
                ->toArray();

            $query->whereNotIn('name', $rolesAbove);
        }

        $roles = $query->paginate(10);

        // Bloqueia visualmente roles críticas na listagem de permissões globais
        $lockedRoles = ['admin', 'superadmin'];

        // Carrega permissões para o modal/view de ajuda
        $permissions = Permission::all();

        return view('admin.roles.index', compact('roles', 'permissions', 'lockedRoles'));
    }

    public function create()
    {
        $permissions = Permission::all();

        return view('admin.roles.create', compact('permissions'));
    }

    public function show(Role $role)
    {
        $this->authorize('view', $role);
        $rolePermissions = $role->permissions;

        return view('admin.roles.show', compact('role', 'rolePermissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'Função criada com sucesso!');
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Função removida com sucesso!');
    }

    public function edit(Role $role)
    {
        $this->authorize('update', $role);

        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();

        // sincroniza permissões da role
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Permissões atualizadas com sucesso!');
    }
}
