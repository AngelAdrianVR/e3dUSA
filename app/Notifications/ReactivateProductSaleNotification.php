<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReactivateProductSaleNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $products, public $days)
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject('Productos sin movimiento')
        ->markdown('emails.reactivate-product-sale', [
            'greeting' => '¡Hola!',
            'intro' => "Hay {$this->products->count()} producto(s) que no han tenido movimiento en un periodo de $this->days días. Favor de revisar y reactivar ventas:",
            'products' => $this->products,
            'url' => route('dashboard'),
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
            //
        ];
    }
}
