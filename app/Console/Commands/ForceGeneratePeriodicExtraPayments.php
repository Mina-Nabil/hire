<?php

namespace App\Console\Commands;

use App\Jobs\GeneratePeriodicExtraPayments;
use Illuminate\Console\Command;

class ForceGeneratePeriodicExtraPayments extends Command
{
    protected $signature = 'app:force-generate-periodic-extra-payments';

    protected $description = 'Force generate any due periodic extra payments now';

    public function handle()
    {
        GeneratePeriodicExtraPayments::dispatch();
        $this->info('Periodic extra payments generation job has been dispatched.');
    }
}
