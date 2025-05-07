<?php

namespace App\Models\Benefits\Vacations;

use Illuminate\Database\Eloquent\Model;
use App\Models\Benefits\Configurations\BenefitPackage;

class VacationDetail extends Model
{
    const MORPH_NAME = 'vacation_detail';
    protected $table = 'vacation_details';
    protected $fillable = [
        'name',
        'type',
        'inc_rate_min',
        'inc_rate_max',
        'max_balance_min',
        'max_balance_max',
        'hour_price_min',
        'hour_price_max',
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


    public function benefitPackage()
    {
        return $this->belongsTo(BenefitPackage::class);
    }
}
