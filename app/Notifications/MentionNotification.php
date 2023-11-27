<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentionNotification extends Notification
{
    use Queueable;

    public $concept;
    public $parent_name;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $task, public $additional_info, public $module)
    {
        if ($module == 'opportunities') {
            $this->concept = 'actividad';
            $this->parent_name = $task->oportunity->name;
        } else {
            $this->concept = 'tarea';
            $this->parent_name = $task->project->project_name;
        }
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
            ->subject('Mención en comentario')
            ->markdown('emails.default-email-template', [
                'greeting' => '¡Hola!',
                'intro' => "Te han mencionado en un comentario de la ". $this->concept ." <span class='text-primary'>{$this->task->title}</span> perteneciente al proyecto <span class='text-primary'>{$this->parent_name}</span>",
                'url' => route('projects.show', $this->task->project->id),
                'salutation' => 'Saludos',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "Te han mencionado en un comentario de la ". $this->concept ." <span class='text-primary'>{$this->task->title}</span> perteneciente al proyecto <span class='text-primary'>{$this->parent_name}</span>.",
            'additional_info' => "$this->additional_info",
            'module' => "$this->module",
        ];
    }
}
