<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Employees Compensations and Benefits
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
                        placeholder="Search by name, email or phone" wire:model.live.debounce.400ms="search">
                </div>

                <div class="flex items-center sm:mt-2">
                    @if ($showFilters)
                        <button type="button" wire:click="resetFilters"
                            class="btn btn-sm btn-outline-danger btn-sm mr-2">
                            <span class="flex items-center">
                                <iconify-icon class="text-lg ltr:mr-1 rtl:ml-1 text-sm"
                                    icon="heroicons-outline:x"></iconify-icon>
                                <span>Clear Filters</span>
                            </span>
                        </button>
                    @endif
                    <button type="button"
                        class="btn inline-flex justify-center btn-outline-primary dropdown-toggle btn-sm"
                        wire:click="toggleFilters" aria-expanded="false">
                        <span class="flex items-center">
                            <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2 text-sm"
                                icon="heroicons-outline:filter"></iconify-icon>
                            <span>Filters</span>
                        </span>
                    </button>
                </div>
            </div>
        </header>

        @if ($showFilters)
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-2">
                <!-- Date Range -->
                <div class="mb-4">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="form-label text-sm">Created From</label>
                            <input type="date" wire:model.live="startDate" class="form-control">
                        </div>
                        <div>
                            <label class="form-label text-sm">Created To</label>
                            <input type="date" wire:model.live="endDate" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- Package and Department -->
                <div class="mb-4">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="form-label">Benefit Package</label>
                            <select wire:model.live="packageId" class="form-control">
                                <option value="">All</option>
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}">{{ $package->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Department</label>
                            <select wire:model.live="departmentId" class="form-control">
                                <option value="">All</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        @endif

        <div class="card-body">
            <div class="overflow-x-auto">
                @if (count($employees) > 0)
                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                        <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col" class="table-th">Employee</th>
                                <th scope="col" class="table-th">Department</th>
                                <th scope="col" class="table-th">Package</th>
                                <th scope="col" class="table-th">Added On</th>
                                <th scope="col" class="table-th">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            @foreach ($employees as $employee)
                                <tr class="even:bg-slate-100 dark:even:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer"
                                    wire:click="viewEmployeeConfiguration({{ $employee->id }})">
                                    <td class="table-td">
                                        <div class="flex items-center">
                                            @if ($employee->full_image_url)
                                                <div class="flex-none">
                                                    <div class="h-10 w-10 rounded-full overflow-hidden mr-2">
                                                        <img src="{{ $employee->full_image_url }}"
                                                            alt="{{ $employee->name }}"
                                                            class="h-full w-full object-cover">
                                                    </div>
                                                </div>
                                            @endif
                                            <div>
                                                <span class="text-sm text-slate-600 dark:text-slate-300 capitalize">
                                                    {{ $employee->name }}
                                                </span>
                                                <span class="block text-xs text-slate-500">
                                                    {{ $employee->email }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-td">
                                        @if ($employee->position)
                                        <span class="text-sm text-slate-600 dark:text-slate-300 capitalize">
                                            {{ $employee->position?->department?->name ?? "N/A" }}
                                        </span>
                                        <span class="block text-xs text-slate-500">
                                                {{ $employee->position?->name ?? "N/A" }}
                                            </span>
                                        @else
                                            <span class="badge badge-danger">N/A</span>
                                        @endif

                                    </td>
                                    <td class="table-td">
                                        @if ($employee->benefitConfiguration?->salaryGrade)
                                            {{ ucfirst($employee->benefitConfiguration?->salaryGrade->name) }}
                                        @else
                                            <span class="badge badge-danger">Not Configured</span>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        {{ $employee->benefitConfiguration ? $employee->benefitConfiguration->created_at->format('Y-m-d') : 'N/A' }}
                                    </td>
                                    <td class="table-td">
                                        <div class="flex space-x-3 rtl:space-x-reverse">
                                            <button class="action-btn btn-edit"
                                                wire:click.stop="editConfiguration({{ $employee->id }})">
                                                <iconify-icon icon="heroicons-outline:pencil-alt"></iconify-icon>
                                            </button>
                                        </div>
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
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No configurations found with
                                    the
                                    applied filters</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>


    </div>


    <livewire:benefits.partials.apply-package-modal />

</div>
