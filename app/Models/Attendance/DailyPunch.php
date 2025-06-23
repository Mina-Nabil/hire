<?php

namespace App\Models\Attendance;

use App\Models\Personel\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPunch extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'punch_time',
        'punch_state',
        'verify_mode',
        'work_code',
        'raw_log'
    ];

    protected $casts = [
        'punch_time' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
