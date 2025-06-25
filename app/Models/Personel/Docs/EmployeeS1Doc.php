<?php

namespace App\Models\Personel\Docs;

use App\Models\Base\InsuranceOffice;
use App\Traits\DocumentModel;
use Illuminate\Database\Eloquent\Model;

class EmployeeS1Doc extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'employee_s1_doc';
    const DOC_TYPE = 'employeeS1Doc';

    protected $fillable = [
        'employee_id',
        'created_by',
        'file_path',
        'issue_date',
        's1_number'
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];



}
