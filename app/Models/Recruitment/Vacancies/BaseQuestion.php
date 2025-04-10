<?php

namespace App\Models\Recruitment\Vacancies;

use App\Exceptions\AppException;
use App\Models\Recruitment\Applicants\Application;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BaseQuestion extends Model
{
    use HasFactory;
    const MORPH_NAME = 'base_question';
    
    protected $fillable = [
        'question',
        'type',
        'options',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'options' => 'array'
    ];

    const TYPE_TEXT = 'text';
    const TYPE_NUMBER = 'number';
    const TYPE_DATE = 'date';
    const TYPE_SELECT = 'select';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RADIO = 'radio';
    const TYPE_TEXTAREA = 'textarea';

    const TYPES = [
        self::TYPE_TEXT,
        self::TYPE_NUMBER,
        self::TYPE_DATE,
        self::TYPE_SELECT,
        self::TYPE_CHECKBOX,
        self::TYPE_RADIO,
        self::TYPE_TEXTAREA,
    ];

    /**
     * Create a new question
     * 
     * @param string $questionText
     * @param string $type
     * @param array|null $options
     * @return BaseQuestion
     */
    public function createNewQuestion($questionText, $type, $options = null)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('create', BaseQuestion::class)) {
            throw new AppException(__('misc.not_authorized'));
        }

        $this->question = $questionText;
        $this->type = $type;
        
        if (in_array($type, [self::TYPE_SELECT, self::TYPE_CHECKBOX, self::TYPE_RADIO]) && !empty($options)) {
            if (is_string($options)) {
                $options = explode(',', $options);
                $options = array_map('trim', $options);
            }
            $this->options = $options;
        }
        
        $this->save();
        
        return $this;
    }

    /**
     * Update this question
     * 
     * @param string $questionText
     * @param string $type
     * @param array|null $options
     * @return BaseQuestion
     */
    public function updateQuestion($questionText, $type, $options = null)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', BaseQuestion::class)) {
            throw new AppException(__('misc.not_authorized'));
        }

        $this->question = $questionText;
        $this->type = $type;
        
        if (in_array($type, [self::TYPE_SELECT, self::TYPE_CHECKBOX, self::TYPE_RADIO]) && !empty($options)) {
            if (is_string($options)) {
                $options = explode(',', $options);
                $options = array_map('trim', $options);
            }
            $this->options = $options;
        } else {
            $this->options = null;
        }
        
        $this->save();
        
        return $this;
    }

    /**
     * Delete this question
     * 
     * @return bool
     */
    public function deleteQuestion()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('delete', BaseQuestion::class)) {
            throw new AppException(__('misc.not_authorized'));
        }
        
        return $this->delete();
    }
}
