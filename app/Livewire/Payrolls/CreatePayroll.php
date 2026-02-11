<?php

namespace App\Livewire\Payrolls;

use App\Models\Attendance\Overtime;
use App\Models\Benefits\Payrolls\Payroll;
use App\Models\Personel\Employee;
use App\Models\Hierarchy\Department;
use App\Models\Users\AppLog;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;

#[Title('Create Payroll')]
class CreatePayroll extends Component
{
    use AlertFrontEnd, WithPagination;

    // Properties for the department selection modal
    public $showDepartmentModal = false;
    public $selectedDepartment = null;
    public $selectedDepartments = [];
    public $selectAllEmployees = true;
    public $selectedEmployees = [];
    public $departments;

    // Properties for the payroll data
    public $startDate;
    public $endDate;
    public $payrollMonth;
    public $payrollData = [];

    // Properties for adjustment modal
    public $showAdjustmentModal = false;
    public $selectedEmployeeForAdjustment = null;
    public $adjustmentAmount = 0;
    public $adjustmentDescription = '';

    // Properties for filtering and display
    public $search = '';
    public $perPage = 10;

    protected $listeners = ['refreshPayroll'];

    protected function ensureArrays()
    {
        // Ensure these properties are always arrays
        if (!is_array($this->selectedDepartments)) {
            $this->selectedDepartments = !empty($this->selectedDepartments) ? [$this->selectedDepartments] : [];
        }

        if (!is_array($this->selectedEmployees)) {
            $this->selectedEmployees = !empty($this->selectedEmployees) ? [$this->selectedEmployees] : [];
        }

        if (!is_array($this->payrollData)) {
            $this->payrollData = [];
        }
    }

    public function hydrate()
    {
        $this->ensureArrays();
    }

    public function selectAllDepartments()
    {
        if (count($this->departments) > 0) {
            $this->selectedDepartments = $this->departments->pluck('id')->toArray();
            $this->loadEmployeesFromMultipleDepartments($this->selectedDepartments);
        }
    }

    public function updatedPayrollMonth($value)
    {
        $newMonth = Carbon::now()->setMonth((int)$value);
        $this->startDate = $newMonth->clone()->setDay(26)->subMonth()->format('Y-m-d');
        $this->endDate = $newMonth->clone()->setDay(25)->format('Y-m-d');
        $this->payrollData = [];
    }

    public function mount()
    {
        $this->payrollMonth = Carbon::now()->month;
        $this->departments = Department::orderBy('name')->get();
        $this->startDate = Carbon::now()->setDay(26)->subMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->setDay(25)->format('Y-m-d');
        $this->ensureArrays();
    }

    public function refreshPayroll()
    {
        $this->loadPayrollData();
    }

    public function openDepartmentModal()
    {
        $this->showDepartmentModal = true;
    }

    public function closeDepartmentModal()
    {
        $this->showDepartmentModal = false;
    }

    public function updatedSelectedDepartment($value)
    {
        if ($this->selectAllEmployees) {
            $this->loadEmployeesFromDepartment($value);
        } else {
            $this->selectedEmployees = [];
        }
    }

    public function updatedSelectedDepartments($value)
    {
        $this->ensureArrays();

        if ($this->selectAllEmployees && !empty($this->selectedDepartments)) {
            $this->loadEmployeesFromMultipleDepartments($this->selectedDepartments);
        } else {
            $this->selectedEmployees = [];
        }
    }

    public function updatedSelectAllEmployees($value)
    {
        $this->ensureArrays();

        if ($value) {
            if ($this->selectedDepartment) {
                $this->loadEmployeesFromDepartment($this->selectedDepartment);
            } elseif (!empty($this->selectedDepartments)) {
                $this->loadEmployeesFromMultipleDepartments($this->selectedDepartments);
            }
        } else {
            $this->selectedEmployees = [];
        }
    }

    public function loadEmployeesFromMultipleDepartments($departmentIds)
    {
        $this->ensureArrays();

        if (empty($departmentIds)) {
            $this->selectedEmployees = [];
            return;
        }

        // Ensure $departmentIds is an array
        if (!is_array($departmentIds)) {
            $departmentIds = [$departmentIds];
        }

        $employees = Employee::whereHas('position', function ($query) use ($departmentIds) {
            $query->whereIn('department_id', $departmentIds);
        })
            ->currentBetween(Carbon::parse($this->startDate), Carbon::parse($this->endDate))
            ->get();

        $this->selectedEmployees = $employees->pluck('id')->toArray();
    }

