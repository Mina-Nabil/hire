<?php

namespace App\Models\Benefits\Payrolls;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Benefits\Vacations\VacationBenefit;
use App\Models\Benefits\Payrolls\Payroll;
use App\Models\Benefits\Vacations\VacationDay;
use App\Models\Users\AppLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppliedVacation extends Model
{
    const MORPH_NAME = 'applied_vacation';
    protected $table = 'applied_vacations';
    protected $fillable = [
        'employee_id',
        'vacation_benefit_id',
        'payroll_id',
        'status',
        'days',
        'hours',
        'new_balance',
        'note'
    ];
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_LIST = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
    ];

    //model functions
    public function approve()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->can('approve', $this)) {
            throw new AppException('You dont have permission to approve vacation');
        }

        $this->status = self::STATUS_APPROVED;
        $this->save();
        AppLog::info('Vacation Approved', "Employee: $this->employee->name, Vacation: $this->vacationBenefit->name", loggable: $this);
    }


    public function reject($note = null)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->can('reject', $this)) {
            throw new AppException('You dont have permission to reject vacation');
        }

        try {
            DB::transaction(function () use ($note) {
                $this->status = self::STATUS_REJECTED;
                $this->note = $note;
                $this->save();
                $this->vacationBenefit->update([
                    'current_balance' => $this->vacationBenefit->current_balance + $this->hours,
                ]);
                $this->new_balance = $this->vacationBenefit->current_balance;
                $this->save();
            });
            AppLog::info('Vacation Rejected', "Employee: $this->employee->name, Vacation: $this->vacationBenefit->name", loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error rejecting vacation', $e->getMessage(), loggable: $this);
            throw new AppException('Error rejecting vacation');
        }
    }

    ///scopes
    public function scopeUserData($query)
    {
        $loggedInUser = Auth::user();
        if ($loggedInUser->is_admin || $loggedInUser->is_hr) {
            return $query;
        }
        $query->where(function ($q) use ($loggedInUser) {
            $q->where('employee_id', $loggedInUser->employee_id)
                ->orWhereHas('employee.benefitConfiguration', function ($q) use ($loggedInUser) {
                    $q->where('manager_id', $loggedInUser->employee_id);
                });
        });
        return $query;
    }

    public function scopeCurrentBalance($query, $vacationDetail, Carbon $startDate, Carbon $endDate)
    {
        $query->where('status', self::STATUS_APPROVED)
            ->whereHas('vacationDays', function ($q) use ($startDate, $endDate) {
                $q->where('vacation_date', '<=', $startDate->format('Y-m-d'))
                    ->where('vacation_date', '>=', $endDate->format('Y-m-d'));
            })
            ->where(function ($q) use ($vacationDetail) {
                $q->where('vacation_detail_id', $vacationDetail->id)
                    ->orWhere('name', $vacationDetail->name);
            });
        return $query;
    }

    ///relations
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function vacationBenefit()
    {
        return $this->belongsTo(VacationBenefit::class);
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function vacationDays()
    {
        return $this->hasMany(VacationDay::class);
    }
}
