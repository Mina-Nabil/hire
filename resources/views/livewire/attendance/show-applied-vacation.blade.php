<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Applied Time off Records
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
                        placeholder="Search by employee name, email, or vacation benefit"
                        wire:model.live.debounce.400ms="search">
                </div>

                <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center rtl:space-x-reverse">
                    <button class="btn inline-flex justify-center btn-outline-secondary" wire:click="toggleFilters">
                        <span class="flex items-center">
                            <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2"
                                icon="heroicons-outline:filter"></iconify-icon>
                            <span>Filter</span>
                        </span>
                    </button>
                    @if ($search || $startDate || $endDate || $status || $benefitName)
                        <button class="btn inline-flex justify-center btn-outline-danger" wire:click="resetFilters">
                            <span class="flex items-center">
                                <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2"
                                    icon="heroicons-outline:x"></iconify-icon>
                                <span>Reset</span>
                            </span>
                        </button>
                    @endif
                </div>
            </div>
        </header>

        @if ($showFilters)
            <div class="p-4">
                <div class="grid grid-cols-4 gap-2">
                    <div>
                        <label class="form-label text-sm">Date From</label>
                        <input type="date" wire:model.live="startDate" class="form-control">
                    </div>
                    <div>
                        <label class="form-label text-sm">Date To</label>
                        <input type="date" wire:model.live="endDate" class="form-control">
                    </div>
                    <div>
                        <label class="form-label text-sm">Status</label>
                        <select wire:model.live="status" class="form-control">
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label text-sm">Benefit Name</label>
                        <select wire:model.live="benefitName" class="form-control">
                            <option value="">All</option>
                            @foreach ($benefitNames as $name)
                                <option value="{{ $name }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif

        <div class="card-body">
            <div class="overflow-x-auto">
                @if (count($appliedVacations) > 0)
                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                        <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col" class="table-th">Employee</th>
                                <th scope="col" class="table-th">Time off</th>
                                <th scope="col" class="table-th">Days</th>
                                <th scope="col" class="table-th">Hours</th>
                                <th scope="col" class="table-th">New Balance</th>
                                <th scope="col" class="table-th">Status</th>
                                <th scope="col" class="table-th">Created</th>
                                <th scope="col" class="table-th">Approved</th>
                                <th scope="col" class="table-th">Payroll</th>
                                <th scope="col" class="table-th">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            @foreach ($appliedVacations as $appliedVacation)
                                <tr
                                    class="even:bg-slate-100 dark:even:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700">
                                    <td class="table-td">
                                        <div class="flex items-center">
                                            @if ($appliedVacation->employee && $appliedVacation->employee->full_image_url)
                                                <div class="flex-none">
                                                    <div class="h-10 w-10 rounded-full overflow-hidden mr-2">
                                                        <img src="{{ $appliedVacation->employee->full_image_url }}"
                                                            alt="{{ $appliedVacation->employee->name }}"
                                                            class="h-full w-full object-cover">
                                                    </div>
                                                </div>
                                            @endif
                                            <div>
                                                <span class="text-sm text-slate-600 dark:text-slate-300 capitalize">
                                                    {{ $appliedVacation->employee ? $appliedVacation->employee->name : 'N/A' }}
                                                </span>
                                                @if ($appliedVacation->employee)
                                                    <span class="block text-xs text-slate-500">
                                                        {{ $appliedVacation->employee->email }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-td">
                                        <div>
                                            <span class="text-sm text-slate-600 dark:text-slate-300">
                                                @if ($appliedVacation->is_mission)
                                                    Mission
                                                @else
                                                    {{ $appliedVacation->vacationBenefit ? $appliedVacation->vacationBenefit->name : 'N/A' }}
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                    <td class="table-td">
                                        <span class="text-sm font-medium">{{ $appliedVacation->vacationDays->pluck('vacation_date')->implode(', ') }}</span>
                                    </td>
                                    <td class="table-td">
                                        <span class="text-sm font-medium">{{ $appliedVacation->hours ?? 0 }}</span>
                                    </td>
                                    <td class="table-td">
                                        <span
                                            class="text-sm font-medium">{{ $appliedVacation->new_balance ?? 0 }}</span>
                                    </td>
                                    <td class="table-td">
                                        @if ($appliedVacation->status === 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif ($appliedVacation->status === 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @else
                                            <span class="badge badge-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        <span class="text-sm text-slate-600 dark:text-slate-300">
                                            {{ $appliedVacation->created_at ? $appliedVacation->created_at->format('M d, Y') : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="table-td">
                                        @if ($appliedVacation->approvedBy)
                                            <span class="text-sm text-slate-600 dark:text-slate-300">
                                                {{ $appliedVacation->approvedBy->name }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400">Not approved</span>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        @if ($appliedVacation->payroll)
                                            <span class="text-sm text-slate-600 dark:text-slate-300">
                                                {{ $appliedVacation->payroll->title }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400">No payroll</span>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        <div class="flex space-x-2">
                                            <button wire:click="openVacationDetailsModal({{ $appliedVacation->id }})"
                                                class="action-btn text-info-500" title="View Details">
                                                <iconify-icon icon="heroicons:information-circle"></iconify-icon>
                                            </button>
                                            @can('approve', $appliedVacation)
                                                <button
                                                    wire:click="$dispatch('showConfirmation',{message:'Are you sure you want to approve this vacation?',color:'success',callback:'approveVacation',params:{{ $appliedVacation->id }}})"
                                                    class="action-btn text-success-500" title="Approve">
                                                    <iconify-icon icon="heroicons:check"></iconify-icon>
                                                </button>
                                            @endcan
                                            @can('reject', $appliedVacation)
                                                <button wire:click="openRejectModal({{ $appliedVacation->id }})"
                                                    class="action-btn text-danger-500" title="Reject">
                                                    <iconify-icon icon="heroicons:x-mark"></iconify-icon>
                                                </button>
                                            @endcan

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <div style="position: sticky; bottom:0;width:100%; z-index:10;"
                            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            {{ $appliedVacations->links('vendor.livewire.simple-bootstrap') }}
                        </div>
                    </table>
                @else
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No applied vacation records
                                    found with
                                    the applied filters</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($showRejectModal)
        <x-modal wire:model="showRejectModal">
            <x-slot name="title">Reject Vacation</x-slot>
            <!-- Modal body -->
            <div class="p-6 space-y-4">
                @if ($selectedAppliedVacation)
                    <div class="alert alert-info">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            <div>
                                <p class="font-medium">Rejecting vacation for:</p>
                                <p>{{ $selectedAppliedVacation->employee ? $selectedAppliedVacation->employee->name : 'N/A' }}
                                </p>
                                <p class="text-sm mt-1">
                                    {{ $selectedAppliedVacation->vacationBenefit ? $selectedAppliedVacation->vacationBenefit->name : 'N/A' }}
                                    - {{ $selectedAppliedVacation->hours ?? 0 }} hours</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="from-group">
                    <div class="input-area">
                        <label for="rejectNote" class="form-label">Rejection Note (Optional)</label>
                        <textarea id="rejectNote" rows="4" class="form-control" wire:model="rejectNote"
                            placeholder="Enter reason for rejection..."></textarea>
                    </div>
                </div>
            </div>
            <!-- Modal footer -->
            <x-slot name="footer">
                <x-secondary-button wire:click="closeRejectModal">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="confirmReject" loadingFunction="confirmReject">Reject
                    Vacation</x-primary-button>
            </x-slot>
        </x-modal>
    @endif

    @if ($showVacationDetailsModal)
        <x-modal wire:model="showVacationDetailsModal">
            <x-slot name="title">Vacation Details</x-slot>
            <!-- Modal body -->
            <div class="p-6 space-y-6">
                @if ($selectedAppliedVacation)
                    <!-- Employee Information -->
                    <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-3">Employee Information</h3>
                        <div class="flex items-center space-x-4">
                            @if ($selectedAppliedVacation->employee && $selectedAppliedVacation->employee->full_image_url)
                                <div class="flex-none">
                                    <div class="h-16 w-16 rounded-full overflow-hidden">
                                        <img src="{{ $selectedAppliedVacation->employee->full_image_url }}"
                                            alt="{{ $selectedAppliedVacation->employee->name }}"
                                            class="h-full w-full object-cover">
                                    </div>
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-slate-900 dark:text-white">
                                    {{ $selectedAppliedVacation->employee ? $selectedAppliedVacation->employee->name : 'N/A' }}
                                </p>
                                @if ($selectedAppliedVacation->employee)
                                    <p class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $selectedAppliedVacation->employee->email }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Vacation Details -->
                    <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-3">Vacation Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Vacation
                                    Benefit</label>
                                <p class="mt-1 text-slate-900 dark:text-white">
                                    {{ $selectedAppliedVacation->vacationBenefit ? $selectedAppliedVacation->vacationBenefit->name : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Status</label>
                                <div class="mt-1">
                                    @if ($selectedAppliedVacation->status === 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif ($selectedAppliedVacation->status === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @else
                                        <span class="badge badge-danger">Rejected</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Days</label>
                                <p class="mt-1 text-slate-900 dark:text-white font-medium">
                                <ul>
                                    @foreach ($selectedAppliedVacation->vacationDays as $day)
                                        <li>
                                            {{ $day->vacation_date }} ({{ $day->hours }} hours)
                                        </li>
                                    @endforeach
                                </ul>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Hours</label>
                                <p class="mt-1 text-slate-900 dark:text-white font-medium">
                                    {{ $selectedAppliedVacation->hours ?? 0 }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600 dark:text-slate-300">New
                                    Balance</label>
                                <p class="mt-1 text-slate-900 dark:text-white font-medium">
                                    {{ $selectedAppliedVacation->new_balance ?? 0 }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Payroll</label>
                                <p class="mt-1 text-slate-900 dark:text-white">
                                    @if ($selectedAppliedVacation->payroll)
                                        {{ $selectedAppliedVacation->payroll->title }}
                                    @else
                                        <span class="text-slate-400">No payroll assigned</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Approved By</label>
                                <p class="mt-1 text-slate-900 dark:text-white">
                                    @if ($selectedAppliedVacation->approvedBy)
                                        {{ $selectedAppliedVacation->approvedBy->name }}
                                    @else
                                        <span class="text-slate-400">Not approved yet</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    @if ($selectedAppliedVacation->note && !empty(trim($selectedAppliedVacation->note)))
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-3">Notes</h3>
                            <p class="text-slate-700 dark:text-slate-300 whitespace-pre-wrap">
                                {{ $selectedAppliedVacation->note }}</p>
                        </div>
                    @endif

                    <!-- Timestamps -->
                    <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-3">Timeline</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Applied
                                    Date</label>
                                <p class="mt-1 text-slate-900 dark:text-white">
                                    {{ $selectedAppliedVacation->created_at ? $selectedAppliedVacation->created_at->format('M d, Y h:i A') : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Last
                                    Updated</label>
                                <p class="mt-1 text-slate-900 dark:text-white">
                                    {{ $selectedAppliedVacation->updated_at ? $selectedAppliedVacation->updated_at->format('M d, Y h:i A') : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <!-- Modal footer -->
            <x-slot name="footer">
                <x-secondary-button wire:click="closeVacationDetailsModal">Close</x-secondary-button>
            </x-slot>
        </x-modal>
    @endif
</div>
