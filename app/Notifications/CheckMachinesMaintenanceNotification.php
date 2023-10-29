<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CheckMachinesMaintenanceNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $machines)
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

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Productos con stock bajo')
            ->markdown('emails.machines-maintenance', [
                'greeting' => '¡Hola!',
                'intro' => "Hay {$this->machines->count()} maquina(s) que necesitan mantenimiento preventivo. Favor de revisar y realizarlos cuanto antes:",
                'machines' => $this->machines,
                'url' => route('machines.index'),
                'salutation' => 'Saludos',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "Hay máquina(s) que necesita(n) mantenimiento preventivo",
            'additional_info' => "",
            'module' => "machine",
        ];
    }
}
