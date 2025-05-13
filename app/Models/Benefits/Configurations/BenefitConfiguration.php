<?php

namespace App\Models\Benefits\Configurations;

use App\Models\Personel\Employee;
use App\Models\Benefits\Configurations\SalaryGrade;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class BenefitConfiguration extends Model
{
    const MORPH_NAME = 'benefit_configuration';
    const ATTENDANCE_CALCULATION_FLEXIBLE = 'flexible';
    const ATTENDANCE_CALCULATION_SEMI_FLEXIBLE = 'semi-flexible';
    const ATTENDANCE_CALCULATION_FIXED = 'fixed';
    const ATTENDANCE_CALCULATION_LIST = [
        self::ATTENDANCE_CALCULATION_FLEXIBLE,
        self::ATTENDANCE_CALCULATION_SEMI_FLEXIBLE,
        self::ATTENDANCE_CALCULATION_FIXED,
    ];

    protected $table = 'benefit_configurations';
    protected $fillable = [
        'employee_id',
        'salary_grade_id',
        'manager_id',
        'vacation_package_id',
        'gross_salary',
        'attendace_calculation',
        'working_day_start_min',
        'working_day_start_max',
        'working_day_end_min',
        'working_day_end_max',
        'daily_working_hours',
        'overtime_rate',
        'is_automatic_overtime'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function salaryGrade()
    {
        return $this->belongsTo(SalaryGrade::class);
    }
    
    
    
}
