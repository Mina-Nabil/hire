<?php

namespace App\Models\Recruitment\Interviews;

use App\Exceptions\AppException;
use App\Models\Users\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class InterviewNote extends Model
{
    const MORPH_NAME = 'interview_note';

    protected $table = 'interview_notes';

    protected $fillable = [
        'interview_id',
        'user_id',
        'title',
        'note',
    ];

    /**
     * Get the interview that this note belongs to
     */
    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }

    /**
     * Get the user who created this note
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new interview note
     * 
     * @param int $interviewId
     * @param int $userId
     * @param string $title
     * @param string|null $note
     * @return InterviewNote
     */
    public static function createNote(
        int $interviewId,
        string $title,
        ?string $note = null
    ): InterviewNote {
        try {
            return self::create([
                'interview_id' => $interviewId,
                'user_id' => Auth::id(),
                'title' => $title,
                'note' => $note,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to create interview note: ' . $e->getMessage());
        }
    }

    /**
     * Update this note
     * 
     * @param string $title
     * @param string|null $note
     * @return bool
     */
    public function updateNote(string $title, ?string $note = null): bool
    {
        try {
            return $this->update([
                'title' => $title,
                'note' => $note,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to update interview note: ' . $e->getMessage());
        }
    }
}
