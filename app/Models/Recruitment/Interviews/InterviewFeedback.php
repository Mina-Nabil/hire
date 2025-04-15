<?php

namespace App\Models\Recruitment\Interviews;

use App\Exceptions\AppException;
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
            return $this->save();
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to edit feedback.');
        }
    }
}
