<?php

namespace App\Services;

use App\Models\ScholarshipHolder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScholarshipHolderService
{
    /**
     * Cria um novo usuário e atribui um papel.
     *
     * @param array $data Dados do usuário (name, email, password, role)
     * @return ScholarshipHolder
     */
    public function createScholarshipHolder(array $data): ScholarshipHolder
    {
        $scholarshipHolder = ScholarshipHolder::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        // Associa as unidades se elas forem enviadas
        if (!empty($data['units'])) {
            $scholarshipHolder->units()->sync($data['units']);
        }

        // Associa o papel (role) se estiver a usar o Spatie/Permission
        if (!empty($data['role'])) {
             $scholarshipHolder->assignRole($data['role']);
        }

        return $scholarshipHolder;
    }

    /**
     * Atualiza um utilizador existente e sincroniza as suas unidades.
     *
     * @param ScholarshipHolder $user O utilizador a ser atualizado.
     * @param array $data Os novos dados do formulário.
     * @return ScholarshipHolder O utilizador atualizado.
     */
    public function updateScholarshipHolder(ScholarshipHolder $scholarshipHolder, array $data): ScholarshipHolder
    {
        // 1. Atualiza os dados básicos do utilizador
        $scholarshipHolder->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // 2. Sincroniza as unidades (a sua lógica)
        // O segundo argumento [] garante que se 'units' não for enviado,
        // todas as associações são removidas.
        $scholarshipHolder->units()->sync($data['units'] ?? []);

        // 3. Opcional: Sincroniza o papel (role)
        if (isset($data['role'])) {
            $scholarshipHolder->syncRoles([$data['role']]);
        }

        return $scholarshipHolder;
    }
    
    /**
     * Atualiza o papel de um usuário existente.
     *
     * @param ScholarshipHolder $scholarshipHolder
     * @param string $role
     * @return ScholarshipHolder
     */
    public function updateScholarshipHolderRole(ScholarshipHolder $scholarshipHolder, string $role): ScholarshipHolder
    {
        $scholarshipHolder->setRole($role);
        $scholarshipHolder->save();

        return $scholarshipHolder;
    }

    /**
     * Atualiza as unidades de um usuário existente.
     *
     * @param ScholarshipHolder $scholarshipHolder
     * @param array $units
     * @return ScholarshipHolder
     */
    public function updateUserUnits(ScholarshipHolder $scholarshipHolder, array $units): ScholarshipHolder
    {
        $scholarshipHolder->units()->sync($units);
        return $scholarshipHolder;
    }
}