<?php

namespace App\Models\Benefits\Payrolls;

use App\Models\Benefits\Configurations\BaseBenefit;
use App\Models\Personel\Employee;
use Illuminate\Database\Eloquent\Model;

class BenefitPayment extends Model
{
    const MORPH_NAME = 'benefit_payment';
    protected $table = 'benefit_payments';
    protected $fillable = [
        'employee_id',
        'benefit_id',
        'amount',
        'status',
        'payroll_id',
        'desc',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
    const STATUS_REJECTED = 'rejected';

    const STATUS_LIST = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_PAID,
        self::STATUS_REJECTED,
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function baseBenefit()
    {
        return $this->belongsTo(BaseBenefit::class);
    }
    
    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
