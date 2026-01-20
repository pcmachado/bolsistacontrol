<?php

namespace App\Services;

use App\Models\ScholarshipHolder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ScholarshipHolderService
{
    /**
     * Retorna uma instância de ScholarshipHolder com os dados necessários.
     *
     * @param int $id
     * @return ScholarshipHolder|null
     */
    public function find(int $id): ?ScholarshipHolder
    {
        return ScholarshipHolder::with(['user', 'unit'])->findOrFail($id);
    }

    /**
     * Cria um novo bolsista (simplificado).
     *
     * @param array $data
     * @return ScholarshipHolder
     */
    public function create(array $data): ScholarshipHolder
    {
        // Implemente a lógica de criação e criptografia de senhas (se aplicável)
        return ScholarshipHolder::create($data);
    }

    /**
     * Atualiza os dados do bolsista.
     *
     * @param ScholarshipHolder $scholarshipHolder
     * @param array $data
     * @return bool
     */
    public function update(ScholarshipHolder $scholarshipHolder, array $data): bool
    {
        // O Model lida com a criptografia dos campos 'bank', 'agency', 'account'
        return $scholarshipHolder->update($data);
    }

    /**
     * Exclui um bolsista (Soft Delete).
     *
     * @param ScholarshipHolder $scholarshipHolder
     * @return bool|null
     */
    public function delete(ScholarshipHolder $scholarshipHolder): bool|null
    {
        return $scholarshipHolder->delete();
    }

    public function holderOrFail(User $user): ScholarshipHolder
    {
        if (!$user->scholarshipHolder) {
            abort(403, 'Usuário não é bolsista.');
        }

        return $user->scholarshipHolder;
    }

    /**
     * Restaura um bolsista excluído (Soft Delete).
     *
     * @param int $id
     * @return bool
     */
    public function restore(int $id): bool
    {
        $scholarshipHolder = ScholarshipHolder::withTrashed()->findOrFail($id);
        return $scholarshipHolder->restore();
    }
}
