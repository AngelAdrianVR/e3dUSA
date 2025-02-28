<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScheduleUpdateProductPriceReminder extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $event)
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
            ->subject('Agenda de cambio de precioa producto')
            ->markdown('emails.default-email-template', [
                'greeting' => '¡Hola!',
                'intro' => "Se ha agendado un recordatorio de cambio de precio para <span class='text-primary'>{$this->event->title}</span>.",
                'url' => route('calendars.index'),
                'salutation' => 'Saludos',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "Se ha agendado un recordatorio de cambio de precio para <span class='text-primary'>{$this->event->title}</span>.",
            'additional_info' => "",
            'module' => "calendar",
        ];
    }
}
