<?php

namespace App\Models\Benefits\Payrolls;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Users\AppLog;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeriodicExtraPayment extends Model
{
    const MORPH_NAME = 'periodic_extra_payment';

    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_QUARTERLY = 'quarterly';

    const FREQUENCIES = [
        self::FREQUENCY_MONTHLY,
        self::FREQUENCY_QUARTERLY,
    ];

    protected $table = 'periodic_extra_payments';

    protected $fillable = [
        'employee_id',
        'creator_id',
        'name',
        'amount',
        'desc',
        'frequency',
        'start_date',
        'is_active',
        'last_generated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'last_generated_at' => 'date',
        'amount' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Extra payments that were generated from this periodic template.
     */
    public function generatedPayments()
    {
        return $this->morphMany(ExtraPayment::class, 'payable');
    }

    /**
     * Number of months between occurrences for this frequency.
     */
    public function intervalMonths(): int
    {
        return $this->frequency === self::FREQUENCY_QUARTERLY ? 3 : 1;
    }

    /**
     * Compute the next occurrence date strictly after $after. Always anchored
     * to start_date + k * interval (never to the previous occurrence), so
     * month-end dates do not drift over time.
     */
    public function nextDueAfter(Carbon $after): Carbon
    {
        $interval = $this->intervalMonths();
        $k = 1;
        do {
            $next = $this->start_date->copy()->addMonthsNoOverflow($interval * $k);
            $k++;
        } while ($next->lessThanOrEqualTo($after));

        return $next;
    }

    /**
     * Create a recurring periodic extra payment template for an employee.
     * The first occurrence (on $startDate) is created immediately by the caller,
     * so last_generated_at is seeded to $startDate.
     *
     * @throws AppException
     */
    public static function createPeriodicExtraPayment(Employee $employee, string $name, float $amount, string $frequency, $startDate, ?string $desc = null): PeriodicExtraPayment
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->can('createExtraPayment', $employee)) {
            throw new AppException('You dont have permission to create periodic extra payment');
        }

        if (!in_array($frequency, self::FREQUENCIES, true)) {
            throw new AppException('Invalid periodic extra payment frequency');
        }

        try {
            $periodicExtraPayment = null;
            $anchor = Carbon::parse($startDate)->toDateString();
            DB::transaction(function () use ($employee, $name, $amount, $frequency, $anchor, $desc, $user, &$periodicExtraPayment) {
                $periodicExtraPayment = self::create([
                    'employee_id' => $employee->id,
                    'creator_id' => $user->id,
                    'name' => $name,
                    'amount' => $amount,
                    'desc' => $desc,
                    'frequency' => $frequency,
                    'start_date' => $anchor,
                    'is_active' => true,
                    'last_generated_at' => $anchor,
                ]);

                AppLog::info('Periodic Extra Payment Created', "Employee: $employee->name, Amount: $amount, Frequency: $frequency, Name: $name", loggable: $periodicExtraPayment);
            });

            return $periodicExtraPayment;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating periodic extra payment', $e->getMessage());
            throw new AppException('Error creating periodic extra payment');
        }
    }

    /**
     * Materialize every occurrence that is now due (up to $now) into concrete
     * ExtraPayment records. Runs in a queue context (no authenticated user), so
     * it creates the ExtraPayments directly and carries the template's creator_id.
     *
     * @return ExtraPayment[]
     */
    public function generateDuePayments(?Carbon $now = null): array
    {
        $now = ($now ?? Carbon::now())->copy()->startOfDay();

        if (!$this->is_active) {
            return [];
        }

        $generated = [];
        DB::transaction(function () use ($now, &$generated) {
            $last = $this->last_generated_at ? $this->last_generated_at->copy() : $this->start_date->copy();

            // Catch up on every occurrence due on or before today (bounded).
            for ($guard = 0; $guard < 240; $guard++) {
                $nextDue = $this->nextDueAfter($last);
                if ($nextDue->copy()->startOfDay()->greaterThan($now)) {
                    break;
                }

                $extraPayment = ExtraPayment::create([
                    'name' => $this->name ?: (ucfirst($this->frequency) . ' Extra Payment'),
                    'employee_id' => $this->employee_id,
                    'amount' => $this->amount,
                    'due_date' => $nextDue->toDateString(),
                    'desc' => $this->desc,
                    'status' => ExtraPayment::STATUS_APPROVED,
                    'creator_id' => $this->creator_id,
                ]);
                $extraPayment->payable()->associate($this);
                $extraPayment->save();

                $generated[] = $extraPayment;
                $last = $nextDue->copy();
                $this->last_generated_at = $nextDue->toDateString();

                AppLog::info('Periodic Extra Payment Generated', "Employee ID: {$this->employee_id}, Amount: {$this->amount}, Frequency: {$this->frequency}, Due: {$nextDue->toDateString()}", loggable: $extraPayment);
            }

            if (!empty($generated)) {
                $this->save();
            }
        });

        return $generated;
    }

    /**
     * Stop future generation for this template. Already-generated extra
     * payments are kept untouched.
     *
     * @throws AppException
     */
    public function deactivate(): void
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->can('deleteExtraPayment', $this->employee)) {
            throw new AppException('You dont have permission to stop periodic extra payment');
        }

        try {
            $this->is_active = false;
            $this->save();

            AppLog::info('Periodic Extra Payment Stopped', "Employee: {$this->employee->name}, Name: {$this->name}, Amount: {$this->amount}", loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error stopping periodic extra payment', $e->getMessage());
            throw new AppException('Error stopping periodic extra payment');
        }
    }
}
