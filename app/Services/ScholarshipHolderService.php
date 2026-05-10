<?php

namespace App\Services;

use App\Models\ScholarshipHolder;
use App\Models\User;

class ScholarshipHolderService
{
    /**
     * Retorna uma instÃ¢ncia de ScholarshipHolder com os dados necessÃ¡rios.
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
        // Implemente a lÃ³gica de criaÃ§Ã£o e criptografia de senhas (se aplicÃ¡vel)
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
            abort(403, 'UsuÃ¡rio nÃ£o Ã© bolsista.');
        }

        return $user->scholarshipHolder;
    }

    public function attendanceContext(User $user, ?int $projectId = null): array
    {
        $holder = $this->holderOrFail($user);

        $projects = $holder->projects()
            ->with(['institution', 'positions'])
            ->orderBy('name')
            ->get();

        $activeProject = $projectId
            ? $projects->firstWhere('id', $projectId)
            : $projects->first();

        if ($projectId !== null && ! $activeProject) {
            abort(403, 'Projeto nÃ£o vinculado ao bolsista.');
        }

        return [
            'holder' => $holder,
            'projects' => $projects,
            'activeProject' => $activeProject,
            'activeProjectId' => $activeProject?->id,
        ];
    }

    /**
     * Restaura um bolsista excluÃ­do (Soft Delete).
     */
    public function restore(int $id): bool
    {
        $scholarshipHolder = ScholarshipHolder::withTrashed()->findOrFail($id);

        return $scholarshipHolder->restore();
    }
}
