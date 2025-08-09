<?php

namespace App\Console\Commands;

use App\Jobs\IncrementVacationBenefits;
use Illuminate\Console\Command;

class ForceIncrementVacation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:force-increment-vacation {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force increment vacation benefits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        IncrementVacationBenefits::dispatch($type);
    }
}
