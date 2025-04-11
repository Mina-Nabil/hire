<?php

namespace App\Models\Recruitment\Vacancies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VacancyQuestion extends Model
{
    const MORPH_NAME = 'vacancy_question';
    
    protected $fillable = [
        'vacancy_id',
        'question',
        'arabic_question',
        'type',
        'required',
        'options'
    ];

    protected $casts = [
        'required' => 'boolean'
    ];

    public function getOptionsArrayAttribute()
    {
        return $this->options ? explode(',', $this->options) : [];
    }

    /**
     * Get the vacancy that owns this question.
     */
    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }
}