    public function loadEmployeesFromDepartment($departmentId)
    {
        $this->ensureArrays();

        if (!$departmentId) {
            $this->selectedEmployees = [];
            return;
        }

        $employees = Employee::whereHas('position', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
            ->currentBetween(Carbon::parse($this->startDate), Carbon::parse($this->endDate))
            ->get();

        $this->selectedEmployees = $employees->pluck('id')->toArray();
    }

    public function submitDepartmentSelection()
    {
        $this->ensureArrays();

        if (empty($this->selectedDepartments) && !$this->selectedDepartment) {
            $this->addError('departmentSelection', 'Please select at least one department.');
            return;
        }

        if (empty($this->selectedEmployees)) {
            $this->alertError('Please select at least one employee.');
            return;
        }

        $this->loadPayrollData();
        $this->closeDepartmentModal();
    }

    public function openAdjustmentModal($departmentId, $employeeIndex)
    {
        $this->selectedEmployeeForAdjustment = [$departmentId, $employeeIndex];
        $employee = $this->payrollData[$departmentId]['employees'][$employeeIndex];
        $this->adjustmentAmount = $employee['adj_amount'] ?? 0;
        $this->adjustmentDescription = $employee['adj_desc'] ?? '';
        $this->showAdjustmentModal = true;
    }

    public function closeAdjustmentModal()
    {
        $this->showAdjustmentModal = false;
        $this->selectedEmployeeForAdjustment = null;
        $this->adjustmentAmount = 0;
        $this->adjustmentDescription = '';
    }

    public function saveAdjustment()
    {
        if (!$this->selectedEmployeeForAdjustment) {
            return;
        }

        [$departmentId, $employeeIndex] = $this->selectedEmployeeForAdjustment;

        // Update the employee data with adjustment
        $this->payrollData[$departmentId]['employees'][$employeeIndex]['adj_amount'] = $this->adjustmentAmount;
        $this->payrollData[$departmentId]['employees'][$employeeIndex]['adj_desc'] = $this->adjustmentDescription;

        // Recalculate net after deductions with adjustment
        $employee = &$this->payrollData[$departmentId]['employees'][$employeeIndex];
        $employee['net_after_deductions'] = $employee['net_after_penalty'] + $employee['extra_payments'] + $employee['overtime_amount'] + $this->adjustmentAmount;

        $this->closeAdjustmentModal();
        $this->alertSuccess('Adjustment saved successfully.');
    }

    public function loadPayrollData()
    {
        $this->ensureArrays();

        $this->payrollData = [];

        $employees = Employee::with(['position', 'position.department', 'benefitConfiguration'])
            ->currentBetween(Carbon::parse($this->startDate), Carbon::parse($this->endDate))
            ->whereIn('id', $this->selectedEmployees)
            ->get()
            ->sortBy('position.department.name');

        $departmentGroups = [];
        $totals = [
            'gross_salary' => 0,
            'insurance_amount' => 0,
            'employee_insurance' => 0,
            'employer_insurance' => 0,
            'total_insurance' => 0,
            'other_amount' => 0,
            'employee_medical' => 0,
            'total_medical' => 0,
            'employee_deductions' => 0,
            'penalties_days' => 0,
            'penalties_amount' => 0,
            'extra_payments' => 0,
            'penalties_hours' => 0,
            'overtime_hours' => 0,
            'overtime_amount' => 0,
            'adj_amount' => 0,
        ];

        foreach ($employees as $employee) {
            $departmentName = $employee->position?->department?->name ?? 'No Department';
            $departmentId = $employee->position?->department?->id ?? 0;

            if (!isset($departmentGroups[$departmentId])) {
                $departmentGroups[$departmentId] = [
                    'name' => $departmentName,
                    'employees' => [],
                    'totals' => [
                        'gross_salary' => 0,
                        'insurance_amount' => 0,
                        'employee_insurance' => 0,
                        'employer_insurance' => 0,
                        'total_insurance' => 0,
                        'other_amount' => 0,
                        'net_income' => 0,
                        'penalties_days' => 0,
                        'penalties_amount' => 0,
                        'extra_payments' => 0,
                        'overtime_hours' => 0,
                        'overtime_amount' => 0,
                        'penalties_hours' => 0,
                        'adj_amount' => 0,
                    ]
                ];
            }

            $employeeTerminationDate = is_null($employee->termination_date) ? null : Carbon::parse($employee->termination_date);
            $employeeStartDate = Carbon::parse($employee->employment_date);
            $grossPercentage = 100;

            // Calculate the percentage of the gross salary based on the included days
            if ($employeeStartDate->isAfter($this->startDate) && $employeeTerminationDate && $employeeTerminationDate->isBefore($this->endDate)) {
                $includedDays = $employeeTerminationDate->diffInDays($this->employeeStartDate);
                $grossPercentage = (min($includedDays, 30) / 30) * 100;
            } else if ($employeeTerminationDate && $employeeTerminationDate->isAfter($this->startDate) && $employeeTerminationDate->isBefore($this->endDate)) {
                $includedDays = $employeeTerminationDate->diffInDays($this->startDate);
                $grossPercentage = (min($includedDays, 30) / 30) * 100;
            } else if ($employeeStartDate->isAfter($this->startDate) && $employeeStartDate->isBefore($this->endDate)) {
                $includedDays = $employeeStartDate->diffInDays($this->endDate);
                $grossPercentage = (min($includedDays, 30) / 30) * 100;
            }

            $grossSalary = ($employee->benefitConfiguration?->gross_salary ?? 0) * $grossPercentage / 100;
            $insuranceAmount = $employee->benefitConfiguration?->insurance_amount ?? 0;
            $employeeInsurance = $insuranceAmount * ($employee->age >= 60 ? Payroll::EMPLOYEE_ABOVE_60_SHARE_SOCIAL_INSURANCE : Payroll::EMPLOYEE_SHARE_SOCIAL_INSURANCE);
            $employerInsurance = $insuranceAmount * Payroll::EMPLOYER_SHARE_SOCIAL_INSURANCE;
            $totalInsurance = $employeeInsurance + $employerInsurance;
            $otherAmount = $grossSalary - $insuranceAmount - $employeeInsurance ?? 0;
            $employeeMedical = $employee->activeMedicalBenefits(Carbon::parse($this->startDate), Carbon::parse($this->endDate))->sum('amount');
            $totalMedical = $employeeMedical;
            $employeeDeductions = $employeeInsurance;
            $netIncome = $otherAmount + $insuranceAmount;
            $dayPrice = $netIncome / 30;
            $employeeBaseBenefits = $employee->getEmployeeBaseBenefitsCalculation(Carbon::parse($this->startDate), Carbon::parse($this->endDate));
            $otherBaseBenefits = $employee->getOtherBaseBenefitsCalculation(Carbon::parse($this->startDate), Carbon::parse($this->endDate));


            // Get daily working hours from employee benefit configuration
            $dailyWorkingHours = $employee->benefitConfiguration?->daily_working_hours ?? 8;

            $hourlyRate = $dayPrice / $dailyWorkingHours;
            $penaltyData = $employee->calculatePenaltyWithVacationOffset($this->startDate, $this->endDate, $hourlyRate);

            // Extract penalty information
            $totalPenaltyHours = $penaltyData['total_penalty_hours'];
            $vacationOffsetHours = $penaltyData['vacation_offset_hours'];
            $remainingPenaltyHours = $penaltyData['remaining_penalty_hours'];
            $directDeductionAmount = $penaltyData['direct_deduction_amount'];
            // $usedApprovedVacations = $penaltyData['used_approved_vacations'];
            $availableVacationBenefits = $penaltyData['available_vacation_benefits'];
            $penaltyAmount = $penaltyData['direct_deduction_amount'];

            $netAfterPenalty = $netIncome - $penaltyAmount;

            // Get extra payments
            $extraPayments = $employee->getExtraPayments($this->startDate, $this->endDate);

            // Calculate overtime
            $overtimeHours = 0;
            $overtimeRate = $employee->benefitConfiguration->overtime_rate ?? 1.5;
            $dailyWorkingHours = $employee->benefitConfiguration->daily_working_hours ?? 8;
            $isAutomaticOvertime = $employee->benefitConfiguration->is_automatic_overtime ?? false;
            $createdOvertimeIds = [];
            $potentialOvertimeData = [];


            // Use only explicitly created and approved overtime records
            $overtimeHours = $employee->overtimes()
                ->where('status', Overtime::STATUS_APPROVED)
                // ->whereBetween('approved_at', [$this->startDate, $this->endDate])
                ->whereNull('payroll_id')
                ->sum('hours');

            // Calculate overtime amount
            $overtimeAmount = $overtimeHours * $hourlyRate * $overtimeRate;

            // Initialize adjustment values (will be 0 initially)
            $adjAmount = 0;
            $adjDesc = '';

            // Calculate net income with overtime and adjustment
            $netAfterDeductions = $netAfterPenalty + $overtimeAmount + $adjAmount;

            // Add to department totals
            $departmentGroups[$departmentId]['totals']['gross_salary'] += $grossSalary;
            $departmentGroups[$departmentId]['totals']['insurance_amount'] += $insuranceAmount;
            $departmentGroups[$departmentId]['totals']['employee_insurance'] += $employeeInsurance;
            $departmentGroups[$departmentId]['totals']['employer_insurance'] += $employerInsurance;
            $departmentGroups[$departmentId]['totals']['total_insurance'] += $totalInsurance;
            $departmentGroups[$departmentId]['totals']['penalties_days'] += $remainingPenaltyHours;
            $departmentGroups[$departmentId]['totals']['penalties_amount'] += $penaltyAmount;
            $departmentGroups[$departmentId]['totals']['extra_payments'] += $extraPayments;
            $departmentGroups[$departmentId]['totals']['overtime_hours'] += $overtimeHours;
            $departmentGroups[$departmentId]['totals']['overtime_amount'] += $overtimeAmount;
            $departmentGroups[$departmentId]['totals']['adj_amount'] += $adjAmount;

            // Add to grand totals
            $totals['gross_salary'] += $grossSalary;
            $totals['insurance_amount'] += $insuranceAmount;
            $totals['employee_insurance'] += $employeeInsurance;
            $totals['employer_insurance'] += $employerInsurance;
            $totals['total_insurance'] += $totalInsurance;
            $totals['penalties_hours'] += $totalPenaltyHours;
            $totals['penalties_days'] += $remainingPenaltyHours;
            $totals['penalties_amount'] += $penaltyAmount;
            $totals['extra_payments'] += $extraPayments;
            $totals['overtime_hours'] += $overtimeHours;
            $totals['overtime_amount'] += $overtimeAmount;
            $totals['adj_amount'] += $adjAmount;

            $benefits = collect()
                ->merge($employee->activeEmployeeBaseBenefits(Carbon::parse($this->startDate), Carbon::parse($this->endDate))->get())
                ->merge($employee->activeOtherBaseBenefits(Carbon::parse($this->startDate), Carbon::parse($this->endDate))->get())
                ->merge($employee->activeMedicalBenefits(Carbon::parse($this->startDate), Carbon::parse($this->endDate))->get());

            // Explicitly use indexed arrays for employees to ensure we have id as a field
            $departmentGroups[$departmentId]['employees'][] = [
                'id' => $employee->id,
                'name' => $employee->name,
                'position' => $employee->position?->name ?? 'No Position',
                'gross_salary' => $grossSalary,
                'insurance_amount' => $insuranceAmount,
                'employee_insurance' => $employeeInsurance,
                'employer_insurance' => $employerInsurance,
                'total_insurance' => $totalInsurance,
                'other_amount' => $otherAmount,
                'employee_medical' => $employeeMedical,
                'total_medical' => $totalMedical,
                'employee_deductions' => $employeeDeductions,
                'penalties_hours' => $totalPenaltyHours,
                'penalties_amount' => $penaltyAmount,
                'penalties_days' => $penaltyData['penalty_days'],
                'total_penalty_hours' => $totalPenaltyHours,
                'vacation_offset_hours' => $vacationOffsetHours,
                'remaining_penalty_hours' => $remainingPenaltyHours,
                'direct_deduction_amount' => $directDeductionAmount,
                // 'used_approved_vacations' => $usedApprovedVacations,
                'available_vacation_benefits' => $availableVacationBenefits,
                'net_income' => $netIncome - $penaltyAmount,
                'net_after_penalty' => $netAfterPenalty,
                'extra_payments' => $extraPayments,
                'overtime_hours' => $overtimeHours,
                'overtime_amount' => $overtimeAmount,
                'adj_amount' => $adjAmount,
                'adj_desc' => $adjDesc,
                'net_after_deductions' => $netAfterDeductions,
                'employee_base_benefits' => $employeeBaseBenefits,
                'other_base_benefits' => $otherBaseBenefits,
                'benefits' => $benefits,
                'potential_overtime_data' => $potentialOvertimeData, // Store potential overtime data for later creation
                'is_automatic_overtime' => $isAutomaticOvertime, // Store the automatic overtime flag
            ];
        }

        $this->payrollData = $departmentGroups;
        $this->payrollData['_totals'] = $totals;
    }

    /**
     * Submit the payroll for processing
     */
    public function submitPayroll()
    {
        try {

            // Create the payroll using the static method with the prepared data
            $payroll = Payroll::createPayroll(Auth::id(), $this->startDate, $this->endDate, $this->payrollData);

            if ($payroll) {
                $this->alertSuccess('Payroll created successfully.');
                $this->reset();
            } else {
                $this->alertError('Failed to create payroll. Please try again.');
            }
        } catch (\Exception $e) {
            report($e);
            $this->alertError('Failed to create payroll: ' . $e->getMessage());
            AppLog::error('Payroll creation error: ' . $e->getMessage());
        }
    }

    public function clearPayrollData()
    {
        $this->payrollData = [];
    }

    public function render()
    {
        return view('livewire.payrolls.create-payroll', [
            'loadedDepartments' => $this->departments,
        ]);
    }
}
