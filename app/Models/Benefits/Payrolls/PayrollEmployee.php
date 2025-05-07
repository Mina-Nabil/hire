<?php

namespace App\Models\Benefits\Payrolls;

use Illuminate\Database\Eloquent\Model;

class PayrollEmployee extends Model
{
    const MORPH_NAME = 'payroll_employee';
    protected $table = 'payroll_employees';
    protected $fillable = [
        'employee_id',
        'payroll_id',
        'payroll_date',
        'payroll_amount',
    ];
}
