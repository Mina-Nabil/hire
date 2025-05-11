<?php

namespace App\Livewire\Employee;

use App\Models\Personel\Employee;
use App\Models\Personel\Docs\EmployeeHrLetterRequest;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.employee')]
#[Title('Request HR Letter')]
class RequestHrLetter extends Component
{
    use AlertFrontEnd;

    public $employee;
    public $directed_to;
    public $employee_note;
    public $showConfirmModal = false;
    public $showDetailsModal = false;
    public $selectedRequest = null;
    public $activeTab = 'request';
    public $hrLetterRequests = [];

    public function mount()
    {
        $this->employee = Employee::where('user_id', Auth::id())->first();
        if ($this->employee) {
            $this->loadHrLetterRequests();
        }
    }

    public function loadHrLetterRequests()
    {
        $this->hrLetterRequests = $this->employee->hrLetterRequests()
            ->with(['requestedBy', 'approvedBy'])
            ->latest()
            ->get()
            ->toArray();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function openConfirmModal()
    {
        $this->validate([
            'directed_to' => 'required|string|max:255',
            'employee_note' => 'nullable|string',
        ]);

        $this->showConfirmModal = true;
    }
    
    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
    }

    public function viewRequestDetails($requestId)
    {
        foreach ($this->hrLetterRequests as $request) {
            if ($request['id'] == $requestId) {
                $this->selectedRequest = $request;
                $this->showDetailsModal = true;
                return;
            }
        }
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedRequest = null;
    }

    public function getStatusBadgeClass($status)
    {
        return match ($status) {
            EmployeeHrLetterRequest::STATUS_PENDING => 'bg-warning-500 text-warning-500 bg-opacity-30',
            EmployeeHrLetterRequest::STATUS_APPROVED => 'bg-info-500 text-info-500 bg-opacity-30',
            EmployeeHrLetterRequest::STATUS_REJECTED => 'bg-danger-500 text-danger-500 bg-opacity-30',
            EmployeeHrLetterRequest::STATUS_COMPLETED => 'bg-success-500 text-success-500 bg-opacity-30',
            default => 'bg-slate-500 text-slate-500 bg-opacity-30',
        };
    }

    public function getStatusLabel($status)
    {
        return ucfirst($status);
    }

    public function submit()
    {
        $this->validate([
            'directed_to' => 'required|string|max:255',
            'employee_note' => 'nullable|string',
        ]);

        try {
            if (!$this->employee) {
                $this->alertError('Employee record not found');
                return;
            }
            
            $this->employee->createHrLetterRequest(
                Auth::id(),
                $this->directed_to,
                $this->employee_note
            );
            
            $this->showConfirmModal = false;
            $this->alertSuccess('HR Letter request submitted successfully');
            $this->resetForm();
            $this->loadHrLetterRequests();
        } catch (\Exception $e) {
            $this->alertError('Error: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->directed_to = null;
        $this->employee_note = null;
    }

    public function render()
    {
        return view('livewire.employee.request-hr-letter', [
            'benefits' => 'active'
        ]);
    }
} 