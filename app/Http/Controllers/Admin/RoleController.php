<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use App\Services\RoleAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(private PermissionService $permissionService) {}

    public function index()
    {
        $user = Auth::user();
        $hierarchy = Role::pluck('level', 'name')->toArray();
        $currentUserLevel = $user->roles->pluck('level')->max() ?? 0;

        $query = Role::with('permissions')
            ->orderBy('level', 'desc')
            ->orderBy('name', 'asc');

        if ($currentUserLevel < max($hierarchy)) {
            $query->where('level', '<=', $currentUserLevel);
        }

        $roles = $query->paginate(10);
        $lockedRoles = ['admin', 'superadmin'];
        $permissions = Permission::all();

        return view('admin.roles.index', compact('roles', 'permissions', 'lockedRoles'));
    }

    public function create()
    {
        $permissionsByCategory = $this->permissionService->getPermissionsByCategory();

        return view('admin.roles.create', compact('permissionsByCategory'));
    }

    public function show(Role $role)
    {
        abort_unless(auth()->user()->can('roles.view'), 403);

        $rolePermissions = $role->permissions;
        $rolePermissionsByCategory = $this->permissionService->getRolePermissionsByCategory($role);
        $permissionCount = $this->permissionService->getRolePermissionCount($role);

        return view('admin.roles.show', compact('role', 'rolePermissions', 'rolePermissionsByCategory', 'permissionCount'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'level' => 'required|integer|min:0|max:100',
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'level' => $request->integer('level'),
            'guard_name' => 'web',
        ]);

        $this->permissionService->syncRolePermissions($role, $request->permissions ?? []);
        RoleAuditService::log('created', $role, [
            'level' => $role->level,
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Função criada com sucesso!');
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        RoleAuditService::log('deleted', $role, [
            'name' => $role->name,
            'level' => $role->level,
            'permissions' => $role->permissions->pluck('name')->toArray(),
        ]);

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Função removida com sucesso!');
    }

    public function edit(Role $role)
    {
        $this->authorize('update', $role);

        $permissionsByCategory = $this->permissionService->getRolePermissionsByCategory($role);
        $permissionCount = $this->permissionService->getRolePermissionCount($role);
        $permissionPercentage = $this->permissionService->getRolePermissionPercentage($role);

        return view('admin.roles.edit', compact('role', 'permissionsByCategory', 'permissionCount', 'permissionPercentage'));
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        $request->validate([
            'level' => 'required|integer|min:0|max:100',
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role->update(['level' => $request->integer('level')]);
        $this->permissionService->syncRolePermissions($role, $request->permissions ?? []);

        RoleAuditService::log('updated', $role, [
            'level' => $role->level,
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Permissões atualizadas com sucesso!');
    }
}
