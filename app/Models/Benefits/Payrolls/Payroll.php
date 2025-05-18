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

    const EMPLOYEE_SHARE_SOCIAL_INSURANCE = 0.11;
    const EMPLOYER_SHARE_SOCIAL_INSURANCE = 0.1875;
    
    
}
