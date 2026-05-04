<?php

namespace App\Services;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    public function createUser(array $data): User
    {
        $unit = ! empty($data['unit_id'])
            ? Unit::query()->withoutGlobalScopes()->find($data['unit_id'])
            : null;

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'unit_id' => $data['unit_id'] ?? null,
            'institution_id' => $unit?->institution_id ?? ($data['institution_id'] ?? null),
        ]);

        if (! empty($data['role'])) {
            $role = Role::where('name', $data['role'])->first();

            if ($role) {
                $user->syncRoles([$role]);
            }
        }

        if ($user->institution_id) {
            $user->institutions()->syncWithoutDetaching([
                $user->institution_id => ['active' => true],
            ]);
        }

        return $user;
    }

    public function updateUser(User $user, array $data): User
    {
        $unitId = Arr::get($data, 'unit_id', $user->unit_id);
        $unit = $unitId
            ? Unit::query()->withoutGlobalScopes()->find($unitId)
            : null;

        $user->update([
            'name' => Arr::get($data, 'name', $user->name),
            'email' => Arr::get($data, 'email', $user->email),
            'unit_id' => $unitId,
            'institution_id' => $unit?->institution_id ?? Arr::get($data, 'institution_id', $user->institution_id),
        ]);

        if (! empty($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        if (! empty($data['role'])) {
            $role = Role::where('name', $data['role'])->first();

            if ($role) {
                $user->syncRoles([$role]);
            }
        }

        if ($user->institution_id) {
            $user->institutions()->syncWithoutDetaching([
                $user->institution_id => ['active' => true],
            ]);
        }

        return $user;
    }
}
