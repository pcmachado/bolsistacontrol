<?php

namespace App\Services;

use App\Models\Unit;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnitService
{
    /**
     * Cria um novo usuário e atribui um papel.
     *
     * @param array $data Dados do usuário (name, email, password, role)
     * @return Unit
     */
    public function createUnit(array $data): Unit
    {
        $unit = Unit::create([
            'name' => $data['name'],
            'city' => $data['city'],
            'address' => $data['address'],
        ]);

        return $unit;
    }
    
}