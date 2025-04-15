<?php

namespace App\Models\Recruitment\JobOffers;

use App\Exceptions\AppException;
use App\Models\Recruitment\Applicants\Application;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

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
            self::STATUS_DRAFT => 'bg-slate-300',
            self::STATUS_SENT => 'bg-info-300',
            self::STATUS_ACCEPTED => 'bg-success-300',
            self::STATUS_REJECTED => 'bg-danger-300',
            self::STATUS_EXPIRED => 'bg-warning-300',
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

    


    ////model functions
    public function editOffer($offered_salary, Carbon $proposed_start_date, Carbon $expiry_date, $benefits, $notes=null)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if(!$loggedInUser->can('update', $this)){
            throw new AppException(__('misc.not_authorized'));
        }

        if($this->status != self::STATUS_DRAFT){
            throw new AppException('You can only edit draft job offers');
        }

        try{

            $this->update([
                'offered_salary' => $offered_salary,
                'proposed_start_date' => $proposed_start_date->format('Y-m-d'),
                'expiry_date' => $expiry_date->format('Y-m-d'),
                'benefits' => $benefits,
                'notes' => $notes,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to edit job offer');
        }
    }

    public function accept($response_notes=null)
    {
          /** @var User $loggedInUser */
          $loggedInUser = Auth::user();
          if(!$loggedInUser->can('update', $this)){
              throw new AppException(__('misc.not_authorized'));
          }

          if($this->status == self::STATUS_REJECTED){
            throw new AppException('You can\'t accept rejected job offers');
          }

          $this->update([
            'status' => self::STATUS_ACCEPTED,
            'response_date' => now(),
            'response_notes' => $response_notes,
          ]);
    }

    public function reject($response_notes=null)
    {
            /** @var User $loggedInUser */
            $loggedInUser = Auth::user();
            if(!$loggedInUser->can('update', $this)){
                throw new AppException(__('misc.not_authorized'));
            }

            if($this->status == self::STATUS_ACCEPTED){
                throw new AppException('You can\'t reject accepted job offers');
            }

            $this->update([
                'status' => self::STATUS_REJECTED,
                'response_date' => now(),
                'response_notes' => $response_notes,
            ]);
    }

    public function expire()
    {
         /** @var User $loggedInUser */
         $loggedInUser = Auth::user();
         if(!$loggedInUser->can('update', $this)){
             throw new AppException(__('misc.not_authorized'));
         }

         if($this->status == self::STATUS_ACCEPTED){
            throw new AppException('You can\'t expire accepted job offers');
         }

         if($this->status == self::STATUS_REJECTED){
            throw new AppException('You can\'t expire rejected job offers');
         }

         $this->update([
            'status' => self::STATUS_EXPIRED,
         ]);
    }

    public function send()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if(!$loggedInUser->can('update', $this)){
            throw new AppException(__('misc.not_authorized'));
        }

        if($this->status !== self::STATUS_DRAFT){
            throw new AppException('You can only send draft job offers');
        }

        $this->update([
            'status' => self::STATUS_SENT,
            'offer_date' => now(),
        ]);
    }
    
    
} 