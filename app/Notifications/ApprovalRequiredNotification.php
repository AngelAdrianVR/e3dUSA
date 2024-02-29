<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalRequiredNotification extends Notification
{
    use Queueable;

    public function __construct(public $concept, public $route_name)
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
        if (app()->environment() === 'production') {
            return ['mail'];
        }
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Solicitud de Aprobación')
            ->markdown('emails.default-email-template', [
                'greeting' => '¡Hola!',
                'intro' => "Hay una nueva $this->concept que requiere de su aprobación. Por favor, revisa la solicitud y toma las medidas necesarias.",
                'url' => route($this->route_name),
                'salutation' => 'Saludos,',
            ]);
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
