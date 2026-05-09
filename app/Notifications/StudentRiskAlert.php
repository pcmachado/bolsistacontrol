<?php

namespace App\Notifications;

use App\Models\ClassOffering;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class StudentRiskAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ClassOffering $offering,
        public Collection $criticalStudents
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        $studentNames = $this->criticalStudents
            ->take(3)
            ->pluck('student_name')
            ->join(', ');

        $more = $this->criticalStudents->count() > 3
            ? ' e mais '.($this->criticalStudents->count() - 3)
            : '';

        return [
            'title' => '🚨 Alunos em Risco Crítico',
            'message' => "A turma {$this->offering->name} tem {$this->criticalStudents->count()} aluno(s) em risco crítico: {$studentNames}{$more}",
            'level' => 'danger',
            'url' => route('admin.dashboard.academic'),
            'offering_id' => $this->offering->id,
            'critical_count' => $this->criticalStudents->count(),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $studentList = $this->criticalStudents
            ->map(fn ($s) => "{$s['student_name']} ({$s['percent']}% de faltas)")
            ->join("\n");

        return (new MailMessage)
            ->subject("🚨 Alerta de Risco: {$this->offering->name}")
            ->greeting("Olá {$notifiable->name},")
            ->line("A turma **{$this->offering->name}** possui {$this->criticalStudents->count()} aluno(s) com potencial risco de evasão:")
            ->line($studentList)
            ->line('Recomendamos entrar em contato com esses alunos para acompanhamento.')
            ->action('Ver Dashboard Acadêmico', route('admin.dashboard.academic'))
            ->line('Isso é um alerta automático do sistema ProBolsas.');
    }
}
