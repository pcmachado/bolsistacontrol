<?php

namespace App\Services;

use App\Models\Position;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PositionService
{
    /**
     * Cria um novo função.
     *
     * @param array $data Dados da função (name)
     * @return Position
     */
    public function createPosition(array $data): Position
    {
        $position = Position::create([
            'name' => $data['name'],
        ]);

        return $position;
    }
    
}