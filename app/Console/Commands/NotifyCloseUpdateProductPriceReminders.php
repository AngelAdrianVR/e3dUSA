<?php

namespace App\Console\Commands;

use App\Models\Calendar;
use App\Models\User;
use App\Notifications\ReminderUpdateProductPriceNotification;
use Illuminate\Console\Command;

class NotifyCloseUpdateProductPriceReminders extends Command
{
    protected $signature = 'app:notify-close-update-product-price-reminders';
    protected $description = 'Notify calendar reminders that are 3 days away from the scheduled date';

    public function handle()
    {
        // Calcula la fecha de 3 días desde hoy
        $dateThreeDaysAhead = now()->addDays(3)->toDateString();

        // Busca recordatorios con fecha de hoy a 3 días despues de hoy
        $today = now()->toDateString();
        $dateThreeDaysAhead = now()->addDays(3)->toDateString();

        $reminders = Calendar::whereBetween('start_date', [$today, $dateThreeDaysAhead])
            ->where('title', 'like', 'Cambiar precio%')
            ->where('status', 'Pendiente')
            ->get();

        foreach ($reminders as $reminder) {
            $user = User::find($reminder->user_id); // Asegúrate de que el modelo tenga una relación con el usuario

            if ($user) {
                // Notifica al usuario del recordatorio
                $user->notify(new ReminderUpdateProductPriceNotification($reminder));
            }
        }

        $this->info('Notificaciones de cambio de precio enviadas para recordatorios que están a 3 días de la fecha agendada.');
    }
}
