<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Models\User;
use App\Notifications\ContactBirthdayNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GetContactBirthdayList extends Command
{
    protected $signature = 'app:get-contact-birthday-list';

    protected $description = "Get the list of contacts who have a birthday in exactly 7 days";

    public function handle()
    {
        // Calcular la fecha dentro de 7 días
        $targetDate = today()->addDays(7);

        // Obtener contactos que cumplen años en 7 días
        $contact_birthdays = Contact::with('contactable')
            ->where('birthdate_month', $targetDate->month - 1)
            ->where('birthdate_day', $targetDate->day)
            ->get();

        // Verificar si hay contactos con cumpleaños en 7 días
        if ($contact_birthdays->isEmpty()) {
            Log::info('No birthdays found for the target date: ' . $targetDate->toDateString());
            return;
        }

        // Usuarios a notificar
        $users = User::whereIn('id', [2,3])->get();

        // Enviar un solo correo con la lista completa de cumpleaños
        foreach ($users as $user) {
            $user->notify(new ContactBirthdayNotification($contact_birthdays));
        }

        // Registrar en el log el número de contactos notificados
        Log::info('app:get-contact-birthday-list executed successfully. ' . $contact_birthdays->count() . ' birthday(s) found for the target date: ' . $targetDate->toDateString());
    }
}
