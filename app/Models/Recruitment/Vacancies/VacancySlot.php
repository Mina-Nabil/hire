<?php

namespace App\Models\Recruitment\Vacancies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VacancySlot extends Model
{
    const MORPH_NAME = 'vacancy_slot';
    
    protected $fillable = [
        'vacancy_id',
        'date',
        'start_time',
        'end_time'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    public $timestamps = false;

    /**
     * Get the vacancy that owns this slot.
     */
    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }
}
