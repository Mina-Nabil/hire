<?php

namespace App\Models\Benefits\Vacations;

use App\Models\Personel\Employee;
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


    public function vacationBenefit()
    {
        return $this->belongsTo(VacationBenefit::class);
    }


    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
