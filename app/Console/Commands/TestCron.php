<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestCron extends Command
{
    protected $signature = 'test:cron';
    protected $description = 'Test cron job';

    public function handle()
    {
        Log::info('Cron job executed successfully.');
    }
}
