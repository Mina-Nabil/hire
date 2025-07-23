<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Bulk Vacation Management
            </h4>
        </div>
    </div>

    <div class="card">
        <header class="card-header noborder">
            <div class="flex flex-wrap justify-between items-center w-full">
                <div class="flex items-center min-w-[310px]">
                    <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg mr-2"
                        icon="line-md:loading-twotone-loop"></iconify-icon>
                    <input type="text" class="form-control !pl-9 mr-1 basis-1/4 w-full" placeholder="Search by name"
                        wire:model.live.debounce.400ms="search">
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
                        style="min-width: 2000px;">
                        <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col"
                                    class="table-th text-nowrap sticky left-0 bg-slate-200 dark:bg-slate-700 z-10"
                                    style="width: 220px;" title="Employee">Employee</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 160px;"
                                    title="Department">Department</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 200px;"
                                    title="Vacation Package">Vacation Package</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 150px;"
                                    title="Start Date">Start Date</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 150px;" title="End Date">
                                    End Date</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 120px;"
                                    title="Benefits Count">Benefits</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 120px;" title="Settings">
                                    Settings</th>
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
                                    <td class="table-td sticky left-0 z-10 bg-slate-50 bg-white" style="width: 220px;">
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
                                        <select wire:model.live="employeesData.{{ $employee->id }}.selectedPackageId"
                                            style="min-width: 180px;"
                                            wire:change="loadVacationPackage({{ $employee->id }})"
                                            class="form-control text-xs py-1 px-2 w-full">
                                            <option value="">Select Package</option>
                                            @foreach ($packages as $package)
                                                <option value="{{ $package->id }}">{{ $package->name }}</option>
                                            @endforeach
                                        </select>
                                        @if (!empty($employeeData['errors']['selectedPackageId']))
                                            <div class="text-red-500 text-xs mt-1 truncate"
                                                title="{{ implode(', ', $employeeData['errors']['selectedPackageId']) }}">
                                                {{ implode(', ', $employeeData['errors']['selectedPackageId']) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        <input type="date" style="width: 150px;"
                                            wire:model.live="employeesData.{{ $employee->id }}.packageStartDate"
                                            class="form-control text-xs py-1 px-2 h-8 w-full">
                                        @if (!empty($employeeData['errors']['packageStartDate']))
                                            <div class="text-red-500 text-xs mt-1 truncate"
                                                title="{{ implode(', ', $employeeData['errors']['packageStartDate']) }}">
                                                {{ implode(', ', $employeeData['errors']['packageStartDate']) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        <input type="date" style="width: 150px;"
                                            wire:model.live="employeesData.{{ $employee->id }}.packageEndDate"
                                            class="form-control text-xs py-1 px-2 h-8 w-full">
                                        @if (!empty($employeeData['errors']['packageEndDate']))
                                            <div class="text-red-500 text-xs mt-1 truncate"
                                                title="{{ implode(', ', $employeeData['errors']['packageEndDate']) }}">
                                                {{ implode(', ', $employeeData['errors']['packageEndDate']) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        @if (!empty($employeeData['vacationBenefits']))
                                            <span class="text-sm text-slate-600">
                                                {{ count($employeeData['vacationBenefits']) }} benefits
                                            </span>
                                        @else
                                            <span class="text-slate-500 text-xs text-nowrap">No benefits</span>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        <button type="button" style="width: 120px;"
                                            class="btn btn-sm btn-outline-primary text-xs py-1 px-2 text-nowrap"
                                            wire:click="openVacationModal({{ $employee->id }})"
                                            @if (empty($employeeData['vacationBenefits'])) disabled @endif>
                                            <iconify-icon icon="heroicons-outline:cog" class="text-xs"></iconify-icon>
                                            Settings
                                        </button>
                                    </td>
                                    <td class="table-td" style="width: 80px;">
                                        <button type="button" wire:click="saveEmployeeVacation({{ $employee->id }})"
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

    <!-- Vacation Settings Modal -->
    @if ($showVacationModal && $modalEmployeeId && isset($employeesData[$modalEmployeeId]))
        @php
            $modalEmployee = $employeesData[$modalEmployeeId]['employee'];
            $modalEmployeeData = $employeesData[$modalEmployeeId];
        @endphp
        <x-modal wire:model="showVacationModal" maxWidth="7xl">
            <x-slot name="title">
                Vacation Settings - {{ $modalEmployee->name }}
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

                @if (!empty($modalEmployeeData['errors']['general']))
                    <div class="alert alert-warning mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            <div>
                                <p class="font-medium">Error:</p>
                                <p>{{ $modalEmployeeData['errors']['general'] }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Package Configuration -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-slate-800 mb-4">Package Configuration</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Delete Old
                                Configuration</label>
                            <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50">
                                <input type="checkbox"
                                    wire:model.live="employeesData.{{ $modalEmployeeId }}.deleteOldConf"
                                    class="form-checkbox h-4 w-4 text-primary-600 rounded">
                                <span class="text-sm text-gray-700">Delete old configuration and create new one</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1">If unchecked, the old configuration will be ended and
                                a new one will be created</p>
                        </div>
                    </div>
                </div>

                <!-- Vacation Benefits -->
                @if (!empty($modalEmployeeData['vacationBenefits']))
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-slate-800 mb-4">Vacation Benefits</h3>

                        <div class="space-y-4">
                            @foreach ($modalEmployeeData['vacationBenefits'] as $index => $benefit)
                                <div class="border rounded-lg p-4">
                                    <div class="grid grid-cols-4 gap-4">
                                        <div class="col-span-3">
                                            <div class="flex justify-between items-center mb-3">
                                                <h4 class="font-medium text-slate-800">{{ $benefit['name'] }}</h4>
                                                <div class="flex items-center">
                                                    <label class="flex items-center text-sm">
                                                        <input type="checkbox"
                                                            wire:click="toggleAutomaticAddToBalance({{ $modalEmployeeId }}, {{ $index }})"
                                                            @if ($benefit['automatic_add_to_balance']) checked @endif
                                                            class="form-checkbox text-blue-600">
                                                        <span class="ml-2 text-gray-600">Auto-add balance for extra
                                                            attendance</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                @if (!$benefit['is_disabled'])
                                                    <div>
                                                        <label
                                                            class="block text-sm font-medium text-gray-700 mb-1">Increment
                                                            Rate (hours)</label>
                                                        <input type="number"
                                                            wire:model.live="employeesData.{{ $modalEmployeeId }}.vacationBenefits.{{ $index }}.inc_rate"
                                                            class="form-control text-sm"
                                                            min="{{ $benefit['inc_rate_min'] }}"
                                                            max="{{ $benefit['inc_rate_max'] }}">
                                                        <span class="text-xs text-gray-500">Range:
                                                            {{ $benefit['inc_rate_min'] }} -
                                                            {{ $benefit['inc_rate_max'] }}</span>
                                                    </div>
                                                @endif

                                                @if (!$benefit['is_disabled'])
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Max
                                                            Balance (hours)</label>
                                                        <input type="number"
                                                            wire:model.live="employeesData.{{ $modalEmployeeId }}.vacationBenefits.{{ $index }}.max_balance"
                                                            class="form-control text-sm"
                                                            min="{{ $benefit['max_balance_min'] }}"
                                                            max="{{ $benefit['max_balance_max'] }}">
                                                        <span class="text-xs text-gray-500">Range:
                                                            {{ $benefit['max_balance_min'] }} -
                                                            {{ $benefit['max_balance_max'] }}</span>
                                                    </div>
                                                @endif

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Current
                                                        Balance (hours)</label>
                                                    <input type="number"
                                                        wire:model.live="employeesData.{{ $modalEmployeeId }}.vacationBenefits.{{ $index }}.current_balance"
                                                        class="form-control text-sm">
                                                </div>

                                                @if (!$benefit['is_disabled'])
                                                    <div>
                                                        <label
                                                            class="block text-sm font-medium text-gray-700 mb-1">Hour
                                                            Price</label>
                                                        <input type="number"
                                                            wire:model.live="employeesData.{{ $modalEmployeeId }}.vacationBenefits.{{ $index }}.hour_price"
                                                            class="form-control text-sm"
                                                            min="{{ $benefit['hour_price_min'] }}"
                                                            max="{{ $benefit['hour_price_max'] }}">
                                                        <span class="text-xs text-gray-500">Range:
                                                            {{ $benefit['hour_price_min'] }} -
                                                            {{ $benefit['hour_price_max'] }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-span-1">
                                            <div class="space-y-2 text-xs text-gray-500">
                                                <div>
                                                    <span class="font-medium">Type:</span>
                                                    {{ ucfirst($benefit['type']) }}
                                                </div>
                                                @if (!$benefit['is_disabled'])
                                                    <div>
                                                        <span class="font-medium">Increment rate:</span>
                                                        {{ $benefit['inc_rate_min'] }} -
                                                        {{ $benefit['inc_rate_max'] }}
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">Max balance:</span>
                                                        {{ $benefit['max_balance_min'] }} -
                                                        {{ $benefit['max_balance_max'] }}
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">Hour price:</span>
                                                        {{ $benefit['hour_price_min'] }} -
                                                        {{ $benefit['hour_price_max'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                    <p class="text-sm text-blue-700">
                                        <strong>Auto-add for extra attendance:</strong> Only one vacation benefit can be
                                        set to automatically add days for extra attendance. When enabled, this benefit
                                        will receive additional days when the employee has extra attendance days beyond
                                        their required working days.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-slate-500">No vacation package selected or no benefits available</p>
                    </div>
                @endif
            </div>

            <!-- Modal footer -->
            <x-slot name="footer">
                <x-secondary-button wire:click="closeVacationModal">
                    Close
                </x-secondary-button>
                <x-primary-button wire:click="saveEmployeeVacation({{ $modalEmployeeId }})"
                    loadingFunction="saveEmployeeVacation">
                    Save Vacation Package
                </x-primary-button>
            </x-slot>
        </x-modal>
    @endif

</div>
