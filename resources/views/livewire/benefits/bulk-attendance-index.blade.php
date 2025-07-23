<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Bulk Attendance Management
            </h4>
        </div>
    </div>

    <div class="card">
        <header class="card-header noborder">
            <div class="flex flex-wrap justify-between items-center w-full">
                <div class="flex items-center min-w-[310px]">
                    <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg mr-2"
                        icon="line-md:loading-twotone-loop"></iconify-icon>
                    <input type="text" class="form-control !pl-9 mr-1 basis-1/4 w-full"
                        placeholder="Search by name" wire:model.live.debounce.400ms="search">
                </div>

                <div class="flex items-center space-x-3">
                    <select wire:model.live="selectedDepartment" class="form-control">
                        <option value="">All Departments</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department['id'] }}">{{ $department['name'] }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="perPage" class="form-control">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </header>

        <div class="card-body">
            <div class="overflow-x-auto">
                @if (count($employees) > 0)
                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700"
                        style="min-width: 1800px;">
                        <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col"
                                    class="table-th text-nowrap sticky left-0 bg-slate-200 dark:bg-slate-700 z-10"
                                    style="width: 220px;" title="Employee">Employee</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 160px;"
                                    title="Department">Department</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 150px;"
                                    title="Working Days">Working Days</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 180px;"
                                    title="Calculation Type">Calculation Type</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 120px;"
                                    title="Daily Hours">Daily Hours</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 150px;"
                                    title="Start Time">Start Time</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 150px;"
                                    title="End Time">End Time</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 120px;"
                                    title="Overtime Rate">Overtime Rate</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 150px;"
                                    title="Bus">Bus</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 120px;"
                                    title="Settings">Settings</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 80px;" title="Actions">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            @foreach ($employees as $employee)
                                @php
                                    // Initialize employee data when first rendered
                                    $this->initializeEmployeeData($employee->id);
                                    $employeeData = $this->employeesData[$employee->id] ?? [];
                                @endphp
                                <tr class="bg-slate-50">
                                    <td class="table-td sticky left-0 z-10 bg-slate-50 bg-white"
                                        style="width: 220px;">
                                        <div class="flex items-center">
                                            @if ($employee->full_image_url)
                                                <div class="flex-none">
                                                    <div class="h-8 w-8 rounded-full overflow-hidden mr-2">
                                                        <img src="{{ $employee->full_image_url }}"
                                                            alt="{{ $employee->name }}"
                                                            class="h-full w-full object-cover">
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex-none">
                                                    <div
                                                        class="h-8 w-8 rounded-full bg-slate-600 flex items-center justify-center mr-2">
                                                        <span
                                                            class="text-white text-xs font-medium">{{ strtoupper(substr($employee->name, 0, 2)) }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                <span
                                                    class="text-sm text-slate-600 dark:text-slate-300 capitalize block truncate"
                                                    title="{{ $employee->name }}">{{ $employee->name }}</span>
                                                <span class="text-xs text-slate-500 block truncate"
                                                    title="{{ $employee->employee_id }}">{{ $employee->employee_id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-td">
                                        <div style="width: 160px;">
                                            @if ($employee->position && $employee->position->department)
                                                <span
                                                    class="text-sm text-slate-600 dark:text-slate-300 capitalize block truncate"
                                                    title="{{ $employee->position->department->name }}">{{ $employee->position->department->name }}</span>
                                                <span class="text-xs text-slate-500 block truncate"
                                                    title="{{ $employee->position->name ?? 'N/A' }}">{{ $employee->position->name ?? 'N/A' }}</span>
                                            @else
                                                <span class="badge badge-danger text-nowrap">Not assigned</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="table-td">
                                        <div style="width: 150px;">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($AllworkingDays as $day)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                                                        {{ in_array($day, $employeeData['workingDays'] ?? []) ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-500' }}">
                                                        {{ strtoupper(substr($day, 0, 3)) }}@if(in_array($day, $employeeData['workingDays'] ?? [])) ✓ @endif  @if(!$loop->last), &nbsp; @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-td">
                                        <select wire:model.live="employeesData.{{ $employee->id }}.attendanceCalculation"
                                            style="min-width: 180px;"
                                            class="form-control text-xs py-1 px-2 w-full">
                                            @foreach ($attendanceCalculations as $type)
                                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                            @endforeach
                                        </select>
                                        @if (!empty($employeeData['errors']['attendanceCalculation']))
                                            <div class="text-red-500 text-xs mt-1 truncate"
                                                title="{{ implode(', ', $employeeData['errors']['attendanceCalculation']) }}">
                                                {{ implode(', ', $employeeData['errors']['attendanceCalculation']) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        <input type="number" style="min-width: 90px;"
                                            wire:model.live="employeesData.{{ $employee->id }}.dailyWorkingHours"
                                            class="form-control text-xs py-1 px-2 h-8 w-full"
                                            placeholder="Hours" min="1" max="24">
                                        @if (!empty($employeeData['errors']['dailyWorkingHours']))
                                            <div class="text-red-500 text-xs mt-1 truncate"
                                                title="{{ implode(', ', $employeeData['errors']['dailyWorkingHours']) }}">
                                                {{ implode(', ', $employeeData['errors']['dailyWorkingHours']) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        <div class="space-y-1">
                                            <input type="time" style="width: 150px;"
                                                wire:model.live="employeesData.{{ $employee->id }}.workingDayStartMin"
                                                class="form-control text-xs py-1 px-2 h-8 w-full"
                                                @if (in_array($employeeData['attendanceCalculation'] ?? '', ['bus', 'flexible'])) disabled @endif>
                                            <input type="time" style="width: 150px;"
                                                wire:model.live="employeesData.{{ $employee->id }}.workingDayStartMax"
                                                class="form-control text-xs py-1 px-2 h-8 w-full"
                                                @if (in_array($employeeData['attendanceCalculation'] ?? '', ['bus', 'flexible'])) disabled @endif>
                                        </div>
                                    </td>
                                    <td class="table-td">
                                        <div class="space-y-1">
                                            <input type="time" style="width: 150px;"
                                                wire:model.live="employeesData.{{ $employee->id }}.workingDayEndMin"
                                                class="form-control text-xs py-1 px-2 h-8 w-full"
                                                @if (in_array($employeeData['attendanceCalculation'] ?? '', ['in-only', 'flexible'])) disabled @endif>
                                            <input type="time" style="width: 150px;"
                                                wire:model.live="employeesData.{{ $employee->id }}.workingDayEndMax"
                                                class="form-control text-xs py-1 px-2 h-8 w-full"
                                                @if (in_array($employeeData['attendanceCalculation'] ?? '', ['in-only', 'flexible'])) disabled @endif>
                                        </div>
                                    </td>
                                    <td class="table-td">
                                        <input type="number" step="0.01" style="min-width: 90px;"
                                            wire:model.live="employeesData.{{ $employee->id }}.overtimeRate"
                                            class="form-control text-xs py-1 px-2 h-8 w-full"
                                            placeholder="Rate" min="1">
                                        @if (!empty($employeeData['errors']['overtimeRate']))
                                            <div class="text-red-500 text-xs mt-1 truncate"
                                                title="{{ implode(', ', $employeeData['errors']['overtimeRate']) }}">
                                                {{ implode(', ', $employeeData['errors']['overtimeRate']) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        <select wire:model.live="employeesData.{{ $employee->id }}.busId"
                                            style="width: 150px;"
                                            class="form-control text-xs w-full"
                                            @if (($employeeData['attendanceCalculation'] ?? '') !== 'bus') disabled @endif>
                                            <option value="">Select Bus</option>
                                            @foreach ($buses as $bus)
                                                <option value="{{ $bus->id }}">{{ $bus->name }}</option>
                                            @endforeach
                                        </select>
                                        @if (!empty($employeeData['errors']['busId']))
                                            <div class="text-red-500 text-xs mt-1 truncate"
                                                title="{{ implode(', ', $employeeData['errors']['busId']) }}">
                                                {{ implode(', ', $employeeData['errors']['busId']) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        <button type="button"
                                            style="width: 120px;"
                                            class="btn btn-sm btn-outline-primary text-xs py-1 px-2 text-nowrap"
                                            wire:click="openAttendanceModal({{ $employee->id }})">
                                            <iconify-icon icon="heroicons-outline:cog"
                                                class="text-xs"></iconify-icon>
                                            Settings
                                        </button>
                                    </td>
                                    <td class="table-td" style="width: 80px;">
                                        <button type="button" wire:click="saveEmployeeAttendance({{ $employee->id }})"
                                            class="action-btn btn-success text-xs py-1 px-2 h-8 w-8 flex items-center justify-center"
                                            @if (!empty($employeeData['isLoading'])) disabled @endif>
                                            @if (!empty($employeeData['isLoading']))
                                                <iconify-icon icon="line-md:loading-twotone-loop"
                                                    class="text-xs"></iconify-icon>
                                            @else
                                                <iconify-icon icon="heroicons-outline:check"
                                                    class="text-xs"></iconify-icon>
                                            @endif
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <div style="position: sticky; bottom:0;width:100%; z-index:10;"
                            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            {{ $employees->links('vendor.livewire.simple-bootstrap') }}
                        </div>
                    </table>
                @else
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No employees found</h2>
                                <p class="card-text">Try adjusting your search criteria or add some employees first.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Attendance Settings Modal -->
    @if ($showAttendanceModal && $modalEmployeeId && isset($employeesData[$modalEmployeeId]))
        @php
            $modalEmployee = $employeesData[$modalEmployeeId]['employee'];
            $modalEmployeeData = $employeesData[$modalEmployeeId];
        @endphp
        <x-modal wire:model="showAttendanceModal" maxWidth="5xl">
            <x-slot name="title">
                Attendance Settings - {{ $modalEmployee->name }}
            </x-slot>
            
            <!-- Modal body -->
            <div class="p-4">
                @if ($errors->any())
                    <div class="alert alert-warning mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            <div>
                                <p class="font-medium">Please fix the following errors:</p>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Working Days Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-slate-800 mb-4">Working Days</h3>
                    
                    <!-- Quick Selection Buttons -->
                    <div class="flex flex-wrap gap-3 mb-6">
                        <button type="button"
                            wire:click="setFixedCalculation({{ $modalEmployeeId }})"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Fixed Calculation
                        </button>
                        <button type="button"
                            wire:click="$set('employeesData.{{ $modalEmployeeId }}.workingDays', ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'])"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Sun - Thu
                        </button>
                        <button type="button"
                            wire:click="$set('employeesData.{{ $modalEmployeeId }}.workingDays', ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'])"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            All days
                        </button>
                    </div>
                    
                    <!-- Individual Day Selection -->
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($AllworkingDays as $day)
                            <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50">
                                <input type="checkbox" 
                                    wire:model="employeesData.{{ $modalEmployeeId }}.workingDays" 
                                    value="{{ $day }}"
                                    class="form-checkbox h-4 w-4 text-primary-600 rounded">
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst($day) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Advanced Settings Section -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-slate-800 mb-4">Advanced Settings</h3>
                    
                    <!-- Overtime Max Time -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Overtime Max Time</label>
                        <input type="time"
                            wire:model.live="employeesData.{{ $modalEmployeeId }}.overtimeMaxTime"
                            class="form-control w-48"
                            placeholder="Leave empty for no limit">
                        <p class="text-xs text-gray-500 mt-1">Leave empty for no limit</p>
                    </div>
                    
                    <!-- Checkbox Settings -->
                    <div class="space-y-4">
                        <label class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50">
                            <input type="checkbox" 
                                wire:model.live="employeesData.{{ $modalEmployeeId }}.isAutomaticOvertime" 
                                class="form-checkbox h-4 w-4 text-primary-600 rounded mt-0.5">
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-700">Enable Automatic Approved Overtime from Attendance Sheet</span>
                                <p class="text-xs text-gray-500 mt-1">Automatically approve overtime based on attendance records</p>
                            </div>
                        </label>
                        
                        <label class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50">
                            <input type="checkbox" 
                                wire:model.live="employeesData.{{ $modalEmployeeId }}.isGenerateOvertime" 
                                class="form-checkbox h-4 w-4 text-primary-600 rounded mt-0.5">
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-700">Generate Unapproved Overtime Automatically after 1 hour</span>
                                <p class="text-xs text-gray-500 mt-1">Create overtime records for hours worked beyond regular schedule</p>
                            </div>
                        </label>
                        
                        <label class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50">
                            <input type="checkbox" 
                                wire:model.live="employeesData.{{ $modalEmployeeId }}.isRequireAttendanceApproval" 
                                class="form-checkbox h-4 w-4 text-primary-600 rounded mt-0.5">
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-700">Require Attendance Approval</span>
                                <p class="text-xs text-gray-500 mt-1">Require manager approval for attendance records</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Modal footer -->
            <x-slot name="footer">
                <x-secondary-button wire:click="closeAttendanceModal">
                    Close
                </x-secondary-button>
                <x-primary-button wire:click="saveEmployeeAttendance({{ $modalEmployeeId }})" 
                    loadingFunction="saveEmployeeAttendance">
                    Save Settings
                </x-primary-button>
            </x-slot>
        </x-modal>
    @endif
</div> 