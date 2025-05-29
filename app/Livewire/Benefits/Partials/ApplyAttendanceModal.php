<?php

namespace App\Livewire\Benefits\Partials;

use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Configurations\WorkingDay;
use App\Models\Personel\Employee;
use App\Traits\AlertFrontEnd;
use Livewire\Component;

class ApplyAttendanceModal extends Component
{
    use AlertFrontEnd;

    public $showApplyAttendanceModal = false;
    public $selectedEmployee;

    public $AllworkingDays = WorkingDay::DAYS_LIST;
    public $workingDays = [];
    public $attendanceCalculation;
    public $workingDayStartMin;
    public $workingDayStartMax;
    public $workingDayEndMin;
    public $workingDayEndMax;
    public $dailyWorkingHours;
    public $overtimeRate;
    public $isAutomaticOvertime;
    public $isRequireAttendanceApproval;
    public $deleteOldConf = true;

    public $attendanceCalculations = BenefitConfiguration::ATTENDANCE_CALCULATION_LIST;

    protected $rules = [
        'workingDays' => 'required|array|min:1',
        'attendanceCalculation' => 'required',
        'workingDayStartMin' => 'required',
        'workingDayStartMax' => 'required',
        'workingDayEndMin' => 'required_unless:attendanceCalculation,in-only',
        'workingDayEndMax' => 'required_unless:attendanceCalculation,in-only',
        'dailyWorkingHours' => 'required|numeric|min:1|max:24',
        'overtimeRate' => 'required|numeric|min:1',
        'isAutomaticOvertime' => 'boolean',
        'isRequireAttendanceApproval' => 'boolean',
    ];

    protected $messages = [
        'workingDays.required' => 'Please select at least one working day',
        'workingDays.min' => 'Please select at least one working day',
    ];

    public $listeners = ['editAttendance'];

    public function editAttendance($employeeId)
    {
        $this->selectedEmployee = Employee::with('benefitConfiguration', 'workingDays')->findOrFail($employeeId);

        $this->workingDays = $this->selectedEmployee->workingDays->pluck('type')->toArray();

        $this->attendanceCalculation = $this->selectedEmployee->benefitConfiguration?->attendance_calculation ?? BenefitConfiguration::ATTENDANCE_CALCULATION_FIXED;
        $this->workingDayStartMin = $this->selectedEmployee->benefitConfiguration?->working_day_start_min;
        $this->workingDayStartMax = $this->selectedEmployee->benefitConfiguration?->working_day_start_max;
        $this->workingDayEndMin = $this->selectedEmployee->benefitConfiguration?->working_day_end_min;
        $this->workingDayEndMax = $this->selectedEmployee->benefitConfiguration?->working_day_end_max;
        $this->dailyWorkingHours = $this->selectedEmployee->benefitConfiguration?->daily_working_hours;
        $this->overtimeRate = $this->selectedEmployee->benefitConfiguration?->overtime_rate;
        $this->isAutomaticOvertime = $this->selectedEmployee->benefitConfiguration?->is_automatic_overtime ? true : false;
        $this->isRequireAttendanceApproval = $this->selectedEmployee->benefitConfiguration?->is_require_attendance_approval ? true : false;
        
        $this->showApplyAttendanceModal = true;
    }

    public function closeApplyAttendanceModal()
    {
        $this->reset();
        $this->showApplyAttendanceModal = false;
    }

    public function setFixedCalculation()
    {
        $this->attendanceCalculation = 'fixed';
        $this->dailyWorkingHours = 8;
        $this->workingDayStartMin = '09:00';
        $this->workingDayStartMax = '09:00';
        $this->workingDayEndMin = '17:00';
        $this->workingDayEndMax = '17:00';
        $this->overtimeRate = 1;
        $this->isAutomaticOvertime = true;
        $this->isRequireAttendanceApproval = false;
    }

    public function applyAttendance()
    {
        $this->validate();

        try {
            $this->selectedEmployee->setAttendanceConfigurations(
                $this->workingDays,
                $this->attendanceCalculation,
                $this->workingDayStartMin,
                $this->workingDayStartMax,
                $this->dailyWorkingHours,
                $this->isAutomaticOvertime,
                $this->overtimeRate,
                $this->workingDayEndMin,
                $this->workingDayEndMax,
                $this->isRequireAttendanceApproval
            );

            $this->alertSuccess('Attendance configuration applied successfully!');
            $this->closeApplyAttendanceModal();
            $this->dispatch('refreshConfiguration');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.benefits.partials.apply-attendance-modal');
    }
} 