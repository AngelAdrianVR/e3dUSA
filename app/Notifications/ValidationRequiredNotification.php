<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ValidationRequiredNotification extends Notification
{
    use Queueable;

    public function __construct(public $concept, public $route, public $machine_name)
    {
        //
    }

    public function via(object $notifiable): array
    {
        if (app()->environment() === 'production') {
            return ['database', 'mail'];
        } else {
            return ['database'];
        }
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Solicitud de validación')
            ->markdown('emails.default-email-template', [
                'greeting' => '¡Hola!',
                'intro' => "Hay un $this->concept que requiere de tu validación. Por favor, revisa la solicitud y toma las medidas necesarias.",
                'url' => $this->route,
                'salutation' => 'Saludos,',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "Se ha registrado un mantenimiento de tipo \"Limpieza\" en la máquina <span class='text-primary'>$this->machine_name</span>. Se requiere validar el mantenimiento realizado (verificar que la limpieza haya sido efectuada correctamente).",
            'additional_info' => "",
            'module' => "machine",
        ];
    }
}
