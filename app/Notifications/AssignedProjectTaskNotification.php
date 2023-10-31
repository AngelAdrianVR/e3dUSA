<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignedProjectTaskNotification extends Notification
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
        if (app()->environment() === 'local') {
            return ['database'];
        } else {
            return ['mail', 'database'];
        }
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tarea asignada')
            ->markdown('emails.assigned-projet-task', [
                'greeting' => '¡Hola!',
                'intro' => "Te han asignado una tarea con el nombre de <span class='text-primary'>{$this->task->title}</span> perteneciente al proyecto <span class='text-primary'>{$this->task->project->project_name}</span>",
                'url' => route('projects.show', $this->task->project->id),
                'salutation' => 'Saludos',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "Te han asignado una tarea con el nombre de <span class='text-primary'>{$this->task->title}</span> perteneciente al proyecto <span class='text-primary'>{$this->task->project->project_name}</span>.",
            'additional_info' => "$this->additional_info",
            'module' => "$this->module",
        ];
    }
}
