<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Bulk Benefits Management
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
                        style="min-width: 1600px;">
                        <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col"
                                    class="table-th text-nowrap sticky left-0 bg-slate-200 dark:bg-slate-700 z-10"
                                    style="width: 220px;" title="Employee">Employee</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 160px;"
                                    title="Department">Department</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 180px;"
                                    title="Salary Grade">Salary Grade</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 160px;"
                                    title="Gross Salary">Gross (Taxable?)</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 150px;" title="Insurance">
                                    Insurance</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 150px;"
                                    title="Start Date">Start Date</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 150px;" title="End Date">
                                    End Date</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 180px;" title="Manager">
                                    Manager</th>
                                <th scope="col" class="table-th text-nowrap" style="width: 120px;" title="Benefits">
                                    Benefits</th>
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
                                    <td class="table-td sticky left-0 z-10 bg-slate-50  bg-white "
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
                                        <select wire:model.live="employeesData.{{ $employee->id }}.selectedPackageId"
                                            style="min-width: 120px;"
                                            wire:change="loadPackageDetails({{ $employee->id }})"
                                            class="form-control text-xs py-1 px-2 w-full" >
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
                                        <div class="flex items-start gap-2" style="min-width: 160px;">
                                            <div class="flex-1">
                                                <input type="number"
                                                    wire:model.live="employeesData.{{ $employee->id }}.grossSalary"
                                                    class="form-control text-xs py-1 px-2 h-8 w-full"
                                                    placeholder="Gross Salary"
                                                    @if (!empty($employeeData['selectedPackage'])) min="{{ $employeeData['selectedPackage']->gross_min }}"
                                                           max="{{ $employeeData['selectedPackage']->gross_max }}" @endif>
                                                
                                                @if (!empty($employeeData['errors']['grossSalary']))
                                                    <div class="text-red-500 text-xs mt-1 truncate"
                                                        title="{{ implode(', ', $employeeData['errors']['grossSalary']) }}">
                                                        {{ implode(', ', $employeeData['errors']['grossSalary']) }}
                                                    </div>
                                                @endif
                                                @if (!empty($employeeData['selectedPackage']))
                                                    <span
                                                        class="text-xs text-slate-500 text-nowrap">{{ $employeeData['selectedPackage']->gross_min }}
                                                        - {{ $employeeData['selectedPackage']->gross_max }}</span>
                                                @endif
                                            </div>
                                            
                                            <div class="flex-shrink-0 flex items-center pt-1">
                                                <input type="checkbox"
                                                    wire:model.live="employeesData.{{ $employee->id }}.isTaxable"
                                                    class="form-checkbox rounded text-xs w-3 h-3"
                                                    id="taxable{{ $employee->id }}"
                                                    title="Taxable">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-td">
                                        <input type="number"
                                         style="width: 150px;"
                                            wire:model.live="employeesData.{{ $employee->id }}.insuranceAmount"
                                            class="form-control text-xs py-1 px-2 h-8 w-full" placeholder="Insurance">
                                        @if (!empty($employeeData['errors']['insuranceAmount']))
                                            <div class="text-red-500 text-xs mt-1 truncate"
                                             style="width: 150px;"
                                                title="{{ implode(', ', $employeeData['errors']['insuranceAmount']) }}">
                                                {{ implode(', ', $employeeData['errors']['insuranceAmount']) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="table-td" >
                                        <input type="date"
                                        style="width: 150px;"
                                            wire:model.live="employeesData.{{ $employee->id }}.packageStartDate"
                                            class="form-control text-xs py-1 px-2 h-8 w-full">
                                        @if (!empty($employeeData['errors']['packageStartDate']))
                                            <div class="text-red-500 text-xs mt-1 truncate"
                                            style="width: 150px;"
                                                title="{{ implode(', ', $employeeData['errors']['packageStartDate']) }}">
                                                {{ implode(', ', $employeeData['errors']['packageStartDate']) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="table-td" >
                                        <input type="date"
                                        style="width: 150px;"
                                            wire:model.live="employeesData.{{ $employee->id }}.packageEndDate"
                                            class="form-control text-xs py-1 px-2 h-8 w-full">
                                        @if (!empty($employeeData['errors']['packageEndDate']))
                                            <div class="text-red-500 text-xs mt-1 truncate"
                                            style="width: 150px;"
                                                title="{{ implode(', ', $employeeData['errors']['packageEndDate']) }}">
                                                {{ implode(', ', $employeeData['errors']['packageEndDate']) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="table-td" >
                                        <select wire:model.live="employeesData.{{ $employee->id }}.managerId"
                                        style="width: 220px;"
                                            class="form-control text-xs w-full">
                                            <option value="">Select Manager</option>
                                            @if (!empty($employeeData['managersList']))
                                                @foreach ($employeeData['managersList'] as $manager)
                                                    <option value="{{ $manager['id'] }}">{{ $manager['name'] }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </td>
                                    <td class="table-td" >
                                                                                 @if (!empty($employeeData['packageDetails']))
                                             <button type="button"
                                                 style="width: 120px;"
                                                 class="btn btn-sm btn-outline-primary text-xs py-1 px-2 text-nowrap"
                                                 wire:click="openBenefitsModal({{ $employee->id }})">
                                                 <iconify-icon icon="heroicons-outline:eye"
                                                     class="text-xs"></iconify-icon>
                                                 ({{ count($employeeData['packageDetails']) }})
                                             </button>
                                         @else
                                             <span class="text-slate-500 text-xs text-nowrap">No benefits</span>
                                         @endif
                                    </td>
                                    <td class="table-td" style="width: 80px;">
                                        <button type="button" wire:click="saveEmployeeBenefits({{ $employee->id }})"
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

    <!-- Benefits Modal -->
    @if ($showBenefitsModal && $modalEmployeeId && isset($employeesData[$modalEmployeeId]))
        @php
            $modalEmployee = $employeesData[$modalEmployeeId]['employee'];
            $modalEmployeeData = $employeesData[$modalEmployeeId];
        @endphp
        <x-modal wire:model="showBenefitsModal" maxWidth="7xl">
            <x-slot name="title">
                Benefits Details - {{ $modalEmployee->name }}
            </x-slot>
            
            <!-- Modal body -->
            <div class="p-6 space-y-4">
                @if (!empty($modalEmployeeData['packageDetails']))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($modalEmployeeData['packageDetails'] as $benefitName => $detail)
                            <div class="border rounded-lg p-4">
                                <div class="mb-3">
                                    <h6 class="text-base font-medium text-slate-800 mb-2">
                                        {{ $detail['name'] }}
                                    </h6>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                            {{ ucfirst($detail['type']) }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($detail['receiver']) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="form-label text-sm font-medium text-slate-700">Amount</label>
                                        <input type="number"
                                            wire:model.live="employeesData.{{ $modalEmployeeId }}.packageDetails.{{ $benefitName }}.amount"
                                            class="form-control"
                                            min="{{ $detail['amount_min'] }}"
                                            max="{{ $detail['amount_max'] }}"
                                            placeholder="Amount">
                                        <span class="text-xs text-slate-500 mt-1">
                                            Range: {{ $detail['amount_min'] }} - {{ $detail['amount_max'] }}
                                        </span>
                                    </div>
                                    <div>
                                        <label class="form-label text-sm font-medium text-slate-700">Receiver</label>
                                        <select wire:model.live="employeesData.{{ $modalEmployeeId }}.packageDetails.{{ $benefitName }}.receiver"
                                            class="form-control">
                                            <option value="employee">Employee</option>
                                            <option value="medical">Medical</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                            wire:model.live="employeesData.{{ $modalEmployeeId }}.packageDetails.{{ $benefitName }}.is_hidden"
                                            class="form-checkbox rounded"
                                            id="hidden{{ $modalEmployeeId }}{{ $benefitName }}">
                                        <label class="ml-2 text-sm text-slate-700"
                                            for="hidden{{ $modalEmployeeId }}{{ $benefitName }}">
                                            Hidden from payslips
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-slate-500">No benefits package details available</p>
                    </div>
                @endif
            </div>
            
            <!-- Modal footer -->
            <x-slot name="footer">
                <x-secondary-button wire:click="closeBenefitsModal">
                    Close
                </x-secondary-button>
                <x-primary-button wire:click="saveEmployeeBenefits({{ $modalEmployeeId }})" 
                    loadingFunction="saveEmployeeBenefits">
                    Save Benefits
                </x-primary-button>
            </x-slot>
        </x-modal>
    @endif
</div>
