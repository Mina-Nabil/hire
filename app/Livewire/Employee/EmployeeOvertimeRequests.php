<?php

namespace App\Livewire\Employee;

use App\Models\Attendance\Overtime;
use App\Models\Personel\Employee;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.employee')]
#[Title('Overtime Requests')]
class EmployeeOvertimeRequests extends Component
{
    use WithPagination, AlertFrontEnd;
    
    public $statusFilter = '';
    public $search = '';
    public $perPage = 10;
    
    // For request form
    public $showRequestModal = false;
    public $startDate;
    public $startTime;
    public $endTime;
    public $reason;
    
    // For details modal
    public $showDetailsModal = false;
    public $selectedOvertime = null;
    
    // Employee
    public $employee;
    
    // Status list for filter dropdown
    public $statusList = [
        Overtime::STATUS_PENDING,
        Overtime::STATUS_APPROVED,
        Overtime::STATUS_REJECTED
    ];
    
    public function mount()
    {
        $this->employee = Employee::where('user_id', Auth::id())->first();
    }
    
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function openRequestModal()
    {
        $this->resetRequestForm();
        $this->showRequestModal = true;
    }
    
    public function closeRequestModal()
    {
        $this->showRequestModal = false;
        $this->resetRequestForm();
    }
    
    public function resetRequestForm()
    {
        $this->startDate = Carbon::now()->format('Y-m-d');
        $this->startTime = Carbon::now()->format('H:i');
        $this->endTime = Carbon::now()->addHours(1)->format('H:i');
        $this->reason = '';
    }
    
    public function submitOvertimeRequest()
    {
        $this->validate([
            'startDate' => 'required|date',
            'startTime' => 'required',
            'endTime' => 'required',
            'reason' => 'nullable|string|max:255',
        ]);
        
        // Check if end time is after start time
        $startDateTime = Carbon::parse($this->startDate . ' ' . $this->startTime);
        $endDateTime = Carbon::parse($this->startDate . ' ' . $this->endTime);
        
        if ($endDateTime <= $startDateTime) {
            $this->alertError('End time must be after start time');
            return;
        }
        
        try {
            $this->employee->addOvertime($startDateTime, $endDateTime);
            
            $this->showRequestModal = false;
            $this->alertSuccess('Overtime request submitted successfully');
            $this->resetRequestForm();
        } catch (\Exception $e) {
            $this->alertError('Error: ' . $e->getMessage());
        }
    }
    
    public function viewDetails($overtimeId)
    {
        $this->selectedOvertime = Overtime::findOrFail($overtimeId);
        $this->showDetailsModal = true;
    }
    
    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedOvertime = null;
    }
    
    public function cancelRequest($overtimeId)
    {
        try {
            $overtime = Overtime::findOrFail($overtimeId);
            
            // Only allow canceling pending requests
            if ($overtime->status !== Overtime::STATUS_PENDING) {
                $this->alertError('Only pending requests can be canceled');
                return;
            }
            
            // Check if this overtime belongs to the logged-in employee
            if ($overtime->employee_id !== $this->employee->id) {
                $this->alertError('You can only cancel your own overtime requests');
                return;
            }
            
            $overtime->delete();
            $this->alertSuccess('Overtime request canceled successfully');
        } catch (\Exception $e) {
            $this->alertError('Error: ' . $e->getMessage());
        }
    }
    
    public function getStatusBadgeClasses($status)
    {
        return match($status) {
            'pending' => 'badge bg-warning-500 text-warning-500 bg-opacity-30',
            'approved' => 'badge bg-success-500 text-success-500 bg-opacity-30',
            'rejected' => 'badge bg-danger-500 text-danger-500 bg-opacity-30',
            default => 'badge bg-slate-500 text-slate-500 bg-opacity-30',
        };
    }
    
    public function render()
    {
        $overtimes = $this->employee ? Overtime::where('employee_id', $this->employee->id)
            ->when($this->statusFilter, function ($query) {
                return $query->where('status', $this->statusFilter);
            })
            ->when($this->search, function ($query) {
                $date = date('Y-m-d', strtotime($this->search));
                return $query->where('date', 'like', "%$date%")
                    ->orWhere('start_time', 'like', "%{$this->search}%")
                    ->orWhere('end_time', 'like', "%{$this->search}%")
                    ->orWhere('hours', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate($this->perPage) : collect();
            
        return view('livewire.employee.employee-overtime-requests', [
            'overtimes' => $overtimes
        ]);
    }
} 