<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Cria um novo usuário e atribui um papel.
     *
     * @param array $data Dados do usuário (name, email, password, role, unit_id)
     * @return User
     */
    public function createUser(array $data): User
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'unit_id'  => $data['unit_id'] ?? null, // FK direta
        ]);

        // Atribui role
        if (!empty($data['role'])) {
            $role = Role::where('name', $data['role'])->first(); // busca objeto Role
            if ($role) {
                $user->syncRoles([$role]); // garante apenas uma role
            }
        }

        return $user;
    }

    /**
     * Atualiza um usuário existente e sua unidade.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): User
    {
        // Atualiza dados básicos com fallback para os valores atuais
        $user->update([
            'name'    => Arr::get($data, 'name', $user->name),
            'email'   => Arr::get($data, 'email', $user->email),
            'unit_id' => Arr::get($data, 'unit_id', $user->unit_id),
        ]);

        // Atualiza senha se enviada
        if (!empty($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        // Sincroniza role (garante apenas uma)
        if (!empty($data['role'])) {
            $role = Role::where('name', $data['role'])->first();
            if ($role) {
                $user->syncRoles([$role]);
            }
        }
        return $user;
    }
}
