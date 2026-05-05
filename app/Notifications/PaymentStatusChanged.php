<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public array $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->data['subject'] ?? 'Status do Pagamento Alterado')
            ->greeting($this->data['greeting'] ?? 'Olá!')
            ->line($this->data['message'] ?? 'O status de um pagamento foi alterado.');

        if (isset($this->data['action_url']) && isset($this->data['action_text'])) {
            $message->action($this->data['action_text'], $this->data['action_url']);
        }

        if (isset($this->data['details'])) {
            foreach ($this->data['details'] as $detail) {
                $message->line($detail);
            }
        }

        return $message;
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->data['title'] ?? 'Status do Pagamento Alterado',
            'message' => $this->data['message'] ?? 'O status de um pagamento foi alterado.',
            'level' => $this->data['level'] ?? 'info',
            'payment_id' => $this->data['payment_id'] ?? null,
            'old_status' => $this->data['old_status'] ?? null,
            'new_status' => $this->data['new_status'] ?? null,
            'url' => $this->data['url'] ?? null,
        ];
    }
}
