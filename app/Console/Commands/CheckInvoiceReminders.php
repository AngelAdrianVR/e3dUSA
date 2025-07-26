<?php

namespace App\Console\Commands;

use App\Models\ProgramedInvoice;
use App\Models\User;
use Illuminate\Console\Command;

class CheckInvoiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:invoice-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->startOfDay();

        $reminders = ProgramedInvoice::where('status', 'Pendiente')
            ->whereDate('reminder_date', $today)
            ->get();

        if ($reminders->count() > 0) {
            // Obtener IDs únicos de los usuarios relacionados a las facturas (si aplica)
            $userIds = $reminders->pluck('user_id')->unique();

            // Actualizar el atributo en cada usuario
            User::whereIn('id', $userIds)->update(['programmed_invoice_reminder' => true]);
        }

        $this->info('Recordatorios de facturas verificados');
    }

}
