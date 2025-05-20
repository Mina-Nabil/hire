<?php

namespace App\Livewire\Payrolls;

use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Payrolls\Payroll;
use App\Models\Personel\Employee;
use App\Models\Hierarchy\Department;
use App\Models\Hierarchy\Position;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
                    ]
                ];
            }
            
            $grossSalary = $employee->benefitConfiguration?->gross_salary ?? 0;
            $insuranceAmount = $employee->benefitConfiguration?->insurance_amount ?? 0;
            $employeeInsurance = $insuranceAmount * Payroll::EMPLOYEE_SHARE_SOCIAL_INSURANCE;
            $employerInsurance = $insuranceAmount * Payroll::EMPLOYER_SHARE_SOCIAL_INSURANCE;
            $totalInsurance = $employeeInsurance + $employerInsurance;
            $otherAmount = $grossSalary - $insuranceAmount - $employerInsurance ?? 0;
            $employeeMedical = $employee->getMonthlyMedicalBenefitsSum();
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
            $netAfterDeductions = $netAfterPenalty + $extraPayments; // Add because extra payments are already negative
            
            // Alternatively, use the new penalty calculation function (uncomment if needed)
            // $penaltyAmount = $employee->calculateTotalPenaltyDeduction($this->startDate, $this->endDate);
            
            // Add to department totals
            $departmentGroups[$departmentId]['totals']['gross_salary'] += $grossSalary;
            $departmentGroups[$departmentId]['totals']['insurance_amount'] += $insuranceAmount;
            $departmentGroups[$departmentId]['totals']['employee_insurance'] += $employeeInsurance;
            $departmentGroups[$departmentId]['totals']['employer_insurance'] += $employerInsurance;
            $departmentGroups[$departmentId]['totals']['total_insurance'] += $totalInsurance;
            $departmentGroups[$departmentId]['totals']['penalties_days'] += $totalPenaltyDays;
            $departmentGroups[$departmentId]['totals']['penalties_amount'] += $penaltyAmount;
            $departmentGroups[$departmentId]['totals']['extra_payments'] += $extraPayments;
                        
            // Add to grand totals
            $totals['gross_salary'] += $grossSalary;
            $totals['insurance_amount'] += $insuranceAmount;
            $totals['employee_insurance'] += $employeeInsurance;
            $totals['employer_insurance'] += $employerInsurance;
            $totals['total_insurance'] += $totalInsurance;
            $totals['penalties_days'] += $totalPenaltyDays;
            $totals['penalties_amount'] += $penaltyAmount;
            $totals['extra_payments'] += $extraPayments;
            
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
                'net_after_deductions' => $netAfterDeductions,
                'employee_base_benefits' => $employeeBaseBenefits,
                'other_base_benefits' => $otherBaseBenefits,
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
        
        // Debug: Check the structure of the first employee
        if (!empty($this->payrollData) && count($this->payrollData) > 0) {
            $firstDept = collect($this->payrollData)->filter(function($item, $key) {
                return $key !== '_totals';
            })->first();
            
            if (!empty($firstDept['employees'])) {
                $firstEmployee = $firstDept['employees'][0] ?? null;
                \Illuminate\Support\Facades\Log::debug('First employee structure:', ['employee' => $firstEmployee]);
            }
        }
        
        try {
            DB::beginTransaction();
            
            // Create the payroll
            $payroll = \App\Models\Benefits\Payrolls\Payroll::create([
                'creator_id' => Auth::id(),
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'total_paid' => $this->payrollData['_totals']['net_amount'] ?? 0,
                'total_vacation_days' => $this->payrollData['_totals']['vacation_days'] ?? 0,
                'total_vacation_amount' => $this->payrollData['_totals']['vacation_amount'] ?? 0,
                'total_employees' => count($this->selectedEmployees),
                'status' => \App\Models\Benefits\Payrolls\Payroll::STATUS_PENDING,
            ]);
            
            // Create payroll employee records
            foreach ($this->payrollData as $departmentId => $department) {
                if ($departmentId === '_totals') {
                    continue;
                }
                
                foreach ($department['employees'] as $index => $employee) {
                    // Make sure we have a valid employee ID
                    $employeeId = $employee['id'] ?? 0;
                    
                    if (!$employeeId) {
                        continue; // Skip invalid employee IDs
                    }
                    
                    // Create the payroll employee record
                    $payrollEmployee = \App\Models\Benefits\Payrolls\PayrollEmployee::create([
                        'employee_id' => $employeeId,
                        'payroll_id' => $payroll->id,
                        'paid' => $employee['net_after_deductions'],
                        'vacation_days' => $employee['vacation_days'] ?? 0,
                        'vacation_amount' => $employee['vacation_amount'] ?? 0,
                        'base_amount' => $employee['gross_salary'],
                        'gross_salary' => $employee['gross_salary'],
                        'insurance_amount' => $employee['insurance_amount'],
                        'other_amount' => $employee['other_amount'],
                        'employee_insurance' => $employee['employee_insurance'],
                        'employer_insurance' => $employee['employer_insurance'],
                        'total_insurance' => $employee['total_insurance'],
                        'employee_medical' => $employee['employee_medical'],
                        'total_medical' => $employee['total_medical'],
                        'employee_deductions' => $employee['employee_deductions'],
                        'penalties_days' => $employee['penalties_days'],
                        'penalties_amount' => $employee['penalties_amount'],
                        'net_after_penalty' => $employee['net_after_penalty'],
                        'extra_payments' => $employee['extra_payments'],
                        'net_after_deductions' => $employee['net_after_deductions'],
                        'employee_base_benefits' => $employee['employee_base_benefits'],
                        'other_base_benefits' => $employee['other_base_benefits'],
                        'position' => $employee['position'],
                        'department' => $department['name'],
                    ]);
                    
                    // Update attendance records with payroll_id
                    \App\Models\Attendance\Attendance::where('employee_id', $employeeId)
                        ->whereBetween('date', [$this->startDate, $this->endDate])
                        ->update(['payroll_id' => $payroll->id]);
                    
                    // Link extra payments to this payroll
                    \App\Models\Benefits\Payrolls\ExtraPayment::where('employee_id', $employeeId)
                        ->where('status', \App\Models\Benefits\Payrolls\ExtraPayment::STATUS_APPROVED)
                        ->whereBetween('due_date', [$this->startDate, $this->endDate])
                        ->whereNull('payroll_id')
                        ->update(['payroll_id' => $payroll->id, 'status' => \App\Models\Benefits\Payrolls\ExtraPayment::STATUS_PAID]);
                    
                    // Create benefit payment records for this employee
                    $employeeModel = \App\Models\Personel\Employee::find($employeeId);
                    if ($employeeModel) {
                        $employeeModel->createBenefitPaymentsForPayroll($payroll, $employee);
                    }
                }
            }
            
            DB::commit();
            
            // Show success notification
            session()->flash('success', 'Payroll has been created successfully.');
            
            // Redirect to the payroll list
            return redirect()->route('payroll.index');
            
        } catch (\Exception $e) {
            $this->alertError('Failed to create payroll: ' . $e->getMessage());
            DB::rollback();
        }
    }

    public function render()
    {
        return view('livewire.payrolls.create-payroll', [
            'departments' => $this->departments,
        ]);
    }
}
