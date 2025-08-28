<?php

namespace App\Livewire\Benefits;

use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Configurations\SalaryGrade;
use App\Models\Benefits\Configurations\PackageDetail;
use App\Models\Hierarchy\Department;
use App\Models\Personel\Employee;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithPagination;

class BulkBenefitsIndex extends Component
{
    use AlertFrontEnd, WithPagination;

    public $employeesData = [];
    public $packages = [];
    public $perPage = 5;
    public $search = '';
    public $selectedDepartment = '';
    public $departments = [];
    public $showBenefitsModal = false;
    public $modalEmployeeId = null;

    protected $listeners = ['refreshBulkBenefits' => '$refresh'];

    public function mount()
    {
        $this->packages = SalaryGrade::all();
        $this->loadDepartments();
    }

    public function loadDepartments()
    {
        $this->departments = Department::all()->toArray();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedDepartment()
    {
        $this->resetPage();
    }

    public function getEmployeesProperty()
    {
        $query = Employee::with([
            'position.salaryGrade',
            'benefitConfiguration.salaryGrade',
            'position.department',
            'baseBenefits' => function ($query) {
                $query->whereNull('end_date');
            }
        ])
        ->current();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedDepartment) {
            $query->whereHas('position.department', function ($q) {
                $q->where('id', $this->selectedDepartment);
            });
        }

        return $query->paginate($this->perPage);
    }

