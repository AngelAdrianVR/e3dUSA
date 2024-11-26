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
        // Fechas relevantes
        $today = now()->toDateString();
        $dateThreeDaysAhead = now()->addDays(1)->toDateString();

        // Obtener recordatorios entre hoy y 3 días adelante o pasados con estado "Pendiente"
        $reminders = Calendar::where(function ($query) use ($today, $dateThreeDaysAhead) {
                $query->whereBetween('start_date', [$today, $dateThreeDaysAhead])
                      ->orWhere('start_date', '<', $today);
            })
            ->where('title', 'like', 'Cambiar precio%')
            ->where('status', 'Pendiente')
            ->get();

        // Variable para rastrear si se encontró al menos un recordatorio importante
        $hasImportantReminder = $reminders->isNotEmpty();

        foreach ($reminders as $reminder) {
            $user = User::find($reminder->user_id); // Asegúrate de que el modelo tenga una relación con el usuario

            if ($user) {
                // Notifica al usuario del recordatorio
                $user->notify(new ReminderUpdateProductPriceNotification($reminder));
            }
        }

        // Actualizar la propiedad `has_important_reminder` en la tabla `users`
        User::query()->update(['has_important_reminder' => $hasImportantReminder]);

        // Mensaje de confirmación en la consola
        $this->info(
            $hasImportantReminder 
                ? 'Notificaciones enviadas y propiedad has_important_reminder actualizada a true.'
                : 'No se encontraron recordatorios importantes. Propiedad has_important_reminder actualizada a false.'
        );
    }
}
