<?php

namespace App\Models\Benefits\Configurations;

use Illuminate\Database\Eloquent\Model;
use App\Models\Benefits\Configurations\SalaryGrade;

///Compensation or benefit details for a benefit package
class PackageDetail extends Model
{
    const MORPH_NAME = 'package_detail';
    protected $table = 'package_details';
    protected $fillable = [
        'name',
        'receiver',
        'type',
        'amount_min',
        'amount_max',
        'is_net',
        'is_gross',
        'is_grand_gross',
        'is_hidden',
    ];
    const RECEIVER_EMPLOYEE = 'employee';
    const RECEIVER_MEDICAL = 'medical';
    const RECEIVER_OTHER = 'other';
    const RECEIVER_LIST = [
        self::RECEIVER_EMPLOYEE,
        self::RECEIVER_MEDICAL,
        self::RECEIVER_OTHER,
    ];

    public function salaryGrade()
    {
        return $this->belongsTo(SalaryGrade::class);
    }
}
