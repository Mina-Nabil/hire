<?php

namespace App\Livewire\Benefits\Partials;

use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Configurations\SalaryGrade;
use App\Models\Benefits\Configurations\PackageDetail;
use App\Models\Benefits\Vacations\VacationDetail;
use App\Models\Personel\Employee;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Models\Benefits\Configurations\VacationPackage;

class ApplyVacationsModal extends Component
{
    use AlertFrontEnd;

    public $packages = [];
    public $attendanceCalculations = BenefitConfiguration::ATTENDANCE_CALCULATION_LIST;

    public $selectedEmployee;
    public $showApplyPackageModal = false;
    public $selectedPackage;
    public $selectedPackageId;
    public $packageDetails;
    public $vacationBenefits;
    public $attendanceCalculation;
    public $workingDayStartMin;
    public $workingDayStartMax;
    public $workingDayEndMin;
    public $workingDayEndMax;
    public $dailyWorkingHours;
    public $overtimeRate;
    public $packageStartDate;
    public $packageEndDate;
    public $showApplyVacationsModal = false;
    public $selectedVacationPackage;
    public $deleteOldConf = true;

    public $listeners = ['editConfiguration'];

    public function editConfiguration($employeeId)
    {
        $this->closeApplyPackageModal();
        $this->selectedEmployee = Employee::with('position.salaryGrade')->findOrFail($employeeId);

        if ($this->selectedEmployee->benefitConfiguration) {
            $this->selectedPackage = $this->selectedEmployee->benefitConfiguration->salaryGrade;
            $this->selectedPackageId = $this->selectedEmployee->benefitConfiguration->salaryGrade->id;
            $this->attendanceCalculation = $this->selectedEmployee->benefitConfiguration->attendace_calculation;
            $this->workingDayStartMin = $this->selectedEmployee->benefitConfiguration->working_day_start_min;
            $this->workingDayStartMax = $this->selectedEmployee->benefitConfiguration->working_day_start_max;
            $this->workingDayEndMin = $this->selectedEmployee->benefitConfiguration->working_day_end_min;
            $this->workingDayEndMax = $this->selectedEmployee->benefitConfiguration->working_day_end_max;
            $this->dailyWorkingHours = $this->selectedEmployee->benefitConfiguration->daily_working_hours;
            $this->overtimeRate = $this->selectedEmployee->benefitConfiguration->overtime_rate;
        } else {
            $this->selectedPackage = $this->selectedEmployee->position->salaryGrade;
            $this->selectedPackageId = $this->selectedEmployee->position->salary_grade_id;
        }

        $this->packageDetails = $this->selectedEmployee->baseBenefits()
            ->bySalaryGrade($this->selectedPackageId)->get()->map(function ($benefit) {
                $tmpPackageDetail = PackageDetail::find($benefit->package_detail_id);
                return [
                    'package_detail_id' => $benefit->package_detail_id,
                    'name' => $benefit->name,
                    'amount' => $benefit->amount,
                    'type' => $benefit->type,
                    'receiver' => $benefit->receiver,
                    'is_hidden' => $benefit->is_hidden,
                    'start_date' => $benefit->start_date,
                    'end_date' => $benefit->end_date,
                    'amount_min' => $tmpPackageDetail->amount_min,
                    'amount_max' => $tmpPackageDetail->amount_max,
                ];
            });

        $this->vacationBenefits = $this->selectedEmployee->vacationBenefits()->byPackage($this->selectedPackageId)->get()->map(function ($benefit) {
            $tmpVacationDetail = VacationDetail::find($benefit->vacation_detail_id);
            return [
                'vacation_detail_id' => $benefit->vacation_detail_id,
                'name' => $benefit->name,
                'inc_rate' => $benefit->inc_rate,
                'max_balance' => $benefit->max_balance,
                'hour_price' => $benefit->hour_price,
                'current_balance' => $benefit->current_balance,
                'type' => $benefit->type,
                'start_date' => $benefit->start_date,
                'end_date' => $benefit->end_date,
                'inc_rate_min' => $tmpVacationDetail->inc_rate_min,
                'inc_rate_max' => $tmpVacationDetail->inc_rate_max,
                'max_balance_min' => $tmpVacationDetail->max_balance_min,
                'max_balance_max' => $tmpVacationDetail->max_balance_max,
                'hour_price_min' => $tmpVacationDetail->hour_price_min,
                'hour_price_max' => $tmpVacationDetail->hour_price_max,
            ];
        });

        if (count($this->packageDetails)) {
            if ($this->packageDetails[0]['start_date']) {
                $this->packageStartDate = Carbon::parse($this->packageDetails[0]['start_date'])->format('Y-m-d');
            }
            if ($this->packageDetails[0]['end_date']) {
                $this->packageEndDate = Carbon::parse($this->packageDetails[0]['end_date'])->format('Y-m-d');
            }
        }

        $this->showApplyPackageModal = true;
    }

    public function closeApplyPackageModal()
    {
        $this->showApplyPackageModal = false;
        $this->reset([
            'showApplyPackageModal',
            'selectedEmployee',
            'selectedPackageId',
            'selectedPackage',
            'packageDetails',
            'vacationBenefits',
            'attendanceCalculation',
            'workingDayStartMin',
            'workingDayStartMax',
            'workingDayEndMin',
            'workingDayEndMax',
            'dailyWorkingHours',
            'overtimeRate'
        ]);
    }

