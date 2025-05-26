<?php

namespace App\Models\Benefits\Payrolls;

use App\Models\Personel\Employee;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\AppException;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExtraPayment extends Model
{
    const MORPH_NAME = 'extra_payment';
    
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
    const STATUS_REJECTED = 'rejected';

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

    /**
     * Create an extra payment for an employee
     * @param Employee $employee
     * @param string $name
     * @param float $amount
     * @param string $due_date
     * @param string|null $desc
     * @return ExtraPayment
     * @throws AppException
     */
    public static function createExtraPayment(Employee $employee, string $name, float $amount, string $due_date, ?string $desc = null)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->can('createExtraPayment', $employee)) {
            throw new AppException('You dont have permission to create extra payment');
        }

        try {
            $extraPayment = null;
            DB::transaction(function () use ($employee, $name, $amount, $due_date, $desc, &$extraPayment, $user) {
                $extraPayment = self::create([
                    'employee_id' => $employee->id,
                    'creator_id' => $user->id,
                    'name' => $name,
                    'amount' => $amount,
                    'due_date' => $due_date,
                    'desc' => $desc,
                    'status' => self::STATUS_PENDING,
                ]);

                AppLog::info('Extra Payment Created', "Employee: $employee->name, Amount: $amount, Name: $name", loggable: $extraPayment);
            });

            return $extraPayment;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating extra payment', $e->getMessage());
            throw new AppException('Error creating extra payment');
        }
    }
}
