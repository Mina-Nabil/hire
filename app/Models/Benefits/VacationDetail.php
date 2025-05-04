<?php

namespace App\Models\Benefits;

use Illuminate\Database\Eloquent\Model;

class VacationDetail extends Model
{
    protected $table = 'vacation_details';
    protected $fillable = [
        'name',
        'monthly_inc_rate',
        'yearly_inc_rate',
        'max_days',
        'hour_price',
    ];

    public function benefitPackage()
    {
        return $this->belongsTo(BenefitPackage::class);
    }
}
