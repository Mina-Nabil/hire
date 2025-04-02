<?php

namespace App\Models\Recruitment\Vacancies;

use App\Models\Recruitment\Applicants\Application;
use Illuminate\Database\Eloquent\Model;

class BaseQuestion extends Model
{
    protected $fillable = ['question', 'type', 'options', 'created_at', 'updated_at'];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
