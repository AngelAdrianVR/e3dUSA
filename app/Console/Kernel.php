<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\UpdatePartNumber::class,
        \App\Console\Commands\IncreaseProductPrice::class,
        \App\Console\Commands\ReactivateProductSale::class,
        \App\Console\Commands\BackupDatabase::class,
        \App\Console\Commands\BackupMedia::class,
        \App\Console\Commands\closePayrollCommand::class,
        \App\Console\Commands\StockReposition::class,
        \App\Console\Commands\TestCron::class,
        \App\Console\Commands\CheckMachinesMaintenance::class,
        \App\Console\Commands\NotifyHighPrioritySales::class,
        \App\Console\Commands\GetContactBirthdayList::class,
        \App\Console\Commands\CheckDevolutionDate::class,
    ];
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:close-payroll')->weekly()->fridays()->at('00:00');
        $schedule->command('app:increase-product-price')->weekly();
        $schedule->command('app:reactivate-product-sale')->weekly();
        $schedule->command('app:backup-database')->daily();
        $schedule->command('app:backup-media')->weekly();
        $schedule->command('app:stock-reposition')->weekly()->fridays()->at('00:00');
        $schedule->command('app:machines-maintenance')->daily();
        $schedule->command('app:high-priority-sales')->daily();
        $schedule->command('app:get-contact-birthday-list')->daily();
        $schedule->command('app:sample-devolution-check')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
