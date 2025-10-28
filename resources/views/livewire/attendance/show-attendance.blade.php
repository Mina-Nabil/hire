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
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="form-label text-sm">Date From</label>
                        <input type="date" wire:model.live="startDate" class="form-control">
                    </div>
                    <div>
                        <label class="form-label text-sm">Date To</label>
                        <input type="date" wire:model.live="endDate" class="form-control">
                    </div>
                    <div>
                        <label class="form-label text-sm">Approval Status</label>
                        <select wire:model.live="isApproved" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="pending">Pending</option>
                        </select>
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
                                <th scope="col" class="table-th">Status</th>
                                <th scope="col" class="table-th">Date</th>
                                <th scope="col" class="table-th">Start Time</th>
                                <th scope="col" class="table-th">End Time</th>
                                <th scope="col" class="table-th">Hours</th>
                                <th scope="col" class="table-th">Extra Hours</th>
                                <th scope="col" class="table-th">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            @foreach ($attendances as $attendance)
                                <tr
                                    class="even:bg-slate-100 dark:even:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700">
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
                                        @if (!$attendance->is_approved)
                                            @can('approve', $attendance)
                                                <div class="dropdown relative">
                                                    <button
                                                        class="btn inline-flex justify-center btn-warning items-center btn-sm"
                                                        type="button" id="attendanceDropdownMenuButton"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        Pending
                                                        <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2"
                                                            icon="ic:round-keyboard-arrow-down"></iconify-icon>
                                                    </button>
                                                    <ul
                                                        class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                                                            z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                                                        <li wire:click.prevent="approveAttendance({{ $attendance->id }})">
                                                            <a href="#"
                                                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                                                    dark:hover:text-white">
                                                                Approve</a>
                                                        </li>
                                                        <li wire:click.prevent="rejectAttendance({{ $attendance->id }})">
                                                            <a href="#"
                                                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                                                    dark:hover:text-white">
                                                                Reject</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @else
                                                <span
                                                    class="inline-block px-3 min-w-[90px] text-center py-1 rounded-[999px] bg-opacity-25 text-warning-500 bg-warning-500 text-xs">
                                                    Pending Manager Approval
                                                </span>
                                            @endcan
                                        @elseif($attendance->is_approved)
                                            <span
                                                class="inline-block px-3 min-w-[90px] text-center py-1 rounded-[999px] bg-opacity-25 text-success-500 bg-success-500 text-xs">
                                                Approved
                                            </span>
                                        @else
                                            <span
                                                class="inline-block px-3 min-w-[90px] text-center py-1 rounded-[999px] bg-opacity-25 text-danger-500 bg-danger-500 text-xs">
                                                Rejected
                                            </span>
                                        @endif
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
                                    <td class="table-td">
                                        <div class="flex items-center gap-2">
                                            @if ($attendance->extra_hours)
                                                <span>
                                                    <span
                                                        class="badge @if ($attendance->extra_hours > 0) bg-success-500 @else bg-danger-500 @endif text-slate-900 bg-opacity-50 capitalize">{{ $attendance->extra_hours }}</span>
                                                </span>
                                            @endif
                                            @if ($isManager)
                                                <button wire:click="openEditExtraHours({{ $attendance->id }})"
                                                    class="action-btn" type="button">
                                                    <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                </button>
                                            @endif
                                            @if ($attendance->extra_hours)
                                                @if ($attendance->is_extra_hours_approved === null)
                                                    @if ($isHr)
                                                        <div class="dropdown relative">
                                                            <button
                                                                class="btn inline-flex justify-center btn-warning items-center btn-sm"
                                                                type="button" id="successDropdownMenuButton"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                Pending
                                                                <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2"
                                                                    icon="ic:round-keyboard-arrow-down"></iconify-icon>
                                                            </button>
                                                            <ul
                                                                class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                                                            z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                                                                <li
                                                                    wire:click.prevent="approveExtraHours({{ $attendance->id }})">
                                                                    <a href="#"
                                                                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                                                    dark:hover:text-white">
                                                                        Approve</a>
                                                                </li>
                                                                <li
                                                                    wire:click.prevent="rejectExtraHours({{ $attendance->id }})">
                                                                    <a href="#"
                                                                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                                                    dark:hover:text-white">
                                                                        Reject</a>
                                                                </li>

                                                            </ul>
                                                        </div>
                                                    @else
                                                        <span
                                                            class="inline-block px-3 min-w-[90px] text-center py-1 rounded-[999px] bg-opacity-25 text-warning-500 bg-warning-500 text-xs">
                                                            Pending HR Approval
                                                        </span>
                                                    @endif
                                                @elseif($attendance->is_extra_hours_approved)
                                                    <span
                                                        class="inline-block px-3 min-w-[90px] text-center py-1 rounded-[999px] bg-opacity-25 text-success-500 bg-success-500 text-xs">
                                                        Approved
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-block px-3 min-w-[90px] text-center py-1 rounded-[999px] bg-opacity-25 text-danger-500 bg-danger-500 text-xs">
                                                        Rejected
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="table-td">
                                        <div class="flex items-center gap-2">
                                            @can('update', $attendance)
                                                <button wire:click="openEditTimes({{ $attendance->id }})"
                                                    class="action-btn" type="button" title="Edit Attendance Times">
                                                    <iconify-icon icon="heroicons:clock"></iconify-icon>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                @else
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No attendance records found
                                    with
                                    the applied filters</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div style="position: sticky; bottom:0;width:100%; z-index:10;"
            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            {{ $attendances->links('vendor.livewire.simple-bootstrap') }}
        </div>
    </div>

    <!-- Extra Hours Edit Modal -->
    <x-modal wire:model="showExtraHoursModal">
        <x-slot name="title">Edit Extra Hours</x-slot>

        <!-- Modal body -->
        <div class="p-6 space-y-4">
            <div class="from-group">
                <label class="form-label">Employee</label>
                <input type="text" class="form-control" disabled value="{{ $employeeName }}">
            </div>

            <div class="from-group">
                <label class="form-label">Date</label>
                <input type="text" class="form-control" disabled value="{{ $attendanceDate }}">
            </div>

            <div class="from-group">
                <label class="form-label">Regular Hours</label>
                <input type="text" class="form-control" disabled value="{{ $attendanceHours }}">
            </div>

            <div class="from-group">
                <label class="form-label">Extra Hours <span class="text-danger-500">*</span></label>
                <input type="number" step="0.01" min="0"
                    class="form-control @error('editExtraHours') !border-danger-500 @enderror"
                    wire:model="editExtraHours">
                @error('editExtraHours')
                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="bg-slate-50 dark:bg-slate-700 p-3 rounded-md">
                <div class="flex items-start">
                    <iconify-icon class="text-lg text-warning-500 mr-2 mt-0.5"
                        icon="heroicons-outline:exclamation-circle"></iconify-icon>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        Extra hours will be submitted to HR for approval. Once approved, they will be
                        included in payroll calculations.
                    </p>
                </div>
            </div>
        </div>

        <!-- Modal footer -->
        <x-slot name="footer">
            <x-secondary-button wire:click="closeExtraHoursModal">Cancel</x-secondary-button>
            <x-primary-button wire:click.prevent="saveExtraHours" loadingFunction="saveExtraHours">Save
                Changes</x-primary-button>
        </x-slot>
    </x-modal>

    <!-- Edit Attendance Times Modal -->
    <x-modal wire:model="showEditTimesModal">
        <x-slot name="title">Edit Attendance Times</x-slot>

        <!-- Modal body -->
        <div class="p-6 space-y-4">
            <div class="from-group">
                <label class="form-label">Employee</label>
                <input type="text" class="form-control" disabled value="{{ $editTimesEmployeeName }}">
            </div>

            <div class="from-group">
                <label class="form-label">Date</label>
                <input type="text" class="form-control" disabled value="{{ $editTimesAttendanceDate }}">
            </div>

            <div class="from-group">
                <label class="form-label">Current Hours</label>
                <input type="text" class="form-control" disabled value="{{ $editTimesCurrentHours }}">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="from-group">
                    <label class="form-label">Start Time <span class="text-danger-500">*</span></label>
                    <input type="time" class="form-control @error('editStartTime') !border-danger-500 @enderror"
                        wire:model="editStartTime">
                    @error('editStartTime')
                        <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="from-group">
                    <label class="form-label">End Time</label>
                    <input type="time" class="form-control @error('editEndTime') !border-danger-500 @enderror"
                        wire:model="editEndTime">
                    @error('editEndTime')
                        <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="bg-slate-50 dark:bg-slate-700 p-3 rounded-md">
                <div class="flex items-start">
                    <iconify-icon class="text-lg text-warning-500 mr-2 mt-0.5"
                        icon="heroicons-outline:exclamation-circle"></iconify-icon>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        Changing attendance times will automatically recalculate hours and overtime.
                        End time is optional - if not provided, the system will use the employee's daily working hours.
                    </p>
                </div>
            </div>
        </div>

        <!-- Modal footer -->
        <x-slot name="footer">
            <x-secondary-button wire:click="closeEditTimesModal">Cancel</x-secondary-button>

            @can('delete', \App\Models\Attendance\Attendance::class)
                <x-secondary-button
                    wire:click="deleteAttendance({{ $editingTimesAttendanceId }})">Delete</x-secondary-button>
            @endcan

            <x-primary-button wire:click.prevent="saveAttendanceTimes" loadingFunction="saveAttendanceTimes">Save
                Changes</x-primary-button>
        </x-slot>
    </x-modal>
</div>
