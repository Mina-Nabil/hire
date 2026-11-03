<?php

namespace App\Console\Commands;

use App\Models\Attendance\Attendance;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RefreshExpectedPenalties extends Command
{
    /**
     * The name and signature of the console command.
     * E.g. php artisan attendance:refresh-expected-penalties 2026-11-01 --end-date=2026-11-03
     *
     * @var string
     */
    protected $signature = 'attendance:refresh-expected-penalties {start-date} {--end-date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate expected penalties for attendance records in a date range (end date defaults to today)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $startDate = Carbon::parse($this->argument('start-date'))->startOfDay();
            $endDate = $this->option('end-date')
                ? Carbon::parse($this->option('end-date'))->startOfDay()
                : Carbon::today();

            if ($startDate->isAfter($endDate)) {
                $this->error("Start date ({$startDate->format('Y-m-d')}) cannot be after end date ({$endDate->format('Y-m-d')})");

                return self::FAILURE;
            }

            $this->info("Refreshing expected penalties from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");

            $query = Attendance::withoutGlobalScopes()
                ->whereDate('date', '>=', $startDate->toDateString())
                ->whereDate('date', '<=', $endDate->toDateString());

            $total = (clone $query)->count();
            $this->info("Attendance records to refresh: {$total}");

            if ($total === 0) {
                return self::SUCCESS;
            }

            $progressBar = $this->output->createProgressBar($total);
            $progressBar->start();

            $updated = 0;
            $failed = 0;

            $query->orderBy('id')->chunkById(200, function ($attendances) use ($progressBar, &$updated, &$failed) {
                foreach ($attendances as $attendance) {
                    try {
                        $attendance->calculateExpectedPenalty();
                        $attendance->saveQuietly();
                        $updated++;
                    } catch (\Exception $e) {
                        $failed++;
                        $this->newLine();
                        $this->error("Failed for attendance {$attendance->id} ({$attendance->date}): {$e->getMessage()}");
                    }

                    $progressBar->advance();
                }
            });

            $progressBar->finish();
            $this->newLine(2);

            $this->info('Summary:');
            $this->line("  • Updated: {$updated}");

            if ($failed > 0) {
                $this->error("  • Failed: {$failed}");

                return self::FAILURE;
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
