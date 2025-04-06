<?php

namespace App\Models\Recruitment\Vacancies;

use App\Models\Recruitment\Applicants\Application;
use Illuminate\Database\Eloquent\Model;

class BaseQuestion extends Model
{
    protected $fillable = [
        'question',
        'type',
        'options',
        'created_at',
        'updated_at'
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

}
