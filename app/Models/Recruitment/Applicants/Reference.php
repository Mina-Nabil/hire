<?php

namespace App\Models\Recruitment\Applicants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reference extends Model
{
    const MORPH_NAME = 'reference';
    
    protected $table = 'applicant_references';
    
    public $timestamps = false;
    
    protected $fillable = [
        'applicant_id',
        'name',
        'phone',
        'email',
        'address',
        'relationship',
    ];

    /**
     * Get the applicant that owns this reference record.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
