<?php

namespace App\Models\Personel\Docs;

use App\Traits\DocumentModel;
use Illuminate\Database\Eloquent\Model;

class BirthCertificate extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'birth_certificate';

    const TYPE_COPY = 'Copy';
    const TYPE_VERIFIED_COPY = 'Verified Copy';
    const TYPE_ORIGINAL = 'Original';

    const TYPES = [
        self::TYPE_COPY,
        self::TYPE_VERIFIED_COPY,
        self::TYPE_ORIGINAL,
    ];

    protected $fillable = [
        'employee_id',
        'type',
        'created_by',
        'file_path',
        'issue_date',
        'expiry_date',
    ];  

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];
}
