<?php

namespace App\Models\Personel;

use App\Models\Base\InsuranceOffice;
use Illuminate\Database\Eloquent\Model;

class EmployeeInfo extends Model
{
    const MORPH_NAME = 'employee_info';
    protected $table = 'employee_info';
    protected $fillable = [
        'employee_id',
        'employee_code',
        'device_id',
        'insurance_office_id',
        'insurance_number',
        'academic_qualification',
        'university',
        'graduation_year',
        'military_status',
        'gender',
        'marital_status',
        'device_id',
    ];


    ///relations
    public function insuranceOffice()
    {
        return $this->belongsTo(InsuranceOffice::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
