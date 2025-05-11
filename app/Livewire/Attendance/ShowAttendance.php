<?php

namespace App\Livewire\Attendance;

use App\Models\Attendance\Attendance;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;

class ShowAttendance extends Component
{
    use WithPagination, AlertFrontEnd;

    public $search = '';
    public $startDate = '';
    public $endDate = '';
    public $showFilters = false;

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->reset(['search', 'startDate', 'endDate']);
    }

    public function render()
    {
        $query = Attendance::query()
            ->with('employee')
            ->when($this->search, function ($query) {
                $query->whereHas('employee', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->startDate, function ($query) {
                $query->where('date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->where('date', '<=', $this->endDate);
            });

        $attendances = $query->latest()->paginate(10);

        return view('livewire.attendance.show-attendance', [
            'attendances' => $attendances
        ]);
    }
}
