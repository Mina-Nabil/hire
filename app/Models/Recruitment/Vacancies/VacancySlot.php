<?php

namespace App\Models\Recruitment\Vacancies;

use Illuminate\Database\Eloquent\Model;

class VacancySlot extends Model
{
    protected $fillable = ['date', 'start_time', 'end_time', 'created_at', 'updated_at'];

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }
}
