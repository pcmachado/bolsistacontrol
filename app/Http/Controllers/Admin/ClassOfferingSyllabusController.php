<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\ClassSession;

class ClassOfferingSyllabusController extends Controller
{
    public function index(ClassOffering $offering)
    {
        // Carregar as disciplinas da turma
        $disciplines = $offering->disciplines()->withPivot('workload')->get();

        $result = [];

        foreach ($disciplines as $d) {
            // Horas previstas
            $planned = $d->pivot->workload ?? $d->workload ?? 0;

            // Horas ministradas
            $taught = ClassSession::where('class_offering_id', $offering->id)
                ->where('discipline_id', $d->id)
                ->sum('duration_hours');

            // Percentual
            $percent = $planned > 0 ? round(($taught / $planned) * 100, 1) : 0;

            // Status baseado no percentual
            $status = match (true) {
                $percent == 0          => 'Não iniciado',
                $percent < 50          => 'Em andamento',
                $percent < 100         => 'Quase concluído',
                $percent == 100        => 'Concluído',
                $percent > 100         => 'Excedido',
            };

            $result[] = [
                'discipline' => $d,
                'planned' => $planned,
                'taught' => $taught,
                'remaining' => max(0, $planned - $taught),
                'percent' => $percent,
                'status' => $status,
            ];
        }

        return view('admin.class-offerings.syllabus.index', [
            'offering' => $offering,
            'rows' => $result,
        ]);
    }
}
