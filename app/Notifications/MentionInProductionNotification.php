<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentionInProductionNotification extends Notification
{
    use Queueable;

    public $concept;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $cpcs)
    {

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if (app()->environment() === 'local') {
            return ['database'];
        } else {
            return ['mail', 'database'];
        }
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Mención en comentario')
            ->markdown('emails.mention', [
                'greeting' => '¡Hola!',
                'intro' => "Te han mencionado en un comentario de la producción OP-<span class='text-primary'>{$this->cpcs->sale->id}</span>. Ingresa a la pestaña de productos para ver los comentarios",
                'url' => route('productions.show', $this->cpcs->sale->id),
                'salutation' => 'Saludos',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "Te han mencionado en un comentario de la producción <span class='text-primary'>OP-{$this->cpcs->sale->id}</span>. Ingresa a la pestaña de productos para ver los comentarios",
            'additional_info' => "",
            'module' => "production",
        ];
    }
}
