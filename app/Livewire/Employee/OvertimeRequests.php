<?php

namespace App\Livewire\Employee;

use App\Models\Attendance\Overtime;
use App\Models\Personel\Employee;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Overtime Requests')]
class OvertimeRequests extends Component
{
    use WithPagination, AlertFrontEnd;
    
    public $statusFilter = '';
    public $search = '';
    public $perPage = 10;
    
    // For approval/rejection modals
    public $showApprovalModal = false;
    public $showRejectionModal = false;
    public $showDetailsModal = false;
    public $adminNote = '';
    public $selectedRequest = null;

    protected $rules = [
        'adminNote' => 'nullable|string|max:500',
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
        $this->selectedRequest = Overtime::with(['employee', 'creator'])
            ->findOrFail($requestId);
        $this->showDetailsModal = true;
    }

    public function openApprovalModal($requestId)
    {
        $this->reset(['adminNote']);
        $this->selectedRequest = Overtime::with(['employee'])
            ->findOrFail($requestId);
        
        if ($this->selectedRequest->status !== Overtime::STATUS_PENDING) {
            $this->alertError('This request cannot be approved because its status is ' . $this->selectedRequest->status);
            return;
        }
        
        $this->showApprovalModal = true;
    }

    public function openRejectionModal($requestId)
    {
        $this->reset(['adminNote']);
        $this->selectedRequest = Overtime::findOrFail($requestId);
        
        if ($this->selectedRequest->status !== Overtime::STATUS_PENDING) {
            $this->alertError('This request cannot be rejected because its status is ' . $this->selectedRequest->status);
            return;
        }
        
        $this->showRejectionModal = true;
    }

    public function approveRequest()
    {
        $this->validate([
            'adminNote' => 'nullable|string|max:500',
        ]);

            if (!$this->selectedRequest) {
                $this->alertError('Request not found');
                return;
            }

            $res = $this->selectedRequest->setStatus(Overtime::STATUS_APPROVED, $this->adminNote);

            if (!$res) {
                $this->alertError('Error approving overtime');
                return;
            }

            $this->showApprovalModal = false;
            $this->reset(['adminNote', 'selectedRequest']);
            $this->alertSuccess('Overtime request approved successfully');
            
    }

    public function rejectRequest()
    {
        $this->validate([
            'adminNote' => 'required|string|max:500',
        ]);

        if (!$this->selectedRequest) {
            $this->alertError('Request not found');
                return;
        }

        $res = $this->selectedRequest->setStatus(Overtime::STATUS_REJECTED, $this->adminNote);

        if (!$res) {
            $this->alertError('Error rejecting overtime');
            return;
        }

        $this->showRejectionModal = false;
        $this->reset(['adminNote', 'selectedRequest']);
        $this->alertSuccess('Overtime request rejected successfully');
    }

    public function deleteRequest($requestId)
    {
        try {
            $request = Overtime::findOrFail($requestId);
            
            if ($request->status !== Overtime::STATUS_PENDING) {
                $this->alertError('Only pending requests can be deleted.');
                return;
            }
            
            $request->delete();
            $this->alertSuccess('Overtime request deleted successfully');
        } catch (\Exception $e) {
            $this->alertError('Error: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showApprovalModal = false;
        $this->showRejectionModal = false;
        $this->showDetailsModal = false;
        $this->reset(['adminNote', 'selectedRequest']);
    }

    public function getStatusBadgeClasses($status)
    {
        return match($status) {
            Overtime::STATUS_PENDING => 'badge bg-warning-500 text-white',
            Overtime::STATUS_APPROVED => 'badge bg-success-500 text-white',
            Overtime::STATUS_REJECTED => 'badge bg-danger-500 text-white',
            default => 'badge bg-slate-500 text-white',
        };
    }

    public function render()
    {
        $requests = Overtime::with(['employee', 'creator'])
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

        return view('livewire.employee.overtime-requests', [
            'requests' => $requests,
            'statusList' => Overtime::STATUS_LIST,
        ]);
    }
} 