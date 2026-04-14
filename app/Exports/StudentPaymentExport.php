<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentPaymentExport implements FromCollection
{
    public function __construct (public Collection $payments)
    {
        $this->payments = $payments;
    }

    public function collection()
    {
        return $this->payments->map(function ($p) {
            return [
                'Aluno' => $p->student->name,
                'Turma' => $p->classOffering->name,
                'Valor' => $p->amount,
                'Status' => $p->status,
                'Mês' => $p->month,
                'Ano' => $p->year,
            ];
        });
    }
}