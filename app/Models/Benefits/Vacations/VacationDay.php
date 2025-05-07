<?php

namespace App\Models\Benefits\Vacations;

use Illuminate\Database\Eloquent\Model;
use App\Models\Benefits\Payrolls\AppliedVacation;

class VacationDay extends Model
{
    const MORPH_NAME = 'vacation_day';
    protected $fillable = [
        'applied_vacation_id',
        'vacation_date',
        'hours',
    ];

    public function appliedVacation()
    {
        return $this->belongsTo(AppliedVacation::class);
    }
    
    
    
}
