<?php

namespace App\Console\Commands;

use App\Models\Machine;
use App\Models\User;
use App\Notifications\CheckMachinesMaintenanceNotification;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\Log;

class CheckMachinesMaintenance extends Command
{
    protected $signature = 'app:machines-maintenance';
    protected $description = 'Check machines that need maintenance';

    public function handle()
    {
        $machines = Machine::select('machines.*')
            ->leftJoinSub(function ($query) {
                $query->from('maintenances')
                    ->select('machine_id', new Expression('MAX(created_at) as last_maintenance_date'))
                    ->groupBy('machine_id');
            }, 'latest_maintenance', 'latest_maintenance.machine_id', '=', 'machines.id')
            ->where(function ($query) {
                $query->whereNull('latest_maintenance.last_maintenance_date')
                    ->orWhere('latest_maintenance.last_maintenance_date', '<=', new Expression('DATE_SUB(CURDATE(), INTERVAL machines.days_next_maintenance DAY)'));
            })
            ->get();

        if ($machines->isEmpty()) {
            $this->info('No machines need maintenance.');
        } else {
            $super_admins = User::whereNull('employee_properties')->get();
            $others = User::whereIn('employee_properties->department', ['Ingeniería', 'Mantenimiento'])->where('is_active', 1)->get();

            // notify users
            foreach ($others as $other) {
                $other->notify(new CheckMachinesMaintenanceNotification($machines));
            }
            foreach ($super_admins as $super) {
                $super->notify(new CheckMachinesMaintenanceNotification($machines));
            }
        }
        Log::info('app:machines-maintenance executed successfully. #Machines:' . $machines->count());
    }
}
