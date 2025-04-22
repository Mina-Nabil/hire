<?php

namespace App\Models\Personel\Docs;

use App\Models\Base\Bank;
use App\Traits\DocumentModel;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'bank_account';

    protected $fillable = [
        'employee_id',
        'created_by',
        'file_path',
        'issue_date', //credit card issue date
        'expiry_date', //credit card expiry date
        'bank_id',
        'account_number',
        'bank_employee_code',
        'old_bank_code',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    ///relations
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
