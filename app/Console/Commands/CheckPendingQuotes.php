<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Quote;
use Carbon\Carbon;

class CheckPendingQuotes extends Command
{
    protected $signature = 'quotes:check-pending-alerts';
    protected $description = 'Activa el campo pendent_quotes_alert si el usuario tiene cotizaciones no enviadas con 3 o 5 días de antigüedad';

    public function handle()
    {
        $from = now()->subDays(5)->startOfDay(); // hace 5 días
        $to = now()->subDays(3)->endOfDay();     // hace 3 días

        // Usuarios con cotizaciones pendientes entre 3 y 5 días
        $userIdsWithPendingQuotes = Quote::whereIn('status', ['No enviada', 'Recibida por el cliente'])
            ->whereBetween('created_at', [$from, $to])
            ->pluck('user_id')
            ->unique();

        // Activar alerta a usuarios con cotizaciones pendientes
        User::whereIn('id', $userIdsWithPendingQuotes)
            ->update(['pendent_quotes_alert' => true]);

        // Desactivar alerta a los demás usuarios
        User::whereNotIn('id', $userIdsWithPendingQuotes)
            ->where('pendent_quotes_alert', true)
            ->update(['pendent_quotes_alert' => false]);

        $this->info('pendent_quotes_alert actualizado correctamente.');
    }

}

