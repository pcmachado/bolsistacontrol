<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class IntelligentSystemAlert extends Notification
{
    use Queueable;

    public string $title;
    public string $message;
    public string $level;
    public ?string $url;

    public function __construct(string $title, string $message, string $level = 'info', ?string $url = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->level = $level;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['database', 'mail']; // usa sua tabela notifications
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'level' => $this->level,
            'url' => $this->url,
        ];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->line($this->message);

        if (!empty($this->url)) {
            $mail->action('Ver detalhes', $this->url);
        }

        return $mail;
    }
}