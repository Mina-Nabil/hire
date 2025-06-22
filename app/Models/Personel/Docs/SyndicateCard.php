<?php

namespace App\Models\Personel\Docs;

use App\Traits\DocumentModel;
use Illuminate\Database\Eloquent\Model;

class SyndicateCard extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'syndicate_card';
    const DOC_TYPE = 'syndicateCard';

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
