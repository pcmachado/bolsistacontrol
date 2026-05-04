<?php

namespace App\Services;

use App\Models\ClassOffering;
use App\Models\ClassSession;
use App\Models\IntelligentAlertSetting;
use App\Notifications\IntelligentSystemAlert;
use Carbon\Carbon;

class IntelligentNotificationService
{
    /**
     * Verifica disciplinas atrasadas em relação ao plano de ensino
     */
    public function checkDisciplineDelays(ClassOffering $offering)
    {
        $settings = IntelligentAlertSetting::getSettings();

        // Regra desativada?
        if (! $settings->check_delays_enabled) {
            return;
        }

        foreach ($offering->disciplines as $d) {

            // Carga prevista
            $planned = $d->pivot->workload ?? $d->workload ?? 0;

            // Carga ministrada
            $taught = ClassSession::query()
                ->where('class_offering_id', $offering->id)
                ->where('discipline_id', $d->id)
                ->sum('duration_hours');

            $percent = $planned > 0 ? ($taught / $planned) : 0;

            // Progresso temporal (dias)
            $start = Carbon::parse($offering->start_date);
            $end = Carbon::parse($offering->end_date);
            $now = Carbon::now();

            if ($now->between($start, $end)) {
                $timeProgress = $start->diffInDays($now) / max(1, $start->diffInDays($end));
            } else {
                $timeProgress = 0;
            }

            // Critério de atraso baseado nas configurações
            $threshold = $settings->delay_percent_threshold; // ex: 0.80

            if ($percent < $timeProgress * $threshold) {

                $roles = explode(',', $settings->delay_notify_roles);

                // Enviar para todos os papéis configurados da unidade
                $recipients = $offering->unit->users()->role($roles)->get();

                foreach ($recipients as $user) {
                    $user->notify(new IntelligentSystemAlert(
                        title: 'Disciplina atrasada',
                        message: "A disciplina {$d->name} da turma {$offering->name} está atrasada no plano de ensino.",
                        level: 'warning',
                        url: route('admin.class-offerings.disciplines.dashboard', [$offering->id, $d->id])
                    ));
                }
            }
        }
    }

    /**
     * Verifica turmas sem aulas há X dias (configurável)
     */
    public function checkNoRecentClasses(ClassOffering $offering)
    {
        $settings = IntelligentAlertSetting::getSettings();

        if (! $settings->check_no_class_enabled) {
            return;
        }

        $lastClass = ClassSession::query()
            ->where('class_offering_id', $offering->id)
            ->orderByDesc('date')
            ->first();

        // Turma nunca teve aula registrada → não notifica
        if (! $lastClass) {
            return;
        }

        // Dias sem aula configurados
        $limit = $settings->no_class_days; // ex: 10

        if ($lastClass->date->lt(now()->subDays($limit))) {

            $roles = explode(',', $settings->no_class_notify_roles);

            $recipients = $offering->unit->users()->role($roles)->get();

            foreach ($recipients as $user) {
                $user->notify(new IntelligentSystemAlert(
                    title: 'Turma sem aulas recentes',
                    message: "A turma {$offering->name} não registra aulas há mais de {$limit} dias.",
                    level: 'danger',
                    url: route('admin.class-offerings.dashboard', $offering->id)
                ));
            }
        }
    }

    /**
     * Método chamado pelo cron (scheduler) para rodar todas as análises.
     */
    public function runAll()
    {
        foreach (ClassOffering::with('unit')->get() as $offering) {

            // Regras inteligentes
            $this->checkDisciplineDelays($offering);
            $this->checkNoRecentClasses($offering);

            // Aqui você pode adicionar novas regras futuramente:
            // $this->checkExcessHours($offering);
            // $this->checkUnevenDistribution($offering);
            // $this->checkMissingTeacherAssignments($offering);
        }
    }
}
