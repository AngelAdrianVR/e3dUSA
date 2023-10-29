<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventInvitationResponseNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $event, public $status, public $participant)
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
            ->markdown('emails.event-response-invitation', [
                'greeting' => '¡Hola!',
                'intro' => "El participante <span class='text-primary'>{$this->participant->name}</span> ha respondido tu invitación al evento <span class='text-primary'>{$this->event->title}</span>. Respuesta: <span class='text-primary'>{$this->status}</span>",
                'url' => route('calendars.index'),
                'salutation' => 'Saludos',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "El participante <span class='text-primary'>{$this->participant->name}</span> ha respondido tu invitación al evento <span class='text-primary'>{$this->event->title}</span>. Respuesta: <span class='text-primary'>{$this->status}</span>",
            'additional_info' => "",
            'module' => "calendar",
        ];
    }
}
