<?php

namespace App\Models\Personel\Docs;

use App\Traits\DocumentModel;
use Illuminate\Database\Eloquent\Model;

class IDCard extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'id_card';

    protected $fillable = [
        'employee_id',
        'created_by',
        'id_number',
        'file_path',
        'issue_date',
        'expiry_date',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

}
