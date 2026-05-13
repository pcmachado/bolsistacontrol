<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use App\Models\RoleAuditLog;
use Illuminate\Support\Facades\Auth;

class RoleAuditService
{
    public static function log(string $action, Role $role, array $metadata = []): void
    {
        RoleAuditLog::create([
            'role_id' => $role->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'metadata' => $metadata,
        ]);
    }
}
