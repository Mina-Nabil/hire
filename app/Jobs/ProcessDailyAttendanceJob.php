<?php

namespace App\Jobs;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\DailyPunch;
use App\Models\Attendance\PublicHoliday;
use App\Models\Benefits\Configurations\BenefitConfiguration;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessDailyAttendanceJob implements ShouldQueue
{
    use Queueable;

    private Carbon $targetDate;

    /**
     * Create a new job instance.
     */
    public function __construct(?Carbon $targetDate = null)
    {
        // Use provided date or default to yesterday (for processing previous day's attendance)
        $this->targetDate = $targetDate ?? Carbon::yesterday();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting daily attendance processing', [
            'target_date' => $this->targetDate->format('Y-m-d'),
            'job_started_at' => now()->format('Y-m-d H:i:s')
        ]);

        // Get all active employees using the current scope
        $activeEmployees = Employee::current($this->targetDate)
            ->where('status', Employee::STATUS_ACTIVE)
            ->whereNotNull('employment_date')
            ->where('employment_date', '<=', $this->targetDate)
            ->with(['benefitConfiguration', 'workingDays']);

        Log::debug('Employees Query', ['query' => $activeEmployees->toSql(), 'bindings' => $activeEmployees->getBindings()]);
        $activeEmployees = $activeEmployees->get();

        Log::info('Found active employees for processing', [
            'count' => $activeEmployees->count(),
            'target_date' => $this->targetDate->format('Y-m-d')
        ]);

        $processedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        foreach ($activeEmployees as $employee) {
            try {
                // Check if employee should work on this day
                // if (!$this->shouldEmployeeWorkOnDate($employee, $this->targetDate)) {
                //     $isPublicHoliday = PublicHoliday::where('date', $this->targetDate->format('Y-m-d'))->exists();
                //     Log::debug('Employee not scheduled to work on date', [
                //         'employee_id' => $employee->id,
                //         'employee_name' => $employee->name,
                //         'date' => $this->targetDate->format('Y-m-d'),
                //         'day_of_week' => $this->targetDate->format('l'),
                //         'is_public_holiday' => $isPublicHoliday,
                //         'reason' => $isPublicHoliday ? 'public_holiday' : 'not_working_day'
                //     ]);
                //     $skippedCount++;
                //     continue;
                // }

                // Process daily punches for this employee
                $this->processEmployeeDailyPunches($employee, $this->targetDate);
                
                $processedCount++;

                Log::debug('Successfully processed employee attendance', [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                    'date' => $this->targetDate->format('Y-m-d')
                ]);

            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Error processing employee attendance', [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                    'date' => $this->targetDate->format('Y-m-d'),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        Log::info('Daily attendance processing completed', [
            'target_date' => $this->targetDate->format('Y-m-d'),
            'total_employees' => $activeEmployees->count(),
            'processed' => $processedCount,
            'skipped' => $skippedCount,
            'errors' => $errorCount,
            'job_completed_at' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Check if employee should work on the given date based on their working days configuration and public holidays
     */
    private function shouldEmployeeWorkOnDate(Employee $employee, Carbon $date): bool
    {
        // First check if it's a public holiday - no one works on public holidays
        $isPublicHoliday = PublicHoliday::where('date', $date->format('Y-m-d'))->exists();
        if ($isPublicHoliday) {
            return false;
        }

        // Get the day of week (e.g., 'sunday', 'monday', etc.)
        $dayOfWeek = strtolower($date->format('l'));
        
        // Check if employee has working days configured
        if (!$employee->workingDays || $employee->workingDays->isEmpty()) {
            // If no working days configured, assume they work Monday to Friday
            return !in_array($dayOfWeek, ['friday', 'saturday']);
        }

        // Check if this day is in their working days
        return $employee->workingDays->contains('type', $dayOfWeek);
    }

    /**
     * Process daily punches for a specific employee and create attendance record
     */
    private function processEmployeeDailyPunches(Employee $employee, Carbon $date): void
    {
        // Get all punches for this employee on the target date
        $punches = DailyPunch::where('employee_id', $employee->id)
            ->whereDate('punch_time', $date)
            ->orderBy('punch_time')
            ->get();

        if ($punches->isEmpty()) {
            Log::debug('No punches found for employee on date', [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'date' => $date->format('Y-m-d')
            ]);
            return;
        }

        // Get first punch (check-in) and last punch (check-out)
        $firstPunch = $punches->first();
        $lastPunch = $punches->last();

        $startTime = $firstPunch->punch_time;
        $endTime = $punches->count() > 1 ? $lastPunch->punch_time : null;

        Log::debug('Processing punches for employee', [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'date' => $date->format('Y-m-d'),
            'total_punches' => $punches->count(),
            'first_punch' => $startTime->format('Y-m-d H:i:s'),
            'last_punch' => $endTime ? $endTime->format('Y-m-d H:i:s') : 'No end punch'
        ]);

        // Get employee's benefit configuration
        $benefitConfig = $employee->benefitConfiguration;
        if (!$benefitConfig) {
            Log::warning('Employee has no benefit configuration', [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'date' => $date->format('Y-m-d')
            ]);
            return;
        }

        // Calculate hours based on attendance calculation type
        $hours = $this->calculateWorkedHours($benefitConfig, $startTime, $endTime);

        // Determine if attendance approval is required
        $isApproved = !$benefitConfig->is_require_attendance_approval;

        // Prepare attendance record data
        $attendanceData = [
            'employee_id' => $employee->id,
            'creator_id' => 1, // System user (admin) for automated processes
            'date' => $date->format('Y-m-d'),
            'start_time' => $startTime->format('H:i'),
            'end_time' => $endTime ? $endTime->format('H:i') : null,
            'hours' => $hours,
            'extra_hours' => null, // Will be calculated later if needed
            'penalized_hours' => null, // Will be calculated during payroll
            'is_extra_hours_approved' => null,
            'is_approved' => $isApproved,
            'payroll_id' => null,
        ];

        // Save attendance using the static method
        try {
            Attendance::saveAttendance([$attendanceData]);
            
            Log::info('Successfully created attendance record from punches', [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'date' => $date->format('Y-m-d'),
                'start_time' => $attendanceData['start_time'],
                'end_time' => $attendanceData['end_time'],
                'hours' => $hours,
                'is_approved' => $isApproved
            ]);
        } catch (AppException $e) {
            Log::error('Failed to save attendance record from punches', [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'date' => $date->format('Y-m-d'),
                'error' => $e->getMessage(),
                'attendance_data' => $attendanceData
            ]);
            throw $e;
        }
    }

    /**
     * Calculate worked hours based on attendance calculation type and punch times
     */
    private function calculateWorkedHours(BenefitConfiguration $benefitConfig, Carbon $startTime, ?Carbon $endTime): float
    {
        $attendanceType = $benefitConfig->attendance_calculation;

        // For IN_ONLY type, use daily working hours regardless of punch times
        if ($attendanceType === BenefitConfiguration::ATTENDANCE_CALCULATION_IN_ONLY) {
            return $benefitConfig->daily_working_hours ?? 8;
        }

        // For other types, calculate based on time difference if we have both punches
        if ($endTime && $startTime) {
            $hours = $startTime->diffInHours($endTime, true);
            return round($hours, 2);
        }

        // If we only have start time, default to daily working hours
        return $benefitConfig->daily_working_hours ?? 8;
    }
} 