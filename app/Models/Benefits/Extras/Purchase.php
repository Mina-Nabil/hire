<?php

namespace App\Models\Benefits\Extras;

use App\Exceptions\AppException;
use App\Models\Benefits\Payrolls\BenefitPayment;
use App\Models\Benefits\Payrolls\ExtraPayment;
use App\Models\Personel\Employee;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Purchase extends Model
{
    const MORPH_NAME = 'purchase';
    protected $table = 'purchases';
    protected $fillable = [
        'employee_id',
        'amount',
        'desc',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

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
    public function createPurchase(Employee $employee, float $amount, ?string $desc = null, $payment_plan = [])
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->can('createPurchase', $employee)) {
            throw new AppException('You dont have permission to create purchase');
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
                $purchase = new Purchase([
                    'employee_id' => $employee->id,
                    'amount' => $amount,
                    'desc' => $desc,
                ]);
                $purchase->save();
                $i = 1;

                $extraPayment = ExtraPayment::create([
                    'name' => 'Purchase Payment to employee ' . $employee->name,
                    'employee_id' => $employee->id,
                    'amount' => $amount,
                    'due_date' => now(),
                    'desc' => "Actual purchase payment to employee balance",
                    'creator_id' => Auth::id(),
                ]);
                $extraPayment->payable()->associate($purchase);
                $extraPayment->save();
                foreach ($payment_plan as $payment) {
                    $extraPayment = ExtraPayment::create([
                        'name' => 'Purchase Payment ' . $i++,
                        'employee_id' => $employee->id,
                        'amount' => -1 * $payment['amount'],
                        'due_date' => $payment['due_date'],
                        'desc' => $payment['desc'],
                        'status' => BenefitPayment::STATUS_APPROVED,
                        'creator_id' => Auth::id(),
                    ]);
                    $extraPayment->payable()->associate($purchase);
                    $extraPayment->save();
                }
                AppLog::info('Purchase Created', "Employee: $employee->name, Amount: $amount, Desc: $desc", loggable: $purchase);
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating purchase', $e->getMessage());
            throw new AppException('Error creating purchase');
        }
    }
}
