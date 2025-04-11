<?php

namespace App\Models\Recruitment\Interviews;

use App\Exceptions\AppException;
use App\Models\Recruitment\Applicants\Application;
use App\Models\Users\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Interview extends Model
{
    const MORPH_NAME = 'interview';

    protected $table = 'interviews';

    protected $fillable = [
        'application_id',
        'user_id',
        'type',
        'date',
        'location',
        'zoom_link',
        'status',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    // Interview types
    const TYPE_IN_PERSON = 'in_person';
    const TYPE_ONLINE = 'online';
    const TYPE_PHONE = 'phone';
    const INTERVIEW_TYPES = [
        self::TYPE_IN_PERSON,
        self::TYPE_ONLINE,
        self::TYPE_PHONE,
    ];

    // Interview statuses
    const STATUS_PENDING = 'pending';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_RESCHEDULED = 'rescheduled';

    const INTERVIEW_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_SCHEDULED,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
        self::STATUS_RESCHEDULED,
    ];


    public function getStatusClassAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-info-200',
            self::STATUS_SCHEDULED => 'bg-warning-200',
            self::STATUS_COMPLETED => 'bg-success-200',
            self::STATUS_CANCELLED => 'bg-danger-200',
            self::STATUS_RESCHEDULED => 'bg-warning-200',
        };
    }

    /**
     * Get the application for this interview
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the user who created/scheduled this interview
     */
    public function scheduler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the interviewers (users) assigned to this interview
     */
    public function interviewers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'interview_users');
    }

    /**
     * Get the notes for this interview
     */
    public function notes(): HasMany
    {
        return $this->hasMany(InterviewNote::class);
    }

    /**
     * Get the feedback for this interview
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(InterviewFeedback::class);
    }

    /**
     * Update the status of this interview
     * 
     * @param string $status Must be one of the INTERVIEW_STATUSES constants
     * @return bool
     */
    public function updateStatus(string $status): bool
    {
        if (!in_array($status, self::INTERVIEW_STATUSES)) {
            throw new AppException('Invalid interview status');
        }

        try {
            return $this->update(['status' => $status]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to update interview status: ' . $e->getMessage());
        }
    }

    /**
     * Add multiple interviewers to this interview
     * 
     * @param array $userIds
     * @return void
     */
    public function setInterviewers(array $userIds): void
    {
        try {
            $this->interviewers()->sync($userIds);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to set interviewers: ' . $e->getMessage());
        }
    }


    /**
     * Add feedback to this interview
     * 
     * @param string $result
     * @param int $rating
     * @param string $strengths
     * @param string $weaknesses
     * @param string $feedback
     * @return InterviewFeedback
     */
    public function addFeedback(int $userId, string $result, int $rating, ?string $strengths, ?string $weaknesses, ?string $feedback): InterviewFeedback
    {   
        try {
            return $this->feedbacks()->updateOrCreate([ 
                'interview_id' => $this->id,
                'user_id' => $userId,
            ], [
                'result' => $result,
                'rating' => $rating,
                'strengths' => $strengths,
                'weaknesses' => $weaknesses,
                'feedback' => $feedback,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to add feedback.');
        }
    }

    /**
     * Reschedule this interview
     * 
     * @param \DateTime $newDate
     * @param string|null $newLocation
     * @param string|null $newZoomLink
     * @return bool
     */
    public function reschedule(\DateTime $newDate, 
    ?string $newType = null, 
    ?string $newLocation = null, ?string $newZoomLink = null): bool
    {
        try {
            $data = ['date' => $newDate, 'status' => self::STATUS_RESCHEDULED];
            
            if ($newLocation) {
                $data['location'] = $newLocation;
            }

            if ($newType) {
                $data['type'] = $newType;
            }
            
            if ($newZoomLink !== null) {
                $data['zoom_link'] = $newZoomLink;
            }
            
            return $this->update($data);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to reschedule interview: ' . $e->getMessage());
        }
    }

    /**
     * Cancel this interview
     * 
     * @return bool
     */
    public function cancel(): bool
    {
        return $this->updateStatus(self::STATUS_CANCELLED);
    }

    /**
     * Mark this interview as completed
     * 
     * @return bool
     */
    public function complete(): bool
    {
        return $this->updateStatus(self::STATUS_COMPLETED);
    }

    /**
     * Add a note to this interview
     * 
     * @param int $userId
     * @param string $title
     * @param string|null $note
     * @return InterviewNote
     */
    public function addNote(string $title, ?string $note = null): InterviewNote
    {
        try {
            return $this->notes()->create([
                'user_id' => Auth::id() ?? 1,
                'title' => $title,
                'note' => $note,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to add note: ' . $e->getMessage());
        }
    }

    /**
     * Scope to filter interviews by vacancy ID
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $vacancyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByVacancyId($query, int $vacancyId)
    {
        return $query->whereHas('application', function ($q) use ($vacancyId) {
            $q->where('vacancy_id', $vacancyId);
        });
    }
}
