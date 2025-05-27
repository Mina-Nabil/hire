<?php

namespace App\Models\Benefits\Payrolls;

use App\Models\Attendance\Attendance;
use App\Models\Attendance\Overtime;
use App\Models\Personel\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollEmployee extends Model
{
    use HasFactory;

    const MORPH_NAME = 'payroll_employee';
    protected $table = 'payroll_employees';
    protected $fillable = [
        'employee_id',
        'payroll_id',
        'paid',
        'vacation_days',
        'vacation_amount',
        'base_amount',
        'gross_salary',
        'insurance_amount',
        'other_amount',
        'employee_insurance',
        'employer_insurance',
        'total_insurance',
        'employee_medical',
        'total_medical',
        'employee_deductions',
        'penalties_days',
        'penalties_amount',
        'total_penalty_hours',
        'vacation_offset_hours',
        'new_vacation_hours',
        'direct_deduction_hours',
        'direct_deduction_amount',
        'net_after_penalty',
        'extra_payments',
        'net_after_deductions',
        'employee_base_benefits',
        'other_base_benefits',
        'adj_amount',
        'adj_desc',
        'position',
        'department',
        'overtime_amount',
        'overtime_hours',
        'net_amount',
        'status',
    ];
    
    /**
     * Get the employee for this payroll record
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
    /**
     * Get the payroll this record belongs to
     */
    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
