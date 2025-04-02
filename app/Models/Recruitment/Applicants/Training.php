<?php

namespace App\Models\Recruitment\Applicants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Training extends Model
{
    const MORPH_NAME = 'training';
    
    protected $table = 'applicant_trainings';
    
    protected $fillable = [
        'applicant_id',
        'name',
        'sponsor',
        'duration',
        'start_date',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    /**
     * Get the applicant that owns this training record.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
