<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InactiveClientsNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $branches)
    {
        //
    }

    public function via(object $notifiable): array
    {
        if (app()->environment() == 'local') {
            return ['database'];
        } else {
            return ['mail', 'database'];
        }
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Clientes inactivos')
            ->markdown('emails.inactive-clients', [
                'greeting' => '¡Hola!',
                'intro' => "Se detectaron sucursales inactivas a las cuales no se les han generado cotizaciones, órdenes de venta ni muestras. A continuación se listan estas sucursales:",
                'url' => route('companies.index'),
                'branches' => $this->branches,
                'salutation' => 'Recuerda reactivar el contacto con los clientes. Saludos',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $branchesNames = implode(', ', $this->branches->map(function ($item, int $key) {
            return $item->name;
        })->all());

        return [
            'description' => "Hay sucursales inactivas. A continuación se listan los nombres: <span class='text-primary'>$branchesNames</span>. Recuerda reactivar el contacto.",
            'additional_info' => "",
            'module' => "companies",
        ];
    }
}
