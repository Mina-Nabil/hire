<?php

namespace App\Livewire\Attendance;

use App\Models\Benefits\Payrolls\AppliedVacation;
use App\Models\Benefits\Vacations\VacationBenefit;
use App\Traits\AlertFrontEnd;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ShowAppliedVacation extends Component
{
    use WithPagination, AlertFrontEnd;

    public $search = '';
    public $startDate = '';
    public $endDate = '';
    public $showFilters = false;
    public $status = '';
    public $benefitName = '';
    public $showRejectModal = false;
    public $showVacationDetailsModal = false;
    public $showEditModal = false;
    public $selectedAppliedVacation = null;
    public $rejectNote = '';
    public $editDays = [];
    public $editReason = '';

    protected $listeners = ['approveVacation'];

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->reset(['search', 'startDate', 'endDate', 'status', 'benefitName']);
    }

    public function openRejectModal($appliedVacationId)
    {
        $this->selectedAppliedVacation = AppliedVacation::findOrFail($appliedVacationId);
        $this->rejectNote = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->selectedAppliedVacation = null;
        $this->rejectNote = '';
    }

    public function openVacationDetailsModal($appliedVacationId)
    {
        $this->selectedAppliedVacation = AppliedVacation::with(['employee', 'vacationBenefit', 'payroll', 'approvedBy'])->findOrFail($appliedVacationId);
        $this->showVacationDetailsModal = true;
    }

    public function closeVacationDetailsModal()
    {
        $this->showVacationDetailsModal = false;
        $this->selectedAppliedVacation = null;
    }

    public function approveVacation($appliedVacationId)
    {
        try {
            $appliedVacation = AppliedVacation::findOrFail($appliedVacationId);
            $appliedVacation->approve();
            $this->alertSuccess('Vacation approved successfully');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function reject($appliedVacationId, $note = null)
    {
        try {
            $appliedVacation = AppliedVacation::findOrFail($appliedVacationId);
            $appliedVacation->reject($note);
            $this->alertSuccess('Vacation rejected successfully');
            $this->closeRejectModal();
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function confirmReject()
    {
        $this->reject($this->selectedAppliedVacation->id, $this->rejectNote);
    }

    public function openEditModal($appliedVacationId)
    {
        $appliedVacation = AppliedVacation::with(['vacationDays', 'vacationBenefit'])->findOrFail($appliedVacationId);

        if (!Auth::user()->can('edit', $appliedVacation)) {
            $this->alertError('You cannot edit this request');
            return;
        }

        $this->selectedAppliedVacation = $appliedVacation;
        $this->editReason = $appliedVacation->reason;
        $this->editDays = $appliedVacation->vacationDays
            ->map(fn($day) => [
                'vacation_date' => \Carbon\Carbon::parse($day->vacation_date)->format('Y-m-d'),
                'hours' => (float) $day->hours,
            ])->toArray();
        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedAppliedVacation = null;
        $this->editDays = [];
        $this->editReason = '';
    }

    public function addEditDay()
    {
        $this->editDays[] = ['vacation_date' => '', 'hours' => 8];
    }

    public function removeEditDay($index)
    {
        unset($this->editDays[$index]);
        $this->editDays = array_values($this->editDays);
    }

    public function getEditTotalHoursProperty()
    {
        return collect($this->editDays)->sum('hours');
    }

    public function saveEdit()
    {
        $this->validate([
            'editDays' => 'required|array|min:1',
            'editDays.*.vacation_date' => 'required|date',
            'editDays.*.hours' => 'required|numeric|min:1|max:24',
            'editReason' => 'nullable|string|max:255',
        ]);

        try {
            $appliedVacation = AppliedVacation::findOrFail($this->selectedAppliedVacation->id);
            $days = collect($this->editDays)->map(fn($day) => [
                'vacation_date' => $day['vacation_date'],
                'hours' => (float) $day['hours'],
            ])->toArray();

            $appliedVacation->updateRequest($days, $this->editReason);
            $this->alertSuccess('Vacation request updated successfully');
            $this->closeEditModal();
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function render()
    {
        $query = AppliedVacation::userData()
            ->with(['employee', 'vacationBenefit', 'payroll', 'approvedBy'])
            ->when($this->search, function ($query) {
                $query->whereHas('employee', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })->orWhereHas('vacationBenefit', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->startDate, function ($query) {
                $query->whereHas('vacationDays', function ($q) {
                    $q->where('vacation_date', '>=', $this->startDate);
                });
            })
            ->when($this->endDate, function ($query) {
                $query->whereHas('vacationDays', function ($q) {
                    $q->where('vacation_date', '<=', $this->endDate . ' 23:59:59');
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->benefitName, function ($query) {
                $query->byTypeName($this->benefitName);
            });

        $appliedVacations = $query->latest()->paginate(10);
        $benefitNames = VacationBenefit::select('name')->whereNotNull('name')->distinct()->get()->pluck('name')->toArray();
        // Add 'Mission' to the list of benefit names
        $benefitNames[] = 'Mission';

        $loggedInUser = Auth::user();
        if($loggedInUser->is_admin || $loggedInUser->is_hr){
            $layout = 'components.layouts.app';
        }else{
            $layout = 'components.layouts.employee';
        }

        return view('livewire.attendance.show-applied-vacation', [
            'appliedVacations' => $appliedVacations,
            'benefitNames' => $benefitNames
        ])->layout($layout);
    }
}
