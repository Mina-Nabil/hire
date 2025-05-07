<?php

namespace App\Models\Benefits\Vacations;

use App\Models\Personel\Employee;
use Illuminate\Database\Eloquent\Model;

class VacationPayment extends Model
{
    const MORPH_NAME = 'vacation_payment';
    protected $table = 'vacation_payments';
    protected $fillable = [
        'employee_id',
        'vacation_benefit_id',
        'payroll_id',
        'amount',
        'new_balance',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
}
