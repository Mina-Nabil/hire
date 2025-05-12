<?php

namespace App\Livewire\Employee;

use App\Models\Personel\Docs\EmployeeHrLetterRequest;
use App\Models\Personel\Employee;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('HR Letter Requests')]
class HrLetterRequests extends Component
{
    use WithPagination, WithFileUploads, AlertFrontEnd;
    
    public $statusFilter = '';
    public $search = '';
    public $perPage = 10;
    
    // For approval/rejection modals
    public $showApprovalModal = false;
    public $showRejectionModal = false;
    public $showDetailsModal = false;
    public $adminNote = '';
    public $filePath = '';
    public $hrLetterFile;
    public $selectedRequest = null;

    protected $rules = [
        'adminNote' => 'nullable|string|max:500',
        'hrLetterFile' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ];

    public function mount()
    {
        $this->showDetailsModal = false;
        $this->showApprovalModal = false;
        $this->showRejectionModal = false;
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function viewRequest($requestId)
    {
        $this->selectedRequest = EmployeeHrLetterRequest::with(['employee', 'requestedBy', 'approvedBy'])
            ->findOrFail($requestId);
        $this->showDetailsModal = true;
    }

    public function openApprovalModal($requestId)
    {
        $this->reset(['adminNote', 'hrLetterFile']);
        $this->selectedRequest = EmployeeHrLetterRequest::with(['employee'])
            ->findOrFail($requestId);
        
        if ($this->selectedRequest->status !== EmployeeHrLetterRequest::STATUS_PENDING) {
            $this->alertError('This request cannot be approved because its status is ' . $this->selectedRequest->status);
            return;
        }
        
        $this->showApprovalModal = true;
    }

    public function openRejectionModal($requestId)
    {
        $this->reset(['adminNote']);
        $this->selectedRequest = EmployeeHrLetterRequest::findOrFail($requestId);
        
        if ($this->selectedRequest->status !== EmployeeHrLetterRequest::STATUS_PENDING) {
            $this->alertError('This request cannot be rejected because its status is ' . $this->selectedRequest->status);
            return;
        }
        
        $this->showRejectionModal = true;
    }

    public function approveRequest()
    {
        $this->validate([
            'adminNote' => 'nullable|string|max:500',
            'hrLetterFile' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            if (!$this->selectedRequest) {
                $this->alertError('Request not found');
                return;
            }

            // Upload the file
            $path = $this->hrLetterFile->store(Employee::FILES_DIRECTORY.'/hr_letters', 's3');

            // Update the request status
            $result = $this->selectedRequest->setStatus(
                EmployeeHrLetterRequest::STATUS_APPROVED,
                Auth::id(),
                $this->adminNote,
                $path
            );

            if ($result) {
                $this->showApprovalModal = false;
                $this->reset(['adminNote', 'hrLetterFile', 'selectedRequest']);
                $this->alertSuccess('HR letter request approved successfully');
            } else {
                $this->alertError('Failed to approve HR letter request');
            }
        } catch (\Exception $e) {
            $this->alertError('Error: ' . $e->getMessage());
        }
    }

    public function rejectRequest()
    {
        $this->validate([
            'adminNote' => 'required|string|max:500',
        ]);

        try {
            if (!$this->selectedRequest) {
                $this->alertError('Request not found');
                return;
            }

            $result = $this->selectedRequest->setStatus(
                EmployeeHrLetterRequest::STATUS_REJECTED,
                Auth::id(),
                $this->adminNote
            );

            if ($result) {
                $this->showRejectionModal = false;
                $this->reset(['adminNote', 'selectedRequest']);
                $this->alertSuccess('HR letter request rejected successfully');
            } else {
                $this->alertError('Failed to reject HR letter request');
            }
        } catch (\Exception $e) {
            $this->alertError('Error: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showApprovalModal = false;
        $this->showRejectionModal = false;
        $this->showDetailsModal = false;
        $this->reset(['adminNote', 'hrLetterFile', 'selectedRequest']);
    }

    public function getStatusBadgeClasses($status)
    {
        return match($status) {
            EmployeeHrLetterRequest::STATUS_PENDING => 'badge bg-warning-500 text-white',
            EmployeeHrLetterRequest::STATUS_APPROVED => 'badge bg-success-500 text-white',
            EmployeeHrLetterRequest::STATUS_REJECTED => 'badge bg-danger-500 text-white',
            EmployeeHrLetterRequest::STATUS_COMPLETED => 'badge bg-primary-500 text-white',
            default => 'badge bg-slate-500 text-white',
        };
    }

    public function render()
    {
        $requests = EmployeeHrLetterRequest::with(['employee', 'requestedBy', 'approvedBy'])
            ->when($this->statusFilter, function($query) {
                return $query->where('status', $this->statusFilter);
            })
            ->when($this->search, function($query) {
                return $query->whereHas('employee', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.employee.hr-letter-requests', [
            'requests' => $requests,
            'statusList' => EmployeeHrLetterRequest::STATUS_LIST,
        ]);
    }
} 