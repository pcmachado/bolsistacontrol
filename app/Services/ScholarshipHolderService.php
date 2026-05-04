<?php

namespace App\Services;

use App\Models\ScholarshipHolder;
use App\Models\User;

class ScholarshipHolderService
{
    /**
     * Retorna uma instância de ScholarshipHolder com os dados necessários.
     */
    public function find(int $id): ?ScholarshipHolder
    {
        return ScholarshipHolder::with(['user', 'unit'])->findOrFail($id);
    }

    /**
     * Cria um novo bolsista (simplificado).
     */
    public function create(array $data): ScholarshipHolder
    {
        // Implemente a lógica de criação e criptografia de senhas (se aplicável)
        return ScholarshipHolder::create($data);
    }

    /**
     * Atualiza os dados do bolsista.
     */
    public function update(ScholarshipHolder $scholarshipHolder, array $data): bool
    {
        // O Model lida com a criptografia dos campos 'bank', 'agency', 'account'
        return $scholarshipHolder->update($data);
    }

    public function holderOrFail(User $user): ScholarshipHolder
    {
        if (! $user->scholarshipHolder) {
            abort(403, 'Usuário não é bolsista.');
        }

        return $user->scholarshipHolder;
    }

    /**
     * Restaura um bolsista excluído (Soft Delete).
     */
    public function restore(int $id): bool
    {
        $scholarshipHolder = ScholarshipHolder::withTrashed()->findOrFail($id);

        return $scholarshipHolder->restore();
    }
}
