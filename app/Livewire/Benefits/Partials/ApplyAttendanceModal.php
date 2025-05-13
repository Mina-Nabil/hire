<?php

namespace App\Livewire\Benefits\Partials;

use App\Models\Personel\Employee;
use App\Traits\AlertFrontEnd;
use Livewire\Component;

class ApplyAttendanceModal extends Component
{
    use AlertFrontEnd;

    public $showApplyAttendanceModal = false;
    public $selectedEmployee;
    public $attendanceCalculation;
    public $workingDayStartMin;
    public $workingDayStartMax;
    public $workingDayEndMin;
    public $workingDayEndMax;
    public $dailyWorkingHours;
    public $overtimeRate;
    public $isAutomaticOvertime;
    public $deleteOldConf = true;

    public $attendanceCalculations = [
        'fixed',
        'semi-flexible',
        'flexible'
    ];

    protected $rules = [
        'attendanceCalculation' => 'required',
        'workingDayStartMin' => 'required',
        'workingDayStartMax' => 'required',
        'workingDayEndMin' => 'required',
        'workingDayEndMax' => 'required',
        'dailyWorkingHours' => 'required|numeric|min:1|max:24',
        'overtimeRate' => 'required|numeric|min:1',
        'isAutomaticOvertime' => 'boolean',
    ];

    public function editConfiguration($employeeId)
    {
        $this->selectedEmployee = Employee::findOrFail($employeeId);
        $this->showApplyAttendanceModal = true;
    }

    public function closeApplyAttendanceModal()
    {
        $this->reset();
        $this->showApplyAttendanceModal = false;
    }

    public function applyAttendance()
    {
        $this->validate();

        try {
            $workingDays = ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday'];
            
            $this->selectedEmployee->setAttendanceConfigurations(
                $workingDays,
                $this->attendanceCalculation,
                $this->workingDayStartMin,
                $this->workingDayStartMax,
                $this->workingDayEndMin,
                $this->workingDayEndMax,
                $this->dailyWorkingHours,
                $this->isAutomaticOvertime,
                $this->overtimeRate
            );

            $this->alertSuccess('Attendance configuration applied successfully!');
            $this->closeApplyAttendanceModal();
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.benefits.partials.apply-attendance-modal');
    }
} 