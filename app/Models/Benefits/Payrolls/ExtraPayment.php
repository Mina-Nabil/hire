<?php

namespace App\Models\Benefits\Payrolls;

use App\Models\Personel\Employee;
use Illuminate\Database\Eloquent\Model;

class ExtraPayment extends Model
{
    const MORPH_NAME = 'extra_payment';
    protected $table = 'extra_payments';
    protected $fillable = [
        'employee_id',
        'creator_id',
        'name',
        'amount',
        'due_date',
        'desc',
        'status',
        'payable_id',
        'payable_type',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payable()
    {
        return $this->morphTo();
    }
}
