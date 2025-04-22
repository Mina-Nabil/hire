<?php

namespace App\Models\Personel\Docs;

use App\Traits\DocumentModel;
use Illuminate\Database\Eloquent\Model;

class EmployeeS2Doc extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'employee_s2_doc';

    protected $fillable = [
        'employee_id',
        'created_by',
        'file_path',
        's2_amount',
        'year',
        'issue_date',
        'expiry_date',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];  
}   
