<?php

namespace App\Console\Commands;

use App\Models\Sample;
use App\Notifications\SampleDevolutionNotification;
use Illuminate\Console\Command;

class CheckDevolutionDate extends Command
{
    protected $signature = 'app:sample-devolution-check';
    protected $description = 'Check samples devolution dates daily';

    public function handle()
    {
        $samples = Sample::whereDate('devolution_date', now())->get();

        foreach ($samples as $sample) {
            $folio = 'MUE-' . str_pad($sample->id, 4, "0", STR_PAD_LEFT);
            $sample->user->notify(new SampleDevolutionNotification($folio));
            $this->info("Sample ID {$sample->id} scheduled date.");
        }

        $this->info('Devolution date check completed.');
    }
}
