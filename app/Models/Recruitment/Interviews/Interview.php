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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Interview extends Model
{
    const MORPH_NAME = 'interview';

    protected $table = 'interviews';

    protected $fillable = [
        'application_id',
        'user_id',
        'date',
        'location',
        'zoom_link',
        'status',
    ];

    protected $casts = [
        'date' => 'datetime',
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
     * Create a new interview
     * 
     * @param int $applicationId
     * @param int $userId
     * @param \DateTime $date
     * @param string $location
     * @param string|null $zoomLink
     * @return Interview
     */
    public static function createInterview(
        int $applicationId,
        int $userId,
        \DateTime $date,
        string $location,
        ?string $zoomLink = null
    ): Interview {
        try {
            return DB::transaction(function () use ($applicationId, $userId, $date, $location, $zoomLink) {
                return self::create([
                    'application_id' => $applicationId,
                    'user_id' => $userId,
                    'date' => $date,
                    'location' => $location,
                    'zoom_link' => $zoomLink,
                    'status' => self::STATUS_PENDING,
                ]);
            });
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to create interview: ' . $e->getMessage());
        }
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
     * Add an interviewer to this interview
     * 
     * @param int $userId
     * @return void
     */
    public function addInterviewer(int $userId): void
    {
        try {
            $this->interviewers()->attach($userId);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to add interviewer: ' . $e->getMessage());
        }
    }

    /**
     * Remove an interviewer from this interview
     * 
     * @param int $userId
     * @return void
     */
    public function removeInterviewer(int $userId): void
    {
        try {
            $this->interviewers()->detach($userId);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to remove interviewer: ' . $e->getMessage());
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
     * Reschedule this interview
     * 
     * @param \DateTime $newDate
     * @param string|null $newLocation
     * @param string|null $newZoomLink
     * @return bool
     */
    public function reschedule(\DateTime $newDate, ?string $newLocation = null, ?string $newZoomLink = null): bool
    {
        try {
            $data = ['date' => $newDate, 'status' => self::STATUS_RESCHEDULED];
            
            if ($newLocation) {
                $data['location'] = $newLocation;
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
                'user_id' => Auth::id(),
                'title' => $title,
                'note' => $note,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to add note: ' . $e->getMessage());
        }
    }
}
