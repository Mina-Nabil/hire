<?php

namespace App\Jobs;

use App\Models\Benefits\Payrolls\PeriodicExtraPayment;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GeneratePeriodicExtraPayments implements ShouldQueue
{
    use Queueable;

    /**
     * @param Carbon|null $now Reference date (defaults to now); useful for testing/backfill.
     */
    public function __construct(private ?Carbon $now = null)
    {
        //
    }

    /**
     * Execute the job. For every active periodic template, generate any
     * occurrences that are now due (anchored to each template's start date).
     * Runs daily; templates only produce a payment when their next occurrence
     * date has arrived.
     */
    public function handle(): void
    {
        $now = $this->now ?? Carbon::now();

        $templates = PeriodicExtraPayment::where('is_active', true)->get();
        Log::info("GeneratePeriodicExtraPayments running for {$now->toDateString()} - active templates: {$templates->count()}");

        $generated = 0;
        foreach ($templates as $template) {
            try {
                $payments = $template->generateDuePayments($now);
                foreach ($payments as $payment) {
                    $generated++;
                    Log::info("Generated ExtraPayment #{$payment->id} from template #{$template->id} ({$template->frequency}) for employee {$template->employee_id} due {$payment->due_date} amount {$template->amount}");
                }
                if (empty($payments)) {
                    Log::info("No occurrence due for template #{$template->id} ({$template->frequency})");
                }
            } catch (\Throwable $e) {
                report($e);
                Log::error("Error generating periodic extra payment for template #{$template->id}: {$e->getMessage()}");
            }
        }

        Log::info("GeneratePeriodicExtraPayments finished - generated: {$generated}");
    }
}
