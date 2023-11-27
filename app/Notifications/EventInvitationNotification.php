<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventInvitationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $event)
    {
        //
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
            ->markdown('emails.default-email-template', [
                'greeting' => '¡Hola!',
                'intro' => "Te han invitado a un evento llamado <span class='text-primary'>{$this->event->title}</span>. Se requiere de tu respuesta.",
                'url' => route('calendars.index'),
                'salutation' => 'Saludos',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "Te han invitado a un evento llamado <span class='text-primary'>{$this->event->title}</span>. Se requiere de tu respuesta.",
            'additional_info' => "",
            'module' => "calendar",
        ];
    }
}
