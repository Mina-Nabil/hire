<?php

namespace App\Models\Recruitment\Applicants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Experience extends Model
{
    const MORPH_NAME = 'experience';

    protected $table = 'applicant_experiences';
    
    protected $fillable = [
        'applicant_id',
        'company_name',
        'position',
        'start_date',
        'salary',
        'reason_for_leaving',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function getFormattedSalaryAttribute()
    {
        return number_format($this->salary, 2) . 'EGP';
    }

    /**
     * Get the applicant that owns this experience record.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
