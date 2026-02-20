<?php

declare(strict_types=1);

namespace App\Livewire\Benefits;

use App\Models\Personel\Employee;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Add Bulk Missions')]
class AddBulkMissions extends Component
{
    use AlertFrontEnd;

    public array $selectedEmployeeIds = [];

    public string $startDate = '';

    public string $endDate = '';

    public string $hours = '';

    public bool $showResults = false;

    public array $results = [
        'success' => [],
        'failed' => [],
    ];

    public function mount(): void
    {
        $user = Auth::user();
        if (!$user?->is_admin) {
            abort(403, 'Only administrators can access this page.');
        }

        $this->startDate = Carbon::now()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    public function getEmployeesProperty()
    {
        return Employee::query()
            ->current()
            ->orderBy('name')
            ->get();
    }

    public function selectAll(): void
    {
        $employees = $this->employees;
        if (count($this->selectedEmployeeIds) === $employees->count()) {
            $this->selectedEmployeeIds = [];
        } else {
            $this->selectedEmployeeIds = $employees->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        }
    }

    public function submit(): void
    {
        $this->validate([
            'selectedEmployeeIds' => 'required|array|min:1',
            'selectedEmployeeIds.*' => 'exists:employees,id',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'hours' => 'required|numeric|min:0.25',
        ]);

        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $totalHours = (float) $this->hours;

        $days = $this->buildDaysArray($start, $end, $totalHours);

        if (empty($days)) {
            $this->alertError('The date range must contain at least one day');
            return;
        }

        $this->results = ['success' => [], 'failed' => []];

        foreach ($this->selectedEmployeeIds as $employeeId) {
            $employee = Employee::find((int) $employeeId);
            $employeeName = $employee?->name ?? 'Unknown';

            try {
                if (!$employee) {
                    $this->results['failed'][] = ['name' => $employeeName, 'reason' => 'Employee not found'];
                    continue;
                }

                if (!Auth::user()?->can('applyForVacation', $employee)) {
                    $this->results['failed'][] = ['name' => $employeeName, 'reason' => 'Permission denied'];
                    continue;
                }

                $employee->applyForVacation(null, $totalHours, $days, true, true);

                $this->results['success'][] = ['name' => $employeeName];
            } catch (Exception $e) {
                $this->results['failed'][] = ['name' => $employeeName, 'reason' => $e->getMessage()];
            }
        }

        $this->showResults = true;

        $successCount = count($this->results['success']);
        if ($successCount > 0) {
            $this->alertSuccess("Mission added successfully for {$successCount} employee(s).");
        }
        if (count($this->results['failed']) > 0) {
            $this->alertError(count($this->results['failed']) . ' employee(s) failed.');
        }
    }

    /**
     * Build days array for applyForVacation - distribute hours evenly across the date range
     */
    protected function buildDaysArray(Carbon $start, Carbon $end, float $totalHours): array
    {
        $days = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            $days[] = [
                'vacation_date' => $current->format('Y-m-d'),
                'hours' => 0,
            ];
            $current->addDay();
        }

        $dayCount = count($days);
        if ($dayCount === 0) {
            return [];
        }

        $hoursPerDay = round($totalHours / $dayCount, 2);
        $remainder = round($totalHours - ($hoursPerDay * ($dayCount - 1)), 2);

        foreach ($days as $i => &$day) {
            $day['hours'] = ($i === $dayCount - 1) ? $remainder : $hoursPerDay;
        }

        return $days;
    }

    public function resetForm(): void
    {
        $this->selectedEmployeeIds = [];
        $this->showResults = false;
        $this->results = ['success' => [], 'failed' => []];
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.benefits.add-bulk-missions');
    }
}
