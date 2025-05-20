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
                        
            // Add to grand totals
            $totals['gross_salary'] += $grossSalary;
            $totals['insurance_amount'] += $insuranceAmount;
            $totals['employee_insurance'] += $employeeInsurance;
            $totals['employer_insurance'] += $employerInsurance;
            $totals['total_insurance'] += $totalInsurance;
            $totals['penalties_days'] += $totalPenaltyDays;
            $totals['penalties_amount'] += $penaltyAmount;
            
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
                'net_after_penalty' => $netAfterPenalty
            ];
        }
        
        $this->payrollData = $departmentGroups;
        $this->payrollData['_totals'] = $totals;
    }

    public function submitPayroll()
    {
        if (empty($this->payrollData)) {
            $this->alertError('No employees selected for payroll processing.');
            return;
        }

        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        // Here, you would typically call a method to create the payroll
        // For now, we'll just show a success message
        
        $this->alertSuccess('Payroll processing initiated for the selected employees.');
        
        // In a real implementation, you would:
        // 1. Create a Payroll record
        // 2. Create PayrollEmployee records for each employee
        // 3. Handle benefit payments, etc.
        
        // Redirect to a confirmation page or payroll list
        // return redirect()->route('payrolls.index');
    }

    public function render()
    {
        return view('livewire.payrolls.create-payroll', [
            'departments' => $this->departments,
        ]);
    }
}
