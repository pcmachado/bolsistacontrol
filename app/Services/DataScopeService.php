<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class DataScopeService
{
    public function getScope(): array
    {
        $user = Auth::user();

        // superadmin (institution_id null) -> global
        if (!$user) return ['type' => 'guest'];

        if ($user->hasRole('superadmin')) {
            return ['type' => 'global'];
        }


        // admin/institutional roles -> institution level
        if ($user->hasRole(['admin','coordenador_geral', 'coordenador_adjunto_geral'])) {
            return ['type' => 'institution', 'institution_id' => $user->institution_id];
        }


        // unit-scoped roles
        if ($user->hasRole(['coordenador_adjunto','supervisor','apoio_administrativo','orientador'])) {
            return ['type' => 'unit', 'institution_id' => $user->institution_id, 'unit_id' => $user->unit_id];
        }


        // bolsista
        if ($user->hasRole('bolsista')) {
            return ['type' => 'user', 'institution_id' => $user->institution_id, 'user_id' => $user->id, 'scholarship_holder_id' => optional($user->scholarshipHolder)->id];
        }


        return ['type' => 'institution', 'institution_id' => $user->institution_id];
    }
}
