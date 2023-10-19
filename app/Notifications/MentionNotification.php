<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentionNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $task, public $additional_info, public $module)
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
        // return ['database'];
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Mención en comentario')
            ->markdown('emails.mention', [
                'greeting' => '¡Hola!',
                'intro' => "Te han mencionado en un comentario de la tarea <span class='text-primary'>{$this->task->title}</span> perteneciente al proyecto <span class='text-primary'>{$this->task->project->project_name}</span>",
                'url' => route('projects.show', $this->task->project->id),
                'salutation' => 'Saludos',
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'description' => "Te han mencionado en un comentario de la tarea <span class='text-primary'>{$this->task->title}</span> perteneciente al proyecto <span class='text-primary'>{$this->task->project->project_name}</span>.",
            'additional_info' => "$this->additional_info",
            'module' => "$this->module",
        ];
    }
}
