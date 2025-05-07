<?php

namespace App\Models\Benefits\Configurations;

use App\Models\Personel\Employee;
use App\Models\Benefits\Configurations\BenefitPackage;
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

    protected $table = 'employee_benefits';
    protected $fillable = [
        'employee_id',
        'benefit_package_id',
        'attendace_calculation',
        'working_day_start_min',
        'working_day_start_max',
        'working_day_end_min',
        'working_day_end_max',
        'daily_working_hours',
        'overtime_rate',
        'creator_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function benefitPackage()
    {
        return $this->belongsTo(BenefitPackage::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
    
    
    
}
