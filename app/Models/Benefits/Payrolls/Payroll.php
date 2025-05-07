<?php

namespace App\Models\Benefits\Payrolls;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    const MORPH_NAME = 'payroll';
    protected $table = 'payrolls';
    protected $fillable = [
        'employee_id',
        'payroll_date',
        'payroll_amount',
    ];
    
}
