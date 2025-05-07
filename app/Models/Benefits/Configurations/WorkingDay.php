<?php

namespace App\Models\Benefits\Configurations;

use App\Models\Personel\Employee;
use Illuminate\Database\Eloquent\Model;

class WorkingDay extends Model
{
    const MORPH_NAME = 'working_day';
    protected $table = 'working_days';
    protected $fillable = [
        'employee_id',
        'type',
    ];
    const DAY_SATURDAY = 'saturday';
    const DAY_SUNDAY = 'sunday';
    const DAY_MONDAY = 'monday';
    const DAY_TUESDAY = 'tuesday';
    const DAY_WEDNESDAY = 'wednesday';
    const DAY_THURSDAY = 'thursday';
    const DAY_FRIDAY = 'friday';

    const DAYS_LIST = [
        self::DAY_SATURDAY,
        self::DAY_SUNDAY,
        self::DAY_MONDAY,
        self::DAY_TUESDAY,
        self::DAY_WEDNESDAY,
        self::DAY_THURSDAY,
        self::DAY_FRIDAY,
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
