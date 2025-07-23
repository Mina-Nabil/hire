<?php

namespace App\Livewire\Benefits;

use App\Models\Attendance\Bus;
use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Configurations\WorkingDay;
use App\Models\Personel\Employee;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithPagination;

class BulkAttendanceIndex extends Component
{
    use AlertFrontEnd, WithPagination;

    public $employeesData = [];
    public $buses = [];
    public $perPage = 5;
    public $search = '';
    public $selectedDepartment = '';
    public $departments = [];
    public $showAttendanceModal = false;
    public $modalEmployeeId = null;

    public $AllworkingDays = WorkingDay::DAYS_LIST;
    public $attendanceCalculations = BenefitConfiguration::ATTENDANCE_CALCULATION_LIST;

    protected $listeners = ['refreshBulkAttendance' => '$refresh'];

    public function mount()
    {
        $this->loadDepartments();
        $this->loadBuses();
    }

    public function loadDepartments()
    {
        $this->departments = \App\Models\Hierarchy\Department::select('id', 'name')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function loadBuses()
    {
        $this->buses = Bus::all();
    }

    public function getEmployeesProperty()
    {
        $query = Employee::with(['position.department', 'workingDays', 'benefitConfiguration.bus'])
            ->current()
            ->orderBy('name');

        if ($this->selectedDepartment) {
            $query->whereHas('position.department', function ($q) {
                $q->where('id', $this->selectedDepartment);
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate($this->perPage);
    }

    public function initializeEmployeeData($employeeId)
    {
        if (isset($this->employeesData[$employeeId])) {
            return;
        }

        $employee = Employee::with(['position.department', 'workingDays', 'benefitConfiguration.bus'])
            ->findOrFail($employeeId);

        $employeeData = [
            'employee' => $employee,
            'isLoading' => false,
            'errors' => [],
        ];

        // Load existing configuration if available
        if ($employee->benefitConfiguration) {
            $config = $employee->benefitConfiguration;
            $employeeData['workingDays'] = $employee->workingDays->pluck('type')->toArray();
            $employeeData['attendanceCalculation'] = $config->attendance_calculation ?? 'fixed';
            $employeeData['workingDayStartMin'] = $config->working_day_start_min;
            $employeeData['workingDayStartMax'] = $config->working_day_start_max;
            $employeeData['workingDayEndMin'] = $config->working_day_end_min;
            $employeeData['workingDayEndMax'] = $config->working_day_end_max;
            $employeeData['dailyWorkingHours'] = $config->daily_working_hours ?? 8;
            $employeeData['overtimeRate'] = $config->overtime_rate ?? 1;
            $employeeData['isAutomaticOvertime'] = $config->is_automatic_overtime ? true : false;
            $employeeData['isGenerateOvertime'] = $config->is_generate_overtime ? true : false;
            $employeeData['overtimeMaxTime'] = $config->overtime_max_time;
            $employeeData['isRequireAttendanceApproval'] = $config->is_require_attendance_approval ? true : false;
            $employeeData['busId'] = $config->bus_id;
        } else {
            // Set default values
            $employeeData['workingDays'] = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'];
            $employeeData['attendanceCalculation'] = 'fixed';
            $employeeData['workingDayStartMin'] = '09:00';
            $employeeData['workingDayStartMax'] = '09:00';
            $employeeData['workingDayEndMin'] = '17:00';
            $employeeData['workingDayEndMax'] = '17:00';
            $employeeData['dailyWorkingHours'] = 8;
            $employeeData['overtimeRate'] = 1;
            $employeeData['isAutomaticOvertime'] = false;
            $employeeData['isGenerateOvertime'] = true;
            $employeeData['overtimeMaxTime'] = null;
            $employeeData['isRequireAttendanceApproval'] = false;
            $employeeData['busId'] = null;
        }

        $this->employeesData[$employeeId] = $employeeData;
    }

    public function openAttendanceModal($employeeId)
    {
        $this->modalEmployeeId = $employeeId;
        $this->showAttendanceModal = true;
    }

    public function closeAttendanceModal()
    {
        $this->showAttendanceModal = false;
        $this->modalEmployeeId = null;
    }

    public function setFixedCalculation($employeeId)
    {
        $this->employeesData[$employeeId]['attendanceCalculation'] = 'fixed';
        $this->employeesData[$employeeId]['dailyWorkingHours'] = 8;
        $this->employeesData[$employeeId]['workingDayStartMin'] = '09:00';
        $this->employeesData[$employeeId]['workingDayStartMax'] = '09:00';
        $this->employeesData[$employeeId]['workingDayEndMin'] = '17:00';
        $this->employeesData[$employeeId]['workingDayEndMax'] = '17:00';
        $this->employeesData[$employeeId]['overtimeRate'] = 1;
        $this->employeesData[$employeeId]['isAutomaticOvertime'] = true;
        $this->employeesData[$employeeId]['isGenerateOvertime'] = true;
        $this->employeesData[$employeeId]['overtimeMaxTime'] = null;
        $this->employeesData[$employeeId]['isRequireAttendanceApproval'] = false;
    }

    public function saveEmployeeAttendance($employeeId)
    {
        $employeeData = $this->employeesData[$employeeId];
        $employee = $employeeData['employee'];

        // Validate the data
        $rules = [
            'workingDays' => 'required|array|min:1',
            'attendanceCalculation' => 'required',
            'workingDayStartMin' => 'required_unless:attendanceCalculation,bus,flexible',
            'workingDayStartMax' => 'required_unless:attendanceCalculation,bus,flexible',
            'workingDayEndMin' => 'required_unless:attendanceCalculation,in-only,flexible',
            'workingDayEndMax' => 'required_unless:attendanceCalculation,in-only,flexible',
            'dailyWorkingHours' => 'required|numeric|min:1|max:24',
            'overtimeRate' => 'required|numeric|min:1',
            'isAutomaticOvertime' => 'boolean',
            'isGenerateOvertime' => 'boolean',
            'overtimeMaxTime' => 'nullable|numeric|min:0',
            'isRequireAttendanceApproval' => 'boolean',
            'busId' => 'required_if:attendanceCalculation,bus',
        ];

        $messages = [
            'workingDays.required' => 'Please select at least one working day',
            'workingDays.min' => 'Please select at least one working day',
            'busId.required_if' => 'Please select a bus',
        ];

        $validator = Validator::make($employeeData, $rules, $messages);
        if ($validator->fails()) {
            $message = '';
            foreach ($validator->errors()->toArray() as $key => $error) {
                $message .= $error[0] . '<br>';
            }
            $this->alertError($message);
            return;
        }

        $this->employeesData[$employeeId]['isLoading'] = true;
        $this->employeesData[$employeeId]['errors'] = [];

        try {
            $employee->setAttendanceConfigurations(
                $employeeData['workingDays'],
                $employeeData['attendanceCalculation'],
                $employeeData['dailyWorkingHours'],
                $employeeData['isAutomaticOvertime'],
                $employeeData['overtimeRate'],
                $employeeData['workingDayStartMin'],
                $employeeData['workingDayStartMax'],
                $employeeData['workingDayEndMin'],
                $employeeData['workingDayEndMax'],
                $employeeData['overtimeMaxTime'],
                $employeeData['isRequireAttendanceApproval'],
                $employeeData['busId'],
                $employeeData['isGenerateOvertime']
            );

            $this->alertSuccess('Attendance configuration applied successfully for ' . $employee->name . '!');

            // Refresh employee data to show updated configuration
            $this->initializeEmployeeData($employeeId);
        } catch (\Exception $e) {
            $this->employeesData[$employeeId]['errors'] = ['general' => $e->getMessage()];
            $this->alertError('Error applying attendance configuration: ' . $e->getMessage());
        } finally {
            $this->employeesData[$employeeId]['isLoading'] = false;
        }
    }

    public function render()
    {
        $employees = $this->getEmployeesProperty();
        return view('livewire.benefits.bulk-attendance-index', compact('employees'));
    }
}
