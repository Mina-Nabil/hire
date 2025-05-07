<?php

namespace App\Models\Benefits\Vacations;

use Illuminate\Database\Eloquent\Model;

class GainedVacation extends Model
{
    const MORPH_NAME = 'gained_vacation';
    protected $table = 'gained_vacations';
    protected $fillable = [
        'employee_id',
        'vacation_benefit_id',
        'days',
    ];
    
}
