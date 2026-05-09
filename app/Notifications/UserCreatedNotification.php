<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $temporaryPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $temporaryPassword)
    {
        $this->temporaryPassword = $temporaryPassword;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bem-vindo ao Sistema de Controle de Bolsistas')
            ->greeting('Olá '.$notifiable->name.',')
            ->line('Sua conta foi criada com sucesso no Sistema de Controle de Bolsistas.')
            ->line('**Dados de acesso:**')
            ->line('**E-mail:** '.$notifiable->email)
            ->line('**Senha temporária:** '.$this->temporaryPassword)
            ->line('Por favor, faça login e altere sua senha imediatamente após o primeiro acesso.')
            ->action('Acessar Sistema', url('/login'))
            ->line('Se você não solicitou esta conta, ignore este e-mail.')
            ->salutation('Atenciosamente, Equipe do Sistema de Controle de Bolsistas');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
