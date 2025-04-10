<?php

namespace App\Models\Recruitment\Vacancies;

use App\Exceptions\AppException;
use App\Models\Hierarchy\Position;
use App\Models\Recruitment\Applicants\Application;
use App\Models\Users\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Vacancy extends Model
{
    const MORPH_NAME = 'vacancy';
    
    const TYPE_FULL_TIME = 'full_time';
    const TYPE_PART_TIME = 'part_time';
    const TYPE_TEMPORARY = 'temporary';

    const TYPE_OPTIONS = [
        self::TYPE_FULL_TIME => 'Full Time',
        self::TYPE_PART_TIME => 'Part Time',
        self::TYPE_TEMPORARY => 'Temporary'
    ];

    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';

    const STATUS_OPTIONS = [
        self::STATUS_OPEN => 'Open',
        self::STATUS_CLOSED => 'Closed'
    ];


    protected $fillable = [
        'assigned_to',
        'position_id',
        'type',
        'status',
        'closing_date',
        'job_responsibilities',
        'arabic_job_responsibilities',
        'job_qualifications',
        'arabic_job_qualifications',
        'job_benefits',
        'arabic_job_benefits',
        'job_salary'
    ];

    protected $casts = [
        'closing_date' => 'date',
        'created_at' => 'date'
    ];

    /**
     * The applications for this vacancy.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * The questions for this vacancy.
     */
    public function vacancy_questions(): HasMany
    {
        return $this->hasMany(VacancyQuestion::class);
    }

    /**
     * The interview slots for this vacancy.
     */
    public function vacancy_slots(): HasMany
    {
        return $this->hasMany(VacancySlot::class);
    }

    /**
     * The position this vacancy is for.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * The user assigned to manage this vacancy.
     */
    public function assigned_to_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * The user assigned to manage this vacancy.
     */
    public function hiring_manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hiring_manager_id');
    }

    /**
     * The user assigned to manage this vacancy.
     */
    public function hr_manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_manager_id');
    }

    /**
     * Scope a query to search vacancies.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->whereHas('position', function($p) use ($search) {
                $p->where('name', 'like', '%' . $search . '%')
                  ->orWhere('arabic_name', 'like', '%' . $search . '%');
            })
            ->orWhere('type', 'like', '%' . $search . '%')
            ->orWhere('status', 'like', '%' . $search . '%');
        });
    }

    /**
     * Create a new vacancy.
     */
    public static function newVacancy(array $data): self
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('create', Vacancy::class)) {
            throw new AppException(__('misc.not_authorized'));
        }

        try {
            $data['status'] = self::STATUS_OPEN;
            $data['closing_date'] = null;

            $vacancy = self::create($data);

            // Create questions if provided
            if (isset($data['questions']) && is_array($data['questions'])) {
                foreach ($data['questions'] as $question) {
                    $vacancy->vacancy_questions()->create($question);
                }
            }

            // Create slots if provided
            if (isset($data['slots']) && is_array($data['slots'])) {
                foreach ($data['slots'] as $slot) {
                    $vacancy->vacancy_slots()->create($slot);
                }
            }

            return $vacancy;
        } catch (Exception $e) {
            report($e);
            throw new AppException(__('misc.something_went_wrong'));
        }
    }

    /**
     * Update this vacancy.
     */
    public function updateVacancy(array $data): self
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException(__('misc.not_authorized'));
        }

        try {
            if($data['status'] == self::STATUS_CLOSED && $this->status != self::STATUS_CLOSED) {
                $data['closing_date'] = now();
            }

            $this->update($data);

            // Update questions if provided
            if (isset($data['questions']) && is_array($data['questions'])) {
                Log::info('questions', $data['questions']);
                // Remove existing questions if any
                if (isset($data['reset_questions']) && $data['reset_questions']) {
                    $this->vacancy_questions()->delete();
                }

                foreach ($data['questions'] as $question) {
                    if (isset($question['id']) && !$data['reset_questions']) {
                        $this->vacancy_questions()->where('id', $question['id'])->update($question);
                    } else {
                        $this->vacancy_questions()->create($question);
                    }
                }
            }

            // Update slots if provided
            if (isset($data['slots']) && is_array($data['slots'])) {
                // Remove existing slots if any
                if (isset($data['reset_slots']) && $data['reset_slots']) {
                    $this->vacancy_slots()->delete();
                }

                foreach ($data['slots'] as $slot) {
                    if (isset($slot['id']) && !$data['reset_slots']) {
                        $this->vacancy_slots()->where('id', $slot['id'])->update($slot);
                    } else {
                        $this->vacancy_slots()->create($slot);
                    }
                }
            }

            return $this;
        } catch (Exception $e) {
            report($e);
            throw new AppException(__('misc.something_went_wrong'));
        }
    }

    /**
     * Delete this vacancy.
     */
    public function deleteVacancy(): bool
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('delete', $this)) {
            throw new AppException(__('misc.not_authorized'));
        }

        try {
            // Check if there are applications
            if ($this->applications()->count() > 0) {
                throw new AppException(__('There are applications linked to this vacancy. Cannot delete.'));
            }

            // Delete related questions and slots
            $this->vacancy_questions()->delete();
            $this->vacancy_slots()->delete();

            // Delete the vacancy
            return $this->delete();
        } catch (AppException $e) {
            throw $e;
        } catch (Exception $e) {
            report($e);
            throw new AppException(__('misc.something_went_wrong'));
        }
    }
}
