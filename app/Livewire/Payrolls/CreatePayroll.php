<?php

namespace App\Livewire\Payrolls;

use App\Models\Attendance\Overtime;
use App\Models\Benefits\Payrolls\Payroll;
use App\Models\Personel\Employee;
use App\Models\Hierarchy\Department;
use App\Models\Users\AppLog;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;

#[Title('Create Payroll')]
class CreatePayroll extends Component
{
    use AlertFrontEnd, WithPagination;

    // Properties for the department selection modal
    public $showDepartmentModal = false;
    public $selectedDepartment = null;
    public $selectedDepartments = [];
    public $selectAllEmployees = true;
    public $selectedEmployees = [];
    public $departments;

    // Properties for the payroll data
    public $startDate;
    public $endDate;
    public $payrollMonth;
    public $payrollData = [];

    // Properties for adjustment modal
    public $showAdjustmentModal = false;
    public $selectedEmployeeForAdjustment = null;
    public $adjustmentAmount = 0;
    public $adjustmentDescription = '';

    // Properties for filtering and display
    public $search = '';
    public $perPage = 10;

    protected $listeners = ['refreshPayroll'];

    protected function ensureArrays()
    {
        // Ensure these properties are always arrays
        if (!is_array($this->selectedDepartments)) {
            $this->selectedDepartments = !empty($this->selectedDepartments) ? [$this->selectedDepartments] : [];
        }

        if (!is_array($this->selectedEmployees)) {
            $this->selectedEmployees = !empty($this->selectedEmployees) ? [$this->selectedEmployees] : [];
        }

        if (!is_array($this->payrollData)) {
            $this->payrollData = [];
        }
    }

    public function hydrate()
    {
        $this->ensureArrays();
    }

    public function selectAllDepartments()
    {
        if (count($this->departments) > 0) {
            $this->selectedDepartments = $this->departments->pluck('id')->toArray();
            $this->loadEmployeesFromMultipleDepartments($this->selectedDepartments);
        }
    }

    public function updatedPayrollMonth($value)
    {
        $newMonth = Carbon::now()->setMonth((int)$value);
        $this->startDate = $newMonth->clone()->setDay(25)->subMonth()->format('Y-m-d');
        $this->endDate = $newMonth->clone()->setDay(25)->format('Y-m-d');
        $this->payrollData = [];
    }

    public function mount()
    {
        $this->payrollMonth = Carbon::now()->month;
        $this->departments = Department::orderBy('name')->get();
        $this->startDate = Carbon::now()->setDay(25)->subMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->setDay(25)->format('Y-m-d');
        $this->ensureArrays();
    }

    public function refreshPayroll()
    {
        $this->loadPayrollData();
    }

    public function openDepartmentModal()
    {
        $this->showDepartmentModal = true;
    }

    public function closeDepartmentModal()
    {
        $this->showDepartmentModal = false;
    }

    public function updatedSelectedDepartment($value)
    {
        if ($this->selectAllEmployees) {
            $this->loadEmployeesFromDepartment($value);
        } else {
            $this->selectedEmployees = [];
        }
    }

    public function updatedSelectedDepartments($value)
    {
        $this->ensureArrays();

        if ($this->selectAllEmployees && !empty($this->selectedDepartments)) {
            $this->loadEmployeesFromMultipleDepartments($this->selectedDepartments);
        } else {
            $this->selectedEmployees = [];
        }
    }

    public function updatedSelectAllEmployees($value)
    {
        $this->ensureArrays();

        if ($value) {
            if ($this->selectedDepartment) {
                $this->loadEmployeesFromDepartment($this->selectedDepartment);
            } elseif (!empty($this->selectedDepartments)) {
                $this->loadEmployeesFromMultipleDepartments($this->selectedDepartments);
            }
        } else {
            $this->selectedEmployees = [];
        }
    }

