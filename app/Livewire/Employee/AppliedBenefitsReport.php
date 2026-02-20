<?php

declare(strict_types=1);

namespace App\Livewire\Employee;

use App\Models\Benefits\Payrolls\AppliedVacation;
use App\Models\Personel\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Layout('components.layouts.app')]
#[Title('Applied Benefits Report')]
class AppliedBenefitsReport extends Component
{
    #[Url(as: 'from')]
    public string $fromDate = '';

    #[Url(as: 'to')]
    public string $toDate = '';

    public function mount(): void
    {
        if (empty($this->fromDate)) {
            $this->fromDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        if (empty($this->toDate)) {
            $this->toDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }
    }

    public function render(): View
    {
        $reportData = $this->getReportData();

        return view('livewire.employee.applied-benefits-report', [
            'reportData' => $reportData,
        ]);
    }

    /**
     * Get report data: employee name, vacation name, total days
     * Grouped by employee name and vacation name (not applied vacation id)
     * Only accepted (approved) vacations
     * Only active employees between the supplied date range
     */
    protected function getReportData(): array
    {
        $from = Carbon::parse($this->fromDate)->startOfDay();
        $to = Carbon::parse($this->toDate)->endOfDay();

        // Active employees: employment_date <= to AND (termination_date IS NULL OR termination_date >= from)
        $activeEmployeeIds = Employee::query()
            ->statusActive()
            ->whereNotNull('employment_date')
            ->where('employment_date', '<=', $to->format('Y-m-d'))
            ->where(function ($q) use ($from) {
                $q->whereNull('termination_date')
                    ->orWhere('termination_date', '>=', $from->format('Y-m-d'));
            })
            ->pluck('id')
            ->toArray();

        if (empty($activeEmployeeIds)) {
            return [];
        }

        // Query: approved applied vacations with vacation_days in range
        // Group by employee_id + vacation name, sum hours from vacation_days
        $rows = AppliedVacation::query()
            ->select([
                'employees.name as employee_name',
                DB::raw('COALESCE(applied_vacations.name, vacation_benefits.name) as vacation_name'),
                DB::raw('SUM(vacation_days.hours) as total_hours'),
            ])
            ->join('employees', 'applied_vacations.employee_id', '=', 'employees.id')
            ->leftJoin('vacation_benefits', 'applied_vacations.vacation_benefit_id', '=', 'vacation_benefits.id')
            ->join('vacation_days', 'applied_vacations.id', '=', 'vacation_days.applied_vacation_id')
            ->where('applied_vacations.status', AppliedVacation::STATUS_APPROVED)
            ->whereIn('applied_vacations.employee_id', $activeEmployeeIds)
            ->whereBetween('vacation_days.vacation_date', [$from->format('Y-m-d'), $to->format('Y-m-d')])
            ->groupBy('employees.id', 'employees.name', 'vacation_name')
            ->get();

        return $rows->map(function ($row) {
            // Convert hours to days (8 hours = 1 day)
            $totalDays = round($row->total_hours / 8, 2);

            return [
                'employee_name' => $row->employee_name,
                'vacation_name' => $row->vacation_name ?? 'N/A',
                'total_days' => $totalDays,
            ];
        })->toArray();
    }
}
