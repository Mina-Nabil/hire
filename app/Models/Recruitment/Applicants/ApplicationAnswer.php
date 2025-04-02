<?php

namespace App\Models\Recruitment\Applicants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApplicationAnswer extends Model
{
    const MORPH_NAME = 'application_answer';
    
    protected $table = 'application_answers';
    
    protected $fillable = [
        'application_id',
        'answerable_type',
        'answerable_id',
        'answer',
    ];
    
    /**
     * Get the application that this answer belongs to.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
    
    /**
     * Get the parent answerable model (polymorphic).
     */
    public function answerable(): MorphTo
    {
        return $this->morphTo();
    }
} 