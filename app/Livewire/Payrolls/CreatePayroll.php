<?php

namespace App\Livewire\Payrolls;

use App\Models\Attendance\Overtime;
use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Payrolls\Payroll;
use App\Models\Personel\Employee;
use App\Models\Hierarchy\Department;
use App\Models\Hierarchy\Position;
use App\Models\Users\AppLog;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
    public $departments = [];

    // Properties for the payroll data
    public $startDate;
    public $endDate;
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

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->loadDepartments();
        $this->ensureArrays();
    }

    public function loadDepartments()
    {
        $this->departments = Department::orderBy('name')->get();
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

        $employees = Employee::whereHas('position', function($query) use ($departmentIds) {
            $query->whereIn('department_id', $departmentIds);
        })->get();

        $this->selectedEmployees = $employees->pluck('id')->toArray();
    }

    public function loadEmployeesFromDepartment($departmentId)
    {
        $this->ensureArrays();
        
        if (!$departmentId) {
            $this->selectedEmployees = [];
            return;
        }

        $employees = Employee::whereHas('position', function($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })->get();

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
                        'net_income'=> 0,
                        'penalties_days' => 0,
                        'penalties_amount' => 0,
                        'extra_payments' => 0,
                        'overtime_hours' => 0,
                        'overtime_amount' => 0,
                        'adj_amount' => 0,
                    ]
                ];
            }
            
            $grossSalary = $employee->benefitConfiguration?->gross_salary ?? 0;
            $insuranceAmount = $employee->benefitConfiguration?->insurance_amount ?? 0;
            $employeeInsurance = $insuranceAmount * Payroll::EMPLOYEE_SHARE_SOCIAL_INSURANCE;
            $employerInsurance = $insuranceAmount * Payroll::EMPLOYER_SHARE_SOCIAL_INSURANCE;
            $totalInsurance = $employeeInsurance + $employerInsurance;
            $otherAmount = $grossSalary - $insuranceAmount - $employerInsurance ?? 0;
            $employeeMedical = $employee->getMedicalBenefits()->sum('amount');
            $totalMedical = $employeeMedical;
            $employeeDeductions = $employeeInsurance + $employeeMedical;
            $netIncome = $otherAmount + $insuranceAmount;
            $dayPrice = $netIncome / 30;
            $employeeBaseBenefits = $employee->getEmployeeBaseBenefits()->sum('amount');
            $otherBaseBenefits = $employee->getOtherBaseBenefits()->sum('amount');
            
            
            // Get daily working hours from employee benefit configuration
            $dailyWorkingHours = $employee->benefitConfiguration?->daily_working_hours ?? 8;
            
            // Calculate penalty hours using the new consolidated function
            $totalPenaltyHours = $employee->getTotalPenaltyHours($this->startDate, $this->endDate);
            
            // Convert total hours to days for display purposes
            $totalPenaltyDays = $dailyWorkingHours > 0 ? $totalPenaltyHours / $dailyWorkingHours : 0;
            
            // Calculate hourly rate and penalty amount
            $hourlyRate = $dayPrice / $dailyWorkingHours;
            $penaltyAmount = $totalPenaltyHours * $hourlyRate;

            $netAfterPenalty = $netIncome - $penaltyAmount;
            
            // Get extra payments
            $extraPayments = $employee->getNegativeExtraPayments($this->startDate, $this->endDate);
            
            // Alternatively, use the new penalty calculation function (uncomment if needed)
            // $penaltyAmount = $employee->calculateTotalPenaltyDeduction($this->startDate, $this->endDate);
            
            // Calculate overtime
            $overtimeHours = 0;
            $overtimeRate = $employee->benefitConfiguration->overtime_rate ?? 1.5;
            $dailyWorkingHours = $employee->benefitConfiguration->daily_working_hours ?? 8;
            $isAutomaticOvertime = $employee->benefitConfiguration->is_automatic_overtime ?? false;
            
            if ($isAutomaticOvertime) {
                // Calculate overtime from attendance records
                // Get all approved attendance records
                $attendances = $employee->attendances()
                    ->where('is_approved', true)
                    ->whereBetween('date', [$this->startDate, $this->endDate])
                    ->whereNull('payroll_id')
                    ->get();
                
                // For each attendance, calculate overtime if hours exceed daily working hours
                foreach ($attendances as $attendance) {
                    $dailyHours = $attendance->hours;
                    if ($dailyHours > $dailyWorkingHours) {
                        $overtimeHours += ($dailyHours - $dailyWorkingHours);
                    }
                }
            } else {
                // Use explicitly created overtime records
                $overtimeHours = $employee->overtimes()
                    ->where('status', Overtime::STATUS_APPROVED)
                    ->whereBetween('date', [$this->startDate, $this->endDate])
                    ->whereNull('payroll_id')
                    ->sum('hours');
            }
            
            // Calculate overtime amount
            $overtimeAmount = $overtimeHours * $hourlyRate * $overtimeRate;
            
            // Initialize adjustment values (will be 0 initially)
            $adjAmount = 0;
            $adjDesc = '';
            
            // Calculate net income with overtime and adjustment
            $netAfterDeductions = $netAfterPenalty + $extraPayments + $overtimeAmount + $adjAmount;
            
            // Add to department totals
            $departmentGroups[$departmentId]['totals']['gross_salary'] += $grossSalary;
            $departmentGroups[$departmentId]['totals']['insurance_amount'] += $insuranceAmount;
            $departmentGroups[$departmentId]['totals']['employee_insurance'] += $employeeInsurance;
            $departmentGroups[$departmentId]['totals']['employer_insurance'] += $employerInsurance;
            $departmentGroups[$departmentId]['totals']['total_insurance'] += $totalInsurance;
            $departmentGroups[$departmentId]['totals']['penalties_days'] += $totalPenaltyDays;
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
            $totals['penalties_days'] += $totalPenaltyDays;
            $totals['penalties_amount'] += $penaltyAmount;
            $totals['extra_payments'] += $extraPayments;
            $totals['overtime_hours'] += $overtimeHours;
            $totals['overtime_amount'] += $overtimeAmount;
            $totals['adj_amount'] += $adjAmount;

            $benefits = collect()
                ->merge($employee->getEmployeeBaseBenefits()->get())
                ->merge($employee->getOtherBaseBenefits()->get())
                ->merge($employee->getMedicalBenefits()->get());

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
                'penalties_days' => $totalPenaltyDays,
                'penalties_amount' => $penaltyAmount,
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
        $this->ensureArrays();
        
        try {
            // Prepare employee data for the payroll
            $employeePayrollData = [];
            
            foreach ($this->payrollData as $deptId => $departmentData) {
                // Skip the _totals entry
                if ($deptId === '_totals') {
                    continue;
                }
                
                foreach ($departmentData['employees'] as $employeeData) {
                    $employee = Employee::find($employeeData['id']);
                    
                    if (!$employee) {
                        continue;
                    }
                    
                    // Get extra payment IDs
                    $extraPaymentIds = $employee->extraPayments()
                        ->where('status', \App\Models\Benefits\Payrolls\ExtraPayment::STATUS_APPROVED)
                        ->whereBetween('due_date', [$this->startDate, $this->endDate])
                        ->whereNull('payroll_id')
                        ->pluck('id')
                        ->toArray();
                    
                    // Get attendance IDs
                    $attendanceIds = $employee->attendances()
                        ->whereBetween('date', [$this->startDate, $this->endDate])
                        ->whereNull('payroll_id')
                        ->pluck('id')
                        ->toArray();
                    
                    // Get overtime IDs
                    $overtimeIds = $employee->overtimes()
                        ->where('status', \App\Models\Attendance\Overtime::STATUS_APPROVED)
                        ->whereBetween('date', [$this->startDate, $this->endDate])
                        ->whereNull('payroll_id')
                        ->pluck('id')
                        ->toArray();
                    
                    // No need to manually recreate benefit data - use the actual models collected in loadPayrollData
                    if (!empty($employeeData['benefits'])) {
                        // Pass the benefits collection directly, along with all required fields for PayrollEmployee
                        $employeePayrollData[] = [
                            'employee_id' => $employeeData['id'],
                            'gross_salary' => $employeeData['gross_salary'],
                            'insurance_amount' => $employeeData['insurance_amount'],
                            'other_amount' => $employeeData['other_amount'] ?? 0,
                            'employee_insurance' => $employeeData['employee_insurance'],
                            'employer_insurance' => $employeeData['employer_insurance'],
                            'total_insurance' => $employeeData['total_insurance'] ?? ($employeeData['employee_insurance'] + $employeeData['employer_insurance']),
                            'employee_medical' => $employeeData['employee_medical'],
                            'total_medical' => $employeeData['total_medical'] ?? $employeeData['employee_medical'],
                            'employee_deductions' => $employeeData['employee_deductions'],
                            'penalties_days' => $employeeData['penalties_days'],
                            'penalties_amount' => $employeeData['penalties_amount'],
                            'overtime_hours' => $employeeData['overtime_hours'] ?? 0,
                            'overtime_amount' => $employeeData['overtime_amount'] ?? 0,
                            'net_after_penalty' => $employeeData['net_after_penalty'],
                            'extra_payments' => $employeeData['extra_payments'],
                            'adj_amount' => $employeeData['adj_amount'] ?? 0,
                            'adj_desc' => $employeeData['adj_desc'] ?? '',
                            'net_after_deductions' => $employeeData['net_after_deductions'],
                            'employee_base_benefits' => $employeeData['employee_base_benefits'],
                            'other_base_benefits' => $employeeData['other_base_benefits'],
                            'position' => $employeeData['position'],
                            'department' => $departmentData['name'],
                            // Include these fields or default values
                            'paid' => $employeeData['net_after_deductions'], // Same as net_after_deductions
                            'vacation_days' => 0, // Default to 0
                            'vacation_amount' => 0, // Default to 0
                            'base_amount' => $employeeData['insurance_amount'], // Use insurance amount as base
                            'extra_payment_ids' => $extraPaymentIds,
                            'attendance_ids' => $attendanceIds,
                            'overtime_ids' => $overtimeIds,
                            'benefits' => $employeeData['benefits']
                        ];
                    }
                }
            }
            
            // Create the payroll using the static method with the prepared data
            $payroll = Payroll::createPayroll(Auth::id(),$this->startDate,$this->endDate,$employeePayrollData);
            
            if ($payroll) {
                $this->alertSuccess('Payroll created successfully.');
                $this->reset();
            } else {
                $this->alertError('Failed to create payroll. Please try again.');
            }
        } catch (\Exception $e) {
            $this->alertError('Failed to create payroll: ' . $e->getMessage());
            AppLog::error('Payroll creation error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.payrolls.create-payroll', [
            'departments' => $this->departments,
        ]);
    }
}
