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

    protected $listeners = ['approvePayroll'];
    
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
    
    public function mount($id)
    {
        $this->payroll = Payroll::with('creator')->findOrFail($id);
        
        // Verify the user has permission to view this payroll
        $this->authorize('view', $this->payroll);
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
        
        return view('livewire.payrolls.payroll-show', [
            'payrollEmployees' => $payrollEmployees
        ]);
    }
} 