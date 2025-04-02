<?php

namespace App\Models\Recruitment\Applicants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    const MORPH_NAME = 'education';

    protected $table = 'applicant_educations';
    
    protected $fillable = [
        'applicant_id',
        'school_name',
        'degree',
        'field_of_study',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the applicant that owns this education record.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
