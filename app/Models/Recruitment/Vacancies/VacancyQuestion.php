<?php

namespace App\Models\Recruitment\Vacancies;

use Illuminate\Database\Eloquent\Model;

class VacancyQuestion extends Model
{
    protected $fillable = ['question', 'type', 'options', 'created_at', 'updated_at'];

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }
}
