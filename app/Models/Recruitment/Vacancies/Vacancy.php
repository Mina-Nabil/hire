<?php

namespace App\Models\Recruitment\Vacancies;

use App\Models\Recruitment\Applicants\Application;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    protected $fillable = ['title', 'description', 'salary', 'location', 'type', 'status', 'created_at', 'updated_at'];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
