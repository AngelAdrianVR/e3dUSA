<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SchedulePartialitiesReminder extends Notification
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
            ->subject('Agenda de envío de parcialidad')
            ->markdown('emails.default-email-template', [
                'greeting' => '¡Hola!',
                'intro' => "Se ha agendado un recordatorio de envío de parcialidad para <span class='text-primary'>{$this->event->title}</span>.",
                'url' => route('calendars.index'),
                'salutation' => 'Saludos',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "Se ha agendado un recordatorio de envío de parcialidad para <span class='text-primary'>{$this->event->title}</span>.",
            'additional_info' => "",
            'module' => "calendar",
        ];
    }
}
