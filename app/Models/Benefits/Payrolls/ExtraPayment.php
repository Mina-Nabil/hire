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
        'extra_payment_date',
        'extra_payment_amount',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
