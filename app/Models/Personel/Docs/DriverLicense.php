<?php

namespace App\Models\Personel\Docs;

use App\Traits\DocumentModel;
use Illuminate\Database\Eloquent\Model;

class DriverLicense extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'driver_license';
    const DOC_TYPE = 'driverLicense';

    // Type enum constants
    const TYPE_PROFESSIONAL_LEVEL_1 = 'Professional Level 1';
    const TYPE_PROFESSIONAL_LEVEL_2 = 'Professional Level 2';
    const TYPE_PROFESSIONAL_LEVEL_3 = 'Professional Level 3';
    const TYPE_PRIVATE = 'Private';
    const TYPE_AGRICULTURE_EQUIPMENT = 'Agriculture Equipment';

    protected $fillable = [
        'employee_id',
        'created_by',
        'file_path',
        'issue_date',
        'expiry_date',
        'type',
        'note',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];
}
