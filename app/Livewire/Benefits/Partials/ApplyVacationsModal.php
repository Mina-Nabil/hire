<?php

namespace App\Livewire\Benefits\Partials;

use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Vacations\VacationDetail;
use App\Models\Personel\Employee;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Benefits\Configurations\VacationPackage;
use App\Models\Benefits\Payrolls\AppliedVacation;
use Illuminate\Support\Facades\Log;

class ApplyVacationsModal extends Component
{
    use AlertFrontEnd;

    public $packages = [];

    public $selectedEmployee;
    public $showApplyPackageModal = false;
    public $selectedPackage;
    public $selectedPackageId;

    public $vacationBenefits;

    public $packageStartDate;
    public $packageEndDate;
    public $showApplyVacationsModal = false;
    public $selectedVacationPackage;
    public $deleteOldConf = true;

    public $listeners = ['editVacations'];

    public function editVacations($employeeId)
    {
        $this->closeApplyVacationsModal();
        $this->selectedEmployee = Employee::findOrFail($employeeId);

        $this->selectedPackageId = $this->selectedEmployee->benefitConfiguration?->vacation_package_id;
        if (!$this->selectedPackageId) {
            $this->showApplyVacationsModal = true;
            return;
        }

        $this->selectedPackage = VacationPackage::with(['vacationDetails'])->findOrFail($this->selectedPackageId);
        $this->vacationBenefits = $this->selectedEmployee->vacationBenefits()->current(Carbon::now())
            ->byPackage($this->selectedPackageId)->get()->map(function ($benefit) {
                $tmpVacationDetail = VacationDetail::find($benefit->vacation_detail_id);
                return [
                    'vacation_detail_id' => $benefit->vacation_detail_id,
                    'name' => $benefit->name,
                    'inc_rate' => $benefit->inc_rate,
                    'max_balance' => $benefit->max_balance,
                    'hour_price' => $benefit->hour_price,
                    'current_balance' => $benefit->current_balance,
                    'original_current_balance' => $benefit->current_balance,
                    'type' => $benefit->type,
                    'start_date' => $benefit->start_date,
                    'end_date' => $benefit->end_date,
                    'inc_rate_min' => $tmpVacationDetail->inc_rate_min,
                    'inc_rate_max' => $tmpVacationDetail->inc_rate_max,
                    'max_balance_min' => $tmpVacationDetail->max_balance_min,
                    'max_balance_max' => $tmpVacationDetail->max_balance_max,
                    'hour_price_min' => $tmpVacationDetail->hour_price_min,
                    'hour_price_max' => $tmpVacationDetail->hour_price_max,
                    'automatic_add_to_balance' => $benefit->automatic_add_to_balance ?? false,
                    'is_disabled' => true,
                ];
            })->toArray();

        if (count($this->vacationBenefits)) {
            if ($this->vacationBenefits[0]['start_date']) {
                $this->packageStartDate = Carbon::parse($this->vacationBenefits[0]['start_date'])->format('Y-m-d');
            }
            if ($this->vacationBenefits[0]['end_date']) {
                $this->packageEndDate = Carbon::parse($this->vacationBenefits[0]['end_date'])->format('Y-m-d');
            }
        }

        $this->updatedSelectedPackageId();

        $this->showApplyVacationsModal = true;
    }

    public function closeApplyVacationsModal()
    {
        $this->showApplyVacationsModal = false;
        $this->reset([
            'showApplyVacationsModal',
            'selectedEmployee',
            'selectedPackageId',
            'selectedPackage',
            'vacationBenefits',

        ]);
    }

    public function updatedSelectedPackageId()
    {
        if (!$this->selectedPackageId) {
            return;
        }

        $this->selectedPackage = VacationPackage::with(['vacationDetails'])->findOrFail($this->selectedPackageId);

        // Initialize vacation benefits with min values
        $this->vacationBenefits = $this->selectedPackage->vacationDetails->map(function ($detail) {

            return [
                'start_date' => $detail->start_date,
                'end_date' => $detail->end_date,
                'vacation_detail_id' => $detail->id,
                'name' => $detail->name,
                'inc_rate' => $detail->inc_rate_min,
                'inc_rate_min' => $detail->inc_rate_min,
                'inc_rate_max' => $detail->inc_rate_max,
                'max_balance' => $detail->max_balance_min,
                'max_balance_min' => $detail->max_balance_min,
                'max_balance_max' => $detail->max_balance_max,
                'hour_price' => $detail->hour_price_min,
                'hour_price_min' => $detail->hour_price_min,
                'hour_price_max' => $detail->hour_price_max,
                'automatic_add_to_balance' => false,
                'is_disabled' => false,
                'type' => $detail->type
            ];
        })->toArray();


        foreach ($this->vacationBenefits as $key => $benefit) {
            $this->updateCurrentBalance($key);
        }
    }

