<?php

namespace App\Models\Attendance;

use App\Exceptions\AppException;
use App\Models\Attendance\PublicHoliday;
use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Payrolls\AppliedVacation;
use App\Models\Benefits\Vacations\VacationBenefit;
use App\Models\Benefits\Vacations\GainedVacation;
use App\Models\Personel\Employee;
use App\Models\Users\AppLog;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Attendance extends Model
{

    protected $fillable = [
        'employee_id',
        'creator_id',
        'date',
        'start_time',
        'end_time',
        'hours',
        'extra_hours',
        'penalized_hours',
        'is_extra_hours_approved',
        'is_approved',
        'payroll_id',
    ];

    const MORPH_NAME = 'attendance';

    protected static function booted()
    {
        static::addGlobalScope('managerAccessibleAttendance', function ($builder) {
            $builder->orderBy('date', 'desc');
            $user = Auth::user();

            // If no user is logged in or if they are admin, don't restrict
            if (!$user || $user->is_admin) {
                return;
            }

            // If user is HR, restrict to employees in their assigned locations
            if ($user->is_hr) {
                // Get the HR user's assigned location IDs
                $locationIds = $user->assignedLocations()->pluck('locations.id')->toArray();

                // Only apply filter if the user has assigned locations
                if (!empty($locationIds)) {
                    $builder->whereHas('employee.position', function ($query) use ($locationIds) {
                        $query->whereIn('location_id', $locationIds);
                    });
                }
                return;
            }

            // If user is a manager (has employees reporting to them)
            $userEmployee = Employee::where('user_id', $user->id)->first();
            if ($userEmployee && $userEmployee->is_manager) {
                // Get attendance records of employees who have this manager as their manager
                $builder->where(function ($q) use ($userEmployee) {
                    $q->where('employee_id', $userEmployee->id)
                        ->orwhereHas('employee.benefitConfiguration', function ($query) use ($userEmployee) {
                            $query->where('manager_id', $userEmployee->id);
                        });
                });
            } else {
                // Regular employee can only see their own attendance
                $builder->where(function ($query) use ($user, $userEmployee) {
                    if ($userEmployee) {
                        $query->where('employee_id', $userEmployee->id);
                    } else {
                        // Force no results if the user doesn't have an employee record
                        $query->where('employee_id', -1);
                    }
                });
            }
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the payroll this attendance record is associated with
     */
    public function payroll()
    {
        return $this->belongsTo(\App\Models\Benefits\Payrolls\Payroll::class);
    }

    ////static functions
    public static function downloadTemplate()
    {
        $employees = Employee::current()->get();
        $template_file = resource_path('sheets/AttendanceSheet.xlsx');
        $template = IOFactory::load($template_file);
        if (!$template) {
            throw new AppException('Failed to read template file');
        }
        $newFile = $template->copy();

        $employees_sheet = $newFile->getSheet(1);
        $i = 2; //start from 2 because the first row is the header
        foreach ($employees as $employee) {
            $employees_sheet->setCellValue('A' . $i, $employee->name);
            $i++;
        }
        $writer = new Xlsx($newFile);
        $file_path = "attendance_template_" . now()->format('Y-m-d') . ".xlsx";
        $public_file_path = storage_path($file_path);
        $writer->save($public_file_path);

        AppLog::info('Attendance Template Downloaded', ['file_path' => $file_path]);
        return response()->download($public_file_path)->deleteFileAfterSend(true);
    }

    public static function getUploadedAttendance($file)
    {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        $attendance = [];
        for ($row = 2; $row <= $highestRow; $row++) {

            $employeeName = trim($sheet->getCell('A' . $row)->getValueString());
            if (!$employeeName) {

                continue;
            }

            if (!$sheet->getCell('B' . $row)->getValue() || !$sheet->getCell('C' . $row)->getValue()) {
                $attendance[] = [
                    'employee_id' => "Not Found",
                    'employee' => null,
                    'uploaded_name' => $employeeName,
                    'attendance_type' => "N/A",
                    'date' => null,
                    'start_time' => null,
                    'end_time' => null,
                    'hours' => null,
                    'extra_hours' => null,
                    'is_extra_hours_approved' => null,
                    'is_approved' => null,
                    "error" => true,
                    'creator_id' => Auth::id(),
                ];
            } //if the start time is empty, skip the row

            $attendanceDay = new Carbon(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sheet->getCell('B' . $row)->getValue()));
            $attendanceStartDate = new Carbon(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sheet->getCell('C' . $row)->getValue()));
            $attendanceEndDate = $sheet->getCell('D' . $row)->getValue() ? new Carbon(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sheet->getCell('D' . $row)->getValue())) : null;

            $extraHours = $sheet->getCell('E' . $row)->getValue();

            $attendanceStartDate = $attendanceDay->copy()->setTimeFromTimeString($attendanceStartDate->format('H:i'));
            $attendanceEndDate = $attendanceDay->copy()->setTimeFromTimeString($attendanceEndDate->format('H:i'));

            $employee = Employee::where('name', $employeeName)->first();
            if (!$employee) {
                $attendance[] = [
                    'employee_id' => "Not Found",
                    'employee' => null,
                    'uploaded_name' => $employeeName,
                    'attendance_type' => "N/A",
                    'date' => $attendanceStartDate->format('Y-m-d'),
                    'start_time' => $attendanceStartDate->format('H:i'),
                    'end_time' => $attendanceEndDate ? $attendanceEndDate->format('H:i') : null,
                    'hours' => null,
                    'extra_hours' => null,
                    'is_extra_hours_approved' => null,
                    'is_approved' => null,
                    "error" => true,
                    'creator_id' => Auth::id(),
                ];
                continue;
            }
            if (!$employee->benefitConfiguration || !$employee->benefitConfiguration->attendance_calculation) {
                $attendance[] = [
                    'employee_id' => "Not Found",
                    'employee' => null,
                    'uploaded_name' => $employeeName . " (No Attendance Calculation)",
                    'attendance_type' => "N/A",
                    'date' => $attendanceStartDate->format('Y-m-d'),
                    'start_time' => $attendanceStartDate->format('H:i'),
                    'end_time' => $attendanceEndDate ? $attendanceEndDate->format('H:i') : null,
                    'hours' => null,
                    'extra_hours' => null,
                    'is_extra_hours_approved' => null,
                    'is_approved' => null,
                    "error" => true,
                    'creator_id' => Auth::id(),
                ];
                continue;
            }

            $attendanceType = $employee->benefitConfiguration->attendance_calculation;

            if ($attendanceType == BenefitConfiguration::ATTENDANCE_CALCULATION_IN_ONLY) {
                $hours = $employee->benefitConfiguration->daily_working_hours;
            } else {
                $hours = abs(round(Carbon::parse($attendanceEndDate)->diffInHours(Carbon::parse($attendanceStartDate)), 2));
            }



            // Determine if attendance approval is required
            $isApproved = null;
            if ($employee) {
                $benefitConfig = $employee->benefitConfiguration;
                if ($benefitConfig && !$benefitConfig->is_require_attendance_approval) {
                    // Auto approve if approval is not required
                    $isApproved = true;
                }
            }

            $attendance[] = [
                'employee_id' => $employee?->id ?? "Not Found",
                'employee' => $employee,
                'uploaded_name' => $employeeName,
                'attendance_type' => $attendanceType,
                'date' => $attendanceStartDate->format('Y-m-d'),
                'start_time' => $attendanceStartDate->format('H:i'),
                'end_time' => $attendanceEndDate ? $attendanceEndDate->format('H:i') : null,
                'hours' => $hours,
                'extra_hours' => $extraHours,
                'is_extra_hours_approved' => null,
                'is_approved' => $isApproved, // Set to null (pending) or true (auto-approved)
                "error" => $employee ? false : true,
                'creator_id' => Auth::id(),
            ];
        }

        AppLog::info('Uploaded Attendance');
        return $attendance;
    }

    public static function handleAttendanceFromDevice($request)
    {
        Log::info('Attendance from device', $request->all());
    }

    /**
     * Save attendance records and generate overtime for each record
     * 
     * @param array $attendance
     * @return void
     * @throws AppException
     */
    public static function saveAttendance($attendance)
    {
        try {
            Log::info('Saving attendance', $attendance);
            DB::transaction(function () use ($attendance) {
                foreach ($attendance as $attendanceData) {
                    if (!isset($attendanceData['error']) || !$attendanceData['error']) {
                        $attendanceRecord = Attendance::updateOrCreate(
                            [
                                'employee_id' => $attendanceData['employee_id'],
                                'date' => $attendanceData['date'],
                            ],
                            [
                                'start_time' => $attendanceData['start_time'],
                                'end_time' => $attendanceData['end_time'],
                                'hours' => $attendanceData['hours'],
                                'extra_hours' => $attendanceData['extra_hours'],
                                'is_extra_hours_approved' => $attendanceData['is_extra_hours_approved'],
                                'creator_id' => $attendanceData['creator_id'],
                                'is_approved' => $attendanceData['is_approved'],
                            ]
                        );
                        $attendanceRecord->generateOvertime();

                        // Check if employee worked on a day they shouldn't have and add vacation balance
                        $attendanceRecord->checkExtraAttendanceAndAddVacationBalance();
                    }
                }
            });
            AppLog::info('Saved Attendance');
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to save attendance', $e->getMessage());
            throw new AppException('Failed to save attendance: ' . $e->getMessage());
        }
    }


    /**
     * Generate overtime for an attendance record if the employee has overtime enabled
     * 
     * @return void
     * @throws AppException
     */
    public function generateOvertime()
    {
        /** @var User $user */
        $user = Auth::user();

        $employeeConfiguration = $this->employee->benefitConfiguration;

        if (!$employeeConfiguration->is_generate_overtime) return;

        if (!$this->end_time) return;

        $overtime = $this->getOvertimeHours($employeeConfiguration);

        if ($overtime <= 1) return;

        $startDate = Carbon::parse($this->date);
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);

        try {
            return Overtime::updateOrCreate([
                'employee_id' => $this->employee_id,
                'creator_id' => $this->employee->user_id,
                'date' => $startDate->format('Y-m-d'),
                'status' => $user?->can('updateOvertime', $this->employee) ? Overtime::STATUS_APPROVED : Overtime::STATUS_PENDING,
                'approved_at' => $user?->can('updateOvertime', $this->employee) ? now() : null,
                'admin_note' => 'Generated after attendance submission',
            ], [
                'start_time' => $startTime->format('H:i'),
                'end_time' => $endTime->format('H:i'),
                'hours' => $overtime,
            ]);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to generate overtime', $e->getMessage());
            throw new AppException('Failed to generate overtime: ' . $e->getMessage());
        }
    }


    public function getOvertimeHours(BenefitConfiguration $employeeConfiguration)
    {
        if (!$this->end_time) return 0;

        $overTimeLimit = $employeeConfiguration->overtime_max_time ? Carbon::parse($employeeConfiguration->overtime_max_time) : null;
        $attendanceEndTime = Carbon::parse($this->end_time);
        $endTime = $overTimeLimit ? $attendanceEndTime->min($overTimeLimit) : $attendanceEndTime;

        switch ($employeeConfiguration->attendance_calculation) {
            case BenefitConfiguration::ATTENDANCE_CALCULATION_IN_ONLY:
                return 0;

            case BenefitConfiguration::ATTENDANCE_CALCULATION_FIXED:
            case BenefitConfiguration::ATTENDANCE_CALCULATION_BUS:
                $fixedEndTime = Carbon::parse($employeeConfiguration->working_day_end_max);
                $diffInHours = $fixedEndTime->diffInHours($endTime);
                return $diffInHours > 0 ? $diffInHours : 0;

            case BenefitConfiguration::ATTENDANCE_CALCULATION_SEMI_FLEXIBLE:
                $startTime = Carbon::parse($this->start_time);
                $diffInHours = $endTime->diffInHours($startTime, true);
                return $diffInHours - $employeeConfiguration->daily_working_hours;

            case BenefitConfiguration::ATTENDANCE_CALCULATION_FLEXIBLE:
                $startTime = Carbon::parse($this->start_time);
                $hours = self::where('employee_id', $this->employee_id)->where('date', $this->date)->sum('hours');
                return $hours - $employeeConfiguration->daily_working_hours;
        }
        return 0;
    }


    /**
     * Approve extra hours for an attendance record
     * 
     * @return bool
     * @throws AppException
     */
    public function approveExtraHours()
    {

        try {
            if ($this->extra_hours === null) {
                throw new AppException('This attendance record has no extra hours to approve.');
            }

            $this->is_extra_hours_approved = true;

            $employee = $this->employee->name ?? 'Unknown Employee';
            $date = $this->date ?? 'Unknown Date';
            $extraHours = $this->extra_hours ?? 0;
            AppLog::info(
                "Approved Extra Hours for $employee",
                "Date: $date, Extra Hours: $extraHours hours",
                loggable: $this
            );

            return $this->save();
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to approve extra hours', $e->getMessage());
            throw new AppException('Failed to approve extra hours: ' . $e->getMessage());
        }
    }

    /**
     * Reject extra hours for an attendance record
     * 
     * @return bool
     * @throws AppException
     */
    public function rejectExtraHours()
    {
        try {
            if ($this->extra_hours === null) {
                throw new AppException('This attendance record has no extra hours to reject.');
            }

            $employee = $this->employee->name ?? 'Unknown Employee';
            $date = $this->date ?? 'Unknown Date';
            $extraHours = $this->extra_hours ?? 0;
            AppLog::info(
                "Rejected Extra Hours for $employee",
                "Date: $date, Extra Hours: $extraHours hours",
                loggable: $this
            );
            $this->is_extra_hours_approved = false;
            return $this->save();
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to reject extra hours', $e->getMessage());
            throw new AppException('Failed to reject extra hours: ' . $e->getMessage());
        }
    }

    /**
     * Approve attendance record
     * 
     * @return bool
     * @throws AppException
     */
    public function approveAttendance()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->can('approve', $this)) {
            throw new AppException('You dont have permission to approve attendance');
        }

        try {
            $this->is_approved = true;
            $employee = $this->employee->name ?? 'Unknown Employee';
            $date = $this->date ?? 'Unknown Date';
            $hours = $this->hours ?? 0;
            AppLog::info(
                "Approved Attendance for $employee",
                "Date: $date, Hours: $hours hours",
                loggable: $this
            );
            return $this->save();
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to approve attendance', $e->getMessage());
            throw new AppException('Failed to approve attendance: ' . $e->getMessage());
        }
    }

    /**
     * Reject attendance record
     * 
     * @return bool
     * @throws AppException
     */
    public function rejectAttendance()
    {
        try {
            $this->is_approved = false;
            $employee = $this->employee->name ?? 'Unknown Employee';
            $date = $this->date ?? 'Unknown Date';
            $hours = $this->hours ?? 0;
            AppLog::info(
                "Rejected Attendance for $employee",
                "Date: $date, Hours: $hours hours",
                loggable: $this
            );
            return $this->save();
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to reject attendance', $e->getMessage());
            throw new AppException('Failed to reject attendance: ' . $e->getMessage());
        }
    }

    /**
     * Edit attendance times
     * 
     * @param string $start_time
     * @param string|null $end_time
     * @return void
     * @throws AppException
     */
    public function editAttendanceTimes(string $start_time, ?string $end_time = null)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->can('update', $this)) {
            throw new AppException('You dont have permission to edit attendance');
        }

        // Check if attendance is linked to payroll
        if (!is_null($this->payroll_id)) {
            throw new AppException('Cannot edit attendance: Record is linked to payroll');
        }

        try {
            DB::transaction(function () use ($start_time, $end_time) {
                $oldStartTime = $this->start_time;
                $oldEndTime = $this->end_time;
                $oldHours = $this->hours;

                $this->start_time = $start_time;
                $this->end_time = $end_time;

                // Recalculate hours based on new times
                if ($end_time) {
                    $startTime = Carbon::parse($start_time);
                    $endTime = Carbon::parse($end_time);
                    $this->hours = abs(round($endTime->diffInHours($startTime), 2));
                } else {
                    // If no end time, use the employee's daily working hours
                    $benefitConfig = $this->employee->benefitConfiguration;
                    $this->hours = $benefitConfig ? $benefitConfig->daily_working_hours : 8;
                }

                $this->save();

                // Regenerate overtime if needed
                $this->generateOvertime();

                AppLog::info('Attendance Times Edited', "Employee: {$this->employee->name}, Date: {$this->date}, Old Start: $oldStartTime, New Start: $start_time, Old End: $oldEndTime, New End: $end_time, Old Hours: $oldHours, New Hours: {$this->hours}", loggable: $this);
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error editing attendance times', $e->getMessage());
            throw new AppException('Error editing attendance times');
        }
    }

    /**
     * Check if employee worked on a day they shouldn't have and add vacation balance if applicable
     * 
     * @return void
     * @throws AppException
     */
    public function checkExtraAttendanceAndAddVacationBalance()
    {
        $attendanceDate = Carbon::parse($this->date);

        // Find the vacation benefit with automatic_add_to_balance = true
        $vacationBenefit = VacationBenefit::where('employee_id', $this->employee_id)
            ->where('automatic_add_to_balance', true)
            ->whereNull('end_date') // Only active benefits
            ->orWhere('end_date', '>=', $attendanceDate)
            ->first();

        if (!$vacationBenefit) {
            // No vacation benefit with automatic_add_to_balance enabled
            return;
        }


        // Check if employee should work on this date
        if ($this->shouldEmployeeWorkOnDate($this->employee, $attendanceDate)) {
            // Employee should work on this day, no extra balance needed
            return;
        }

        // Employee shouldn't work on this day but did - add vacation balance
        $this->addVacationBalanceForExtraAttendance($attendanceDate);
    }

    /**
     * Check if employee should work on the given date based on their working days configuration and public holidays
     * (Copied from ProcessDailyAttendanceJob)
     */
    private function shouldEmployeeWorkOnDate(Employee $employee, Carbon $date): bool
    {
        // First check if it's a public holiday - no one works on public holidays
        $isPublicHoliday = PublicHoliday::where('date', $date->format('Y-m-d'))->exists();
        if ($isPublicHoliday) {
            return false;
        }

        $isAppliedVacation = AppliedVacation::where('employee_id', $employee->id)
            ->where('status', AppliedVacation::STATUS_APPROVED)
            ->whereHas('vacationDays', function ($query) use ($date) {
                $query->where('vacation_date', $date->format('Y-m-d'));
            })
            ->exists();
        if ($isAppliedVacation) {
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
     * Add vacation balance to the employee's vacation benefit that has automatic_add_to_balance enabled
     * 
     * @param Carbon $attendanceDate
     * @return void
     * @throws AppException
     */
    private function addVacationBalanceForExtraAttendance(Carbon $attendanceDate)
    {
        // Find the vacation benefit with automatic_add_to_balance = true
        $vacationBenefit = VacationBenefit::where('employee_id', $this->employee_id)
            ->where('automatic_add_to_balance', true)
            ->whereNull('end_date') // Only active benefits
            ->orWhere('end_date', '>=', $attendanceDate)
            ->first();

        if (!$vacationBenefit) {
            // No vacation benefit with automatic_add_to_balance enabled
            return;
        }

        try {
            // Calculate hours to add based on employee's daily working hours
            $benefitConfig = $this->employee->benefitConfiguration;
            $hoursToAdd = $benefitConfig ? $benefitConfig->daily_working_hours : 8; // Default to 8 hours

            // Check if adding these hours would exceed max balance
            $newBalance = $vacationBenefit->current_balance + $hoursToAdd;
            if ($newBalance > $vacationBenefit->max_balance) {
                $hoursToAdd = $vacationBenefit->max_balance - $vacationBenefit->current_balance;
                if ($hoursToAdd <= 0) {
                    // Already at max balance, no need to add
                    return;
                }
                $newBalance = $vacationBenefit->max_balance;
            }

            // Update vacation benefit balance
            $vacationBenefit->update([
                'current_balance' => $newBalance
            ]);

            // Create a gained vacation record for tracking
            GainedVacation::create([
                'employee_id' => $this->employee_id,
                'vacation_benefit_id' => $vacationBenefit->id,
                'days' => $hoursToAdd / 8, // Convert hours to days for display purposes
                'new_balance' => $newBalance,
            ]);

            AppLog::info(
                'Added vacation balance for extra attendance',
                "Employee: {$this->employee->name}, Date: {$attendanceDate->format('Y-m-d')}, Hours Added: {$hoursToAdd}, New Balance: {$newBalance}",
                loggable: $this
            );
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to add vacation balance for extra attendance', $e->getMessage(), loggable: $this);
            throw new AppException('Failed to add vacation balance for extra attendance: ' . $e->getMessage());
        }
    }
}
