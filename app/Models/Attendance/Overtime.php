<?php

namespace App\Models\Attendance;

use App\Models\Personel\Employee;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    protected $fillable = [
        'employee_id',
        'creator_id',
        'date',
        'start_time',
        'end_time',
        'hours',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
