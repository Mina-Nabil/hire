<?php

namespace App\Models\Payrolls;

use Illuminate\Database\Eloquent\Model;

class PenaltyDay extends Model
{
    const MORPH_NAME = 'penalty_day';

    protected $table = 'penalty_days';
    protected $fillable = [
        'employee_id',
        'payroll_id',
        'date',
        'type',
        'hours',
    ];

    const PENALTY_TYPE_MISSING_DAY = 'missing_day';
    const PENALTY_TYPE_INSUFFICIENT_VACATION = 'insufficient_vacation';
    const PENALTY_TYPE_MISSING_START_OR_END_TIME = 'missing_start_or_end_time';
    const PENALTY_TYPE_MISSING_START_AND_END_TIME = 'missing_start_and_end_time';
    const PENALTY_TYPE_LATE_ARRIVAL = 'late_arrival';
    const PENALTY_TYPE_EARLY_DEPARTURE = 'early_departure';
    const PENALTY_TYPE_LIST = [
        self::PENALTY_TYPE_MISSING_DAY,
        self::PENALTY_TYPE_INSUFFICIENT_VACATION,
        self::PENALTY_TYPE_MISSING_START_OR_END_TIME,
        self::PENALTY_TYPE_MISSING_START_AND_END_TIME,
        self::PENALTY_TYPE_LATE_ARRIVAL,
        self::PENALTY_TYPE_EARLY_DEPARTURE,
    ];
}