    public function loadEmployeesFromMultipleDepartments($departmentIds)
    {
        $this->ensureArrays();

        if (empty($departmentIds)) {
            $this->selectedEmployees = [];
            return;
        }

        // Ensure $departmentIds is an array
        if (!is_array($departmentIds)) {
            $departmentIds = [$departmentIds];
        }

        $employees = Employee::whereHas('position', function ($query) use ($departmentIds) {
            $query->whereIn('department_id', $departmentIds);
        })
            ->current(Carbon::parse($this->startDate), Carbon::parse($this->endDate))
            ->get();

        $this->selectedEmployees = $employees->pluck('id')->toArray();
    }

    public function loadEmployeesFromDepartment($departmentId)
    {
        $this->ensureArrays();

        if (!$departmentId) {
            $this->selectedEmployees = [];
            return;
        }

        $employees = Employee::whereHas('position', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
            ->current(Carbon::parse($this->startDate), Carbon::parse($this->endDate))
            ->get();

        $this->selectedEmployees = $employees->pluck('id')->toArray();
    }

    public function submitDepartmentSelection()
    {
        $this->ensureArrays();

        if (empty($this->selectedDepartments) && !$this->selectedDepartment) {
            $this->addError('departmentSelection', 'Please select at least one department.');
            return;
        }

        if (empty($this->selectedEmployees)) {
            $this->alertError('Please select at least one employee.');
            return;
        }

        $this->loadPayrollData();
        $this->closeDepartmentModal();
    }

    public function openAdjustmentModal($departmentId, $employeeIndex)
    {
        $this->selectedEmployeeForAdjustment = [$departmentId, $employeeIndex];
        $employee = $this->payrollData[$departmentId]['employees'][$employeeIndex];
        $this->adjustmentAmount = $employee['adj_amount'] ?? 0;
        $this->adjustmentDescription = $employee['adj_desc'] ?? '';
        $this->showAdjustmentModal = true;
    }

    public function closeAdjustmentModal()
    {
        $this->showAdjustmentModal = false;
        $this->selectedEmployeeForAdjustment = null;
        $this->adjustmentAmount = 0;
        $this->adjustmentDescription = '';
    }

    public function saveAdjustment()
    {
        if (!$this->selectedEmployeeForAdjustment) {
            return;
        }

        [$departmentId, $employeeIndex] = $this->selectedEmployeeForAdjustment;

        // Update the employee data with adjustment
        $this->payrollData[$departmentId]['employees'][$employeeIndex]['adj_amount'] = $this->adjustmentAmount;
        $this->payrollData[$departmentId]['employees'][$employeeIndex]['adj_desc'] = $this->adjustmentDescription;

        // Recalculate net after deductions with adjustment
        $employee = &$this->payrollData[$departmentId]['employees'][$employeeIndex];
        $employee['net_after_deductions'] = $employee['net_after_penalty'] + $employee['extra_payments'] + $employee['overtime_amount'] + $this->adjustmentAmount;

        $this->closeAdjustmentModal();
        $this->alertSuccess('Adjustment saved successfully.');
    }

    public function loadPayrollData()
    {
        $this->ensureArrays();

        $this->payrollData = [];

        $employees = Employee::with(['position', 'position.department', 'benefitConfiguration'])
            ->current(Carbon::parse($this->startDate), Carbon::parse($this->endDate))
            ->whereIn('id', $this->selectedEmployees)
            ->get()
            ->sortBy('position.department.name');

        $departmentGroups = [];
        $totals = [
            'gross_salary' => 0,
            'insurance_amount' => 0,
            'employee_insurance' => 0,
            'employer_insurance' => 0,
            'total_insurance' => 0,
            'other_amount' => 0,
            'employee_medical' => 0,
            'total_medical' => 0,
            'employee_deductions' => 0,
            'penalties_days' => 0,
            'penalties_amount' => 0,
            'extra_payments' => 0,
            'overtime_hours' => 0,
            'overtime_amount' => 0,
            'adj_amount' => 0,
        ];

        foreach ($employees as $employee) {
            $departmentName = $employee->position?->department?->name ?? 'No Department';
            $departmentId = $employee->position?->department?->id ?? 0;

            if (!isset($departmentGroups[$departmentId])) {
                $departmentGroups[$departmentId] = [
                    'name' => $departmentName,
                    'employees' => [],
                    'totals' => [
                        'gross_salary' => 0,
                        'insurance_amount' => 0,
                        'employee_insurance' => 0,
                        'employer_insurance' => 0,
                        'total_insurance' => 0,
                        'other_amount' => 0,
                        'net_income' => 0,
                        'penalties_days' => 0,
                        'penalties_amount' => 0,
                        'extra_payments' => 0,
                        'overtime_hours' => 0,
                        'overtime_amount' => 0,
                        'adj_amount' => 0,
                    ]
                ];
            }

            $employeeTerminationDate = is_null($employee->termination_date) ? null : Carbon::parse($employee->termination_date);
            $employeeStartDate = Carbon::parse($employee->employment_date);
            $grossPercentage = 100;

            // Calculate the percentage of the gross salary based on the included days
            if ($employeeStartDate->isAfter($this->startDate) && $employeeTerminationDate && $employeeTerminationDate->isBefore($this->endDate)) {
                $includedDays = $employeeTerminationDate->diffInDays($this->employeeStartDate);
                $grossPercentage = (min($includedDays, 30) / 30) * 100;
            } else if ($employeeTerminationDate && $employeeTerminationDate->isAfter($this->startDate) && $employeeTerminationDate->isBefore($this->endDate)) {
                $includedDays = $employeeTerminationDate->diffInDays($this->startDate);
                $grossPercentage = (min($includedDays, 30) / 30) * 100;
            } else if ($employeeStartDate->isAfter($this->startDate) && $employeeStartDate->isBefore($this->endDate)) {
                $includedDays = $employeeStartDate->diffInDays($this->endDate);
                $grossPercentage = (min($includedDays, 30) / 30) * 100;
            }

            $grossSalary = ($employee->benefitConfiguration?->gross_salary ?? 0) * $grossPercentage / 100;
            $insuranceAmount = $employee->benefitConfiguration?->insurance_amount ?? 0;
            $employeeInsurance = $insuranceAmount * Payroll::EMPLOYEE_SHARE_SOCIAL_INSURANCE;
            $employerInsurance = $insuranceAmount * Payroll::EMPLOYER_SHARE_SOCIAL_INSURANCE;
            $totalInsurance = $employeeInsurance + $employerInsurance;
            $otherAmount = $grossSalary - $insuranceAmount - $employerInsurance ?? 0;
            $employeeMedical = $employee->activeMedicalBenefits(Carbon::parse($this->startDate))->sum('amount');
            $totalMedical = $employeeMedical;
            $employeeDeductions = $employeeInsurance + $employeeMedical;
            $netIncome = $otherAmount + $insuranceAmount;
            $dayPrice = $netIncome / 30;
            $employeeBaseBenefits = $employee->getEmployeeBaseBenefitsCalculation(Carbon::parse($this->startDate), Carbon::parse($this->endDate));
            $otherBaseBenefits = $employee->getOtherBaseBenefitsCalculation(Carbon::parse($this->startDate), Carbon::parse($this->endDate));

            
            // Get daily working hours from employee benefit configuration
            $dailyWorkingHours = $employee->benefitConfiguration?->daily_working_hours ?? 8;
            dd('hourly rate: ' . $employee->calculateHourlyRate(), 'day price: ' . $dayPrice / $dailyWorkingHours);
            // Calculate penalty hours using the new penalty offset method
            $hourlyRate = $dayPrice / $dailyWorkingHours;
            $penaltyData = $employee->calculatePenaltyWithVacationOffset($this->startDate, $this->endDate, $hourlyRate);

            // Extract penalty information
            $totalPenaltyHours = $penaltyData['total_penalty_hours'];
            $vacationOffsetHours = $penaltyData['vacation_offset_hours'];
            $remainingPenaltyHours = $penaltyData['remaining_penalty_hours'];
            $directDeductionAmount = $penaltyData['direct_deduction_amount'];
            $usedApprovedVacations = $penaltyData['used_approved_vacations'];
            $availableVacationBenefits = $penaltyData['available_vacation_benefits'];

            // Convert total hours to days for display purposes
            $totalPenaltyDays = $dailyWorkingHours > 0 ? $totalPenaltyHours / $dailyWorkingHours : 0;

            // The penalty amount is now only the direct deduction amount (after vacation offset)
            $penaltyAmount = $directDeductionAmount;

            $netAfterPenalty = $netIncome - $penaltyAmount;

            // Get extra payments
            $extraPayments = $employee->getNegativeExtraPayments($this->startDate, $this->endDate);

            // Alternatively, use the new penalty calculation function (uncomment if needed)
            // $penaltyAmount = $employee->calculateTotalPenaltyDeduction($this->startDate, $this->endDate);

            // Calculate overtime
            $overtimeHours = 0;
            $overtimeRate = $employee->benefitConfiguration->overtime_rate ?? 1.5;
            $dailyWorkingHours = $employee->benefitConfiguration->daily_working_hours ?? 8;
            $isAutomaticOvertime = $employee->benefitConfiguration->is_automatic_overtime ?? false;
            $createdOvertimeIds = [];
            $potentialOvertimeData = [];

            if ($isAutomaticOvertime) {
                // Calculate potential overtime from attendance without creating records yet
                $attendances = $employee->attendances()
                    ->where('is_approved', true)
                    ->whereBetween('date', [$this->startDate, $this->endDate])
                    ->whereNull('payroll_id')
                    ->get();

                foreach ($attendances as $attendance) {
                    $dailyHours = $attendance->hours;

                    // Check if this day has overtime
                    if ($dailyHours > $dailyWorkingHours) {
                        $overtimeHours += ($dailyHours - $dailyWorkingHours);

                        // Store potential overtime data for later creation
                        $potentialOvertimeData[] = [
                            'attendance_id' => $attendance->id,
                            'date' => $attendance->date,
                            'hours' => $dailyHours - $dailyWorkingHours,
                            'start_time' => $attendance->start_time,
                            'end_time' => $attendance->end_time,
                        ];
                    }
                }

                // Also include existing approved overtime records for this period
                $existingOvertimeHours = $employee->overtimes()
                    ->where('status', Overtime::STATUS_APPROVED)
                    ->whereBetween('date', [$this->startDate, $this->endDate])
                    ->whereNull('payroll_id')
                    ->sum('hours');

                $overtimeHours += $existingOvertimeHours;
            } else {
                // Use only explicitly created and approved overtime records
                $overtimeHours = $employee->overtimes()
                    ->where('status', Overtime::STATUS_APPROVED)
                    ->whereBetween('approved_at', [$this->startDate, $this->endDate])
                    ->whereNull('payroll_id')
                    ->sum('hours');

                // No new overtime records are created in manual mode
                $createdOvertimeIds = [];
            }

            // Calculate overtime amount
            $overtimeAmount = $overtimeHours * $hourlyRate * $overtimeRate;

            // Initialize adjustment values (will be 0 initially)
            $adjAmount = 0;
            $adjDesc = '';

            // Calculate net income with overtime and adjustment
            $netAfterDeductions = $netAfterPenalty + $extraPayments + $overtimeAmount + $adjAmount;

            // Add to department totals
            $departmentGroups[$departmentId]['totals']['gross_salary'] += $grossSalary;
            $departmentGroups[$departmentId]['totals']['insurance_amount'] += $insuranceAmount;
            $departmentGroups[$departmentId]['totals']['employee_insurance'] += $employeeInsurance;
            $departmentGroups[$departmentId]['totals']['employer_insurance'] += $employerInsurance;
            $departmentGroups[$departmentId]['totals']['total_insurance'] += $totalInsurance;
            $departmentGroups[$departmentId]['totals']['penalties_days'] += $totalPenaltyDays;
            $departmentGroups[$departmentId]['totals']['penalties_amount'] += $penaltyAmount;
            $departmentGroups[$departmentId]['totals']['extra_payments'] += $extraPayments;
            $departmentGroups[$departmentId]['totals']['overtime_hours'] += $overtimeHours;
            $departmentGroups[$departmentId]['totals']['overtime_amount'] += $overtimeAmount;
            $departmentGroups[$departmentId]['totals']['adj_amount'] += $adjAmount;

            // Add to grand totals
            $totals['gross_salary'] += $grossSalary;
            $totals['insurance_amount'] += $insuranceAmount;
            $totals['employee_insurance'] += $employeeInsurance;
            $totals['employer_insurance'] += $employerInsurance;
            $totals['total_insurance'] += $totalInsurance;
            $totals['penalties_days'] += $totalPenaltyDays;
            $totals['penalties_amount'] += $penaltyAmount;
            $totals['extra_payments'] += $extraPayments;
            $totals['overtime_hours'] += $overtimeHours;
            $totals['overtime_amount'] += $overtimeAmount;
            $totals['adj_amount'] += $adjAmount;

            $benefits = collect()
                ->merge($employee->activeEmployeeBaseBenefits(Carbon::parse($this->startDate))->get())
                ->merge($employee->activeOtherBaseBenefits(Carbon::parse($this->startDate))->get())
                ->merge($employee->activeMedicalBenefits(Carbon::parse($this->startDate))->get());

            // Explicitly use indexed arrays for employees to ensure we have id as a field
            $departmentGroups[$departmentId]['employees'][] = [
                'id' => $employee->id,
                'name' => $employee->name,
                'position' => $employee->position?->name ?? 'No Position',
                'gross_salary' => $grossSalary,
                'insurance_amount' => $insuranceAmount,
                'employee_insurance' => $employeeInsurance,
                'employer_insurance' => $employerInsurance,
                'total_insurance' => $totalInsurance,
                'other_amount' => $otherAmount,
                'employee_medical' => $employeeMedical,
                'total_medical' => $totalMedical,
                'employee_deductions' => $employeeDeductions,
                'penalties_days' => $totalPenaltyDays,
                'penalties_amount' => $penaltyAmount,
                'total_penalty_hours' => $totalPenaltyHours,
                'vacation_offset_hours' => $vacationOffsetHours,
                'remaining_penalty_hours' => $remainingPenaltyHours,
                'direct_deduction_amount' => $directDeductionAmount,
                'used_approved_vacations' => $usedApprovedVacations,
                'available_vacation_benefits' => $availableVacationBenefits,
                'net_income' => $netIncome - $penaltyAmount,
                'net_after_penalty' => $netAfterPenalty,
                'extra_payments' => $extraPayments,
                'overtime_hours' => $overtimeHours,
                'overtime_amount' => $overtimeAmount,
                'adj_amount' => $adjAmount,
                'adj_desc' => $adjDesc,
                'net_after_deductions' => $netAfterDeductions,
                'employee_base_benefits' => $employeeBaseBenefits,
                'other_base_benefits' => $otherBaseBenefits,
                'benefits' => $benefits,
                'potential_overtime_data' => $potentialOvertimeData, // Store potential overtime data for later creation
                'is_automatic_overtime' => $isAutomaticOvertime, // Store the automatic overtime flag
            ];
        }

        $this->payrollData = $departmentGroups;
        $this->payrollData['_totals'] = $totals;
    }

    /**
     * Submit the payroll for processing
     */
    public function submitPayroll()
    {
        $this->ensureArrays();

        try {
            // Prepare employee data for the payroll
            $employeePayrollData = [];

            foreach ($this->payrollData as $deptId => $departmentData) {
                // Skip the _totals entry
                if ($deptId === '_totals') {
                    continue;
                }

                foreach ($departmentData['employees'] as $employeeData) {
                    $employee = Employee::find($employeeData['id']);

                    if (!$employee) {
                        continue;
                    }

                    // Create overtime records if automatic overtime is enabled and we have potential overtime data
                    $createdOvertimeIds = [];
                    if ($employeeData['is_automatic_overtime'] && !empty($employeeData['potential_overtime_data'])) {
                        foreach ($employeeData['potential_overtime_data'] as $overtimeData) {
                            // Check if overtime record already exists for this date and employee
                            $existingOvertime = Overtime::where('employee_id', $employee->id)
                                ->where('date', $overtimeData['date'])
                                ->first();

                            if (!$existingOvertime) {
                                // Calculate overtime start and end times
                                $overtimeStartTime = null;
                                $overtimeEndTime = null;

                                if ($overtimeData['start_time'] && $overtimeData['end_time']) {
                                    $dailyWorkingHours = $employee->benefitConfiguration->daily_working_hours ?? 8;

                                    // Parse the attendance times
                                    $attendanceStart = Carbon::parse($overtimeData['date'] . ' ' . $overtimeData['start_time']);
                                    $attendanceEnd = Carbon::parse($overtimeData['date'] . ' ' . $overtimeData['end_time']);

                                    // If end time is before start time, it means it crossed midnight
                                    if ($attendanceEnd->lt($attendanceStart)) {
                                        $attendanceEnd->addDay();
                                    }

                                    // Calculate when normal working hours should end
                                    $normalWorkingEnd = $attendanceStart->copy()->addHours($dailyWorkingHours);

                                    // Overtime starts when normal working hours end
                                    $overtimeStartTime = $normalWorkingEnd->format('H:i:s');
                                    $overtimeEndTime = $attendanceEnd->format('H:i:s');

                                    // If overtime crosses midnight, adjust the end time
                                    if ($attendanceEnd->day != $attendanceStart->day) {
                                        $overtimeEndTime = $attendanceEnd->format('H:i:s');
                                    }
                                }

                                // Create new overtime record with pending status
                                $overtime = Overtime::updateOrCreate([
                                    'employee_id' => $employee->id,
                                    'date' => $overtimeData['date'],
                                ], [
                                    'creator_id' => Auth::id(),
                                    'start_time' => $overtimeStartTime,
                                    'end_time' => $overtimeEndTime,
                                    'hours' => $overtimeData['hours'],
                                    'status' => Overtime::STATUS_APPROVED,
                                    'approved_at' => null,
                                    'admin_note' => 'Auto-created from attendance during payroll creation',
                                    'payroll_id' => null, // Will be set when payroll is created
                                ]);

                                $createdOvertimeIds[] = $overtime->id;

                                // Log the creation
                                AppLog::info(
                                    'Overtime Record Auto-Created',
                                    "Created overtime record for {$employee->name} on {$overtimeData['date']}. Hours: {$overtimeData['hours']}, Start: {$overtimeStartTime}, End: {$overtimeEndTime}",
                                    loggable: $overtime
                                );
                            }
                        }
                    }

                    // Get extra payment IDs
                    $extraPaymentIds = $employee->extraPayments()
                        ->where('status', \App\Models\Benefits\Payrolls\ExtraPayment::STATUS_APPROVED)
                        ->whereBetween('due_date', [$this->startDate, $this->endDate])
                        ->whereNull('payroll_id')
                        ->pluck('id')
                        ->toArray();

                    // Get attendance IDs
                    $attendanceIds = $employee->attendances()
                        ->whereBetween('date', [$this->startDate, $this->endDate])
                        ->whereNull('payroll_id')
                        ->pluck('id')
                        ->toArray();

                    // Get overtime IDs - include both existing approved ones and newly created pending ones
                    $existingOvertimeIds = $employee->overtimes()
                        ->where('status', \App\Models\Attendance\Overtime::STATUS_APPROVED)
                        ->whereBetween('date', [$this->startDate, $this->endDate])
                        ->whereNull('payroll_id')
                        ->pluck('id')
                        ->toArray();

                    // Check if automatic overtime is enabled for this employee
                    $isAutomaticOvertime = $employeeData['is_automatic_overtime'] ?? false;

                    if ($isAutomaticOvertime) {
                        // Include both existing approved and newly created overtime records
                        $overtimeIds = array_merge($existingOvertimeIds, $createdOvertimeIds);
                    } else {
                        // Only include existing approved overtime records
                        $overtimeIds = $existingOvertimeIds;
                    }

                    // No need to manually recreate benefit data - use the actual models collected in loadPayrollData
                    if (!empty($employeeData['benefits'])) {
                        // Pass the benefits collection directly, along with all required fields for PayrollEmployee
                        $employeePayrollData[] = [
                            'employee_id' => $employeeData['id'],
                            'gross_salary' => $employeeData['gross_salary'],
                            'insurance_amount' => $employeeData['insurance_amount'],
                            'other_amount' => $employeeData['other_amount'] ?? 0,
                            'employee_insurance' => $employeeData['employee_insurance'],
                            'employer_insurance' => $employeeData['employer_insurance'],
                            'total_insurance' => $employeeData['total_insurance'] ?? ($employeeData['employee_insurance'] + $employeeData['employer_insurance']),
                            'employee_medical' => $employeeData['employee_medical'],
                            'total_medical' => $employeeData['total_medical'] ?? $employeeData['employee_medical'],
                            'employee_deductions' => $employeeData['employee_deductions'],
                            'penalties_days' => $employeeData['penalties_days'],
                            'penalties_amount' => $employeeData['penalties_amount'],
                            'total_penalty_hours' => $employeeData['total_penalty_hours'] ?? 0,
                            'vacation_offset_hours' => $employeeData['vacation_offset_hours'] ?? 0,
                            'direct_deduction_hours' => $employeeData['remaining_penalty_hours'] ?? 0,
                            'direct_deduction_amount' => $employeeData['direct_deduction_amount'] ?? 0,
                            'overtime_hours' => $employeeData['overtime_hours'] ?? 0,
                            'overtime_amount' => $employeeData['overtime_amount'] ?? 0,
                            'net_after_penalty' => $employeeData['net_after_penalty'],
                            'extra_payments' => $employeeData['extra_payments'],
                            'adj_amount' => $employeeData['adj_amount'] ?? 0,
                            'adj_desc' => $employeeData['adj_desc'] ?? '',
                            'net_after_deductions' => $employeeData['net_after_deductions'],
                            'employee_base_benefits' => $employeeData['employee_base_benefits'],
                            'other_base_benefits' => $employeeData['other_base_benefits'],
                            'position' => $employeeData['position'],
                            'department' => $departmentData['name'],
                            // Include these fields or default values
                            'paid' => $employeeData['net_after_deductions'], // Same as net_after_deductions
                            'vacation_days' => 0, // Default to 0
                            'vacation_amount' => 0, // Default to 0
                            'base_amount' => $employeeData['insurance_amount'], // Use insurance amount as base
                            'extra_payment_ids' => $extraPaymentIds,
                            'attendance_ids' => $attendanceIds,
                            'overtime_ids' => $overtimeIds, // Include both existing and newly created overtime IDs
                            'benefits' => $employeeData['benefits']
                        ];
                    }
                }
            }

            // Create the payroll using the static method with the prepared data
            $payroll = Payroll::createPayroll(Auth::id(), $this->startDate, $this->endDate, $employeePayrollData);

            if ($payroll) {
                $this->alertSuccess('Payroll created successfully.');
                $this->reset();
            } else {
                $this->alertError('Failed to create payroll. Please try again.');
            }
        } catch (\Exception $e) {
            $this->alertError('Failed to create payroll: ' . $e->getMessage());
            AppLog::error('Payroll creation error: ' . $e->getMessage());
        }
    }

    public function clearPayrollData()
    {
        $this->payrollData = [];
    }

    public function render()
    {
        return view('livewire.payrolls.create-payroll', [
            'loadedDepartments' => $this->departments,
        ]);
    }
}
