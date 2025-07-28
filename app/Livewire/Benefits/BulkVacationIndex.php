<?php

namespace App\Livewire\Benefits;

use App\Models\Benefits\Configurations\VacationPackage;
use App\Models\Benefits\Payrolls\AppliedVacation;
use App\Models\Benefits\Vacations\VacationDetail;
use App\Models\Personel\Employee;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithPagination;

class BulkVacationIndex extends Component
{
    use AlertFrontEnd, WithPagination;

    public $employeesData = [];
    public $packages = [];
    public $perPage = 5;
    public $search = '';
    public $selectedDepartment = '';
    public $departments = [];
    public $showVacationModal = false;
    public $modalEmployeeId = null;

    protected $listeners = ['refreshBulkVacation' => '$refresh'];

    public function mount()
    {
        $this->loadDepartments();
        $this->loadPackages();
    }

    public function loadDepartments()
    {
        $this->departments = \App\Models\Hierarchy\Department::select('id', 'name')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function loadPackages()
    {
        $this->packages = VacationPackage::all();
    }

    public function getEmployeesProperty()
    {
        $query = Employee::with(['position.department', 'benefitConfiguration', 'vacationBenefits'])
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

        $employee = Employee::with(['position.department', 'benefitConfiguration', 'vacationBenefits'])
            ->findOrFail($employeeId);

        $employeeData = [
            'employee' => $employee,
            'isLoading' => false,
            'errors' => [],
        ];

        // Load existing configuration if available
        if ($employee->benefitConfiguration && $employee->benefitConfiguration->vacation_package_id) {
            $selectedPackageId = $employee->benefitConfiguration->vacation_package_id;
            $selectedPackage = VacationPackage::with(['vacationDetails'])->findOrFail($selectedPackageId);

            $employeeData['selectedPackageId'] = $selectedPackageId;
            $employeeData['selectedPackage'] = $selectedPackage;

            // Load existing vacation benefits
            $vacationBenefits = $employee->vacationBenefits()->byPackage($selectedPackageId)->get()->map(function ($benefit) {
                $tmpVacationDetail = VacationDetail::find($benefit->vacation_detail_id);
                return [
                    'vacation_detail_id' => $benefit->vacation_detail_id,
                    'name' => $benefit->name,
                    'inc_rate' => $benefit->inc_rate,
                    'max_balance' => $benefit->max_balance,
                    'hour_price' => $benefit->hour_price,
                    'current_balance' => $benefit->current_balance,
                    'original_current_balance' => $benefit->current_balance,
                    'type' => $benefit->type,
                    'start_date' => $benefit->start_date,
                    'end_date' => $benefit->end_date,
                    'inc_rate_min' => $tmpVacationDetail->inc_rate_min,
                    'inc_rate_max' => $tmpVacationDetail->inc_rate_max,
                    'max_balance_min' => $tmpVacationDetail->max_balance_min,
                    'max_balance_max' => $tmpVacationDetail->max_balance_max,
                    'hour_price_min' => $tmpVacationDetail->hour_price_min,
                    'hour_price_max' => $tmpVacationDetail->hour_price_max,
                    'automatic_add_to_balance' => $benefit->automatic_add_to_balance ?? false,
                    'is_disabled' => true,
                ];
            })->toArray();

            $employeeData['vacationBenefits'] = $vacationBenefits;

            // Set start and end dates
            if (count($vacationBenefits)) {
                if ($vacationBenefits[0]['start_date']) {
                    $employeeData['packageStartDate'] = Carbon::parse($vacationBenefits[0]['start_date'])->format('Y-m-d');
                } else {
                    $employeeData['packageStartDate'] = Carbon::now()->format('Y-m-d');
                }
                if ($vacationBenefits[0]['end_date']) {
                    $employeeData['packageEndDate'] = Carbon::parse($vacationBenefits[0]['end_date'])->format('Y-m-d');
                } else {
                    $employeeData['packageEndDate'] = null;
                }
            }

            $employeeData['deleteOldConf'] = true;
        } else {
            // Set default values
            $employeeData['selectedPackageId'] = null;
            $employeeData['selectedPackage'] = null;
            $employeeData['vacationBenefits'] = [];
            $employeeData['packageStartDate'] = Carbon::now()->format('Y-m-d');
            $employeeData['packageEndDate'] = null;
            $employeeData['deleteOldConf'] = true;
        }

        $this->employeesData[$employeeId] = $employeeData;
    }

    public function loadVacationPackage($employeeId)
    {
        if (!$this->employeesData[$employeeId]['selectedPackageId']) {
            return;
        }

        $selectedPackage = VacationPackage::with(['vacationDetails'])->findOrFail($this->employeesData[$employeeId]['selectedPackageId']);

        $this->employeesData[$employeeId]['selectedPackage'] = $selectedPackage;

        // Initialize vacation benefits with min values
        $vacationBenefits = $selectedPackage->vacationDetails->map(function ($detail) {
            return [
                'start_date' => $detail->start_date,
                'end_date' => $detail->end_date,
                'vacation_detail_id' => $detail->id,
                'name' => $detail->name,
                'inc_rate' => $detail->inc_rate_min,
                'inc_rate_min' => $detail->inc_rate_min,
                'inc_rate_max' => $detail->inc_rate_max,
                'max_balance' => $detail->max_balance_min,
                'max_balance_min' => $detail->max_balance_min,
                'max_balance_max' => $detail->max_balance_max,
                'hour_price' => $detail->hour_price_min,
                'hour_price_min' => $detail->hour_price_min,
                'hour_price_max' => $detail->hour_price_max,
                'automatic_add_to_balance' => false,
                'is_disabled' => false,
                'type' => $detail->type
            ];
        })->toArray();

        $this->employeesData[$employeeId]['vacationBenefits'] = $vacationBenefits;

        // Update current balance for each benefit
        foreach ($this->employeesData[$employeeId]['vacationBenefits'] as $key => $benefit) {
            $this->updateCurrentBalance($employeeId, $key);
        }
    }

    public function updateCurrentBalance($employeeId, $key)
    {
        $benefit = $this->employeesData[$employeeId]['vacationBenefits'][$key];
        $value = $benefit['inc_rate'];
        $vacationDetail = VacationDetail::find($benefit['vacation_detail_id']);
        $startDate = Carbon::parse($benefit['start_date'] ?? $this->employeesData[$employeeId]['packageStartDate'] ?? Carbon::now()->format('Y-m-d'));
        $startDate = $startDate->isBefore(Carbon::now()->startOfYear()) ? Carbon::now()->startOfYear() : $startDate;
        switch ($benefit['type']) {
            case VacationDetail::TYPE_YEARLY:
                $endOfYear = $startDate->clone()->endOfYear();
                $leftRatio = $endOfYear->diffInDays($startDate, true) / 365;
                $appliedVacations = AppliedVacation::currentBalance($vacationDetail, $startDate, $endOfYear)->count();
                $this->employeesData[$employeeId]['vacationBenefits'][$key]['current_balance'] = ($value * $leftRatio) - $appliedVacations;
                break;
            case VacationDetail::TYPE_MONTHLY:
                $endOfMonth = $startDate->clone()->endOfMonth();
                $leftRatio = $endOfMonth->diffInDays($startDate, true) / 30;
                $appliedVacations = AppliedVacation::currentBalance($vacationDetail, $startDate, $endOfMonth)->count();
                $this->employeesData[$employeeId]['vacationBenefits'][$key]['current_balance'] = ($value * $leftRatio) - $appliedVacations;
                break;
            case VacationDetail::TYPE_WEEKLY:
                $endOfWeek = $startDate->clone()->endOfWeek();
                $leftRatio = $endOfWeek->diffInDays($startDate, true) / 7;
                $appliedVacations = AppliedVacation::currentBalance($vacationDetail, $startDate, $endOfWeek)->count();
                $this->employeesData[$employeeId]['vacationBenefits'][$key]['current_balance'] = ($value * $leftRatio) - $appliedVacations;
                break;
            case VacationDetail::TYPE_QUARTERLY:
                $endOfQuarter = $startDate->clone()->endOfQuarter();
                $leftRatio = $endOfQuarter->diffInDays($startDate, true) / 90;
                $appliedVacations = AppliedVacation::currentBalance($vacationDetail, $startDate, $endOfQuarter)->count();
                $this->employeesData[$employeeId]['vacationBenefits'][$key]['current_balance'] = ($value * $leftRatio) - $appliedVacations;
                break;
            case VacationDetail::TYPE_DAILY:
                $this->employeesData[$employeeId]['vacationBenefits'][$key]['current_balance'] = $value;
                break;
        }
    }

    public function openVacationModal($employeeId)
    {
        $this->modalEmployeeId = $employeeId;
        $this->showVacationModal = true;
    }

    public function closeVacationModal()
    {
        $this->showVacationModal = false;
        $this->modalEmployeeId = null;
    }

    public function toggleAutomaticAddToBalance($employeeId, $index)
    {
        // If this benefit is being checked, uncheck all others
        if (!$this->employeesData[$employeeId]['vacationBenefits'][$index]['automatic_add_to_balance']) {
            // First, set all to false
            foreach ($this->employeesData[$employeeId]['vacationBenefits'] as $key => $benefit) {
                $this->employeesData[$employeeId]['vacationBenefits'][$key]['automatic_add_to_balance'] = false;
            }
            // Then set only this one to true
            $this->employeesData[$employeeId]['vacationBenefits'][$index]['automatic_add_to_balance'] = true;
        } else {
            // If unchecking, just set to false
            $this->employeesData[$employeeId]['vacationBenefits'][$index]['automatic_add_to_balance'] = false;
        }
    }

    public function saveEmployeeVacation($employeeId)
    {
        $employeeData = $this->employeesData[$employeeId];
        $employee = $employeeData['employee'];
        Log::info($employeeData);
        // Validate the data
        $rules = [
            'selectedPackageId' => 'required|exists:vacation_packages,id',
            'vacationBenefits.*.inc_rate' => 'required|numeric',
            'vacationBenefits.*.max_balance' => 'required|numeric',
            'vacationBenefits.*.hour_price' => 'required|numeric',
            'vacationBenefits.*.current_balance' => 'required|numeric',
            'vacationBenefits.*.automatic_add_to_balance' => 'boolean',
            'packageStartDate' => 'required|date',
            'packageEndDate' => 'nullable|date|after:packageStartDate',
        ];

        $messages = [
            'selectedPackageId.required' => 'Please select a vacation package',
            'vacationBenefits.*.inc_rate.required' => 'Inc Rate is required',
            'vacationBenefits.*.max_balance.required' => 'Max Balance is required',
            'vacationBenefits.*.hour_price.required' => 'Hour Price is required',
            'vacationBenefits.*.current_balance.required' => 'Current Balance is required',
            'packageStartDate.required' => 'Start Date is required',
            'packageEndDate.after' => 'End Date must be after Start Date',
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


        // Custom validation: only one vacation benefit can have automatic_add_to_balance = true
        $automaticAddCount = collect($employeeData['vacationBenefits'])->where('automatic_add_to_balance', true)->count();
        if ($automaticAddCount > 1) {
            $this->employeesData[$employeeId]['errors'] = ['general' => 'Only one vacation benefit can be set to automatically add balance for extra attendance.'];
            return;
        }

        $this->employeesData[$employeeId]['isLoading'] = true;
        $this->employeesData[$employeeId]['errors'] = [];

        try {
            $selectedPackage = $employeeData['selectedPackage'];
            $vacationBenefits = $employeeData['vacationBenefits'];

            // Set start and end dates for each benefit
            foreach ($vacationBenefits as &$benefit) {
                $benefit['start_date'] = $employeeData['packageStartDate'];
                if (isset($employeeData['packageEndDate']) && $employeeData['packageEndDate']) {
                    $benefit['end_date'] = $employeeData['packageEndDate'];
                } else {
                    $benefit['end_date'] = null;
                }
            }

            $employee->applyVacationPackage(
                $selectedPackage,
                $vacationBenefits,
                $employeeData['deleteOldConf']
            );

            $this->alertSuccess('Vacation package applied successfully for ' . $employee->name . '!');

            // Refresh employee data to show updated configuration
            $this->initializeEmployeeData($employeeId);
        } catch (\Exception $e) {
            $this->employeesData[$employeeId]['errors'] = ['general' => $e->getMessage()];
            $this->alertError('Error applying vacation package: ' . $e->getMessage());
        } finally {
            $this->employeesData[$employeeId]['isLoading'] = false;
        }
    }

    public function render()
    {
        $employees = $this->getEmployeesProperty();
        return view('livewire.benefits.bulk-vacation-index', compact('employees'));
    }
}
