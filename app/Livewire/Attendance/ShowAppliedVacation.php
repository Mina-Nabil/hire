<?php

namespace App\Livewire\Attendance;

use App\Models\Benefits\Payrolls\AppliedVacation;
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

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->reset(['search', 'startDate', 'endDate', 'status']);
    }

    public function approve($appliedVacationId)
    {
        try {
            $appliedVacation = AppliedVacation::findOrFail($appliedVacationId);
            $appliedVacation->approve();
            $this->alertSuccess('Vacation approved successfully');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function reject($appliedVacationId)
    {
        try {
            $appliedVacation = AppliedVacation::findOrFail($appliedVacationId);
            $appliedVacation->reject();
            $this->alertSuccess('Vacation rejected successfully');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function render()
    {
        $query = AppliedVacation::query()
            ->with(['employee', 'vacationBenefit', 'payroll'])
            ->when($this->search, function ($query) {
                $query->whereHas('employee', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })->orWhereHas('vacationBenefit', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->startDate, function ($query) {
                $query->where('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->where('created_at', '<=', $this->endDate . ' 23:59:59');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            });

        $appliedVacations = $query->latest()->paginate(10);

        $loggedInUser = Auth::user();
        if($loggedInUser->is_admin || $loggedInUser->is_hr){
            $layout = 'components.layouts.app';
        }else{
            $layout = 'components.layouts.employee';
        }

        return view('livewire.attendance.show-applied-vacation', [
            'appliedVacations' => $appliedVacations
        ])->layout($layout);
    }
}
