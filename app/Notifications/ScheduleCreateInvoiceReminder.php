<?php

namespace App\Notifications;

use App\Models\Calendar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ScheduleCreateInvoiceReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public $reminder;

    public function __construct(Calendar $reminder)
    {
        $this->reminder = $reminder;
    }

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
                'intro' => "Se ha agendado un recordatorio de cambio de precio para <span class='text-primary'>{$this->reminder->title}</span>.",
                'url' => route('calendars.index'),
                'salutation' => 'Saludos',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "Se ha agendado un recordatorio de cambio de precio para <span class='text-primary'>{$this->reminder->title}</span>.",
            'additional_info' => "",
            'module' => "calendar",
        ];
    }

    // public function via($notifiable)
    // {
    //     return ['mail', 'database']; // Puedes quitar 'mail' si solo quieres notificaciones in-app
    // }

    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //         ->subject('Programar factura')
    //         ->line($this->reminder->description)
    //         ->line('Título: ' . $this->reminder->title)
    //         ->line('Fecha: ' . $this->reminder->start_date)
    //         ->line('Hora: ' . $this->reminder->start_time)
    //         ->action('Ver recordatorio', url('/calendars')) // cambia la URL si aplica
    //         ->line('Gracias por usar el sistema.');
    // }

    // public function toArray($notifiable)
    // {
    //     return [
    //         'title' => $this->reminder->title,
    //         'start_date' => $this->reminder->start_date,
    //         'start_time' => $this->reminder->start_time,
    //         'calendar_id' => $this->reminder->id,
    //     ];
    // }
}

