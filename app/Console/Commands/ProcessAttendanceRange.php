<?php

namespace App\Console\Commands;

use App\Jobs\ProcessDailyAttendanceJob;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessAttendanceRange extends Command
{
    /**
     * The name and signature of the console command.
     * E.g. php artisan attendance:process-range 2026-03-01 --end-date=2026-03-31
     *
     * @var string
     */
    protected $signature = 'attendance:process-range {start-date} {--end-date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process daily attendance for a date range from start date to end date (defaults to today)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            // Parse start date
            $startDate = Carbon::parse($this->argument('start-date'));
            
            // Parse end date (defaults to today if not provided)
            $endDate = $this->option('end-date')
                ? Carbon::parse($this->option('end-date'))
                : Carbon::today();

            // Validate that start date is not after end date
            if ($startDate->isAfter($endDate)) {
                $this->error("Start date ({$startDate->format('Y-m-d')}) cannot be after end date ({$endDate->format('Y-m-d')})");
                return self::FAILURE;
            }

            $this->info("Processing attendance from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");

            // Calculate total days
            $totalDays = $startDate->diffInDays($endDate) + 1;
            
            $this->info("Total days to process: {$totalDays}");

            // Create progress bar
            $progressBar = $this->output->createProgressBar($totalDays);
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% -- %message%');
            $progressBar->setMessage('Starting...');
            $progressBar->start();

            $processedCount = 0;
            $failedCount = 0;
            $currentDate = $startDate->copy();

            // Process each date in the range
            while ($currentDate->lte($endDate)) {
                $progressBar->setMessage("Processing {$currentDate->format('Y-m-d')}...");
                
                try {
                    ProcessDailyAttendanceJob::dispatch($currentDate->copy());
                    $processedCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $this->newLine();
                    $this->error("Failed to dispatch job for {$currentDate->format('Y-m-d')}: {$e->getMessage()}");
                }

                $progressBar->advance();
                $currentDate->addDay();
            }

            $progressBar->setMessage('Completed!');
            $progressBar->finish();
            $this->newLine(2);

            // Display summary
            $this->info("Summary:");
            $this->line("  • Total dates: {$totalDays}");
            $this->line("  • Successfully dispatched: {$processedCount}");
            
            if ($failedCount > 0) {
                $this->error("  • Failed: {$failedCount}");
            }

            $this->info("\nAll jobs have been dispatched to the queue.");

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