    public function loadPackageDetails()
    {
        if (!$this->selectedPackageId) {
            return;
        }

        $this->selectedPackage = SalaryGrade::with(['packageDetails', 'vacationDetails'])->findOrFail($this->selectedPackageId);

        // Initialize package details with min values
        $this->packageDetails = $this->selectedPackage->packageDetails->map(function ($detail) {
            return [
                'start_date' => $detail->start_date,
                'end_date' => $detail->end_date,
                'package_detail_id' => $detail->id,
                'name' => $detail->name,
                'amount' => $detail->amount_min,
                'amount_min' => $detail->amount_min,
                'amount_max' => $detail->amount_max,
                'type' => $detail->type,
                'receiver' => 'employee',
                'is_net' => $detail->is_net,
                'is_gross' => $detail->is_gross,
                'is_grand_gross' => $detail->is_grand_gross,
                'is_hidden' => $detail->is_hidden
            ];
        })->toArray();

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
                'type' => $detail->type
            ];
        })->toArray();

        // Set default working hours
        $this->workingDayStartMin = '09:00';
        $this->workingDayStartMax = '09:00';
        $this->workingDayEndMin = '17:00';
        $this->workingDayEndMax = '17:00';
        $this->dailyWorkingHours = 8;
        $this->overtimeRate = 1.5;
    }

    public function applyPackage()
    {
        $this->validate([
            'selectedPackageId' => 'required|exists:salary_grades,id',
            'packageDetails.*.amount' => 'required|numeric',
            'vacationBenefits.*.inc_rate' => 'required|numeric',
            'vacationBenefits.*.max_balance' => 'required|numeric',
            'vacationBenefits.*.hour_price' => 'required|numeric',
            'vacationBenefits.*.current_balance' => 'required|numeric',
            'attendanceCalculation' => 'required|in:' . implode(',', BenefitConfiguration::ATTENDANCE_CALCULATION_LIST),
            'workingDayStartMin' => 'required',
            'workingDayStartMax' => 'required',
            'workingDayEndMin' => 'required',
            'workingDayEndMax' => 'required',
            'dailyWorkingHours' => 'required|numeric|min:1|max:24',
            'overtimeRate' => 'required|numeric|min:1',
            'packageStartDate' => 'required|date',
            'packageEndDate' => 'nullable|date|after:packageStartDate',
        ], [
            'packageDetails.*.amount' => 'Amount#:position is required',
            'vacationBenefits.*.inc_rate' => 'Inc Rate#:position is required',
            'vacationBenefits.*.max_balance' => 'Max Balance#:position is required',
            'vacationBenefits.*.hour_price' => 'Hour Price#:position is required',
            'vacationBenefits.*.current_balance' => 'Current Balance#:position is required',
            'attendanceCalculation' => 'Attendance Calculation#:position is required',
            'workingDayStartMin' => 'Working Day Start Min#:position is required',
            'workingDayStartMax' => 'Working Day Start Max#:position is required',
            'workingDayEndMin' => 'Working Day End Min#:position is required',
            'workingDayEndMax' => 'Working Day End Max#:position is required',
            'dailyWorkingHours' => 'Daily Working Hours#:position is required',
        ]);
        try {
            foreach ($this->packageDetails as &$detail) {
                $detail['package_detail_id'] = $detail['package_detail_id'];
                $detail['start_date'] = $this->packageStartDate;
                if ($this->packageEndDate) {
                    $detail['end_date'] = $this->packageEndDate;
                } else {
                    $detail['end_date'] = null;
                }
            }
            foreach ($this->vacationBenefits as &$benefit) {
                $benefit['vacation_detail_id'] = $benefit['vacation_detail_id'];
                $benefit['start_date'] = $this->packageStartDate;
                if ($this->packageEndDate) {
                    $benefit['end_date'] = $this->packageEndDate;
                } else {
                    $benefit['end_date'] = null;
                }
            }

            $this->selectedEmployee->applyBenefitsPackage(
                $this->selectedPackage,
                $this->packageDetails,
                $this->vacationBenefits,
                $this->attendanceCalculation,
                $this->workingDayStartMin,
                $this->workingDayStartMax,
                $this->workingDayEndMin,
                $this->workingDayEndMax,
                $this->dailyWorkingHours,
                $this->overtimeRate
            );

            $this->closeApplyPackageModal();
            $this->alertSuccess('Benefits package applied successfully.');
        } catch (\Exception $e) {
            $this->alertError('Error applying benefits package: ' . $e->getMessage());
        }
    }

    public function applyVacation()
    {
        $this->validate([
            'selectedVacationPackage' => 'required|exists:vacation_packages,id',
        ]);

        try {
            $this->selectedEmployee->applyVacationPackage(
                $this->selectedVacationPackage,
                $this->deleteOldConf
            );

            $this->alertSuccess('Vacation package applied successfully!');
            $this->closeApplyVacationsModal();
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function mount()
    {
        $this->packages = SalaryGrade::all();
    }

    public function render()
    {
        return view('livewire.benefits.partials.apply-package-modal', [
            'vacationPackages' => VacationPackage::all(),
        ]);
    }
}
