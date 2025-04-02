<?php

namespace App\Models\Recruitment\Applicants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantHealth extends Model
{
    const MORPH_NAME = 'applicant_health';

    protected $table = 'applicant_health';
    
    protected $fillable = [
        'applicant_id',
        'has_health_issues',
        'health_issues',
    ];
    
    protected $casts = [
        'has_health_issues' => 'boolean',
    ];

    /**
     * Get the applicant that owns this health record.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
