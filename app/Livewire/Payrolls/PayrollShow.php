<?php

namespace App\Livewire\Payrolls;

use App\Models\Benefits\Payrolls\Payroll;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Payroll Details')]
class PayrollShow extends Component
{
    use AlertFrontEnd, WithPagination;
    
    public $payroll;
    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Tab management
    public $activeTab = 'overview';
    
    protected $queryString = ['activeTab'];
    
    protected $listeners = ['approvePayroll', 'deletePayroll'];
    
    // Modal properties
    public $showEmployeeDetailsModal = false;
    public $selectedEmployeeId = null;
    public $selectedPayrollEmployee = null;
    public $employeeAttendance = [];
    public $employeeBenefitPayments = [];
    public $employeeOvertimes = [];
    public $employeeExtraPayments = [];
    
    // Adjustment editing properties
    public $showAdjustmentModal = false;
    public $editingPayrollEmployeeId = null;
    public $adjustmentAmount = 0;
    public $adjustmentDescription = '';
    
    // Penalty breakdown modal properties
    public $showPenaltyBreakdownModal = false;
    public $selectedPenaltyEmployee = null;
    public $penaltyBreakdownData = [];
    public $employeeVacationBenefits = [];
    public $employeeAppliedVacations = [];
    public $employeeMissingDays = [];
    
    // Vacation application for penalty offset modal properties
    public $showVacationApplicationModal = false;
    public $availableVacationBenefits = [];
    public $selectedVacationBenefitId = null;
    public $vacationHoursToApply = 0;
    public $maxApplicableHours = 0;
    public $remainingPenaltyHours = 0;
    
    public function mount($id)
    {
        $this->payroll = Payroll::with('creator')->findOrFail($id);
        
        // Verify the user has permission to view this payroll
        $this->authorize('view', $this->payroll);
    }
    
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage(); // Reset pagination when switching tabs
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    public function approvePayroll()
    {
        $this->authorize('update', $this->payroll);
        
        $res = $this->payroll->approve();

        if($res){
            $this->payroll->refresh();
            $this->alertSuccess('Payroll approved successfully.');
        }else{
                $this->alertError('Failed to approve payroll');
        }

    }

    public function deletePayroll()
    {
        $this->authorize('delete', $this->payroll);
        
        $this->payroll->deletePayroll();
        $this->alertSuccess('Payroll deleted successfully.');
        //route to payrolls index
        return redirect()->route('payrolls.index');
    }
    
    public function showEmployeeDetails($payrollEmployeeId)
    {
        $this->selectedPayrollEmployee = \App\Models\Benefits\Payrolls\PayrollEmployee::with('employee')
            ->findOrFail($payrollEmployeeId);
        
        $this->selectedEmployeeId = $this->selectedPayrollEmployee->employee_id;
        
        // Load attendance records for this employee and payroll
        $this->employeeAttendance = \App\Models\Attendance\Attendance::where('employee_id', $this->selectedEmployeeId)
            ->where('payroll_id', $this->payroll->id)
            ->orderBy('date')
            ->get();
        
        // Load benefit payments for this employee and payroll
        $this->employeeBenefitPayments = \App\Models\Benefits\Payrolls\BenefitPayment::where('employee_id', $this->selectedEmployeeId)
            ->where('payroll_id', $this->payroll->id)
            ->orderBy('created_at')
            ->get();
        
        // Load overtime records for this employee and payroll
        $this->employeeOvertimes = \App\Models\Attendance\Overtime::where('employee_id', $this->selectedEmployeeId)
            ->where('payroll_id', $this->payroll->id)
            ->orderBy('date')
            ->get();
        
        // Load extra payments for this employee and payroll
        $this->employeeExtraPayments = \App\Models\Benefits\Payrolls\ExtraPayment::where('employee_id', $this->selectedEmployeeId)
            ->where('payroll_id', $this->payroll->id)
            ->orderBy('due_date')
            ->get();

        $this->employeeMissingDays = \App\Models\Attendance\MissingDay::where('employee_id', $this->selectedEmployeeId)
            ->betweenDates($this->payroll->start_date, $this->payroll->end_date)
            ->orderBy('date')
            ->get();
        
        $this->showEmployeeDetailsModal = true;
    }
    
