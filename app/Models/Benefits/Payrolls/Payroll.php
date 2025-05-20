<?php

namespace App\Models\Benefits\Payrolls;

use App\Models\Attendance\Attendance;
use App\Models\Personel\Employee;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    const MORPH_NAME = 'payroll';
    
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    
    const STATUS_LIST = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
    ];
    
    protected $table = 'payrolls';
    protected $fillable = [
        'creator_id',
        'start_date',
        'end_date',
        'total_paid',
        'total_vacation_days',
        'total_vacation_amount',
        'total_employees',
        'status',
    ];

    const EMPLOYEE_SHARE_SOCIAL_INSURANCE = 0.11;
    const EMPLOYER_SHARE_SOCIAL_INSURANCE = 0.1875;
    
    /**
     * Get the employee records for this payroll
     */
    public function payrollEmployees()
    {
        return $this->hasMany(PayrollEmployee::class);
    }
    
    /**
     * Get the employees for this payroll
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'payroll_employees');
    }
    
    /**
     * Get the attendance records for this payroll
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    
    /**
     * Get the extra payments for this payroll
     */
    public function extraPayments()
    {
        return $this->hasMany(ExtraPayment::class);
    }
    
    /**
     * Get the creator of this payroll
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the benefit payments for this payroll
     */
    public function benefitPayments()
    {
        return $this->hasMany(BenefitPayment::class);
    }
}
