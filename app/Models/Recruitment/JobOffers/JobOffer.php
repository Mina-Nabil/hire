<?php

namespace App\Models\Recruitment\JobOffers;

use App\Models\Recruitment\Applicants\Application;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobOffer extends Model
{
    const MORPH_NAME = 'job_offer';

    protected $table = 'job_offers';
    
    protected $fillable = [
        'application_id',
        'offered_salary',
        'proposed_start_date',
        'expiry_date',
        'offer_date',
        'benefits',
        'notes',
        'response_date',
        'response_notes',
        'status',
        'created_by'
    ];

    protected $casts = [
        'proposed_start_date' => 'date',
        'expiry_date' => 'date',
        'offer_date' => 'date',
        'response_date' => 'date',
    ];

    // Job offer statuses
    const STATUS_DRAFT = 'Draft';
    const STATUS_SENT = 'Sent';
    const STATUS_ACCEPTED = 'Accepted';
    const STATUS_REJECTED = 'Rejected';
    const STATUS_EXPIRED = 'Expired';
    
    const JOB_OFFER_STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_SENT,
        self::STATUS_ACCEPTED,
        self::STATUS_REJECTED,
        self::STATUS_EXPIRED,
    ];

    /**
     * Get the formatted salary amount with currency symbol
     * 
     * @return string
     */
    public function getFormattedSalaryAttribute(): string
    {
        return number_format($this->offered_salary, 2) . ' EGP';
    }

    /**
     * Get the CSS class for the status badge
     * 
     * @return string
     */
    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'bg-slate-500',
            self::STATUS_SENT => 'bg-info',
            self::STATUS_ACCEPTED => 'bg-success',
            self::STATUS_REJECTED => 'bg-danger',
            self::STATUS_EXPIRED => 'bg-warning',
            default => 'bg-secondary',
        };
    }

    /**
     * Get the application that this job offer is for.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the user who created this job offer.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
} 