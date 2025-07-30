<?php

use App\Jobs\IncrementVacationBenefits;
use App\Jobs\ProcessDailyAttendanceJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the daily attendance processing job
Schedule::job(new ProcessDailyAttendanceJob())
    ->daily()
    ->at('01:00') // Run at 1:00 AM to process previous day's attendance
    ->description('Process daily attendance records from employee punches')
    ->withoutOverlapping() // Prevent multiple instances from running simultaneously
    ->appendOutputTo(storage_path('logs/daily-attendance-processing.log'));

Schedule::job(new IncrementVacationBenefits())
    ->daily()
    ->at('02:00') // Run at 2:00 AM to increment vacation benefits
    ->description('Increment vacation benefits')
    ->withoutOverlapping() // Prevent multiple instances from running simultaneously
    ->appendOutputTo(storage_path('logs/increment-vacation-benefits.log'));

// Add a command to manually trigger the attendance processing for a specific date
Artisan::command('attendance:process {date?}', function (string $date = null) {
    $targetDate = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::yesterday();
    
    $this->info("Processing attendance for date: {$targetDate->format('Y-m-d')}");
    
    ProcessDailyAttendanceJob::dispatch($targetDate);
    
    $this->info('Daily attendance processing job has been dispatched.');
})->purpose('Manually process daily attendance for a specific date (default: yesterday)');
