<?php

namespace App\Jobs;

use App\Models\Benefits\Vacations\VacationBenefit;
use App\Models\Benefits\Vacations\VacationDay;
use App\Models\Benefits\Vacations\VacationDetail;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class IncrementVacationBenefits implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private $forceType = null, private $benefitName = null)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $now = Carbon::now();
        $vacationBenefits = VacationBenefit::current($now)->when($this->benefitName, function ($query) {
            $query->whereHas('vacationDetail', function ($q) {
                $q->where('vacation_benefits.name', $this->benefitName);
            });
        })->get();

        Log::info("Incrementing vacation benefits for {$now->toDateString()}");
        Log::info("Force type: {$this->forceType}");
        Log::info("Vacation benefits: " . $vacationBenefits->count());

        foreach ($vacationBenefits as $vacationBenefit) {
            switch ($vacationBenefit->type) {
                case VacationDetail::TYPE_MONTHLY:
                    if ($this->forceType === VacationDetail::TYPE_MONTHLY || $now->dayOfMonth === 1) {
                        $newBalance = min($vacationBenefit->current_balance + $vacationBenefit->inc_rate, $vacationBenefit->max_balance);
                        $vacationBenefit->update([
                            'current_balance' => $newBalance,
                        ]);
                        Log::info("Incrementing monthly vacation benefit for {$vacationBenefit->employee->name} ({$vacationBenefit->employee->id}) - {$vacationBenefit->current_balance} -> {$newBalance}");
                    }
                    break;
                case VacationDetail::TYPE_YEARLY:
                    if ($this->forceType === VacationDetail::TYPE_YEARLY || $now->dayOfYear === 1) {
                        $newBalance = min($vacationBenefit->current_balance + $vacationBenefit->inc_rate, $vacationBenefit->max_balance);
                        $vacationBenefit->update([
                            'current_balance' => $newBalance,
                        ]);
                        Log::info("Incrementing yearly vacation benefit for {$vacationBenefit->employee->name} ({$vacationBenefit->employee->id}) - {$vacationBenefit->current_balance} -> {$newBalance}");
                    }
                    break;

                case VacationDetail::TYPE_DAILY:
                    $newBalance = min($vacationBenefit->current_balance + $vacationBenefit->inc_rate, $vacationBenefit->max_balance);
                    $vacationBenefit->update([
                        'current_balance' => $newBalance,
                    ]);
                    Log::info("Incrementing daily vacation benefit for {$vacationBenefit->employee->name} ({$vacationBenefit->employee->id})");
                    break;

                case VacationDetail::TYPE_WEEKLY:
                    if ($this->forceType === VacationDetail::TYPE_WEEKLY || $now->dayOfWeek === 6) {
                        $newBalance = min($vacationBenefit->current_balance + $vacationBenefit->inc_rate, $vacationBenefit->max_balance);
                        $vacationBenefit->update([
                            'current_balance' => $newBalance,
                        ]);
                        Log::info("Incrementing weekly vacation benefit for {$vacationBenefit->employee->name} ({$vacationBenefit->employee->id}) - {$vacationBenefit->current_balance} -> {$newBalance}");
                    }
                    break;

                case VacationDetail::TYPE_QUARTERLY:
                    if ($this->forceType === VacationDetail::TYPE_QUARTERLY || $now->dayOfQuarter === 1) {
                        $newBalance = min($vacationBenefit->current_balance + $vacationBenefit->inc_rate, $vacationBenefit->max_balance);
                        $vacationBenefit->update([
                            'current_balance' => $newBalance,
                        ]);
                        Log::info("Incrementing quarterly vacation benefit for {$vacationBenefit->employee->name} ({$vacationBenefit->employee->id}) - {$vacationBenefit->current_balance} -> {$newBalance}");
                    }
                    break;

                default:
                    break;
            }
        }
    }
}
