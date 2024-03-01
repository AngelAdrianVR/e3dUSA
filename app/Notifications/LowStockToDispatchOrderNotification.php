<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockToDispatchOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public $operator_name, public $product, public $folio, public $route)
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
        if (app()->environment() === 'production') {
            return ['mail'];
        }
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Requerimiento de compra')
            ->markdown('emails.default-email-template', [
                'greeting' => '¡Hola!',
                'intro' => "*$this->operator_name* ha indicado que no puede continuar la producción de *$this->product* que corresponde al folio *$this->folio* ya que no hay suficiente materia prima. Favor de revisar",
                'url' => $this->route,
                'salutation' => 'Saludos,',
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
            //
        ];
    }
}