    public function closeEmployeeDetailsModal()
    {
        $this->showEmployeeDetailsModal = false;
        $this->selectedEmployeeId = null;
        $this->selectedPayrollEmployee = null;
        $this->employeeAttendance = [];
        $this->employeeBenefitPayments = [];
        $this->employeeOvertimes = [];
        $this->employeeExtraPayments = [];
        $this->employeeMissingDays = [];
    }
    
    public function showPenaltyBreakdown($payrollEmployeeId)
    {
        $this->selectedPenaltyEmployee = \App\Models\Benefits\Payrolls\PayrollEmployee::with('employee')
            ->findOrFail($payrollEmployeeId);
        
        $employee = $this->selectedPenaltyEmployee->employee;
        
        // Calculate penalty breakdown using the new method
        $penaltyCalculation = $employee->calculatePenaltyWithVacationOffset(
            $this->payroll->start_date,
            $this->payroll->end_date
        );
        
        // Get penalty breakdown data
        $this->penaltyBreakdownData = [
            'total_penalty_hours' => $penaltyCalculation['total_penalty_hours'],
            'vacation_offset_hours' => $penaltyCalculation['vacation_offset_hours'],
            'remaining_penalty_hours' => $penaltyCalculation['remaining_penalty_hours'],
            'direct_deduction_amount' => $penaltyCalculation['direct_deduction_amount'],
            'used_approved_vacations' => $penaltyCalculation['used_approved_vacations'],
            'available_vacation_benefits' => $penaltyCalculation['available_vacation_benefits']
        ];
        
        // Store available vacation benefits for potential application
        $this->availableVacationBenefits = $penaltyCalculation['available_vacation_benefits'];
        $this->remainingPenaltyHours = $penaltyCalculation['remaining_penalty_hours'];
        
        // Load employee's vacation benefits
        $this->employeeVacationBenefits = $employee->vacationBenefits()
            ->whereNull('end_date')
            ->with('vacationDetail')
            ->get();
        
        // Load applied vacations for the payroll period
        $this->employeeAppliedVacations = $employee->appliedVacations()
            ->whereHas('vacationDays', function ($query) {
                $query->whereBetween('vacation_date', [$this->payroll->start_date, $this->payroll->end_date]);
            })
            ->with(['vacationBenefit', 'vacationDays' => function ($query) {
                $query->whereBetween('vacation_date', [$this->payroll->start_date, $this->payroll->end_date]);
            }])
            ->get();
        
        $this->showPenaltyBreakdownModal = true;
    }
    
    public function closePenaltyBreakdownModal()
    {
        $this->showPenaltyBreakdownModal = false;
        $this->selectedPenaltyEmployee = null;
        $this->penaltyBreakdownData = [];
        $this->employeeVacationBenefits = [];
        $this->employeeAppliedVacations = [];
        $this->availableVacationBenefits = [];
        $this->remainingPenaltyHours = 0;
    }
    
    public function openVacationApplicationModal()
    {
        // Check if user can update the payroll
        $this->authorize('update', $this->payroll);
        
        if (empty($this->availableVacationBenefits) || $this->remainingPenaltyHours <= 0) {
            $this->alertError('No vacation benefits available or no remaining penalty hours to offset.');
            return;
        }
        
        $this->selectedVacationBenefitId = null;
        $this->vacationHoursToApply = 0;
        $this->maxApplicableHours = 0;
        $this->showVacationApplicationModal = true;
    }
    
    public function closeVacationApplicationModal()
    {
        $this->showVacationApplicationModal = false;
        $this->selectedVacationBenefitId = null;
        $this->vacationHoursToApply = 0;
        $this->maxApplicableHours = 0;
    }
    
    public function updatedSelectedVacationBenefitId($value)
    {
        if ($value) {
            $selectedBenefit = collect($this->availableVacationBenefits)
                ->firstWhere('vacation_benefit_id', $value);
            
            if ($selectedBenefit) {
                $this->maxApplicableHours = $selectedBenefit['max_applicable_hours'];
                $this->vacationHoursToApply = min($this->remainingPenaltyHours, $this->maxApplicableHours);
            }
        } else {
            $this->maxApplicableHours = 0;
            $this->vacationHoursToApply = 0;
        }
    }
    
