<?php

namespace App\Exports;

use App\Models\Payment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FinancialReportExport implements FromCollection, WithHeadings
{
    protected array $filters;

    public function __construct(Request $request)
    {
        $this->filters = $request->all();
    }

    public function collection()
    {
        $query = Payment::with(['unit','project','scholarshipHolder']);

        if (!empty($this->filters['year'])) {
            $query->where('year', $this->filters['year']);
        }

        if (!empty($this->filters['month'])) {
            $query->where('month', $this->filters['month']);
        }

        if (!empty($this->filters['project'])) {
            $query->where('project_id', $this->filters['project']);
        }

        if (!empty($this->filters['unit'])) {
            $query->where('unit_id', $this->filters['unit']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->get()->map(function ($p) {
            return [
                'Bolsista' => $p->scholarshipHolder->name ?? '',
                'Projeto'  => $p->project->name ?? '',
                'Unidade'  => $p->unit->name ?? '',
                'Ano'      => $p->year,
                'Mês'      => $p->month,
                'Valor'    => $p->amount,
                'Status'   => $p->status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Bolsista',
            'Projeto',
            'Unidade',
            'Ano',
            'Mês',
            'Valor',
            'Status',
        ];
    }
}