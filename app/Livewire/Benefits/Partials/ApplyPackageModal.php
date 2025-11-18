<?php

namespace App\Livewire\Benefits\Partials;

use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Configurations\SalaryGrade;
use App\Models\Benefits\Configurations\PackageDetail;
use App\Models\Personel\Employee;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Livewire\Component;

class ApplyPackageModal extends Component
{
    use AlertFrontEnd;

    public $packages = [];
    public $managersList = [];

    public $selectedEmployee;
    public $showApplyPackageModal = false;

    public $selectedPackage;
    public $selectedPackageId;
    public $managerId;
    public $packageDetails = [];
    public $grossSalary;
    public $insuranceAmount;
    public $packageStartDate;
    public $packageEndDate;
    public $deleteOldConf = true;
    public $isTaxable = true;

    public $listeners = ['editConfiguration'];

    public function editConfiguration($employeeId)
    {
        $this->closeApplyPackageModal();
        $this->selectedEmployee = Employee::with('position.salaryGrade')->findOrFail($employeeId);

        if ($this->selectedEmployee->benefitConfiguration?->salaryGrade && $this->selectedEmployee->position->salaryGrade->id == $this->selectedEmployee->benefitConfiguration->salaryGrade->id) {
            $this->selectedPackage = $this->selectedEmployee->benefitConfiguration->salaryGrade;
            $this->selectedPackageId = $this->selectedEmployee->benefitConfiguration->salaryGrade->id;
            $this->grossSalary = $this->selectedEmployee->benefitConfiguration->gross_salary;
            $this->insuranceAmount = $this->selectedEmployee->benefitConfiguration->insurance_amount;
            $this->packageStartDate = Carbon::parse($this->selectedEmployee->benefitConfiguration->start_date)->format('Y-m-d');
            $this->isTaxable = $this->selectedEmployee->benefitConfiguration->is_taxable ?? true;

            $savedPackageDetails = $this->selectedEmployee->baseBenefits()
                ->bySalaryGrade($this->selectedPackageId)->get()->mapWithKeys(function ($benefit) {
                    $tmpPackageDetail = PackageDetail::find($benefit->package_detail_id);
                    return [
                        $tmpPackageDetail->name => [
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
                        ]
                    ];
                })->toArray();

            $defaultPackageDetails = PackageDetail::bySalaryGrade($this->selectedPackageId)->get()->mapWithKeys(function ($detail) {
                return [
                    $detail->name => [
                        'package_detail_id' => $detail->id,
                        'name' => $detail->name,
                        'type' => $detail->type,
                        'receiver' => $detail->receiver,
                        'is_hidden' => $detail->is_hidden,
                        'start_date' => $detail->start_date,
                        'end_date' => $detail->end_date,
                        'amount_min' => $detail->amount_min,
                        'amount_max' => $detail->amount_max,
                    ]
                ];
            })->toArray();
            $this->packageDetails = array_merge($defaultPackageDetails, $savedPackageDetails);

            $this->managersList = $this->selectedEmployee->position?->potentialManagers;
        } else if ($this->selectedEmployee->position?->salaryGrade) {
            $this->selectedPackage = $this->selectedEmployee->position->salaryGrade;
            $this->selectedPackageId = $this->selectedEmployee->position->salary_grade_id;

            $this->packageDetails = PackageDetail::bySalaryGrade($this->selectedPackageId)->get()->mapWithKeys(function ($detail) {
                return [$detail->name => [
                    'package_detail_id' => $detail->id,
                    'name' => $detail->name,
                    'type' => $detail->type,
                    'receiver' => $detail->receiver,
                    'is_hidden' => $detail->is_hidden,
                    'start_date' => $detail->start_date,
                    'end_date' => $detail->end_date,
                    'amount_min' => $detail->amount_min,
                    'amount_max' => $detail->amount_max,
                ]];
            })->toArray();

            $this->managersList = $this->selectedEmployee->position?->potentialManagers;
        } else {
            $this->showApplyPackageModal = true;
            return;
        }



        if (!$this->packageStartDate && count($this->packageDetails)) {
            if (isset($this->packageDetails[array_key_first($this->packageDetails)]['start_date'])) {
                $this->packageStartDate = Carbon::parse($this->packageDetails[array_key_first($this->packageDetails)]['start_date'])->format('Y-m-d');
            }
            if (isset($this->packageDetails[array_key_first($this->packageDetails)]['end_date'])) {
                $this->packageEndDate = Carbon::parse($this->packageDetails[array_key_first($this->packageDetails)]['end_date'])->format('Y-m-d');
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
            'insuranceAmount',
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

        $this->selectedPackage = SalaryGrade::with(['packageDetails'])->findOrFail($this->selectedPackageId);

        // Initialize package details with min values
        $this->packageDetails = $this->selectedPackage->packageDetails->map(function ($detail) {
            return [
                'start_date' => $detail->start_date,
                'end_date' => $detail->end_date,
                'package_detail_id' => $detail->id,
                'name' => $detail->name,
                'amount' => $detail->amount_max,
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
            'grossSalary' => 'required|numeric|min:' . $this->selectedPackage->gross_min . '|max:' . $this->selectedPackage->gross_max,
            'insuranceAmount' => 'required|numeric',
        ], [
            'packageDetails.*.amount' => 'Amount#:position is required',
            'packageStartDate.required' => 'Start date is required',
            'packageEndDate.after' => 'End date must be after start date',
            'grossSalary.required' => 'Gross salary is required',
            'grossSalary.numeric' => 'Gross salary must be a number',
            'grossSalary.min' => 'Gross salary must be greater than ' . $this->selectedPackage->gross_min,
            'grossSalary.max' => 'Gross salary must be less than ' . $this->selectedPackage->gross_max,
            'insuranceAmount.required' => 'Social Insurance Salary is required',
            'insuranceAmount.numeric' => 'Social Insurance Salary must be a number',
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
                Carbon::parse($this->packageStartDate),
                $this->packageDetails,
                $this->grossSalary,
                $this->insuranceAmount,
                $this->managerId,
                $this->deleteOldConf,
                $this->isTaxable
            );

            $this->alertSuccess('Benefits package applied successfully!');
            $this->closeApplyPackageModal();
            $this->dispatch('refreshConfiguration');
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
