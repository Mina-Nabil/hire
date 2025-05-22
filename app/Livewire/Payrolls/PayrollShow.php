<?php

namespace App\Livewire\Payrolls;

use App\Models\Benefits\Payrolls\Payroll;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;

class PayrollShow extends Component
{
    use AlertFrontEnd, WithPagination;
    
    public $payroll;
    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Modal properties
    public $showEmployeeDetailsModal = false;
    public $selectedEmployeeId = null;
    public $selectedPayrollEmployee = null;
    public $employeeAttendance = [];
    public $employeeBenefitPayments = [];
    public $employeeOvertimes = [];
    public $employeeExtraPayments = [];
    
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
        // Verify the user has permission to update this payroll
        $this->authorize('update', $this->payroll);
        
        $this->payroll->update([
            'status' => Payroll::STATUS_APPROVED
        ]);
        
        \App\Models\Users\AppLog::info(
            'Payroll Approved', 
            'Approved payroll for period ' . $this->payroll->start_date . ' to ' . $this->payroll->end_date, 
            loggable: $this->payroll
        );
        
        $this->alertSuccess('Payroll approved successfully.');
        $this->payroll->refresh();
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