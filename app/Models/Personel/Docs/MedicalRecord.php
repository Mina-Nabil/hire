<?php

namespace App\Models\Personel\Docs;

use App\Traits\DocumentModel;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'medical_record';
    const DOC_TYPE = 'medicalRecord';

    const STATUS_NOT_COVERED = 'Not Covered';
    const STATUS_EXAMINATION = 'Examination';
    const STATUS_ISSUING = 'Issuing';
    const STATUS_COVERED = 'Covered';
    const STATUS_EXTERNAL_COVER = 'External Cover';

    const STATUSES = [
        self::STATUS_NOT_COVERED,
        self::STATUS_EXAMINATION,
        self::STATUS_ISSUING,
        self::STATUS_COVERED,
        self::STATUS_EXTERNAL_COVER,
    ];

    const STATUS111_UNAVAILABLE = 'Unavailable';
    const STATUS111_NOT_COMPLETED = 'Not Completed';
    const STATUS111_EXAMINATION = 'Examination';
    const STATUS111_COMPLETED = 'Completed';

    const STATUS111_STATUSES = [
        self::STATUS111_UNAVAILABLE,
        self::STATUS111_NOT_COMPLETED,
        self::STATUS111_EXAMINATION,
        self::STATUS111_COMPLETED,
    ];

    protected $fillable = [
        'employee_id',
        'created_by',
        'file_path',
        'issue_date',
        'expiry_date',
        'status',
        'insurance_number',
        'medical_card_code',
        'medical_card_start',
        'medical_card_expiry',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];
}
