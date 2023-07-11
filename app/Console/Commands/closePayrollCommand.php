<?php

namespace App\Console\Commands;

use App\Models\Payroll;
use Illuminate\Console\Command;

class closePayrollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-payroll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close current payroll and create a new one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $current = Payroll::getCurrent();
        Payroll::create([
            'start_date' => $current->start_date->addDays(7)->toDateString(),
            'week' => today()->weekOfYear,
        ]);

        $current->update(['is_active' => 0]);
    }
}
