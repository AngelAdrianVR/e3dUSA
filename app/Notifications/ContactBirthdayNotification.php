<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactBirthdayNotification extends Notification
{
    use Queueable;

    public function __construct(public $contact)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Cumpleaños de contacto')
            ->markdown('emails.default-email-template', [
                'greeting' => '¡Hola!',
                'intro' => "Hoy es cumpleaños del contacto con nombre <span class='text-primary'>{$this->contact->name}</span> y correo <span class='text-primary'>{$this->contact->email}</span>. Envía felicitaciones",
                'url' => route('companies.show', ['company' => $this->contact->contactable->company_id, 'defaultTab' => 2]),
                'salutation' => 'Saludos',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
