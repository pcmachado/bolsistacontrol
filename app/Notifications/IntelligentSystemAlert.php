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
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => trim($this->sanitize($this->title)),
            'message' => trim($this->sanitize($this->message)),
            'level' => $this->level,
            'url' => $this->url ? trim($this->sanitize($this->url)) : null,
        ];
    }

    public function toMail($notifiable)
    {
        $title = $this->sanitize($this->title);
        $message = $this->sanitize($this->message);
        $url = $this->url ? $this->sanitize($this->url) : null;

        $mail = (new MailMessage)
            ->subject($title)
            ->line($message);

        if (!empty($url)) {
            $mail->action('Ver detalhes', $url);
        }

        return $mail;
    }

    protected function sanitize($value)
    {
        return preg_replace('/[\r\n]+/', ' ', $value);
    }
}