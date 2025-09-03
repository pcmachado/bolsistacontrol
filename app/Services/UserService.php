<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
            'role' => $data['role'],
        ]);

        return $user;
    }
    
    /**
     * Atualiza o papel de um usuário existente.
     *
     * @param User $user
     * @param string $role
     * @return User
     */
    public function updateUserRole(User $user, string $role): User
    {
        $user->setRole($role);
        $user->save();

        return $user;
    }
}