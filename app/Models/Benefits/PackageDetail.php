<?php

namespace App\Models\Benefits;

use Illuminate\Database\Eloquent\Model;

class PackageDetail extends Model
{
    protected $table = 'package_details';
    protected $fillable = [
        'name',
        'type',
        'amount_min',
        'amount_max',
    ];

    public function benefitPackage()
    {
        return $this->belongsTo(BenefitPackage::class);
    }
}
