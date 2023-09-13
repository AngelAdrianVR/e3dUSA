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
        \App\Console\Commands\closePayrollCommand::class,
        \App\Console\Commands\TestCron::class,
    ];
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:close-payroll')->weekly()->fridays()->at('00:00');
        $schedule->command('app:increase-product-price')->weekly();
        $schedule->command('app:reactivate-product-sale')->weekly();
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
