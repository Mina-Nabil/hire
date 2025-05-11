<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Attendance Records
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
                        placeholder="Search by employee name or email" wire:model.live.debounce.400ms="search">
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
            <div class="p-4">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="form-label text-sm">Date From</label>
                        <input type="date" wire:model.live="startDate" class="form-control">
                    </div>
                    <div>
                        <label class="form-label text-sm">Date To</label>
                        <input type="date" wire:model.live="endDate" class="form-control">
                    </div>
                </div>
            </div>
        @endif

        <div class="card-body">
            <div class="overflow-x-auto">
                @if (count($attendances) > 0)
                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                        <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col" class="table-th">Employee</th>
                                <th scope="col" class="table-th">Date</th>
                                <th scope="col" class="table-th">Start Time</th>
                                <th scope="col" class="table-th">End Time</th>
                                <th scope="col" class="table-th">Hours</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            @foreach ($attendances as $attendance)
                                <tr class="even:bg-slate-100 dark:even:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700">
                                    <td class="table-td">
                                        <div class="flex items-center">
                                            @if ($attendance->employee && $attendance->employee->full_image_url)
                                                <div class="flex-none">
                                                    <div class="h-10 w-10 rounded-full overflow-hidden mr-2">
                                                        <img src="{{ $attendance->employee->full_image_url }}"
                                                            alt="{{ $attendance->employee->name }}"
                                                            class="h-full w-full object-cover">
                                                    </div>
                                                </div>
                                            @endif
                                            <div>
                                                <span class="text-sm text-slate-600 dark:text-slate-300 capitalize">
                                                    {{ $attendance->employee ? $attendance->employee->name : 'N/A' }}
                                                </span>
                                                @if ($attendance->employee)
                                                    <span class="block text-xs text-slate-500">
                                                        {{ $attendance->employee->email }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-td">
                                        {{ $attendance->date }}
                                    </td>
                                    <td class="table-td">
                                        {{ $attendance->start_time }}
                                    </td>
                                    <td class="table-td">
                                        {{ $attendance->end_time }}
                                    </td>
                                    <td class="table-td">
                                        {{ $attendance->hours }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <div style="position: sticky; bottom:0;width:100%; z-index:10;"
                            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            {{ $attendances->links('vendor.livewire.simple-bootstrap') }}
                        </div>
                    </table>
                @else
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No attendance records found with
                                    the applied filters</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
