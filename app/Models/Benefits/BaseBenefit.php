<?php

namespace App\Models\Benefits;

use App\Models\Personel\Employee;
use Illuminate\Database\Eloquent\Model;

class BaseBenefit extends Model
{
    protected $table = 'base_benefits';
    protected $fillable = [
        'employee_id',
        'name',
        'amount',
        'type',
        'start_date',
        'end_date',
        'benefit_package_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    const TYPE_MONTHLY = 'monthly';
    const TYPE_WEEKLY = 'weekly';
    const TYPE_QUARTERLY = 'quarterly';
    const TYPE_YEARLY = 'yearly';
    const TYPE_DAILY = 'daily';

    const TYPE_LIST = [
        self::TYPE_MONTHLY,
        self::TYPE_WEEKLY,
        self::TYPE_QUARTERLY,
        self::TYPE_YEARLY,
        self::TYPE_DAILY,
    ];

    
    ///relations
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function benefitPackage()
    {
        return $this->belongsTo(BenefitPackage::class);
    }

    public function benefitPayments()
    {
        return $this->hasMany(BenefitPayment::class);
    }
    
}