    public function applyVacationForPenalty()
    {
        // Check if user can update the payroll
        $this->authorize('update', $this->payroll);
        
        if (!$this->selectedVacationBenefitId || $this->vacationHoursToApply <= 0) {
            $this->alertError('Please select a vacation benefit and specify hours to apply.');
            return;
        }
        
        if ($this->vacationHoursToApply > $this->maxApplicableHours) {
            $this->alertError('Hours to apply cannot exceed the maximum applicable hours.');
            return;
        }
        
        $employee = $this->selectedPenaltyEmployee->employee;
        
        $result = $employee->applyVacationForPenaltyOffset(
            $this->selectedVacationBenefitId,
            $this->vacationHoursToApply,
            $this->payroll->start_date,
            $this->payroll->end_date
        );
        
        if ($result['success']) {
            // Update the payroll employee record with new penalty calculations
            $this->updatePayrollEmployeePenaltyData();
            
            $this->alertSuccess($result['message']);
            $this->closeVacationApplicationModal();
            
            // Refresh the penalty breakdown data
            $this->showPenaltyBreakdown($this->selectedPenaltyEmployee->id);
        } else {
            $this->alertError($result['message']);
        }
    }
    
    private function updatePayrollEmployeePenaltyData()
    {
        $employee = $this->selectedPenaltyEmployee->employee;
        
        // Recalculate penalty breakdown
        $penaltyCalculation = $employee->calculatePenaltyWithVacationOffset(
            $this->payroll->start_date,
            $this->payroll->end_date
        );
        
        // Update payroll employee record
        $this->selectedPenaltyEmployee->update([
            'total_penalty_hours' => $penaltyCalculation['total_penalty_hours'],
            'vacation_offset_hours' => $penaltyCalculation['vacation_offset_hours'],
            'direct_deduction_hours' => $penaltyCalculation['remaining_penalty_hours'],
            'direct_deduction_amount' => $penaltyCalculation['direct_deduction_amount'],
        ]);
        
        // Recalculate net amounts
        $grossSalary = $this->selectedPenaltyEmployee->gross_salary;
        $insuranceAmount = $this->selectedPenaltyEmployee->insurance_amount;
        $otherAmount = $this->selectedPenaltyEmployee->other_amount;
        $employeeInsurance = $this->selectedPenaltyEmployee->employee_insurance;
        $employeeMedical = $this->selectedPenaltyEmployee->employee_medical;
        $employeeDeductions = $this->selectedPenaltyEmployee->employee_deductions;
        $directDeductionAmount = $penaltyCalculation['direct_deduction_amount'];
        
        $netAfterPenalty = $grossSalary + $insuranceAmount + $otherAmount - $employeeInsurance - $employeeMedical - $employeeDeductions - $directDeductionAmount;
        
        $extraPayments = $this->selectedPenaltyEmployee->extra_payments;
        $overtimeAmount = $this->selectedPenaltyEmployee->overtime_amount;
        $adjAmount = $this->selectedPenaltyEmployee->adj_amount;
        
        $netAfterDeductions = $netAfterPenalty + $extraPayments + $overtimeAmount + $adjAmount;
        
        $this->selectedPenaltyEmployee->update([
            'net_after_penalty' => $netAfterPenalty,
            'net_after_deductions' => $netAfterDeductions,
            'paid' => $netAfterDeductions
        ]);
        
        // Update payroll total
        $this->payroll->refresh();
        $newTotal = $this->payroll->payrollEmployees()->sum('paid');
        $this->payroll->update(['total_paid' => $newTotal]);
    }
    
    public function openAdjustmentModal($payrollEmployeeId)
    {
        // Check if user can update the payroll
        $this->authorize('update', $this->payroll);
        
        $payrollEmployee = \App\Models\Benefits\Payrolls\PayrollEmployee::findOrFail($payrollEmployeeId);
        
        $this->editingPayrollEmployeeId = $payrollEmployeeId;
        $this->adjustmentAmount = $payrollEmployee->adj_amount;
        $this->adjustmentDescription = $payrollEmployee->adj_desc;
        $this->showAdjustmentModal = true;
    }
    
    public function closeAdjustmentModal()
    {
        $this->showAdjustmentModal = false;
        $this->editingPayrollEmployeeId = null;
        $this->adjustmentAmount = 0;
        $this->adjustmentDescription = '';
    }
    
