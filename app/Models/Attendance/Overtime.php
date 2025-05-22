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
            throw new AppException('You dont have permission to approve overtime');
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
            AppLog::error('Error approving overtime', $e->getMessage());
            throw new AppException('Error approving overtime');
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
}
