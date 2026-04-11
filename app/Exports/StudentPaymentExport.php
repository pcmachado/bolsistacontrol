<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentPaymentExport implements FromCollection
{
    protected $payments;

    public function __construct(Collection $payments)
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