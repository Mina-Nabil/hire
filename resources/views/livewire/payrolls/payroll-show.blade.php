<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Payroll Details</h4>
            <div class="flex items-center gap-2">
                @if ($payroll->status === \App\Models\Benefits\Payrolls\Payroll::STATUS_PENDING)
                    <div class="flex justify-end gap-2">

                        @can('delete', $payroll)
                            <button type="button" class="btn btn-danger btn-sm"
                                wire:click="$dispatch('showConfirmation',{message:'Are you sure you want to delete this payroll? This action cannot be undone.',color:'danger',callback:'deletePayroll'})">
                                <span>
                                    <span class="flex items-center">
                                        <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2"
                                            icon="heroicons-outline:trash"></iconify-icon>
                                        Delete Payroll
                                    </span>
                                </span>
                            </button>
                        @endcan
                        @can('update', $payroll)
                            <button type="button" class="btn btn-success btn-sm"
                                wire:click="$dispatch('showConfirmation',{message:'Are you sure you want to approve this payroll?',color:'success',callback:'approvePayroll'})">
                                <span>
                                    <span class="flex items-center">
                                        <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2"
                                            icon="heroicons-outline:check"></iconify-icon>
                                        Approve Payroll
                                    </span>
                                </span>
                            </button>
                        @endcan
                    </div>
                @endif

                <a href="{{ route('payrolls.index') }}">
                    <button class="btn inline-flex justify-center btn-light btn-sm">Back to Payrolls</button>
                </a>
            </div>
        </div>
        <div class="card-body p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-4">
                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-300 mb-1">Period</h5>
                    <div class="text-base font-semibold">
                        {{ \Carbon\Carbon::parse($payroll->start_date)->format('M d, Y') }} -
                        {{ \Carbon\Carbon::parse($payroll->end_date)->format('M d, Y') }}
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-4">
                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-300 mb-1">Total Employees</h5>
                    <div class="text-base font-semibold">{{ $payroll->total_employees }}</div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-4">
                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-300 mb-1">Total Paid</h5>
                    <div class="text-base font-semibold">{{ number_format($payroll->total_paid, 2) }}</div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-4">
                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-300 mb-1">Status</h5>
                    <div class="text-base font-semibold">
                        @if ($payroll->status === \App\Models\Benefits\Payrolls\Payroll::STATUS_APPROVED)
                            <span class="badge bg-success-500 text-white">Approved</span>
                        @else
                            <span class="badge bg-warning-500 text-white">Pending</span>
                        @endif
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-4">
                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-300 mb-1">Created By</h5>
                    <div class="text-base font-semibold">{{ $payroll->creator->name ?? 'N/A' }}</div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-4">
                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-300 mb-1">Created At</h5>
                    <div class="text-base font-semibold">{{ $payroll->created_at->format('M d, Y H:i') }}</div>
                </div>
            </div>

            <div class="flex justify-between items-center mb-6">
                <div class="flex-1 mr-4">
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search..."
                            class="form-control py-2 !pl-12">
                        <span class="absolute left-2 top-2 text-lg">
                            <iconify-icon icon="heroicons-outline:search"></iconify-icon>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="border-b border-slate-200 dark:border-slate-700 mb-6">
                <ul class="flex flex-wrap -mb-px">
                    <li class="mr-2">
                        <button wire:click="setActiveTab('overview')"
                            class="inline-block py-2 px-4 text-sm font-medium border-b-2 {{ $activeTab === 'overview' ? 'border-primary-500 text-primary-500' : 'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300' }}">
                            <span class="flex items-center">
                                <iconify-icon icon="heroicons-outline:users"
                                    class="text-base ltr:mr-2 rtl:ml-2"></iconify-icon>
                                Overview
                            </span>
                        </button>
                    </li>
                    <li class="mr-2">
                        <button wire:click="setActiveTab('benefits')"
                            class="inline-block py-2 px-4 text-sm font-medium border-b-2 {{ $activeTab === 'benefits' ? 'border-primary-500 text-primary-500' : 'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300' }}">
                            <span class="flex items-center">
                                <iconify-icon icon="heroicons-outline:gift"
                                    class="text-base ltr:mr-2 rtl:ml-2"></iconify-icon>
                                Benefit Payments
                            </span>
                        </button>
                    </li>
                    <li class="mr-2">
                        <button wire:click="setActiveTab('overtime')"
                            class="inline-block py-2 px-4 text-sm font-medium border-b-2 {{ $activeTab === 'overtime' ? 'border-primary-500 text-primary-500' : 'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300' }}">
                            <span class="flex items-center">
                                <iconify-icon icon="heroicons-outline:clock"
                                    class="text-base ltr:mr-2 rtl:ml-2"></iconify-icon>
                                Overtime
                            </span>
                        </button>
                    </li>
                    <li>
                        <button wire:click="setActiveTab('extra-payments')"
                            class="inline-block py-2 px-4 text-sm font-medium border-b-2 {{ $activeTab === 'extra-payments' ? 'border-primary-500 text-primary-500' : 'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300' }}">
                            <span class="flex items-center">
                                <iconify-icon icon="heroicons-outline:currency-dollar"
                                    class="text-base ltr:mr-2 rtl:ml-2"></iconify-icon>
                                Extra Payments
                            </span>
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Tab Content -->
            @if ($activeTab === 'overview')
                @include('livewire.payrolls.partials.overview-tab')
            @elseif($activeTab === 'benefits')
                @include('livewire.payrolls.partials.benefits-tab')
            @elseif($activeTab === 'overtime')
                @include('livewire.payrolls.partials.overtime-tab')
            @elseif($activeTab === 'extra-payments')
                @include('livewire.payrolls.partials.extra-payments-tab')
            @endif
        </div>
    </div>

    <!-- Employee Details Modal -->
    @if ($showEmployeeDetailsModal && $selectedPayrollEmployee)
        <x-modal wire:model="showEmployeeDetailsModal" size="xl">
            <x-slot name="title">
                <div class="flex items-center">
                    <div class="flex-none">
                        <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center text-white">
                            {{ substr($selectedPayrollEmployee->employee->name ?? 'N/A', 0, 2) }}
                        </div>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-slate-100 dark:text-white">
                            {{ $selectedPayrollEmployee->employee->name ?? 'N/A' }}
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ $selectedPayrollEmployee->position }} - {{ $selectedPayrollEmployee->department }}
                        </p>
                    </div>
                </div>
            </x-slot>

            <div class="space-y-5">
                <!-- Payroll Summary Section -->
                <div>
                    <h4 class="text-base font-medium mb-3 border-b pb-2">Payroll Summary</h4>
                    <div class="grid  md:grid-cols-6 sm:grid-cols-1 gap-4">
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Gross Salary</h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->gross_salary, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Social Insurance
                                Salary
                            </h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->insurance_amount, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Other Amount</h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->other_amount, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Employee Insurance
                            </h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->employee_insurance, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Employer Insurance
                            </h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->employer_insurance, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Total Insurance
                            </h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->total_insurance, 2) }}</div>
                        </div>

                        <!-- Enhanced Penalty Information -->
                        @if (($selectedPayrollEmployee->total_penalty_hours ?? 0) > 0)
                            <div class="bg-warning-50 dark:bg-warning-900/20 rounded-md p-3 md:col-span-2">
                                <h5 class="text-xs font-medium text-warning-600 dark:text-warning-400 mb-2">Penalty
                                    Breakdown</h5>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span>Total Abscence Hours:</span>
                                        <span
                                            class="font-semibold">{{ number_format($selectedPayrollEmployee->total_penalty_hours ?? 0, 1) }}h</span>
                                    </div>
                                    @if (($selectedPayrollEmployee->vacation_offset_hours ?? 0) > 0)
                                        <div class="flex justify-between text-xs text-info-600">
                                            <span>Vacation Offset:</span>
                                            <span
                                                class="font-semibold">{{ number_format($selectedPayrollEmployee->vacation_offset_hours, 1) }}h</span>
                                        </div>
                                    @endif
                                    @if (($selectedPayrollEmployee->new_vacation_hours ?? 0) > 0)
                                        <div class="flex justify-between text-xs text-success-600">
                                            <span>New Vacation Applied:</span>
                                            <span
                                                class="font-semibold">{{ number_format($selectedPayrollEmployee->new_vacation_hours, 1) }}h</span>
                                        </div>
                                    @endif
                                    @if (($selectedPayrollEmployee->direct_deduction_hours ?? 0) > 0)
                                        <div class="flex justify-between text-xs text-danger-600">
                                            <span>Deduction:</span>
                                            <span
                                                class="font-semibold">{{ number_format($selectedPayrollEmployee->direct_deduction_hours, 1) }}h</span>
                                        </div>
                                        <div class="flex justify-between text-xs text-danger-600 border-t pt-1">
                                            <span>Amount Deducted:</span>
                                            <span
                                                class="font-semibold">{{ number_format($selectedPayrollEmployee->direct_deduction_amount ?? 0, 2) }}
                                                EGP</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <!-- Legacy penalty display for backward compatibility -->
                            @if (($selectedPayrollEmployee->penalties_days ?? 0) > 0)
                                <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                                    <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Penalty
                                        Days</h5>
                                    <div class="text-sm font-semibold">
                                        {{ number_format($selectedPayrollEmployee->penalties_days, 2) }}</div>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                                    <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Penalty
                                        Amount</h5>
                                    <div class="text-sm font-semibold text-danger-500">
                                        -{{ number_format($selectedPayrollEmployee->penalties_amount, 2) }}</div>
                                </div>
                            @else
                                <div class="bg-success-50 dark:bg-success-900/20 rounded-md p-3 md:col-span-2">
                                    <h5 class="text-xs font-medium text-success-600 dark:text-success-400 mb-1">
                                        Penalties</h5>
                                    <div class="text-sm font-semibold text-success-600">No penalties applied</div>
                                </div>
                            @endif
                        @endif

                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Net After Penalty
                            </h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->net_after_penalty, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Extra Payments</h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->extra_payments, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Overtime Hours</h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->overtime_hours, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Overtime Amount
                            </h5>
                            <div class="text-sm font-semibold text-success-500">
                                +{{ number_format($selectedPayrollEmployee->overtime_amount, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Before Tax
                            </h5>
                            <div class="text-sm font-semibold text-success-500">
                                +{{ number_format($selectedPayrollEmployee->net_after_deductions, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Adjustment Amount
                            </h5>
                            <div
                                class="text-sm font-semibold @if ($selectedPayrollEmployee->adj_amount >= 0) text-success-500 @else text-danger-500 @endif">
                                @if ($selectedPayrollEmployee->adj_amount >= 0)
                                    +
                                @endif{{ number_format($selectedPayrollEmployee->adj_amount, 2) }}
                            </div>
                            @if (!empty($selectedPayrollEmployee->adj_desc))
                                <div class="text-xs text-slate-500 mt-1">{{ $selectedPayrollEmployee->adj_desc }}
                                </div>
                            @endif
                        </div>
                        <div class="bg-secondary-50 dark:bg-secondary-900/20 rounded-md p-3 md:col-span-6">
                            <h5 class="text-xs font-medium text-secondary-500 dark:text-secondary-400 mb-1">Tax Amount
                            </h5>
                            <div class="text-lg font-semibold text-secondary-600 dark:text-secondary-400">
                                {{ number_format($selectedPayrollEmployee->tax_amount, 2) }}</div>
                        </div>
                        <div class="bg-primary-50 dark:bg-primary-900/20 rounded-md p-3 md:col-span-6">
                            <h5 class="text-xs font-medium text-primary-500 dark:text-primary-400 mb-1">Net Amount</h5>
                            <div class="text-lg font-semibold text-primary-600 dark:text-primary-400">
                                {{ number_format($selectedPayrollEmployee->after_tax_salary, 2) }}</div>
                        </div>
                    </div>
                </div>



                <!-- Tabs for different sections -->
                <div x-data="{ activeTab: 'attendance' }">
                    <div class="border-b border-slate-200 dark:border-slate-700">
                        <ul class="flex flex-wrap -mb-px">
                            <li class="mr-2">
                                <button @click="activeTab = 'attendance'"
                                    :class="activeTab === 'attendance' ? 'border-primary-500 text-primary-500' :
                                        'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300'"
                                    class="inline-block py-2 px-4 text-sm font-medium border-b-2">
                                    Attendance Records
                                </button>
                            </li>
                            <li class="mr-2">
                                <button @click="activeTab = 'benefits'"
                                    :class="activeTab === 'benefits' ? 'border-primary-500 text-primary-500' :
                                        'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300'"
                                    class="inline-block py-2 px-4 text-sm font-medium border-b-2">
                                    Benefit Payments
                                </button>
                            </li>
                            <li class="mr-2">
                                <button @click="activeTab = 'overtime'"
                                    :class="activeTab === 'overtime' ? 'border-primary-500 text-primary-500' :
                                        'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300'"
                                    class="inline-block py-2 px-4 text-sm font-medium border-b-2">
                                    Overtime
                                </button>
                            </li>
                            <li class="mr-2">
                                <button @click="activeTab = 'penalties'"
                                    :class="activeTab === 'penalties' ? 'border-primary-500 text-primary-500' :
                                        'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300'"
                                    class="inline-block py-2 px-4 text-sm font-medium border-b-2">
                                    Penalties
                                </button>
                            </li>
                            <li>
                                <button @click="activeTab = 'extras'"
                                    :class="activeTab === 'extras' ? 'border-primary-500 text-primary-500' :
                                        'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300'"
                                    class="inline-block py-2 px-4 text-sm font-medium border-b-2">
                                    Extra Payments
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Attendance Records Tab -->
                    <div x-show="activeTab === 'attendance'" class="mt-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead>
                                    <tr>
                                        <th class="table-th">Day</th>
                                        <th class="table-th">Date</th>
                                        <th class="table-th">Check In</th>
                                        <th class="table-th">Check Out</th>
                                        <th class="table-th">Total Hours</th>
                                        <th class="table-th">Status</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse($employeeAttendance as $attendance)
                                        <tr>
                                            <td class="table-td">
                                                {{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</td>
                                            <td class="table-td">
                                                {{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                                            <td class="table-td">
                                                {{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i A') : 'N/A' }}
                                            </td>
                                            <td class="table-td">
                                                {{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i A') : 'N/A' }}
                                            </td>
                                            <td class="table-td">
                                                <span
                                                    class="
                                            @if ($attendance->penalized_hours > 0) text-danger-500 @else text-success-500 @endif
                                            ">
                                                    {{ $attendance->hours ? $attendance->hours . 'h' : 'N/A' }}
                                                    @if ($attendance->penalized_hours > 0)
                                                        <span>
                                                            -
                                                            ({{ $attendance->penalized_hours > 1.0 ? number_format($attendance->penalized_hours, 0) . ' h' : number_format($attendance->penalized_hours * 60, 2) . ' min' }})
                                                        </span>
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="table-td">
                                                @if ($attendance->status === 'present')
                                                    <span class="badge bg-success-500 text-white">Present</span>
                                                @elseif($attendance->status === 'absent')
                                                    <span class="badge bg-danger-500 text-white">Absent</span>
                                                @elseif($attendance->status === 'late')
                                                    <span class="badge bg-warning-500 text-white">Late</span>
                                                @else
                                                    <span
                                                        class="badge @if ($attendance->is_approved) bg-success-500 @else bg-slate-500 @endif text-white">
                                                        {{ ucfirst($attendance->is_approved ? 'Approved' : 'Pending') }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="table-td text-center py-4">No attendance records
                                                found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Benefit Payments Tab -->
                    <div x-show="activeTab === 'benefits'" class="mt-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead>
                                    <tr>
                                        <th class="table-th">Benefit</th>
                                        <th class="table-th">Type</th>
                                        <th class="table-th">Amount</th>
                                        <th class="table-th">Status</th>
                                        <th class="table-th">Created At</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse($employeeBenefitPayments as $benefit)
                                        <tr>
                                            <td class="table-td">{{ $benefit->baseBenefit->name ?? 'N/A' }}</td>
                                            <td class="table-td">{{ ucfirst($benefit->baseBenefit->type ?? 'N/A') }}
                                            </td>
                                            <td class="table-td">{{ number_format($benefit->amount, 2) }}</td>
                                            <td class="table-td">
                                                @if ($benefit->status === 'pending')
                                                    <span class="badge bg-warning-500 text-white">Pending</span>
                                                @elseif($benefit->status === 'approved')
                                                    <span class="badge bg-info-500 text-white">Approved</span>
                                                @elseif($benefit->status === 'paid')
                                                    <span class="badge bg-success-500 text-white">Paid</span>
                                                @elseif($benefit->status === 'rejected')
                                                    <span class="badge bg-danger-500 text-white">Rejected</span>
                                                @else
                                                    <span
                                                        class="badge bg-warning-500 text-white">{{ ucfirst($benefit->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="table-td">{{ $benefit->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="table-td text-center py-4">No benefit payments
                                                found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Overtime Tab -->
                    <div x-show="activeTab === 'overtime'" class="mt-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead>
                                    <tr>
                                        <th class="table-th">Date</th>
                                        <th class="table-th">Hours</th>
                                        <th class="table-th">Rate</th>
                                        <th class="table-th">Status</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse($employeeOvertimes as $overtime)
                                        <tr>
                                            <td class="table-td">
                                                {{ \Carbon\Carbon::parse($overtime->date)->format('d M Y') }}</td>
                                            <td class="table-td">{{ $overtime->hours }}</td>
                                            <td class="table-td">{{ $overtime->rate ?? 'Standard' }}</td>
                                            <td class="table-td">
                                                <span
                                                    class="badge bg-success-500 text-white">{{ ucfirst($overtime->status) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="table-td text-center py-4">No overtime records
                                                found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Penalties Tab -->
                    <div x-show="activeTab === 'penalties'" class="mt-4">
                        @if (($selectedPayrollEmployee->total_penalty_hours ?? 0) > 0)
                            <!-- Penalty Summary Cards -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                <div class="bg-warning-50 dark:bg-warning-900/20 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-warning-600 dark:text-warning-400">
                                        {{ number_format($selectedPayrollEmployee->total_penalty_hours ?? 0, 1) }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">Total Abscence Hours</div>
                                </div>
                                <div class="bg-info-50 dark:bg-info-900/20 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-info-600 dark:text-info-400">
                                        {{ number_format($selectedPayrollEmployee->vacation_offset_hours ?? 0, 1) }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">Vacation Offset</div>
                                </div>
                                <div class="bg-success-50 dark:bg-success-900/20 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-success-600 dark:text-success-400">
                                        {{ number_format($selectedPayrollEmployee->new_vacation_hours ?? 0, 1) }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">New Vacation</div>
                                </div>
                                <div class="bg-danger-50 dark:bg-danger-900/20 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-danger-600 dark:text-danger-400">
                                        {{ number_format($selectedPayrollEmployee->direct_deduction_hours ?? 0, 1) }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">Deduction</div>
                                </div>
                            </div>

                            <!-- Penalty Flow Explanation -->
                            <div
                                class="bg-info-50 dark:bg-info-900/20 border border-info-200 dark:border-info-800 rounded-lg p-4 mb-6">
                                <h5 class="text-base font-medium mb-3 text-info-800 dark:text-info-200">How Penalties
                                    Were Processed</h5>
                                <div class="space-y-3">
                                    <div class="flex items-start">
                                        <div
                                            class="w-6 h-6 rounded-full bg-warning-500 text-white flex items-center justify-center text-xs font-bold mr-3 mt-0.5">
                                            1</div>
                                        <div>
                                            <div class="font-medium text-sm">Penalty Hours Calculated</div>
                                            <div class="text-xs text-slate-600 dark:text-slate-400">
                                                {{ number_format($selectedPayrollEmployee->total_penalty_hours ?? 0, 1) }}
                                                hours based on attendance issues (late arrivals, early departures,
                                                missed days)
                                            </div>
                                        </div>
                                    </div>

                                    @if (($selectedPayrollEmployee->vacation_offset_hours ?? 0) > 0)
                                        <div class="flex items-start">
                                            <div
                                                class="w-6 h-6 rounded-full bg-info-500 text-white flex items-center justify-center text-xs font-bold mr-3 mt-0.5">
                                                2</div>
                                            <div>
                                                <div class="font-medium text-sm">Existing Vacations Applied</div>
                                                <div class="text-xs text-slate-600 dark:text-slate-400">
                                                    {{ number_format($selectedPayrollEmployee->vacation_offset_hours, 1) }}
                                                    hours offset using previously approved vacation applications
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if (($selectedPayrollEmployee->new_vacation_hours ?? 0) > 0)
                                        <div class="flex items-start">
                                            <div
                                                class="w-6 h-6 rounded-full bg-success-500 text-white flex items-center justify-center text-xs font-bold mr-3 mt-0.5">
                                                3</div>
                                            <div>
                                                <div class="font-medium text-sm">New Vacation Applications Created
                                                </div>
                                                <div class="text-xs text-slate-600 dark:text-slate-400">
                                                    {{ number_format($selectedPayrollEmployee->new_vacation_hours, 1) }}
                                                    hours automatically applied from available vacation benefits
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if (($selectedPayrollEmployee->direct_deduction_hours ?? 0) > 0)
                                        <div class="flex items-start">
                                            <div
                                                class="w-6 h-6 rounded-full bg-danger-500 text-white flex items-center justify-center text-xs font-bold mr-3 mt-0.5">
                                                4</div>
                                            <div>
                                                <div class="font-medium text-sm">Direct Deduction</div>
                                                <div class="text-xs text-slate-600 dark:text-slate-400">
                                                    {{ number_format($selectedPayrollEmployee->direct_deduction_hours, 1) }}
                                                    hours
                                                    ({{ number_format($selectedPayrollEmployee->direct_deduction_amount ?? 0, 2) }}
                                                    EGP) deducted from net salary
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Detailed Breakdown Table -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                                    <thead>
                                        <tr>
                                            <th class="table-th">Penalty Type</th>
                                            <th class="table-th">Hours</th>
                                            <th class="table-th">Handling Method</th>
                                            <th class="table-th">Amount Impact</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                        @if (($selectedPayrollEmployee->vacation_offset_hours ?? 0) > 0)
                                            <tr>
                                                <td class="table-td">Vacation Offset</td>
                                                <td class="table-td">
                                                    {{ number_format($selectedPayrollEmployee->vacation_offset_hours, 1) }}
                                                </td>
                                                <td class="table-td">
                                                    <span class="badge bg-info-500 text-white">Existing Vacation
                                                        Used</span>
                                                </td>
                                                <td class="table-td text-info-600">No deduction</td>
                                            </tr>
                                        @endif

                                        @if (($selectedPayrollEmployee->new_vacation_hours ?? 0) > 0)
                                            <tr>
                                                <td class="table-td">New Vacation Applied</td>
                                                <td class="table-td">
                                                    {{ number_format($selectedPayrollEmployee->new_vacation_hours, 1) }}
                                                </td>
                                                <td class="table-td">
                                                    <span class="badge bg-success-500 text-white">Auto Vacation
                                                        Created</span>
                                                </td>
                                                <td class="table-td text-success-600">No deduction</td>
                                            </tr>
                                        @endif

                                        @if (($selectedPayrollEmployee->direct_deduction_hours ?? 0) > 0)
                                            <tr>
                                                <td class="table-td">Direct Deduction</td>
                                                <td class="table-td">
                                                    {{ number_format($selectedPayrollEmployee->direct_deduction_hours, 1) }}
                                                </td>
                                                <td class="table-td">
                                                    <span class="badge bg-danger-500 text-white">Deduction</span>
                                                </td>
                                                <td class="table-td text-danger-600">
                                                    -{{ number_format($selectedPayrollEmployee->direct_deduction_amount ?? 0, 2) }}
                                                    EGP
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- Missing Days Table -->
                            <div class="overflow-x-auto mt-4">
                                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                                    <thead>
                                        <tr>
                                            <th class="table-th">Missed Day</th>
                                            <th class="table-th">Hours</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                        @forelse($employeeMissingDays as $missingDay)
                                            <tr>
                                                <td class="table-td">
                                                    {{ \Carbon\Carbon::parse($missingDay->date)->format('d M Y') }}
                                                </td>
                                                <td class="table-td">{{ $missingDay->hours }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="table-td text-center py-4">No missing days
                                                    found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="flex flex-col items-center">
                                    <iconify-icon icon="heroicons-outline:check-circle"
                                        class="text-5xl text-success-400 mb-2"></iconify-icon>
                                    <h5 class="text-xl font-medium text-success-600">No Penalties Applied</h5>
                                    <p class="text-sm text-slate-500 mt-1">This employee had perfect attendance during
                                        the payroll period</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Extra Payments Tab -->
                    <div x-show="activeTab === 'extras'" class="mt-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead>
                                    <tr>
                                        <th class="table-th">Description</th>
                                        <th class="table-th">Amount</th>
                                        <th class="table-th">Due Date</th>
                                        <th class="table-th">Status</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse($employeeExtraPayments as $payment)
                                        <tr>
                                            <td class="table-td">{{ $payment->name }}</td>
                                            <td class="table-td">{{ number_format($payment->amount, 2) }}</td>
                                            <td class="table-td">
                                                {{ \Carbon\Carbon::parse($payment->due_date)->format('d M Y') }}</td>
                                            <td class="table-td">
                                                @if ($payment->status === 'pending')
                                                    <span class="badge bg-warning-500 text-white">Pending</span>
                                                @elseif($payment->status === 'approved')
                                                    <span class="badge bg-info-500 text-white">Approved</span>
                                                @elseif($payment->status === 'paid')
                                                    <span class="badge bg-success-500 text-white">Paid</span>
                                                @elseif($payment->status === 'rejected')
                                                    <span class="badge bg-danger-500 text-white">Rejected</span>
                                                @else
                                                    <span
                                                        class="badge bg-warning-500 text-white">{{ ucfirst($payment->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="table-td text-center py-4">No extra payments
                                                found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end">
                    <x-secondary-button wire:click="closeEmployeeDetailsModal">Close</x-secondary-button>
                </div>
            </x-slot>
        </x-modal>
    @endif

    <!-- Penalty Breakdown Modal -->
    @if ($showPenaltyBreakdownModal && $selectedPenaltyEmployee)
        <x-modal wire:model="showPenaltyBreakdownModal" size="xl">
            <x-slot name="title">
                <div class="flex items-center">
                    <div class="flex-none">
                        <div class="w-10 h-10 rounded-full bg-warning-500 flex items-center justify-center text-white">
                            <iconify-icon icon="heroicons-outline:exclamation-triangle"
                                class="text-lg"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-slate-100 dark:text-white">
                            Penalty Breakdown - {{ $selectedPenaltyEmployee->employee->name ?? 'N/A' }}
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ $selectedPenaltyEmployee->position }} - {{ $selectedPenaltyEmployee->department }}
                        </p>
                    </div>
                </div>
            </x-slot>

            <div class="space-y-6">
                <!-- Penalty Summary -->
                <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="text-base font-medium text-slate-800 dark:text-slate-200">Penalty Summary</h4>
                        @if (
                            ($penaltyBreakdownData['remaining_penalty_hours'] ?? 0) > 0 &&
                                !empty($penaltyBreakdownData['available_vacation_benefits'] ?? []))
                            @can('update', $payroll)
                                <button wire:click="openVacationApplicationModal" class="btn btn-sm btn-primary">
                                    <iconify-icon icon="heroicons-outline:plus" class="mr-1"></iconify-icon>
                                    Apply Vacation
                                </button>
                            @endcan
                        @endif
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-warning-600 dark:text-warning-400">
                                {{ number_format($penaltyBreakdownData['total_penalty_hours'] ?? 0, 1) }}
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Total Absence Hours</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-info-600 dark:text-info-400">
                                {{ number_format($penaltyBreakdownData['vacation_offset_hours'] ?? 0, 1) }}
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Vacation Offset Hours</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-danger-600 dark:text-danger-400">
                                {{ number_format($penaltyBreakdownData['remaining_penalty_hours'] ?? 0, 1) }}
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Remaining Penalty Hours</div>
                        </div>
                    </div>

                    @if (($penaltyBreakdownData['direct_deduction_amount'] ?? 0) > 0)
                        <div
                            class="mt-4 p-3 bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-md">
                            <div class="flex items-center">
                                <iconify-icon icon="heroicons-outline:currency-dollar"
                                    class="text-danger-500 mr-2"></iconify-icon>
                                <span class="text-sm font-medium text-danger-700 dark:text-danger-300">
                                    Direct Deduction:
                                    {{ number_format($penaltyBreakdownData['direct_deduction_amount'], 2) }} EGP
                                </span>
                            </div>
                        </div>
                    @endif

                    @if (
                        ($penaltyBreakdownData['remaining_penalty_hours'] ?? 0) > 0 &&
                            !empty($penaltyBreakdownData['available_vacation_benefits'] ?? []))
                        <div
                            class="mt-4 p-3 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-md">
                            <div class="flex items-center">
                                <iconify-icon icon="heroicons-outline:exclamation-triangle"
                                    class="text-warning-500 mr-2"></iconify-icon>
                                <span class="text-sm font-medium text-warning-700 dark:text-warning-300">
                                    {{ number_format($penaltyBreakdownData['remaining_penalty_hours'], 1) }} hours can
                                    be offset using available vacation benefits
                                </span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Vacation Benefits Status -->
                <div>
                    <h4 class="text-base font-medium mb-3 border-b pb-2">Available Vacation Benefits</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                            <thead>
                                <tr>
                                    <th class="table-th">Benefit Name</th>
                                    <th class="table-th">Type</th>
                                    <th class="table-th">Current Balance</th>
                                    <th class="table-th">Max Balance</th>
                                    <th class="table-th">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse($employeeVacationBenefits as $benefit)
                                    <tr>
                                        <td class="table-td">{{ $benefit->name }}</td>
                                        <td class="table-td">{{ ucfirst($benefit->type) }}</td>
                                        <td class="table-td">
                                            <span
                                                class="@if ($benefit->current_balance > 0) text-success-500 @else text-danger-500 @endif">
                                                {{ number_format($benefit->current_balance, 1) }} hours
                                            </span>
                                        </td>
                                        <td class="table-td">{{ number_format($benefit->max_balance, 1) }} hours</td>
                                        <td class="table-td">
                                            @if ($benefit->current_balance > 0)
                                                <span class="badge bg-success-500 text-white">Available</span>
                                            @else
                                                <span class="badge bg-danger-500 text-white">Exhausted</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="table-td text-center py-4">No vacation benefits
                                            found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Applied Vacations in Period -->
                <div>
                    <h4 class="text-base font-medium mb-3 border-b pb-2">Applied Vacations in Payroll Period</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                            <thead>
                                <tr>
                                    <th class="table-th">Vacation Benefit</th>
                                    <th class="table-th">Total Hours</th>
                                    <th class="table-th">Days Count</th>
                                    <th class="table-th">Status</th>
                                    <th class="table-th">Used for Penalty</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse($employeeAppliedVacations as $appliedVacation)
                                    @php
                                        $totalHoursInPeriod = $appliedVacation->vacationDays->sum('hours');
                                        $daysCount = $appliedVacation->vacationDays->count();
                                    @endphp
                                    <tr>
                                        <td class="table-td">{{ $appliedVacation->vacationBenefit->name ?? 'N/A' }}
                                        </td>
                                        <td class="table-td">{{ number_format($totalHoursInPeriod, 1) }} hours</td>
                                        <td class="table-td">{{ $daysCount }} days</td>
                                        <td class="table-td">
                                            @if ($appliedVacation->status === 'approved')
                                                <span class="badge bg-success-500 text-white">Approved</span>
                                            @elseif($appliedVacation->status === 'pending')
                                                <span class="badge bg-warning-500 text-white">Pending</span>
                                            @else
                                                <span
                                                    class="badge bg-slate-500 text-white">{{ ucfirst($appliedVacation->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="table-td">
                                            @if (str_contains($appliedVacation->admin_note ?? '', 'Auto-created from attendance during payroll creation'))
                                                <span class="badge bg-info-500 text-white">Auto-Applied</span>
                                            @else
                                                <span class="badge bg-slate-500 text-white">Manual</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="table-td text-center py-4">No applied vacations in
                                            this period</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Available Vacation Benefits for Manual Application -->
                @if (!empty($penaltyBreakdownData['available_vacation_benefits'] ?? []))
                    <div>
                        <h4 class="text-base font-medium mb-3 border-b pb-2">Available Vacation Benefits for Penalty
                            Offset</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                                <thead>
                                    <tr>
                                        <th class="table-th">Benefit Name</th>
                                        <th class="table-th">Type</th>
                                        <th class="table-th">Current Balance</th>
                                        <th class="table-th">Max Applicable Hours</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @foreach ($penaltyBreakdownData['available_vacation_benefits'] as $benefit)
                                        <tr>
                                            <td class="table-td">{{ $benefit['vacation_benefit_name'] }}</td>
                                            <td class="table-td">{{ ucfirst($benefit['type']) }}</td>
                                            <td class="table-td">
                                                <span class="text-success-500">
                                                    {{ number_format($benefit['current_balance'], 1) }} hours
                                                </span>
                                            </td>
                                            <td class="table-td">
                                                <span class="text-info-500">
                                                    {{ number_format($benefit['max_applicable_hours'], 1) }} hours
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Penalty Offset Flow -->
                <div class="bg-info-50 dark:bg-info-900/20 border border-info-200 dark:border-info-800 rounded-lg p-4">
                    <h4 class="text-base font-medium mb-3 text-info-800 dark:text-info-200">How Penalties Were Handled
                    </h4>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-warning-500 text-white flex items-center justify-center text-sm font-bold mr-3">
                                1</div>
                            <div>
                                <div class="font-medium">Total Absence Hours Calculated</div>
                                <div class="text-sm text-slate-600 dark:text-slate-400">
                                    {{ number_format($penaltyBreakdownData['total_penalty_hours'] ?? 0, 1) }} hours
                                    based on attendance issues
                                </div>
                            </div>
                        </div>

                        @if (($penaltyBreakdownData['vacation_offset_hours'] ?? 0) > 0)
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 rounded-full bg-info-500 text-white flex items-center justify-center text-sm font-bold mr-3">
                                    2</div>
                                <div>
                                    <div class="font-medium">Existing Approved Vacations Used</div>
                                    <div class="text-sm text-slate-600 dark:text-slate-400">
                                        {{ number_format($penaltyBreakdownData['vacation_offset_hours'], 1) }} hours
                                        from previously approved vacation applications
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (($penaltyBreakdownData['remaining_penalty_hours'] ?? 0) > 0)
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 rounded-full bg-warning-500 text-white flex items-center justify-center text-sm font-bold mr-3">
                                    3</div>
                                <div>
                                    <div class="font-medium">Remaining Penalty Hours</div>
                                    <div class="text-sm text-slate-600 dark:text-slate-400">
                                        {{ number_format($penaltyBreakdownData['remaining_penalty_hours'], 1) }} hours
                                        @if (!empty($penaltyBreakdownData['available_vacation_benefits'] ?? []))
                                            - can be offset using vacation benefits or will be deducted
                                        @else
                                            - will be deducted
                                            ({{ number_format($penaltyBreakdownData['direct_deduction_amount'] ?? 0, 2) }}
                                            EGP)
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end">
                    <x-secondary-button wire:click="closePenaltyBreakdownModal">Close</x-secondary-button>
                </div>
            </x-slot>
        </x-modal>
    @endif

    <!-- Adjustment Edit Modal -->
    @if ($showAdjustmentModal)
        <x-modal wire:model="showAdjustmentModal">
            <x-slot name="title">Edit Employee Adjustment</x-slot>

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

                <div
                    class="bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-md p-3">
                    <p class="text-sm text-warning-600 dark:text-warning-400">
                        <strong>Note:</strong> Changing the adjustment will recalculate the employee's net amount and
                        update the payroll total.
                    </p>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end space-x-3">
                    <x-secondary-button wire:click="closeAdjustmentModal">Cancel</x-secondary-button>
                    <x-primary-button wire:click="saveAdjustment" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveAdjustment">Update Adjustment</span>
                        <span wire:loading wire:target="saveAdjustment">Updating...</span>
                    </x-primary-button>
                </div>
            </x-slot>
        </x-modal>
    @endif

    <!-- Vacation Application Modal -->
    @if ($showVacationApplicationModal)
        <x-modal wire:model="showVacationApplicationModal">
            <x-slot name="title">Apply Vacation for Penalty Offset</x-slot>

            <div class="space-y-4">
                <div class="bg-info-50 dark:bg-info-900/20 border border-info-200 dark:border-info-800 rounded-md p-3">
                    <p class="text-sm text-info-600 dark:text-info-400">
                        <strong>Employee:</strong> {{ $selectedPenaltyEmployee->employee->name ?? 'N/A' }}<br>
                        <strong>Remaining Penalty Hours:</strong> {{ number_format($remainingPenaltyHours, 1) }}
                        hours<br>
                        <strong>Payroll Period:</strong>
                        {{ \Carbon\Carbon::parse($payroll->start_date)->format('M d, Y') }} -
                        {{ \Carbon\Carbon::parse($payroll->end_date)->format('M d, Y') }}
                    </p>
                </div>

                <div>
                    <label class="form-label">Select Vacation Benefit</label>
                    <select wire:model.live="selectedVacationBenefitId" class="form-control">
                        <option value="">Choose a vacation benefit...</option>
                        @foreach ($availableVacationBenefits as $benefit)
                            <option value="{{ $benefit['vacation_benefit_id'] }}">
                                {{ $benefit['vacation_benefit_name'] }}
                                ({{ ucfirst($benefit['type']) }})
                                -
                                Available: {{ number_format($benefit['current_balance'], 1) }}h
                            </option>
                        @endforeach
                    </select>
                </div>

                @if ($selectedVacationBenefitId)
                    <div>
                        <label class="form-label">Hours to Apply</label>
                        <input type="number" wire:model="vacationHoursToApply" class="form-control" min="0.5"
                            max="{{ $maxApplicableHours }}" step="0.5" placeholder="Enter hours to apply">
                        <p class="text-xs text-slate-500 mt-1">
                            Maximum applicable: {{ number_format($maxApplicableHours, 1) }} hours
                        </p>
                    </div>

                    <div
                        class="bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-md p-3">
                        <p class="text-sm text-warning-600 dark:text-warning-400">
                            <strong>Note:</strong> This will create an approved vacation application for the specified
                            hours during the payroll period.
                            The vacation will be automatically distributed across working days in the period.
                        </p>
                    </div>
                @endif
            </div>

            <x-slot name="footer">
                <div class="flex justify-end space-x-3">
                    <x-secondary-button wire:click="closeVacationApplicationModal">Cancel</x-secondary-button>
                    @if ($selectedVacationBenefitId && $vacationHoursToApply > 0)
                        <x-primary-button wire:click="applyVacationForPenalty" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="applyVacationForPenalty">Apply Vacation</span>
                            <span wire:loading wire:target="applyVacationForPenalty">Applying...</span>
                        </x-primary-button>
                    @endif
                </div>
            </x-slot>
        </x-modal>
    @endif

    <div wire:loading wire:target="approvePayroll"
        class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
        tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog">
        <div class="modal-dialog relative w-auto pointer-events-none">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75" role="dialog"
                aria-labelledby="vertically_center" aria-modal="true" style="z-index: 500">

                <div class="text-center">
                    <div class="text-success-500 ">
                        <iconify-icon icon="svg-spinners:180-ring" style="font-size: 5rem;"></iconify-icon>
                    </div>
                    <div class="text-slate-800">
                        <h5 class="text-lg font-medium">approving payroll</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
