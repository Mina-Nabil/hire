<?php

namespace App\Models\Personel\Docs;

use App\Traits\DocumentModel;
use Illuminate\Database\Eloquent\Model;

class EmployeeS6Doc extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'employee_s6_doc';
    const DOC_TYPE = 'employeeS6Doc';

    const REASON_RESIGNATION = 'Resignation';
    const REASON_NOT_SHOWING_UP = 'Not showing up';
    const REASON_DISMISSAL = 'Dismissal';
    const REASON_RETIREMENT = 'Retirement';
    const REASON_DEATH = 'Death';

    const LEAVING_REASONS = [
        self::REASON_RESIGNATION,
        self::REASON_NOT_SHOWING_UP,
        self::REASON_DISMISSAL,
        self::REASON_RETIREMENT,
        self::REASON_DEATH,
    ];

    protected $fillable = [
        'employee_id',
        'created_by',
        'file_path',
        's6_number',
        'leaving_reason',
        'issue_date',
        'expiry_date',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];
}
