<?php

namespace App\Livewire\Employee;

use App\Models\Benefits\Payrolls\AppliedVacation;
use App\Models\Personel\Employee;
use App\Models\Benefits\Vacations\VacationBenefit;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Exception;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

#[Title('Apply for Vacation')]
class ApplyForVacation extends Component
{
    use AlertFrontEnd;

    public $selectedEmployee;
    public $childrenEmployees;
    public $employee;
    public $vacationBenefits = [];
    public $selectedBenefitId;
    public $totalHours = 0;
    public $days = [];
    public $fromDate;
    public $toDate;
    public $description;
    public $showConfirmModal = false;
    public $selectedBenefit = null;

    public function mount()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        $this->employee = Employee::where('user_id', $loggedInUser->id)->first();


        if ($loggedInUser->can('applyForAny', AppliedVacation::class)) {
            $this->childrenEmployees = Employee::current()->get();
            if (!$this->employee) {
                $this->employee = $this->childrenEmployees->first();
            }
        } else {
            $this->childrenEmployees = $this->employee->childrenEmployees;
            Log::info('childrenEmployees', [$this->childrenEmployees]);
        }

        if ($this->employee) {
            $this->loadVacationBenefits();
        }
    }

    public function updatedSelectedEmployee()
    {
        if ($this->selectedEmployee) {
            $this->employee = Employee::find($this->selectedEmployee);
        } else {
            $this->employee = Employee::where('user_id', Auth::id())->first();
        }

        if ($this->employee) {
            $this->loadVacationBenefits();
        } else {
            $this->vacationBenefits = [];
        }
    }

    public function loadVacationBenefits()
    {
        $now = Carbon::now();
        $this->vacationBenefits = $this->employee->vacationBenefits()
            ->where('start_date', '<=', $now)
            ->where(function ($query) use ($now) {
                $query->where('end_date', '>=', $now)
                    ->orWhereNull('end_date');
            })
            // ->where('current_balance', '>', 0)
            ->get();
    }

    public function updatedFromDate()
    {
        $this->resetDays();
    }

    public function updatedToDate()
    {
        $this->generateDays();
    }

    public function resetDays()
    {
        $this->days = [];
        $this->totalHours = 0;
    }

    public function generateDays()
    {
        if (empty($this->fromDate) || empty($this->toDate)) {
            return;
        }

        $this->resetDays();

        $startDate = Carbon::parse($this->fromDate);
        $endDate = Carbon::parse($this->toDate);

        if ($startDate->gt($endDate)) {
            $this->alertError('Start date cannot be after end date');
            return;
        }

        $currentDate = $startDate->copy();
        $employeeWorkingDays = $this->employee->workingDays()->get()->pluck('type')->toArray();

        while ($currentDate->lte($endDate)) {

            if (in_array(strtolower($currentDate->format('l')), $employeeWorkingDays)) {
                $this->days[] = [
                    'vacation_date' => $currentDate->format('Y-m-d'),
                    'hours' => 8, // Default to 8 hours per day
                ];
                $this->totalHours += 8;
            }

            $currentDate->addDay();
        }
    }

    public function updatedDays()
    {
        try {
            $this->totalHours = collect($this->days)->sum('hours');
        } catch (Exception $e) {
            $this->alertError("Make sure you set valid hours");
        }
    }

    public function removeDay($index)
    {
        $hours = $this->days[$index]['hours'] ?? 0;
        $this->totalHours -= $hours;
        unset($this->days[$index]);
        $this->days = array_values($this->days);
    }

    public function openConfirmModal()
    {
        if (empty($this->days)) {
            $this->generateDays();
        }

        if (empty($this->days)) {
            $this->alertError('You must select a date range which covers at least one working day');
            return;
        }

        $this->validate([
            'selectedBenefitId' => 'required',
            'days' => 'required|array|min:1',
            'days.*.vacation_date' => 'required|date',
            'days.*.hours' => 'required|numeric|min:1|max:24',
            'description' => 'nullable|string|max:255',
        ]);


        try {
            $this->selectedBenefit = VacationBenefit::findOrFail($this->selectedBenefitId);

            if ($this->selectedBenefit->current_balance < $this->totalHours) {
                $this->alertError('You don\'t have enough balance for this vacation request');
                return;
            }

            $this->showConfirmModal = true;
        } catch (\Exception $e) {
            $this->alertError('Error: ' . $e->getMessage());
        }
    }

    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
    }

    public function submit()
    {
        if (empty($this->days)) {
            $this->alertError('You must add at least one day for vacation');
            return;
        }

        try {
            $vacationBenefit = VacationBenefit::findOrFail($this->selectedBenefitId);

            if ($vacationBenefit->current_balance < $this->totalHours) {
                $this->alertError('You don\'t have enough balance for this vacation request');
                return;
            }

            $this->employee->applyForVacation($vacationBenefit, $this->totalHours, $this->days);

            $this->showConfirmModal = false;
            $this->alertSuccess('Vacation request submitted successfully');
            $this->resetForm();
            $this->loadVacationBenefits();
        } catch (\Exception $e) {
            $this->alertError('Error: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->selectedBenefitId = null;
        $this->fromDate = null;
        $this->toDate = null;
        $this->days = [];
        $this->totalHours = 0;
        $this->description = null;
    }

    public function render()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if ($loggedInUser->can('applyForAny', AppliedVacation::class)) {
            $layout = 'components.layouts.app';
        } else {
            $layout = 'components.layouts.employee';
        }

        return view('livewire.employee.apply-for-vacation', [
            'benefits' => 'active'
        ])->layout($layout);
    }
}