    public function initializeEmployeeData($employeeId)
    {
        if (isset($this->employeesData[$employeeId])) {
            return;
        }

        $employee = Employee::with([
            'position.salaryGrade',
            'benefitConfiguration.salaryGrade',
            'baseBenefits' => function ($query) {
                $query->whereNull('end_date');
            }
        ])->find($employeeId);

        if (!$employee) {
            return;
        }

        $employeeData = [
            'employee' => $employee,
            'selectedPackageId' => '',
            'selectedPackage' => null,
            'packageDetails' => [],
            'grossSalary' => '',
            'insuranceAmount' => '',
            'managerId' => '',
            'packageStartDate' => '',
            'packageEndDate' => '',
            'deleteOldConf' => true,
            'isTaxable' => true,
            'managersList' => [],
            'isLoading' => false,
            'errors' => []
        ];

        // Load existing configuration if available
        if ($employee->benefitConfiguration && $employee->benefitConfiguration->salaryGrade && $employee->position->salaryGrade->id == $employee->benefitConfiguration->salaryGrade->id) {
            $employeeData['selectedPackageId'] = $employee->benefitConfiguration->salaryGrade->id;
            $employeeData['selectedPackage'] = $employee->benefitConfiguration->salaryGrade;
            $employeeData['grossSalary'] = $employee->benefitConfiguration->gross_salary;
            $employeeData['insuranceAmount'] = $employee->benefitConfiguration->insurance_amount;
            $employeeData['managerId'] = $employee->benefitConfiguration->manager_id;
            $employeeData['isTaxable'] = $employee->benefitConfiguration->is_taxable ?? true;

            // Load existing package details
            $savedPackageDetails = $employee->baseBenefits()
                ->bySalaryGrade($employee->benefitConfiguration->salaryGrade->id)
                ->get()
                ->mapWithKeys(function ($benefit) {
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

            $defaultPackageDetails = PackageDetail::bySalaryGrade($employee->benefitConfiguration->salaryGrade->id)
                ->get()
                ->mapWithKeys(function ($detail) {
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

            $employeeData['packageDetails'] = array_merge($defaultPackageDetails, $savedPackageDetails);

            if (count($employeeData['packageDetails'])) {
                $firstDetail = reset($employeeData['packageDetails']);
                if (isset($firstDetail['start_date'])) {
                    $employeeData['packageStartDate'] = Carbon::parse($firstDetail['start_date'])->format('Y-m-d');
                }
                if (isset($firstDetail['end_date'])) {
                    $employeeData['packageEndDate'] = Carbon::parse($firstDetail['end_date'])->format('Y-m-d');
                }
            }
        } else if ($employee->position && $employee->position->salaryGrade) {
            // Load default from position
            $employeeData['selectedPackageId'] = $employee->position->salary_grade_id;
            $employeeData['selectedPackage'] = $employee->position->salaryGrade;

            $employeeData['packageDetails'] = PackageDetail::bySalaryGrade($employee->position->salary_grade_id)
                ->get()
                ->mapWithKeys(function ($detail) {
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
        }

        if ($employee->position && $employee->position->potentialManagers) {
            $employeeData['managersList'] = $employee->position->potentialManagers;
        }

        $this->employeesData[$employeeId] = $employeeData;
    }

    public function loadPackageDetails($employeeId)
    {
        if (!isset($this->employeesData[$employeeId])) {
            return;
        }

        $packageId = $this->employeesData[$employeeId]['selectedPackageId'];
        if (!$packageId) {
            return;
        }

        $package = SalaryGrade::with(['packageDetails'])->find($packageId);
        if (!$package) {
            return;
        }

        $this->employeesData[$employeeId]['selectedPackage'] = $package;
        // $this->employeesData[$employeeId]['packageStartDate'] = Carbon::parse($package->start_date)->format('Y-m-d');
        $this->employeesData[$employeeId]['packageDetails'] = $package->packageDetails->mapWithKeys(function ($detail) {
            return [
                $detail->name => [
                    'package_detail_id' => $detail->id,
                    'name' => $detail->name,
                    'amount' => $detail->amount_min,
                    'type' => $detail->type,
                    'receiver' => 'employee',
                    'is_hidden' => $detail->is_hidden,
                    'start_date' => $detail->start_date,
                    'end_date' => $detail->end_date,
                    'amount_min' => $detail->amount_min,
                    'amount_max' => $detail->amount_max,
                ]
            ];
        })->toArray();

        // Initialize package start and end dates from package details (same as ApplyPackageModal.php)
        if (!$this->employeesData[$employeeId]['packageStartDate'] && count($this->employeesData[$employeeId]['packageDetails'])) {
            $firstDetail = reset($this->employeesData[$employeeId]['packageDetails']);
            if (isset($firstDetail['start_date'])) {
                $this->employeesData[$employeeId]['packageStartDate'] = Carbon::parse($firstDetail['start_date'])->format('Y-m-d');
            }
            if (isset($firstDetail['end_date'])) {
                $this->employeesData[$employeeId]['packageEndDate'] = Carbon::parse($firstDetail['end_date'])->format('Y-m-d');
            }
        }
    }

    public function openBenefitsModal($employeeId)
    {
        $this->modalEmployeeId = $employeeId;
        $this->showBenefitsModal = true;
    }

    public function closeBenefitsModal()
    {
        $this->showBenefitsModal = false;
        $this->modalEmployeeId = null;
    }

    public function saveEmployeeBenefits($employeeId)
    {
        if (!isset($this->employeesData[$employeeId])) {
            $this->alertError('Employee data not found');
            return;
        }

        $employeeData = $this->employeesData[$employeeId];
        $employee = $employeeData['employee'];
        $selectedPackage = SalaryGrade::find($employeeData['selectedPackageId']);

        // Individual validation for this employee
        $rules = [
            'selectedPackageId' => 'required|exists:salary_grades,id',
            'packageDetails.*.amount' => 'required|numeric',
            'packageStartDate' => 'required|date',
            'packageEndDate' => 'nullable|date|after:packageStartDate',
            'grossSalary' => 'required|numeric',
            'insuranceAmount' => 'required|numeric',
        ];

        if ($selectedPackage) {
            $rules['grossSalary'] .= '|min:' . $selectedPackage->gross_min . '|max:' . $selectedPackage->gross_max;
        }

        $validator = Validator::make([
            'selectedPackageId' => $employeeData['selectedPackageId'],
            'packageDetails' => $employeeData['packageDetails'],
            'packageStartDate' => $employeeData['packageStartDate'],
            'packageEndDate' => $employeeData['packageEndDate'],
            'grossSalary' => $employeeData['grossSalary'],
            'insuranceAmount' => $employeeData['insuranceAmount'],
        ], $rules, [
            'packageDetails.*.amount' => 'Amount is required for all benefits',
            'packageStartDate.required' => 'Start date is required',
            'packageEndDate.after' => 'End date must be after start date',
            'grossSalary.required' => 'Gross salary is required',
            'grossSalary.numeric' => 'Gross salary must be a number',
            'grossSalary.min' => 'Gross salary must be at least ' . ($selectedPackage ? $selectedPackage->gross_min : 0),
            'grossSalary.max' => 'Gross salary cannot exceed ' . ($selectedPackage ? $selectedPackage->gross_max : 0),
            'insuranceAmount.required' => 'Social Insurance Salary is required',
            'insuranceAmount.numeric' => 'Social Insurance Salary must be a number',
        ]);

        if ($validator->fails()) {
            $this->employeesData[$employeeId]['errors'] = $validator->errors()->toArray();
            $this->alertError('Validation failed for employee: ' . $employee->name);
            return;
        }

        try {
            $this->employeesData[$employeeId]['isLoading'] = true;
            $this->employeesData[$employeeId]['errors'] = [];

            // Prepare package details with dates
            $packageDetails = $employeeData['packageDetails'];
            foreach ($packageDetails as &$detail) {
                $detail['start_date'] = $employeeData['packageStartDate'];
                $detail['end_date'] = $employeeData['packageEndDate'] ?: null;
            }

            // Apply benefits package
            $employee->applyBenefitsPackage(
                $selectedPackage,
                Carbon::parse($employeeData['packageStartDate']),
                array_values($packageDetails),
                $employeeData['grossSalary'],
                $employeeData['insuranceAmount'],
                $employeeData['managerId'],
                $employeeData['deleteOldConf'],
                $employeeData['isTaxable']
            );

            $this->alertSuccess('Benefits package applied successfully for ' . $employee->name);
            $this->employeesData[$employeeId]['isLoading'] = false;

        } catch (\Exception $e) {
            $this->employeesData[$employeeId]['isLoading'] = false;
            $this->employeesData[$employeeId]['errors'] = ['general' => [$e->getMessage()]];
            $this->alertError('Failed to apply benefits for ' . $employee->name . ': ' . $e->getMessage());
        }
    }

    public function render()
    {
        $employees = $this->getEmployeesProperty();
        return view('livewire.benefits.bulk-benefits-index', compact('employees'));
    }
} 