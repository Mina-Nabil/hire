<?php

namespace App\Models\Recruitment\Applicants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Language extends Model
{
    const MORPH_NAME = 'language';

    protected $table = 'applicant_languages';
    
    public $timestamps = false;
    
    protected $fillable = [
        'applicant_id',
        'language',
        'speaking_level',
        'writing_level',
        'reading_level',
    ];

    // Language proficiency levels
    const LEVEL_BASIC = 'Basic';
    const LEVEL_GOOD = 'Good';
    const LEVEL_VERY_GOOD = 'Very Good';
    const LEVEL_FLUENT = 'Fluent';
    
    const PROFICIENCY_LEVELS = [
        self::LEVEL_BASIC,
        self::LEVEL_GOOD,
        self::LEVEL_VERY_GOOD,
        self::LEVEL_FLUENT,
    ];

    /**
     * Get the applicant that owns this language record.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
