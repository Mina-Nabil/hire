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
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $now = Carbon::now();
        $vacationBenefits = VacationBenefit::current($now)->get();

        foreach ($vacationBenefits as $vacationBenefit) {
            switch ($vacationBenefit->type) {
                case VacationDetail::TYPE_MONTHLY:
                    if ($now->isStartOfMonth()) {
                        $newBalance = min($vacationBenefit->current_balance + $vacationBenefit->inc_rate, $vacationBenefit->max_balance);
                        $vacationBenefit->update([
                            'current_balance' => $newBalance,
                        ]);
                        Log::info("Incrementing monthly vacation benefit for {$vacationBenefit->employee->name} ({$vacationBenefit->employee->id})");
                    }
                    break;
                case VacationDetail::TYPE_YEARLY:
                    if ($now->isStartOfYear()) {
                        $newBalance = min($vacationBenefit->current_balance + $vacationBenefit->inc_rate, $vacationBenefit->max_balance);
                        $vacationBenefit->update([
                            'current_balance' => $newBalance,
                        ]);
                        Log::info("Incrementing yearly vacation benefit for {$vacationBenefit->employee->name} ({$vacationBenefit->employee->id})");
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
                    if ($now->isStartOfWeek()) {
                        $newBalance = min($vacationBenefit->current_balance + $vacationBenefit->inc_rate, $vacationBenefit->max_balance);
                        $vacationBenefit->update([
                            'current_balance' => $newBalance,
                        ]);
                        Log::info("Incrementing weekly vacation benefit for {$vacationBenefit->employee->name} ({$vacationBenefit->employee->id})");
                    }
                    break;

                case VacationDetail::TYPE_QUARTERLY:
                    if ($now->isStartOfQuarter()) {
                        $newBalance = min($vacationBenefit->current_balance + $vacationBenefit->inc_rate, $vacationBenefit->max_balance);
                        $vacationBenefit->update([
                            'current_balance' => $newBalance,
                        ]);
                        Log::info("Incrementing quarterly vacation benefit for {$vacationBenefit->employee->name} ({$vacationBenefit->employee->id})");
                    }
                    break;

                default:
                    break;
            }
        }
    }
}