    public function saveAdjustment()
    {
        // Check if user can update the payroll
        $this->authorize('update', $this->payroll);
        
        if (!$this->editingPayrollEmployeeId) {
            return;
        }
        
        $payrollEmployee = \App\Models\Benefits\Payrolls\PayrollEmployee::findOrFail($this->editingPayrollEmployeeId);
        
        // Store the old net amount for comparison
        $oldNetAmount = $payrollEmployee->net_after_deductions;
        $oldAdjAmount = $payrollEmployee->adj_amount;
        
        // Update adjustment fields
        $payrollEmployee->adj_amount = $this->adjustmentAmount;
        $payrollEmployee->adj_desc = $this->adjustmentDescription;
        
        // Recalculate net after deductions
        $newNetAmount = $payrollEmployee->net_after_penalty + $payrollEmployee->extra_payments + $payrollEmployee->overtime_amount + $this->adjustmentAmount;
        $payrollEmployee->net_after_deductions = $newNetAmount;
        $payrollEmployee->paid = $newNetAmount; // Update paid amount as well
        
        $payrollEmployee->save();
        
        // Update payroll total if needed
        $adjustmentDifference = $this->adjustmentAmount - $oldAdjAmount;
        if ($adjustmentDifference != 0) {
            $this->payroll->total_paid += $adjustmentDifference;
            $this->payroll->save();
        }
        
        // Log the adjustment
        \App\Models\Users\AppLog::info(
            'Payroll Adjustment Updated',
            'Updated adjustment for employee ' . $payrollEmployee->employee->name . ' from ' . $oldAdjAmount . ' to ' . $this->adjustmentAmount,
            loggable: $this->payroll
        );
        
        $this->closeAdjustmentModal();
        $this->alertSuccess('Adjustment updated successfully.');
    }
    
    public function render()
    {
        $data = [];
        
        if ($this->activeTab === 'overview') {
            $payrollEmployees = $this->payroll->payrollEmployees()
                ->with('employee')
                ->when($this->search, function ($query) {
                    return $query->whereHas('employee', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('position', 'like', '%' . $this->search . '%')
                            ->orWhere('department', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage);
            
            // Calculate totals for all employees (not just paginated ones)
            $totalsQuery = $this->payroll->payrollEmployees()
                ->when($this->search, function ($query) {
                    return $query->whereHas('employee', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('position', 'like', '%' . $this->search . '%')
                            ->orWhere('department', 'like', '%' . $this->search . '%');
                    });
                });
            
            $totals = [
                'total_employees' => $totalsQuery->count(),
                'gross_salary' => $totalsQuery->sum('gross_salary'),
                'insurance_amount' => $totalsQuery->sum('insurance_amount'),
                'other_amount' => $totalsQuery->sum('other_amount'),
                'penalties_days' => $totalsQuery->sum('penalties_days'),
                'penalties_amount' => $totalsQuery->sum('penalties_amount'),
                'extra_payments' => $totalsQuery->sum('extra_payments'),
                'overtime_hours' => $totalsQuery->sum('overtime_hours'),
                'overtime_amount' => $totalsQuery->sum('overtime_amount'),
                'adj_amount' => $totalsQuery->sum('adj_amount'),
                'net_after_deductions' => $totalsQuery->sum('net_after_deductions'),
                'tax_amount' => $totalsQuery->sum('tax_amount'),
            ];
            
            $data = [
                'payrollEmployees' => $payrollEmployees,
                'totals' => $totals
            ];
        } elseif ($this->activeTab === 'benefits') {
            $benefitPayments = $this->payroll->benefitPayments()
                ->with(['employee', 'baseBenefit'])
                ->when($this->search, function ($query) {
                    return $query->whereHas('employee', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })->orWhereHas('baseBenefit', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage);
            
            $data['benefitPayments'] = $benefitPayments;
        } elseif ($this->activeTab === 'overtime') {
            $overtimes = $this->payroll->overtimes()
                ->with('employee')
                ->when($this->search, function ($query) {
                    return $query->whereHas('employee', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage);
            
            $data['overtimes'] = $overtimes;
        } elseif ($this->activeTab === 'extra-payments') {
            $extraPayments = $this->payroll->extraPayments()
                ->with('employee')
                ->when($this->search, function ($query) {
                    return $query->whereHas('employee', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })->orWhere('name', 'like', '%' . $this->search . '%');
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage);
            
            $data['extraPayments'] = $extraPayments;
        }
        
        return view('livewire.payrolls.payroll-show', $data);
    }
} 