<?php

namespace App\Models\Personel\Docs;

use App\Traits\DocumentModel;
use Illuminate\Database\Eloquent\Model;

class DriverLicense extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'driver_license';
    const DOC_TYPE = 'driverLicense';

    protected $fillable = [
        'employee_id',
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
