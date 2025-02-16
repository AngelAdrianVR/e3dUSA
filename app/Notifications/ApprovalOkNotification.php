<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalOkNotification extends Notification
{
    use Queueable;

    public function __construct(public $concept, public $folio, public $module, public $url)
    {
        //
    }

    public function via(object $notifiable): array
    {
        if (app()->environment() === 'production') {
            return ['mail','database'];
        } else {
            return ['database'];
        }
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Solicitud Aprobada')
            ->markdown('emails.default-email-template', [
                'greeting' => '¡Hola!',
                'intro' => "$this->concept con folio/ID $this->folio se ha aprobado. Por favor, dar seguimiento oportuno y terminar a tiempo.",
                'url' => $this->url,
                'salutation' => 'Saludos,',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "Solicitud con folio/ID $this->folio se ha aprobado. Por favor, dar seguimiento oportuno y terminar a tiempo.",
            'additional_info' => "",
            'module' => "$this->module",
        ];
    }
}
