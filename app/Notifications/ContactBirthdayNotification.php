<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactBirthdayNotification extends Notification
{
    use Queueable;

    protected $contacts;

    public function __construct($contacts)
    {
        $this->contacts = $contacts;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $contactList = '';

        // Construir la lista de contactos
        foreach ($this->contacts as $contact) {
            $contactList .= "Nombre: {$contact->name}, Email: {$contact->email}\n";
        }

        return (new MailMessage)
            ->subject('Cumpleaños de Contactos en los próximos 7 días')
            ->markdown('emails.customers-birthday-template', [
                'greeting' => '¡Hola!',
                'intro' => "Estos son los contactos de clientes registrados que cumplen años en los próximos 7 días:",
                'contactList' => $contactList,
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
