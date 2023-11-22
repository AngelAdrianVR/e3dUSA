<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Notifications\HighPrioritySaleNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyHighPrioritySales extends Command
{
    protected $signature = 'app:high-priority-sales';
    protected $description = 'Notify vendors of high priority sales without related productions';

    public function handle()
    {
        $highPrioritySales = Sale::where('is_high_priority', true)
            ->whereDoesntHave('productions')
            ->get();

        foreach ($highPrioritySales as $sale) {
            $sale->user->notify(new HighPrioritySaleNotification($sale));
        }

        $this->info('High priority sales notification sent successfully.');
        Log::info('app:high-priority-sales executed successfully. There where ' . $highPrioritySales->count() . ' sale(s)');
    }
}
