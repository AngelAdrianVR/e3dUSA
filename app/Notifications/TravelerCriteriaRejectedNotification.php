<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TravelerCriteriaRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(public $user_name, public $traveler_folio, public $production_folio)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'description' => "El usuario <span class='text-primary'>$this->user_name</span> ha rechazado un criterio de aceptación de la hoja viajera con folio <span class='text-primary'>$this->traveler_folio</span> perteneciente a la producción <span class='text-primary'>$this->production_folio</span>.",
            'additional_info' => "",
            'module' => "production",
        ];
    }
}
