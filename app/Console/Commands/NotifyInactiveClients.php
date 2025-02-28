<?php

namespace App\Console\Commands;

use App\Models\CompanyBranch;
use App\Models\User;
use App\Notifications\InactiveClientsNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyInactiveClients extends Command
{
    protected $signature = 'app:inactive-clients';
    protected $description = 'Notify sales users about inactive clients';

    public function handle()
    {
        // Obtener todos los vendedores activos
        $salesUsers = $this->getSalesUsers();

        // Iterar sobre cada vendedor para notificarles sobre sus clientes inactivos
        foreach ($salesUsers as $user) {
            // Obtener los clientes inactivos del vendedor actual
            $inactiveClients = $this->getInactiveClientsForUser($user);

            // Si hay clientes inactivos, enviar notificación al vendedor
            if ($inactiveClients->count()) {
                $user->notify(new InactiveClientsNotification($inactiveClients));
                $this->info('Inactive clients notification sent successfully to user: ' . $user->name);
                Log::info('Inactive clients notification sent to user ' . $user->name . '. There were ' . $inactiveClients->count() . ' inactive client(s)');
            }
        }

        $this->info('Inactive clients notification process completed.');
    }

    protected function getSalesUsers()
    {
        return User::whereIn('id', [1, 2, 3])->orWhere('employee_properties->department', 'Ventas')->get();
    }

    protected function getInactiveClientsForUser(User $user)
    {
        $inactiveClients = collect();

        // Obtener todas las sucursales del vendedor
        $companies = $user->companiesAsSeller ?? [];

        // Iterar sobre cada cliente
        foreach ($companies as $company) {
            $companyBranches = $company->companyBranches;
            
            // Iterar sobre cada sucursal y verificar la inactividad
            foreach ($companyBranches as $branch) {
                $days_to_reactivate = $branch->days_to_reactivate;
    
                // Verificar la inactividad de la sucursal actual
                if (
                    $days_to_reactivate > 0 &&
                    !$this->hasRecentActivity($branch, $days_to_reactivate)
                ) {
                    $inactiveClients->push($branch);
                }
            }
        }

        return $inactiveClients;
    }

    protected function hasRecentActivity(CompanyBranch $customerBranch, $days_to_reactivate)
    {
        $threshold = now()->subDays($days_to_reactivate);

        return $customerBranch->quotes()->whereDate('created_at', '>=', $threshold)->exists()
            || $customerBranch->sales()->whereDate('created_at', '>=', $threshold)->exists()
            || $customerBranch->samples()->whereDate('created_at', '>=', $threshold)->exists();
    }
}
