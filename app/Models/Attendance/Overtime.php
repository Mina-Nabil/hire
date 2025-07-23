<?php

namespace App\Models\Attendance;

use App\Exceptions\AppException;
use App\Models\Benefits\Payrolls\Payroll;
use App\Models\Personel\Employee;
use App\Models\Users\AppLog;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Overtime extends Model
{

    protected $table = 'overtimes';

    const MORPH_NAME = 'overtime';

    protected $fillable = [
        'employee_id',
        'creator_id',
        'date',
        'start_time',
        'end_time',
        'hours',
        'status',
        'approved_at',
        'admin_note',
        'payroll_id',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const STATUS_LIST = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
    ];

    public function setStatus(string $status, ?string $admin_note = null)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('updateOvertime', $this->employee)) {
            throw new AppException('You dont have permission to set overtime status');
        }

        try {
            $this->update([
                'status' => $status,
                'approved_at' => now(),
                'admin_note' => $admin_note,
            ]);

            AppLog::info('Overtime Status Updated for ' . $this->employee->name, "Status: $status, Admin Note: $admin_note", loggable: $this);
            $this->save();
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error setting overtime status', $e->getMessage());
            throw new AppException('Error setting overtime status');
        }
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    protected static function booted()
    {
        static::addGlobalScope('managerAccessibleOvertime', function ($builder) {
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
}
