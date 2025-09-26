<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * Cria um novo usuário e atribui um papel.
     *
     * @param array $data Dados do usuário (name, email, password, role)
     * @return User
     */
    public function createUser(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'] ?? 'bolsista'
        ]);

        // Associa as unidades se elas forem enviadas
        if (!empty($data['units'])) {
            $user->units()->sync($data['units']);
        }

        $user->assignRole($user->role);

        return $user;
    }

    /**
     * Atualiza um utilizador existente e sincroniza as suas unidades.
     *
     * @param User $user O utilizador a ser atualizado.
     * @param array $data Os novos dados do formulário.
     * @return User O utilizador atualizado.
     */
    public function updateUser(User $user, array $data): User
    {
        // 1. Atualiza os dados básicos do utilizador
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // 2. Sincroniza as unidades (a sua lógica)
        // O segundo argumento [] garante que se 'units' não for enviado,
        // todas as associações são removidas.
        $user->units()->sync($data['units'] ?? []);

        if (!empty($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user;
    }

    /**
     * Atualiza as unidades de um usuário existente.
     *
     * @param User $user
     * @param array $units
     * @return User
     */
    public function updateUserUnits(User $user, array $units): User
    {
        $user->units()->sync($units);
        return $user;
    }
}