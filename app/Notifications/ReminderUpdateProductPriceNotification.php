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
    public int $daysDifference;
    public bool $isExpired;

    public function __construct(public $event)
    {
        // Determinar si la fecha del evento ya pasó
        $this->isExpired = now()->isAfter($this->event->start_date);

        // Calcular la diferencia en días (positiva si está en el futuro, negativa si ya pasó)
        $this->daysDifference = now()->diffInDays($this->event->start_date, false);
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
        // Calcula el mensaje de introducción
        $daysElapsed = abs($this->daysDifference); // Siempre positivo para fechas pasadas
        $intro = $this->isExpired
            ? "El recordatorio <span class='text-primary'>{$this->event->title}</span> expiró hace {$daysElapsed} días."
            : "Faltan {$this->daysDifference} días para <span class='text-primary'>{$this->event->title}</span>.";

        return (new MailMessage)
            ->subject('Recordatorio de cambio de precio a producto')
            ->markdown('emails.default-email-template', [
                'greeting' => '¡Hola!',
                'intro' => $intro,
                'url' => route('calendars.index'),
                'salutation' => 'Tomar precauciones para evitar problemas con el envío. Saludos',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        // Calcula el mensaje de descripción
        $daysElapsed = abs($this->daysDifference); // Siempre positivo para fechas pasadas
        $description = $this->isExpired
            ? "El recordatorio <span class='text-primary'>{$this->event->title}</span> expiró hace {$daysElapsed} días."
            : "Faltan {$this->daysDifference} días para <span class='text-primary'>{$this->event->title}</span>.";

        return [
            'description' => $description,
            'additional_info' => "",
            'module' => "calendar",
        ];
    }
}

