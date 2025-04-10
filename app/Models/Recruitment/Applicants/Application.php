<?php

namespace App\Models\Recruitment\Applicants;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Recruitment\Interviews\Interview;
use App\Models\Recruitment\Vacancies\Vacancy;
use App\Models\Recruitment\Vacancies\VacancySlot;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Application extends Model
{
    const MORPH_NAME = 'application';
    
    protected $table = 'applications';
    
    protected $fillable = [
        'applicant_id',
        'vacancy_id',
        'cover_letter',
        'referred_by_id',
        'status',
    ];
    
    // Application statuses
    const STATUS_PENDING = 'pending';
    const STATUS_SHORTLISTED = 'shortlisted';
    const STATUS_INTERVIEW = 'interview';
    const STATUS_HIRED = 'hired';
    const STATUS_REJECTED = 'rejected';
    
    const APPLICATION_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_SHORTLISTED,
        self::STATUS_INTERVIEW,
        self::STATUS_HIRED,
        self::STATUS_REJECTED,
    ];

    ////attributes
    public function getStatusClassAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-info-200',
            self::STATUS_SHORTLISTED => 'bg-warning-200',
            self::STATUS_INTERVIEW => 'bg-primary-200', 
            self::STATUS_HIRED => 'bg-success-200',
            self::STATUS_REJECTED => 'bg-danger-200'
        };
    }



    /**
     * Get the applicant that owns this application.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
    
    /**
     * Get the vacancy that this application is for.
     */
    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }
    
    /**
     * Get the answers for this application.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(ApplicationAnswer::class);
    }

    /**
     * Get the interviews for this application.
     */
    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    /**
     * Get the slots booked for this application.
     */
    public function slots(): HasMany
    {
        return $this->hasMany(ApplicationSlot::class);
    }

    /**
     * Get the employee who referred this application.
     */
    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'referred_by_id');
    }

    /**
     * Create a new application
     * 
     * @param int $applicantId
     * @param int $vacancyId
     * @param string|null $coverLetter
     * @return Application
     */
    public static function createApplication(int $applicantId, int $vacancyId, ?string $coverLetter = null, ?int $refered_by_id = null): Application
    {
        try {
            return self::create([
                'applicant_id' => $applicantId,
                'vacancy_id' => $vacancyId,
                'cover_letter' => $coverLetter,
                'status' => self::STATUS_PENDING,
                'referred_by_id' => $refered_by_id,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to create application: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of this application
     * 
     * @param string $status Must be one of the APPLICATION_STATUSES constants
     * @return bool
     */
    public function updateStatus(string $status): bool
    {
        if (!in_array($status, self::APPLICATION_STATUSES)) {
            throw new AppException('Invalid application status');
        }

        try {
            return $this->update(['status' => $status]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to update application status: ' . $e->getMessage());
        }
    }

    /**
     * Book a slot for this application
     * 
     * @param int $vacancySlotId
     * @return ApplicationSlot
     */
    public function bookSlot(int $vacancySlotId): ApplicationSlot
    {
        try {
            return DB::transaction(function () use ($vacancySlotId) {
                // Verify the slot belongs to the vacancy of this application
                $slot = VacancySlot::findOrFail($vacancySlotId);
                if ($slot->vacancy_id != $this->vacancy_id) {
                    throw new AppException('This slot does not belong to the vacancy of this application');
                }
                
                return $this->slots()->create([
                    'vacancy_slot_id' => $vacancySlotId,
                ]);
            });
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to book slot: ' . $e->getMessage());
        }
    }

       /**
     * Create a new interview
     * 
     * @param int $applicationId
     * @param int $userId
     * @param \DateTime $date
     * @param string $location
     * @param string|null $zoomLink
     * @return Interview
     */
    public function createInterview(
        int $userId,
        \DateTime $date,
        string $type,
        ?string $location = null,
        ?string $zoomLink = null,
    ): Interview {
        try {
            return DB::transaction(function () use ($userId, $date, $type, $location, $zoomLink) {
                 $ret = $this->interviews()->create([
                    'user_id' => $userId,
                    'date' => $date,
                    'type' => $type,
                    'location' => $location,
                    'zoom_link' => $zoomLink,
                    'status' => self::STATUS_PENDING,
                ]);

                $this->moveToInterview();

                return $ret;
            });
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to create interview: ' . $e->getMessage());
        }
    }

    /**
     * Add an answer to this application
     * 
     * @param string $answer
     * @param Model $answerable The model this answer is for (polymorphic)
     * @return ApplicationAnswer
     */
    public function addAnswer(string $answer, Model $answerable): ApplicationAnswer
    {
        try {
            return $this->answers()->create([
                'answerable_type' => get_class($answerable),
                'answerable_id' => $answerable->id,
                'answer' => $answer,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to add answer: ' . $e->getMessage());
        }
    }

    /**
     * Shortlist this application
     * 
     * @return bool
     */
    public function shortlist(): bool
    {
        return $this->updateStatus(self::STATUS_SHORTLISTED);
    }

    /**
     * Move this application to interview stage
     * 
     * @return bool
     */
    public function moveToInterview(): bool
    {
        return $this->updateStatus(self::STATUS_INTERVIEW);
    }

    /**
     * Hire the applicant
     * 
     * @return bool
     */
    public function hire(): bool
    {
        return $this->updateStatus(self::STATUS_HIRED);
    }

    /**
     * Reject the application
     * 
     * @return bool
     */
    public function reject(): bool
    {
        return $this->updateStatus(self::STATUS_REJECTED);
    }
}
