<?php

namespace App\Models\Recruitment\Applicants;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Recruitment\Interviews\Interview;
use App\Models\Recruitment\Interviews\InterviewFeedback;
use App\Models\Recruitment\JobOffers\JobOffer;
use App\Models\Recruitment\Vacancies\Vacancy;
use App\Models\Recruitment\Vacancies\VacancySlot;
use App\Models\Users\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
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
    const STATUS_OFFER = 'offer';
    const STATUS_HIRED = 'hired';
    const STATUS_REJECTED = 'rejected';

    const APPLICATION_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_SHORTLISTED,
        self::STATUS_INTERVIEW,
        self::STATUS_OFFER,
        self::STATUS_HIRED,
        self::STATUS_REJECTED,
    ];

    ////attributes
    public function getStatusClassAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-info-200',
            self::STATUS_SHORTLISTED => 'bg-warning-200',
            self::STATUS_INTERVIEW => 'bg-primary-200',
            self::STATUS_OFFER => 'bg-success-200',
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
     * Get the feedbacks for this application.
     */
    public function feedbacks(): HasManyThrough
    {
        return $this->hasManyThrough(InterviewFeedback::class, Interview::class);
    }

    /**
     * Get the slots booked for this application.
     */
    public function slots(): HasMany
    {
        return $this->hasMany(ApplicationSlot::class);
    }

    /**
     * Get the job offer for this application.
     */
    public function jobOffer(): HasOne
    {
        return $this->hasOne(JobOffer::class);
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
            return self::updateOrCreate([
                'applicant_id' => $applicantId,
                'vacancy_id' => $vacancyId
            ], [
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

        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('create', Interview::class)) {
            throw new AppException(__('misc.not_authorized'));
        }

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
            $newAnswer = new ApplicationAnswer();
            $newAnswer->application_id = $this->id;
            $newAnswer->answer = $answer;
            $newAnswer->answerable()->associate($answerable);
            $newAnswer->save();
            return $newAnswer;
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

    /**
     * Offer the applicant
     * 
     * @param float $salary
     * @param \DateTime $proposed_start_date
     * @param \DateTime $expiry_date
     * @param string|null $benefits
     * @param string|null $notes
     * @return bool
     */
    public function offer(float $salary, \DateTime $proposed_start_date, \DateTime $expiry_date, ?string $benefits = null, ?string $notes = null)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('create', JobOffer::class)) {
            throw new AppException(__('misc.not_authorized'));
        }

        $vacancy = $this->vacancy;
        $hiringManager = $vacancy->hiring_manager;
        $hr = $vacancy->hr_manager;

        $hr_approved = false;
        $hiring_manager_approved = false;

        foreach ($this->feedbacks as $feedback) {
            if ($feedback->result == InterviewFeedback::RESULT_PASSED) {
                if ($feedback->user_id == $hiringManager->id) {
                    $hiring_manager_approved = true;
                } else if ($feedback->user_id == $hr->id) {
                    $hr_approved = true;
                }
            }
        }

        if (!$hr_approved) {
            throw new AppException('The application is not approved by the HR');
        }

        if (!$hiring_manager_approved) {
            throw new AppException('The application is not approved by the hiring manager');
        }
        try {
            DB::transaction(function () use ($salary, $proposed_start_date, $expiry_date, $benefits, $notes) {
                $this->jobOffer()->updateOrCreate([
                    'application_id' => $this->id,
                ], [
                    'offered_salary' => $salary,
                    'proposed_start_date' => $proposed_start_date,
                    'expiry_date' => $expiry_date,
                    'benefits' => $benefits,
                    'notes' => $notes,
                ]);
                $this->applicant->hire();
                return $this->updateStatus(self::STATUS_OFFER);
            });
            return true;
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to create offer');
        }
    }
}
