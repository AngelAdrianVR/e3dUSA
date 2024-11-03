<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReminderUpdateProductPriceNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public int $daysRemaining;

    public function __construct(public $event)
    {
        $this->daysRemaining = now()->diffInDays($this->event->start_date);
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
            ->subject('Recordatorio de cambio de precio a producto')
            ->markdown('emails.default-email-template', [
                'greeting' => '¡Hola!',
                'intro' => "Faltan {$this->daysRemaining} días para <span class='text-primary'>{$this->event->title}</span>.",
                'url' => route('calendars.index'),
                'salutation' => 'Tomar precausiones para evitar problemas con el envío. Saludos',
            ]);
    }

    public function toArray(object $notifiable): array
    {   
        if ( $this->daysRemaining == 0 ) {
            $description = "El día de hoy es el <span class='text-primary'>{$this->event->title}</span>.";
        } else {
            $description = "Faltan {$this->daysRemaining} días para <span class='text-primary'>{$this->event->title}</span>.";
        }
        
        return [
            'description' => $description,
            'additional_info' => "",
            'module' => "calendar",
        ];
    }
}
