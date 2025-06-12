<?php

namespace App\Models\Attendance;

use App\Exceptions\AppException;
use App\Models\Benefits\Configurations\BenefitConfiguration;
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
                    $builder->whereHas('employee.position', function($query) use ($locationIds) {
                        $query->whereIn('location_id', $locationIds);
                    });
                }
                return;
            }
            
            // If user is a manager (has employees reporting to them)
            $userEmployee = Employee::where('user_id', $user->id)->first();
            if ($userEmployee && $userEmployee->is_manager) {
                // Get attendance records of employees who have this manager as their manager
                $builder->whereHas('employee.benefitConfiguration', function($query) use ($userEmployee) {
                    $query->where('manager_id', $userEmployee->id);
                });
            } else {
                // Regular employee can only see their own attendance
                $builder->where(function($query) use ($user, $userEmployee) {
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
            if (!$employeeName) continue;

            if(!$sheet->getCell('B' . $row)->getValue()) continue; //if the start time is empty, skip the row
            $attendanceStartDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sheet->getCell('B' . $row)->getValue());
            $attendanceEndDate = $sheet->getCell('C' . $row)->getValue() ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sheet->getCell('C' . $row)->getValue()) : null;
            $extraHours = $sheet->getCell('D' . $row)->getValue();

            $employee = Employee::where('name', $employeeName)->first();
            if(!$employee) {
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

            $attendanceType = $employee->benefitConfiguration->attendance_calculation;

            if($attendanceType == BenefitConfiguration::ATTENDANCE_CALCULATION_IN_ONLY){
                $hours = $employee->benefitConfiguration->daily_working_hours;
            } else {
                $hours = abs(round(Carbon::parse($attendanceEndDate)->diffInDays(Carbon::parse($attendanceStartDate)), 2));
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

    public static function saveAttendance($attendance)
    {
        try {
            DB::transaction(function () use ($attendance) {
                foreach ($attendance as $attendance) {
                    if (!$attendance['error']) {
                        Attendance::create($attendance);
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
}
