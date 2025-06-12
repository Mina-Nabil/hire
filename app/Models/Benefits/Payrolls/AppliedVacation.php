<?php

namespace App\Models\Benefits\Payrolls;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Benefits\Vacations\VacationBenefit;
use App\Models\Benefits\Payrolls\Payroll;
use App\Models\Benefits\Vacations\VacationDay;
use App\Models\Users\AppLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
        if(!$user->can('approve', $this)) {
            throw new AppException('You dont have permission to approve vacation');
        }

        $this->status = self::STATUS_APPROVED;
        $this->save();
        AppLog::info('Vacation Approved', "Employee: $this->employee->name, Vacation: $this->vacationBenefit->name", loggable: $this);
    }


    public function reject()
    {
        /** @var User $user */
        $user = Auth::user();
        if(!$user->can('reject', $this)) {
            throw new AppException('You dont have permission to reject vacation');
        }

        $this->status = self::STATUS_REJECTED;
        $this->save();
        AppLog::info('Vacation Rejected', "Employee: $this->employee->name, Vacation: $this->vacationBenefit->name", loggable: $this);
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
