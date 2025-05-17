<?php

namespace App\Models\Benefits\Extras;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Benefits\Payrolls\ExtraPayment;
use App\Models\Benefits\Payrolls\BenefitPayment;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Loan extends Model
{

    const MORPH_NAME = 'loan';

    protected $table = 'loans';
    protected $fillable = [
        'employee_id',
        'amount',
        'desc',
        'creator_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    ////static functions////

    /**
     * Create a loan
     * @param Employee $employee
     * @param float $amount
     * @param string|null $desc
     * @param array $payment_plan
     * [
     *  [
     *      'amount' => 1000,
     *      'due_date' => '2025-01-01',
     *  ],
     * ]
     * @return void
     */
    public function createLoan(Employee $employee, float $amount, ?string $desc = null, $payment_plan = [])
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->can('createLoan', $employee)) {
            throw new AppException('You dont have permission to create loan');
        }
        $totalPaymentAmount = 0;
        foreach ($payment_plan as $payment) {
            $totalPaymentAmount += $payment['amount'];
        }
        if ($totalPaymentAmount != $amount) {
            throw new AppException('Total payment amount does not match the loan amount');
        }
        try {
            DB::transaction(function () use ($employee, $amount, $desc, $payment_plan) {
                $loan = new Loan([
                    'employee_id' => $employee->id,
                    'amount' => $amount,
                'desc' => $desc,
                ]);
                $loan->save();
                $i = 1;
                $extraPayment = ExtraPayment::create([
                    'name' => 'Loan Payment to employee ' . $employee->name,
                    'employee_id' => $employee->id,
                    'amount' => $amount,
                    'due_date' => now(),
                    'desc' => "Actual loan payment to employee balance",
                    'status' => BenefitPayment::STATUS_APPROVED,
                    'creator_id' => Auth::id(),
                ]);
                $extraPayment->payable()->associate($loan);
                $extraPayment->save();
                foreach ($payment_plan as $payment) {
                    $extraPayment = ExtraPayment::create([
                        'name' => 'Loan Payment ' . $i++,
                        'employee_id' => $employee->id,
                        'amount' => -1 * $payment['amount'],
                        'due_date' => $payment['due_date'],
                        'desc' => $payment['desc'],
                        'status' => BenefitPayment::STATUS_APPROVED,
                        'creator_id' => Auth::id(),
                    ]);
                    $extraPayment->payable()->associate($loan);
                    $extraPayment->save();
                }
                AppLog::info('Loan Created', "Employee: $employee->name, Amount: $amount, Desc: $desc", loggable: $loan);
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating loan', $e->getMessage());
            throw new AppException('Error creating loan');
        }
    }
}
