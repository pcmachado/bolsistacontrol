<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class DataScopeService
{
    public function getScope(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [
                'institution_id' => null,
                'unit_id' => null,
                'scope' => 'none',
            ];
        }

        // ADMIN → vê tudo
        if ($user->hasRole('admin')) {
            return [
                'institution_id' => null,
                'unit_id' => null,
                'scope' => 'all',
            ];
        }

        // COORDENADOR GERAL → vê toda a instituição
        if ($user->hasRole('coordenador_geral')) {
            return [
                'institution_id' => session('institution_id'),
                'unit_id' => null,
                'scope' => 'institution',
            ];
        }

        // COORDENADOR ADJUNTO ou SUPERVISOR → vê unidade
        if ($user->hasRole(['coordenador_adjunto', 'supervisor', 'apoio_administrativo', 'orientador'])) {
            return [
                'institution_id' => session('institution_id'),
                'unit_id' => $user->unit_id,
                'scope' => 'unit',
            ];
        }

        // BOLSISTA → vê só ele mesmo
        if ($user->hasRole('bolsista')) {
            return [
                'institution_id' => session('institution_id'),
                'unit_id' => $user->unit_id,
                'scope' => 'self',
            ];
        }

        return [
            'institution_id' => null,
            'unit_id' => null,
            'scope' => 'none',
        ];
    }
}
