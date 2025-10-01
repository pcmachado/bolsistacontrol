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
        ]);

        // Atribui role
        if (!empty($data['role'])) {
            $user->assignRole($data['role']);
        }

        // Atribui units (many-to-many)
        if (!empty($data['unit'])) {
            $user->unit()->sync($data['unit']);
        }

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
        // Atualiza dados básicos
        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        // Atualiza senha se enviada
        if (!empty($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        // Sincroniza role
        if (!empty($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        // Sincroniza unidade
        if (!empty($data['unit'])) {
            $user->unit()->associate($data['unit']);
        } else {
            $user->unit()->dissociate();
        }

        return $user;
    }

    public function getUsersWithUnits()
    {
        $user = Auth::user();

        // Se for admin ou coordenador geral → vê todas as unidades
        if ($user->hasRole(['admin','coordenador-geral'])) {
            return User::with('units','roles')->get();
        }

        // Caso contrário → apenas usuários da(s) mesma(s) unidade(s)
        return User::whereHas('units', function($q) use ($user) {
            $q->whereIn('units.id', $user->units->pluck('id'));
        })->with('units','roles')->get();
    }
}