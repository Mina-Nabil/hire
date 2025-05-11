<?php

namespace App\Livewire\Attendance;

use App\Models\Attendance\PublicHoliday;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PublicHolidayIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    public $search = '';
    public $setHolidaySec = false;
    public $holidayId;
    public $name;
    public $date;

    protected $listeners = ['deletePublicHoliday'];

    protected $rules = [
        'name' => 'required|min:3',
        'date' => 'required|date',
    ];

    public function render()
    {
        $holidays = PublicHoliday::where('name', 'like', '%' . $this->search . '%')
            ->orderByDesc('date')
            ->paginate(10);

        return view('livewire.attendance.public-holiday-index', [
            'holidays' => $holidays
        ])->layout('components.layouts.app', [
            'title' => 'Public Holidays',
            'publicHolidaysIndex' => 'active'
        ]);
    }

    public function openNewHolidaySec()
    {
        $this->resetForm();
        $this->setHolidaySec = true;
    }

    public function closeSetHolidaySec()
    {
        $this->resetForm();
        $this->setHolidaySec = false;
    }

    public function resetForm()
    {
        $this->holidayId = null;
        $this->name = '';
        $this->date = '';
    }

    public function addNewHoliday()
    {
        $this->validate();

        try {
            PublicHoliday::createPublicHoliday($this->name, Carbon::parse($this->date));
            $this->closeSetHolidaySec();
            $this->alertSuccess('Public holiday created successfully!');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function updateThisHoliday($id)
    {
        $holiday = PublicHoliday::findOrFail($id);
        $this->holidayId = $holiday->id;
        $this->name = $holiday->name;
        $this->date = $holiday->date->format('Y-m-d');
        $this->setHolidaySec = $id;
    }

    public function editHoliday()
    {
        $this->validate();

        try {
            $holiday = PublicHoliday::findOrFail($this->holidayId);
            $holiday->editPublicHoliday($this->name, Carbon::parse($this->date));
            $this->closeSetHolidaySec();
            $this->alertSuccess('Public holiday updated successfully!');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function deletePublicHoliday($id)
    {
        try {
            $holiday = PublicHoliday::findOrFail($id);
            $holiday->deletePublicHoliday();
            $this->alertSuccess('Public holiday deleted successfully!');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }
} 