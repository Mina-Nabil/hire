<?php

namespace App\Models\Recruitment\Interviews;

use App\Exceptions\AppException;
use App\Models\Users\AppLog;
use App\Models\Users\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewFeedback extends Model
{

    protected $table = 'interview_feedbacks';

    protected $fillable = [
        'interview_id',
        'user_id',
        'result',
        'rating',
        'strengths',
        'weaknesses',
        'feedback',
    ];

    const RESULT_PASSED = 'Passed';
    const RESULT_FAILED = 'Failed';
    const RESULT_ON_HOLD = 'On Hold';

    const RESULTS = [
        self::RESULT_ON_HOLD,
        self::RESULT_PASSED,
        self::RESULT_FAILED,
    ];

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function edit(string $result, int $rating, ?string $strengths, ?string $weaknesses, ?string $feedback): bool
    {
        try {
            $this->result = $result;
            $this->rating = $rating;
            $this->strengths = $strengths;
            $this->weaknesses = $weaknesses;
            $this->feedback = $feedback;
            $saved = $this->save();
            AppLog::info('Feedback Updated', 'Feedback updated for interview: ' . $this->interview->id, loggable: $this);
            return $saved;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error updating feedback', $e->getMessage());
            throw new AppException('Failed to edit feedback.');
        }
    }

    public static function stepToStatus(string $step)
    {
        return match ($step) {
            'Move to Next Round' => self::RESULT_PASSED,
            'Make Offer' => self::RESULT_PASSED,
            'Reject' => self::RESULT_FAILED,
            'Keep on Hold' => self::RESULT_ON_HOLD,
        };
    }
}
