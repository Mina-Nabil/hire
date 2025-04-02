<?php

namespace App\Models\Recruitment\Applicants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantSkill extends Model
{
    const MORPH_NAME = 'applicant_skill';

    protected $table = 'applicant_skills';
    
    public $timestamps = false;
    
    protected $fillable = [
        'applicant_id',
        'skill',
        'level',
    ];
    
    // Skill proficiency levels
    const LEVEL_BASIC = 'Basic';
    const LEVEL_GOOD = 'Good';
    const LEVEL_VERY_GOOD = 'Very Good';
    const LEVEL_EXCELLENT = 'Excellent';
    
    const SKILL_LEVELS = [
        self::LEVEL_BASIC,
        self::LEVEL_GOOD,
        self::LEVEL_VERY_GOOD,
        self::LEVEL_EXCELLENT,
    ];

    /**
     * Get the applicant that owns this skill record.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
