<?php

namespace App\Jobs;

use App\Models\ClassOffering;
use App\Models\User;
use App\Notifications\StudentRiskAlert;
use App\Services\AcademicRiskService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyCoordinatorStudentRisk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ClassOffering $offering) {}

    public function handle(): void
    {
        $service = app(AcademicRiskService::class);

        // Análise de risco para a turma
        $riskData = $service->analyze($this->offering->id);

        // Filtra apenas alunos críticos
        $criticalStudents = $riskData->filter(fn ($r) => $r['level'] === 'critical');

        if ($criticalStudents->isEmpty()) {
            return;
        }

        // Encontra os coordenadores da turma/projeto
        $coordinators = User::whereHas('roles', function ($query) {
            $query->whereIn('name', [
                'coordenador_geral',
                'coordenador_adjunto_geral',
                'coordenador_adjunto',
            ]);
        })
            ->where('unit_id', $this->offering->course?->project?->unit_id)
            ->get();

        // Notifica cada coordenador
        foreach ($coordinators as $coordinator) {
            $coordinator->notify(new StudentRiskAlert($this->offering, $criticalStudents));
        }
    }
}