    public function updateCurrentBalance($key)
    {
        $value = $this->vacationBenefits[$key]['inc_rate'];
        $currentBalance = $this->vacationBenefits[$key]['original_current_balance'] ?? 0;
        $startOfThisYear = Carbon::now()->startOfYear();
        $startDate = Carbon::parse($this->vacationBenefits[$key]['start_date'] ?? $this->packageStartDate);
        $startDate = $startDate->isBefore($startOfThisYear) ? $startOfThisYear : $startDate;
        switch ($this->vacationBenefits[$key]['type']) {
            case VacationDetail::TYPE_YEARLY:
                $endOfYear = $startDate->clone()->endOfYear();
                $leftRatio = $endOfYear->diffInDays($startDate, true) / 365;
                $appliedVacations = AppliedVacation::getAppliedHours($this->selectedEmployee->id, $startDate, $endOfYear, $this->vacationBenefits[$key]['name'], $this->vacationBenefits[$key]['id'] ?? null);
                $this->vacationBenefits[$key]['current_balance'] = round(($value * $leftRatio) - $appliedVacations, 2);
                break;
            case VacationDetail::TYPE_MONTHLY:
                $endOfMonth = $startDate->clone()->endOfMonth();
                $leftRatio = $endOfMonth->diffInDays($startDate, true) / 30;
                $appliedVacations = AppliedVacation::getAppliedHours($this->selectedEmployee->id, $startDate, $endOfMonth, $this->vacationBenefits[$key]['name'], $this->vacationBenefits[$key]['id'] ?? null);
                $this->vacationBenefits[$key]['current_balance'] = round(($value * $leftRatio) - $appliedVacations, 2);
                break;
            case VacationDetail::TYPE_WEEKLY:
                $endOfWeek = $startDate->clone()->endOfWeek();
                $leftRatio = $endOfWeek->diffInDays($startDate, true) / 7;
                $appliedVacations = AppliedVacation::getAppliedHours($this->selectedEmployee->id, $startDate, $endOfWeek, $this->vacationBenefits[$key]['name'], $this->vacationBenefits[$key]['id'] ?? null);
                $this->vacationBenefits[$key]['current_balance'] = round(($value * $leftRatio) - $appliedVacations, 2);
                break;
            case VacationDetail::TYPE_QUARTERLY:
                $endOfQuarter = $startDate->clone()->endOfQuarter();
                $leftRatio = $endOfQuarter->diffInDays($startDate, true) / 90;
                $appliedVacations = AppliedVacation::getAppliedHours($this->selectedEmployee->id, $startDate, $endOfQuarter, $this->vacationBenefits[$key]['name'], $this->vacationBenefits[$key]['id'] ?? null);
                $this->vacationBenefits[$key]['current_balance'] = round(($value * $leftRatio) - $appliedVacations, 2);
                break;

            case VacationDetail::TYPE_DAILY:
                $this->vacationBenefits[$key]['current_balance'] = $value;
                break;
        }
    }

    public function updatedPackageStartDate()
    {
        foreach ($this->vacationBenefits as $key => $benefit) {
            $this->updateCurrentBalance($key, 0);
        }
    }

    public function toggleAutomaticAddToBalance($index)
    {
        // If this benefit is being checked, uncheck all others
        if (!$this->vacationBenefits[$index]['automatic_add_to_balance']) {
            // First, set all to false
            foreach ($this->vacationBenefits as $key => $benefit) {
                $this->vacationBenefits[$key]['automatic_add_to_balance'] = false;
            }
            // Then set only this one to true
            $this->vacationBenefits[$index]['automatic_add_to_balance'] = true;
        } else {
            // If unchecking, just set to false
            $this->vacationBenefits[$index]['automatic_add_to_balance'] = false;
        }
    }

    public function applyVacationPackage()
    {
        $this->validate([
            'selectedPackageId' => 'required|exists:vacation_packages,id',
            'vacationBenefits.*.inc_rate' => 'required|numeric',
            'vacationBenefits.*.max_balance' => 'required|numeric',
            'vacationBenefits.*.hour_price' => 'required|numeric',
            'vacationBenefits.*.current_balance' => 'required|numeric',
            'vacationBenefits.*.automatic_add_to_balance' => 'boolean',
            'packageStartDate' => 'required|date',
            'packageEndDate' => 'nullable|date|after:packageStartDate',
        ], [
            'vacationBenefits.*.inc_rate' => 'Inc Rate#:position is required',
            'vacationBenefits.*.max_balance' => 'Max Balance#:position is required',
            'vacationBenefits.*.hour_price' => 'Hour Price#:position is required',
            'vacationBenefits.*.current_balance' => 'Current Balance#:position is required',
            'packageStartDate' => 'Start Date#:position is required',
            'packageEndDate' => 'End Date#:position is required',
        ]);

        // Custom validation: only one vacation benefit can have automatic_add_to_balance = true
        $automaticAddCount = collect($this->vacationBenefits)->where('automatic_add_to_balance', true)->count();
        if ($automaticAddCount > 1) {
            $this->addError('vacationBenefits', 'Only one vacation benefit can be set to automatically add balance for extra attendance.');
            return;
        }
        try {

            foreach ($this->vacationBenefits as &$benefit) {
                $benefit['vacation_detail_id'] = $benefit['vacation_detail_id'];
                $benefit['start_date'] = $this->packageStartDate;
                if ($this->packageEndDate) {
                    $benefit['end_date'] = $this->packageEndDate;
                } else {
                    $benefit['end_date'] = null;
                }
            }

            $this->selectedEmployee->applyVacationPackage(
                $this->selectedPackage,
                $this->vacationBenefits,
                $this->deleteOldConf
            );

            $this->closeApplyVacationsModal();
            $this->alertSuccess('Vacation package applied successfully.');
        } catch (\Exception $e) {
            $this->alertError('Error applying benefits package: ' . $e->getMessage());
        }
    }

    public function mount()
    {
        $this->packages = VacationPackage::all();
    }

    public function render()
    {
        return view('livewire.benefits.partials.apply-vacations-modal');
    }
}
