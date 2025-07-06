<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Create Payroll</h4>
        </div>
        <div class="card-body p-6">
            <div class="flex justify-between mb-5">
                @empty($payrollData)
                    <x-select wire:model.live="payrollMonth">
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </x-select>
                @endempty
                <div>
                    <h5 class="text-lg font-medium">Attendance Period:
                        {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</h5>
                </div>
                <div>
                    @empty($payrollData)
                    <button type="button" class="btn btn-dark btn-sm" wire:click="openDepartmentModal">
                        <span class="flex items-center">
                            <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2"
                                icon="heroicons-outline:plus"></iconify-icon>
                            Add Departments
                        </span>
                    </button>
                    @endempty
                
                    <button type="button" class="btn btn-dark btn-sm" wire:click="clearPayrollData">
                        <span class="flex items-center">
                            <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2"
                                icon="heroicons-outline:x-circle"></iconify-icon>
                            Clear Data
                        </span>
                    </button>
                </div>
            </div>

            <!-- Payroll Data Table -->
            @if (count($payrollData) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 no-wrap">
                        <thead>
                            <tr>
                                <th class="table-th border sticky-colomn border-slate-100 dark:bg-slate-800 dark:border-slate-700 "
                                    rowspan="2">Employee</th>
                                <th class=" table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700 "
                                    rowspan="2">Gross Salary</th>
                                <th class=" table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700 "
                                    rowspan="2">Basic Salary</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700 "
                                    rowspan="2">Other Amount</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-center th-highlight-red"
                                    colspan="3">Social Insurance Details</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-center th-highlight-green"
                                    colspan="2">Medical Insurance Details</th>
                                <th
                                    class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-center">
                                </th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-center th-highlight-yellow"
                                colspan="3">Penalty</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-center th-highlight-red"
                                    colspan="2">Extra Payments</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-center th-highlight-green"
                                    colspan="2">Overtime</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-center th-highlight-blue"
                                    colspan="2">Base Benefits</th>
                                <th
                                    class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-center">
                                    Adjustment</th>
                                <th
                                    class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700 text-center">
                                    Actions</th>
                            </tr>
                            <tr>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                    Employee</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                    Employer</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                    Total</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                    Employee</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                    Total</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                    Employee Deductions</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                    Days</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                    Amount</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">Net
                                    After Penalty</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                    Amount</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">Net
                                    After Deductions</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                    Hours</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                    Amount</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                    Paid To Employee</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                    Paid To Other</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                    Amount</th>
                                <th class="table-th border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            @foreach ($payrollData as $departmentId => $department)
                                @if ($departmentId !== '_totals')
                                    <tr class="bg-slate-50 dark:bg-slate-700">
                                        <td colspan="10" class="px-6 py-3 text-lg font-semibold depratmnet-name">
                                            {{ $department['name'] }}
                                        </td>
                                    </tr>
                                    @foreach ($department['employees'] as $employeeIndex => $employee)
                                        <tr>
                                            <td
                                                class="table-td sticky-colomn border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                <div>
                                                    <h6 class="text-base font-medium text-slate-900 dark:text-white">
                                                        {{ $employee['name'] }}
                                                    </h6>
                                                    <p class="text-sm text-slate-500 dark:text-slate-300">
                                                        {{ $employee['position'] }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['gross_salary'], 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['insurance_amount'], 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['other_amount'], 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['employee_insurance'], 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['employer_insurance'], 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['total_insurance'], 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['employee_medical'], 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['total_medical'], 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['employee_deductions'], 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['penalties_hours'], 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['penalties_amount'], 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['net_after_penalty'], 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['extra_payments'], 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['net_after_deductions'], 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm">{{ number_format($employee['overtime_hours'] ?? 0, 2) }}</span>
                                                    {{-- @if ($employee['is_automatic_overtime'])
                                                        @if (!empty($employee['potential_overtime_data']))
                                                            <span class="text-xs text-warning-500" title="Will be auto-created when payroll is submitted">
                                                                <iconify-icon icon="heroicons-outline:clock" class="text-xs"></iconify-icon>
                                                                {{ count($employee['potential_overtime_data']) }} from attendance
                                                            </span>
                                                        @endif
                                                        @php
                                                            // Calculate existing approved overtime hours for this employee
                                                            $existingOvertimeHours = \App\Models\Attendance\Overtime::where('employee_id', $employee['id'])
                                                                ->where('status', \App\Models\Attendance\Overtime::STATUS_APPROVED)
                                                                ->whereBetween('date', [$this->startDate, $this->endDate])
                                                                ->whereNull('payroll_id')
                                                                ->sum('hours');
                                                        @endphp
                                                        @if ($existingOvertimeHours > 0)
                                                            <span class="text-xs text-success-500" title="Existing approved overtime records">
                                                                <iconify-icon icon="heroicons-outline:check-circle" class="text-xs"></iconify-icon>
                                                                {{ number_format($existingOvertimeHours, 2) }}h approved
                                                            </span>
                                                        @endif
                                                    @elseif($employee['overtime_hours'] > 0)
                                                        <span class="text-xs text-success-500" title="Manually approved overtime records">
                                                            <iconify-icon icon="heroicons-outline:check-circle" class="text-xs"></iconify-icon>
                                                            Manual overtime
                                                        </span>
                                                    @endif --}}
                                                </div>
                                            </td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['overtime_amount'] ?? 0, 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['employee_base_benefits'], 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                {{ number_format($employee['other_base_benefits'], 2) }}</td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm">{{ number_format($employee['adj_amount'] ?? 0, 2) }}</span>
                                                    @if (!empty($employee['adj_desc']))
                                                        <span
                                                            class="text-xs text-slate-500">{{ Str::limit($employee['adj_desc'], 20) }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td
                                                class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    wire:click="openAdjustmentModal({{ $departmentId }}, {{ $employeeIndex }})">
                                                    <span class="flex items-center">
                                                        <iconify-icon icon="heroicons-outline:pencil"
                                                            class="text-base ltr:mr-1 rtl:ml-1"></iconify-icon>
                                                        Adjust
                                                    </span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="button" class="btn btn-primary" wire:click="submitPayroll">
                        Process Payroll
                    </button>
                </div>
            @else
                <div class="text-center py-10">
                    <div class="text-5xl text-slate-400 mb-4">
                        <iconify-icon icon="heroicons-outline:clipboard-list"></iconify-icon>
                    </div>
                    <h5 class="text-xl font-medium text-slate-400">No employees selected yet</h5>
                    <p class="text-sm text-slate-400 mt-2">Add a department to start creating your payroll</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Department Selection Modal -->
    <div>
        @if ($showDepartmentModal)
            <x-modal wire:model="showDepartmentModal">
                <x-slot name="title">Select Departments</x-slot>

                <div class="space-y-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="form-label">Departments*</label>
                            <div
                                class="border rounded-md p-4 mt-1 max-h-60 overflow-y-auto grid grid-cols-1 md:grid-cols-4 gap-2">
                                @foreach ($departments as $department)
                                    <div
                                        class="flex items-center gap-2 mb-2 p-2 rounded-md {{ is_array($this->selectedDepartments) && in_array($department->id, $this->selectedDepartments) ? 'bg-primary-50 dark:bg-primary-900/20' : '' }}">
                                        <div class="checkbox-area ml-2">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" id="department-{{ $department->id }}"
                                                    value="{{ $department->id }}" class="hidden" name="checkbox"
                                                    wire:model.live="selectedDepartments">
                                                <span
                                                    class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                    <img src="{{ asset('images/icon/ck-white.svg') }}" alt=""
                                                        class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                                <span
                                                    class="text-slate-500 dark:text-slate-400 text-sm leading-6">{{ $department->name }}</span>
                                            </label>
                                        </div>
                                        @if (is_array($this->selectedDepartments) && in_array($department->id, $this->selectedDepartments))
                                            @php
                                                $count = \App\Models\Personel\Employee::whereHas('position', function (
                                                    $query,
                                                ) use ($department) {
                                                    $query->where('department_id', $department->id);
                                                })->count();
                                            @endphp
                                            <span
                                                class="text-xs bg-primary-500 text-white rounded-full px-2 py-1">{{ $count }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @error('departmentSelection')
                                <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="checkbox-area ml-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="selectAllEmployees" wire:model.live="selectAllEmployees"
                                    class="hidden" name="checkbox">
                                <span
                                    class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                    <img src="{{ asset('images/icon/ck-white.svg') }}" alt=""
                                        class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                <span class="text-slate-500 dark:text-slate-400 text-sm leading-6">Select all employees
                                    in the selected departments</span>
                            </label>
                        </div>

                        @if (!$selectAllEmployees && is_array($this->selectedDepartments) && !empty($this->selectedDepartments))
                            <div class="border rounded-md p-4 mt-3">
                                <h6 class="font-medium mb-3">Select Employees</h6>
                                <div class="space-y-2 max-h-60 overflow-y-auto">
                                    @php
                                        $departmentEmployees = \App\Models\Personel\Employee::whereHas(
                                            'position',
                                            function ($query) {
                                                $query->whereIn('department_id', $this->selectedDepartments);
                                            },
                                        )->get();
                                    @endphp

                                    @forelse($departmentEmployees as $employee)
                                        <div class="flex items-center">
                                            <div class="checkbox-area ml-2">
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" id="employee-{{ $employee->id }}"
                                                        value="{{ $employee->id }}" class="hidden" name="checkbox"
                                                        wire:model.live="selectedEmployees">
                                                    <span
                                                        class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                        <img src="{{ asset('images/icon/ck-white.svg') }}"
                                                            alt=""
                                                            class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                                    <span
                                                        class="text-slate-500 dark:text-slate-400 text-sm leading-6">{{ $employee->name }}
                                                        ({{ $employee->position?->name ?? 'No Position' }})
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-sm text-slate-500">No employees found</div>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Summary of selection -->
                @if (is_array($this->selectedDepartments) && !empty($this->selectedDepartments))
                    <div class="mt-6 p-3 bg-slate-50 dark:bg-slate-700 rounded-md">
                        <div class="font-medium text-sm mb-1">Summary:</div>
                        <div class="flex justify-between">
                            <div class="text-sm">Selected departments: <span
                                    class="font-semibold">{{ is_array($this->selectedDepartments) ? count($this->selectedDepartments) : 0 }}</span>
                            </div>
                            <div class="text-sm">Selected employees: <span
                                    class="font-semibold">{{ is_array($this->selectedEmployees) ? count($this->selectedEmployees) : 0 }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <x-slot name="footer">
                    <div class="flex justify-end space-x-3">
                        <x-secondary-button wire:click="selectAllDepartments">Select All
                            Departments</x-secondary-button>
                        <x-secondary-button wire:click="closeDepartmentModal">Cancel</x-secondary-button>
                        <x-primary-button wire:click="submitDepartmentSelection" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submitDepartmentSelection">Add Employees</span>
                            <span wire:loading wire:target="submitDepartmentSelection">Processing...</span>
                        </x-primary-button>
                    </div>
                </x-slot>
            </x-modal>
        @endif
    </div>

    <!-- Adjustment Modal -->
    @if ($showAdjustmentModal)
        <x-modal wire:model="showAdjustmentModal">
            <x-slot name="title">Employee Adjustment</x-slot>

            <div class="space-y-4">
                <div>
                    <label class="form-label">Adjustment Amount</label>
                    <input type="number" step="0.01" wire:model="adjustmentAmount" class="form-control"
                        placeholder="Enter adjustment amount">
                    <p class="text-xs text-slate-500 mt-1">Use positive values for additions, negative for deductions
                    </p>
                </div>

                <div>
                    <label class="form-label">Adjustment Description</label>
                    <textarea wire:model="adjustmentDescription" rows="3" class="form-control"
                        placeholder="Enter reason for adjustment"></textarea>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end space-x-3">
                    <x-secondary-button wire:click="closeAdjustmentModal">Cancel</x-secondary-button>
                    <x-primary-button wire:click="saveAdjustment" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveAdjustment">Save Adjustment</span>
                        <span wire:loading wire:target="saveAdjustment">Saving...</span>
                    </x-primary-button>
                </div>
            </x-slot>
        </x-modal>
    @endif
</div>
