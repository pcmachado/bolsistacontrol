<?php

namespace App\Services;

use App\Models\Unit;

class UnitService
{
    /**
     * Cria uma nova unidade.
     *
     * @param array $data Dados da unidade
     */
    public function createUnit(array $data): Unit
    {
        return Unit::create($data);
    }
}
