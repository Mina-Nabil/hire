<?php

namespace App\Console\Commands;

use App\Jobs\IncrementVacationBenefits;
use Illuminate\Console\Command;

class ForceIncrementVacation extends Command
{
    /**
     * The name and signature of the console command.
     * e.g. php artisan app:force-increment-vacation monthly Excuse
     * @var string
     */
    protected $signature = 'app:force-increment-vacation {type} {benefitName?}';

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
        $benefitName = $this->argument('benefitName');
        IncrementVacationBenefits::dispatch($type, $benefitName);
    }
}
