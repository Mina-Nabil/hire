<?php

namespace App\Models\Attendance;

use App\Models\Benefits\Payrolls\Payroll;
use App\Models\Personel\Employee;
use Illuminate\Database\Eloquent\Model;

class MissingDay extends Model
{
    const MORPH_NAME = 'missing_day';

    protected $fillable = [
        'employee_id',
        'payroll_id',
        'date',
        'hours',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    //scopes 
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
