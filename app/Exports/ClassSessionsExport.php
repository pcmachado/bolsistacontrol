<?php

namespace App\Exports;

use App\Models\ClassOffering;
use App\Models\ClassSession;
use Maatwebsite\Excel\Concerns\FromArray;

class ClassSessionsExport implements FromArray
{
    protected ClassOffering $offering;

    public function __construct(ClassOffering $offering)
    {
        $this->offering = $offering;
    }

    public function array(): array
    {
        $sessions = ClassSession::where('class_offering_id', $this->offering->id)
            ->with(['discipline', 'teacher'])
            ->orderBy('date')
            ->get();

        $rows = [];
        $rows[] = ['Data', 'Disciplina', 'Professor', 'Início', 'Fim', 'Horas'];

        foreach ($sessions as $s) {
            $rows[] = [
                $s->date->format('d/m/Y'),
                $s->discipline->name,
                $s->teacher->name,
                $s->start_time,
                $s->end_time,
                $s->duration_hours
            ];
        }

        return $rows;
    }
}
