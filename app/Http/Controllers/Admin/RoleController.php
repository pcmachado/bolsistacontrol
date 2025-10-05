<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RoleController extends Controller
{
    public function index()
    {
        // 1. Carrega Roles com suas permissões para evitar o problema N+1
        $roles = Role::with('permissions')->orderBy('id', 'asc')->paginate(10);
        
        // 2. Carrega todas as permissões disponíveis
        $permissions = Permission::all(); 

    return view('admin.roles.index', compact('roles', 'permissions'));
    }
    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function show(Role $role)
    {
        $rolePermissions = $role->permissions;
        return view('admin.roles.show', compact('role', 'rolePermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'Role criada com sucesso!');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role removida!');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all(); // todas as permissões disponíveis
        $rolePermissions = $role->permissions->pluck('id')->toArray(); // permissões já atribuídas

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // sincroniza permissões da role
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Permissões atualizadas com sucesso!');
    }
}
