<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HighPrioritySaleNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $sale)
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
            ->subject('OV urgente sin producción')
            ->markdown('emails.high-priority-sale', [
                'greeting' => '¡Hola!',
                'intro' => "La OV {$this->sale->id} tiene prioridad alta y no se ha generado orden de producción. Comunicalo con dpto. de producción",
                'url' => route('sales.show', $this->sale->id),
                'salutation' => 'Saludos',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "La OV <span class='text-primary'>{$this->sale->id}</span> tiene prioridad alta y no se ha generado orden de producción. Comunicalo con dpto. correspondiente.",
            'additional_info' => "",
            'module' => "sales",
        ];
    }
}
