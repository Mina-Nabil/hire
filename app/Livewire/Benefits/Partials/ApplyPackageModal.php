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

class ApplyPackageModal extends Component
{
    use AlertFrontEnd;

    public $packages = [];
    public $attendanceCalculations = BenefitConfiguration::ATTENDANCE_CALCULATION_LIST;

    public $selectedEmployee;
    public $showApplyPackageModal = false;

    public $selectedPackage;
    public $selectedPackageId;

    public $packageDetails = [];
    public $grossSalary;
    public $packageStartDate;
    public $packageEndDate;
    public $deleteOldConf = true;

    public $listeners = ['editConfiguration'];

    public function editConfiguration($employeeId)
    {
        $this->closeApplyPackageModal();
        $this->selectedEmployee = Employee::with('position.salaryGrade')->findOrFail($employeeId);




        if ($this->selectedEmployee->benefitConfiguration) {
            $this->selectedPackage = $this->selectedEmployee->benefitConfiguration->salaryGrade;
            $this->selectedPackageId = $this->selectedEmployee->benefitConfiguration->salaryGrade->id;
            $this->grossSalary = $this->selectedEmployee->benefitConfiguration->gross_salary;
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
            'grossSalary',
            'packageStartDate',
            'packageEndDate',
            'deleteOldConf',
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
                'is_hidden' => $detail->is_hidden
            ];
        })->toArray();

    }

    public function applyPackage()
    {
        $this->validate([
            'selectedPackageId' => 'required|exists:salary_grades,id',
            'packageDetails.*.amount' => 'required|numeric',
            'packageStartDate' => 'required|date',
            'packageEndDate' => 'nullable|date|after:packageStartDate',
            'grossSalary' => 'required|numeric|min:1',
        ], [
            'packageDetails.*.amount' => 'Amount#:position is required',
            'packageStartDate.required' => 'Start date is required',
            'packageEndDate.after' => 'End date must be after start date',
            'grossSalary.required' => 'Gross salary is required',
            'grossSalary.numeric' => 'Gross salary must be a number',
            'grossSalary.min' => 'Gross salary must be greater than 0',
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


            $this->selectedEmployee->applyBenefitsPackage(
                $this->selectedPackage,
                $this->grossSalary,
                $this->packageDetails,
                $this->deleteOldConf
            );

            $this->alertSuccess('Benefits package applied successfully!');
            $this->closeApplyPackageModal();
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
        return view('livewire.benefits.partials.apply-package-modal');
    }
}
