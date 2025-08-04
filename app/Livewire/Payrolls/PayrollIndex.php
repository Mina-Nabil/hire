<?php

namespace App\Livewire\Payrolls;

use App\Models\Benefits\Payrolls\Payroll;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;

#[Title('Payrolls')]
class PayrollIndex extends Component
{
    use AlertFrontEnd, WithPagination;
    
    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Modal properties
    public $showEmployeeModal = false;
    public $selectedPayroll = null;
    public $payrollEmployees = [];
    
    public function mount()
    {
        // Verify the user has permission to view payrolls
        $this->authorize('viewAny', Payroll::class);
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
    
    public function showEmployees($payrollId)
    {
        $this->selectedPayroll = Payroll::find($payrollId);
        
        if ($this->selectedPayroll) {
            $this->payrollEmployees = $this->selectedPayroll->payrollEmployees()
                ->with('employee')
                ->orderBy('employee.name')
                ->get();
            $this->showEmployeeModal = true;
        }
    }
    
    public function closeEmployeeModal()
    {
        $this->showEmployeeModal = false;
        $this->selectedPayroll = null;
        $this->payrollEmployees = [];
    }
    
    public function render()
    {
        $payrolls = Payroll::query()
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->whereDate('start_date', 'like', '%' . $this->search . '%')
                      ->orWhereDate('end_date', 'like', '%' . $this->search . '%')
                      ->orWhere('status', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
        
        return view('livewire.payrolls.payroll-index', [
            'payrolls' => $payrolls,
            'payrollsIndex' => 'active'
        ]);
    }
} 