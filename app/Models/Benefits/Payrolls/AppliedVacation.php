<?php

namespace App\Models\Benefits\Payrolls;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Benefits\Vacations\VacationBenefit;
use App\Models\Benefits\Payrolls\Payroll;
use App\Models\Benefits\Vacations\VacationDay;
use App\Models\Users\AppLog;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        'note',
        'reason',
        'is_mission',
        'approved_by_id',
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
            throw new AppException('You dont have permission to approve vacation or mission');
        }

        $this->status = self::STATUS_APPROVED;
        $this->approved_by_id = $user->id;
        $this->save();
        AppLog::info('Vacation Approved', "Employee: $this->employee->name, Vacation: $this->vacationBenefit->name", loggable: $this);
    }


    public function reject($note = null)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->can('reject', $this)) {
            throw new AppException('You dont have permission to reject vacation or mission');
        }

        try {
            DB::transaction(function () use ($note) {
                $this->status = self::STATUS_REJECTED;
                $this->note = $note;
                $this->save();
                if ($this->vacationBenefit) {
                    $this->vacationBenefit->update([
                        'current_balance' => $this->vacationBenefit->current_balance + $this->hours,
                    ]);
                    $this->new_balance = $this->vacationBenefit->current_balance;
                }
                $this->save();
            });
            AppLog::info('Vacation Rejected', "Employee: $this->employee->name, Vacation: $this->vacationBenefit->name", loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error rejecting vacation', $e->getMessage(), loggable: $this);
            throw new AppException('Error rejecting vacation');
        }
    }

    /**
     * Update a pending vacation request (days, hours and reason).
     * Adjusts the linked vacation benefit balance to reflect the change.
     *
     * @param array $days Array of ['vacation_date' => 'Y-m-d', 'hours' => int]
     * @param string|null $reason
     * @return void
     */
    public function updateRequest(array $days, ?string $reason = null)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->can('edit', $this)) {
            throw new AppException('You dont have permission to edit this request');
        }

        if ($this->status !== self::STATUS_PENDING) {
            throw new AppException('Only pending requests can be edited');
        }

        if (empty($days)) {
            throw new AppException('You must add at least one day for the request');
        }

        $newHours = collect($days)->sum('hours');

        try {
            DB::transaction(function () use ($days, $reason, $newHours) {
                // Adjust the vacation balance for non-mission requests: restore the
                // hours previously deducted, then deduct the new amount.
                if (!$this->is_mission && $this->vacationBenefit) {
                    $restoredBalance = $this->vacationBenefit->current_balance + $this->hours;
                    if ($restoredBalance < $newHours) {
                        throw new AppException('You dont have enough vacation days');
                    }
                    $this->vacationBenefit->update([
                        'current_balance' => $restoredBalance - $newHours,
                    ]);
                    $this->new_balance = $restoredBalance - $newHours;
                }

                $this->hours = $newHours;
                $this->reason = $reason;
                $this->save();

                // Replace the vacation days
                $this->vacationDays()->delete();
                $this->vacationDays()->createMany($days);

                AppLog::info('Vacation Request Updated', 'Vacation request updated for employee: ' . $this->employee->name, loggable: $this);
            });
        } catch (AppException $e) {
            throw $e;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error updating vacation request', $e->getMessage(), loggable: $this);
            throw new AppException('Error updating vacation request');
        }
    }

    public static function getAppliedHours($employeeId, $startDate, $endDate, $vacationBenefitName, $vacationBenefitId = null)
    {
        $query = self::currentBalance($employeeId, $startDate, $endDate, $vacationBenefitName, $vacationBenefitId);
        return $query->sum('hours');
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

    public function scopeCurrentBalance($query, $employeeId, Carbon $startDate, Carbon $endDate, $vacationBenefitName)
    {
        $query
            ->where('employee_id', $employeeId)
            ->whereNot('status', self::STATUS_REJECTED)
            ->whereHas('vacationDays', function ($q) use ($startDate, $endDate) {
                $q->where('vacation_date', '>=', $startDate->format('Y-m-d'))
                    ->where('vacation_date', '<=', $endDate->format('Y-m-d'));
            })
            ->where('name', $vacationBenefitName);
        return $query;
    }

    public function scopeByTypeName($query, $typeName)
    {
        if ($typeName === 'Mission') {
            return $query->where('is_mission', true);
        }
        
        return $query->whereHas('vacationBenefit', function ($q) use ($typeName) {
            $q->where('name', $typeName);
        });
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

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }
}
