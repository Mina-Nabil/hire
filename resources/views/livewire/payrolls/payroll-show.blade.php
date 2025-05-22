<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Payroll Details</h4>
            <div>
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

            @if ($payroll->status === \App\Models\Benefits\Payrolls\Payroll::STATUS_PENDING)
                <div class="flex justify-end mb-6">
                    <button type="button" class="btn btn-success" wire:click="approvePayroll"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="approvePayroll">
                            <span class="flex items-center">
                                <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2"
                                    icon="heroicons-outline:check"></iconify-icon>
                                Approve Payroll
                            </span>
                        </span>
                        <span wire:loading wire:target="approvePayroll">Processing...</span>
                    </button>
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <div class="flex-1 mr-4">
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search employees..."
                            class="form-control py-2 pl-10">
                        <span class="absolute left-2 top-2 text-lg">
                            <iconify-icon icon="heroicons-outline:search"></iconify-icon>
                        </span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 no-wrap">
                    <thead>
                        <tr>
                            <th class="table-th">Employee</th>
                            <th class="table-th">Position</th>
                            <th class="table-th">Gross Salary</th>
                            <th class="table-th">Insurance Amount</th>
                            <th class="table-th">Other Amount</th>
                            <th class="table-th">Penalties</th>
                            <th class="table-th">Extra Payments</th>
                            <th class="table-th">Overtime</th>
                            <th class="table-th">Net Amount</th>
                            <th class="table-th">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                        @forelse($payrollEmployees as $payrollEmployee)
                            <tr wire:key="employee-{{ $payrollEmployee->id }}">
                                <td class="table-td">
                                    <div class="flex items-center">
                                        <div class="flex-none">
                                            <div
                                                class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                                <span class="text-slate-600 dark:text-slate-300 text-sm font-medium">
                                                    {{ substr($payrollEmployee->employee->name ?? 'N/A', 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <h6 class="text-sm font-medium text-slate-900 dark:text-white">
                                                {{ $payrollEmployee->employee->name ?? 'N/A' }}
                                            </h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="table-td">
                                    <div class="flex-1 text-start">
                                        <h4 class="text-sm font-medium text-slate-600 whitespace-nowrap">
                                            {{ $payrollEmployee->position }}
                                        </h4>
                                        <div class="text-xs font-normal text-slate-600 dark:text-slate-400">
                                            {{ $payrollEmployee->department }}
                                        </div>
                                    </div>
                                </td>
                                <td class="table-td">{{ number_format($payrollEmployee->gross_salary, 2) }}</td>
                                <td class="table-td">{{ number_format($payrollEmployee->insurance_amount, 2) }}</td>
                                <td class="table-td">{{ number_format($payrollEmployee->other_amount, 2) }}</td>
                                <td class="table-td">
                                    <div class="flex flex-col">
                                        <span>{{ number_format($payrollEmployee->penalties_days, 2) }} days</span>
                                        <span
                                            class="text-xs text-danger-500">-{{ number_format($payrollEmployee->penalties_amount, 2) }}
                                            <small>EGP</small></span>
                                    </div>
                                </td>
                                <td class="table-td">{{ number_format($payrollEmployee->extra_payments, 2) }}</td>
                                <td class="table-td">
                                    <div class="flex flex-col">
                                        <span>{{ number_format($payrollEmployee->overtime_hours, 2) }} hours</span>
                                        <span
                                            class="text-xs text-success-500">+{{ number_format($payrollEmployee->overtime_amount, 2) }}
                                            <small>EGP</small></span>
                                    </div>
                                </td>
                                <td class="table-td font-semibold">
                                    {{ number_format($payrollEmployee->net_after_deductions, 2) }}</td>
                                <td class="table-td">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                        wire:click="showEmployeeDetails({{ $payrollEmployee->id }})">
                                        <span class="flex items-center">
                                            <iconify-icon icon="heroicons-outline:eye" class="text-base ltr:mr-1 rtl:ml-1"></iconify-icon>
                                            Details
                                        </span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="table-td text-center py-8">
                                    <div class="flex flex-col items-center">
                                        <iconify-icon icon="heroicons-outline:user-group"
                                            class="text-5xl text-slate-400 mb-2"></iconify-icon>
                                        <h5 class="text-xl font-medium text-slate-400">No employees found</h5>
                                        <p class="text-sm text-slate-400 mt-1">This payroll doesn't have any employee
                                            records</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $payrollEmployees->links() }}
            </div>
        </div>
    </div>

    <!-- Employee Details Modal -->
    @if($showEmployeeDetailsModal && $selectedPayrollEmployee)
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
                        <div class="text-sm font-semibold">{{ number_format($selectedPayrollEmployee->gross_salary, 2) }}</div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                        <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Insurance Amount</h5>
                        <div class="text-sm font-semibold">{{ number_format($selectedPayrollEmployee->insurance_amount, 2) }}</div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                        <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Other Amount</h5>
                        <div class="text-sm font-semibold">{{ number_format($selectedPayrollEmployee->other_amount, 2) }}</div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                        <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Employee Insurance</h5>
                        <div class="text-sm font-semibold">{{ number_format($selectedPayrollEmployee->employee_insurance, 2) }}</div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                        <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Employer Insurance</h5>
                        <div class="text-sm font-semibold">{{ number_format($selectedPayrollEmployee->employer_insurance, 2) }}</div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                        <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Total Insurance</h5>
                        <div class="text-sm font-semibold">{{ number_format($selectedPayrollEmployee->total_insurance, 2) }}</div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                        <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Penalty Days</h5>
                        <div class="text-sm font-semibold">{{ number_format($selectedPayrollEmployee->penalties_days, 2) }}</div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                        <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Penalty Amount</h5>
                        <div class="text-sm font-semibold text-danger-500">-{{ number_format($selectedPayrollEmployee->penalties_amount, 2) }}</div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                        <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Net After Penalty</h5>
                        <div class="text-sm font-semibold">{{ number_format($selectedPayrollEmployee->net_after_penalty, 2) }}</div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                        <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Extra Payments</h5>
                        <div class="text-sm font-semibold">{{ number_format($selectedPayrollEmployee->extra_payments, 2) }}</div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                        <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Overtime Hours</h5>
                        <div class="text-sm font-semibold">{{ number_format($selectedPayrollEmployee->overtime_hours, 2) }}</div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                        <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Overtime Amount</h5>
                        <div class="text-sm font-semibold text-success-500">+{{ number_format($selectedPayrollEmployee->overtime_amount, 2) }}</div>
                    </div>
                    <div class="bg-primary-50 dark:bg-primary-900/20 rounded-md p-3 md:col-span-6">
                        <h5 class="text-xs font-medium text-primary-500 dark:text-primary-400 mb-1">Net Amount</h5>
                        <div class="text-lg font-semibold text-primary-600 dark:text-primary-400">{{ number_format($selectedPayrollEmployee->net_after_deductions, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Tabs for different sections -->
            <div x-data="{ activeTab: 'attendance' }">
                <div class="border-b border-slate-200 dark:border-slate-700">
                    <ul class="flex flex-wrap -mb-px">
                        <li class="mr-2">
                            <button @click="activeTab = 'attendance'" 
                                :class="activeTab === 'attendance' ? 'border-primary-500 text-primary-500' : 'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300'"
                                class="inline-block py-2 px-4 text-sm font-medium border-b-2">
                                Attendance Records
                            </button>
                        </li>
                        <li class="mr-2">
                            <button @click="activeTab = 'benefits'"
                                :class="activeTab === 'benefits' ? 'border-primary-500 text-primary-500' : 'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300'"
                                class="inline-block py-2 px-4 text-sm font-medium border-b-2">
                                Benefit Payments
                            </button>
                        </li>
                        <li class="mr-2">
                            <button @click="activeTab = 'overtime'"
                                :class="activeTab === 'overtime' ? 'border-primary-500 text-primary-500' : 'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300'"
                                class="inline-block py-2 px-4 text-sm font-medium border-b-2">
                                Overtime
                            </button>
                        </li>
                        <li>
                            <button @click="activeTab = 'extras'"
                                :class="activeTab === 'extras' ? 'border-primary-500 text-primary-500' : 'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300'"
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
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse($employeeAttendance as $attendance)
                                    <tr>
                                        <td class="table-td">{{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</td>
                                        <td class="table-td">{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                                        <td class="table-td">{{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i A') : 'N/A' }}</td>
                                        <td class="table-td">{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i A') : 'N/A' }}</td>
                                        <td class="table-td">
                                            <span class="
                                            @if($attendance->hours < $attendance->employee->benefitConfiguration->daily_working_hours) text-danger-500  @endif
                                            @if($attendance->hours > $attendance->employee->benefitConfiguration->daily_working_hours) text-success-500  @endif
                                            ">
                                                {{ $attendance->hours ?? 'N/A' }}
                                                <small>Hours</small>
                                            </span>
                                        </td>
                                        <td class="table-td">
                                            @if($attendance->status === 'present')
                                                <span class="badge bg-success-500 text-white">Present</span>
                                            @elseif($attendance->status === 'absent')
                                                <span class="badge bg-danger-500 text-white">Absent</span>
                                            @elseif($attendance->status === 'late')
                                                <span class="badge bg-warning-500 text-white">Late</span>
                                            @else
                                                <span class="badge @if($attendance->is_approved) bg-success-500 @else bg-slate-500 @endif text-white">
                                                    {{ ucfirst($attendance->is_approved ? 'Approved' : 'Pending') }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="table-td text-center py-4">No attendance records found</td>
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
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse($employeeBenefitPayments as $benefit)
                                    <tr>
                                        <td class="table-td">{{ $benefit->baseBenefit->name ?? 'N/A' }}</td>
                                        <td class="table-td">{{ ucfirst($benefit->baseBenefit->type ?? 'N/A') }}</td>
                                        <td class="table-td">{{ number_format($benefit->amount, 2) }}</td>
                                        <td class="table-td">{{ ucfirst($benefit->status ?? 'N/A') }}</td>
                                        <td class="table-td">{{ $benefit->created_at->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="table-td text-center py-4">No benefit payments found</td>
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
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse($employeeOvertimes as $overtime)
                                    <tr>
                                        <td class="table-td">{{ \Carbon\Carbon::parse($overtime->date)->format('d M Y') }}</td>
                                        <td class="table-td">{{ $overtime->hours }}</td>
                                        <td class="table-td">{{ $overtime->rate ?? 'Standard' }}</td>
                                        <td class="table-td">
                                            <span class="badge bg-success-500 text-white">{{ ucfirst($overtime->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="table-td text-center py-4">No overtime records found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse($employeeExtraPayments as $payment)
                                    <tr>
                                        <td class="table-td">{{ $payment->description }}</td>
                                        <td class="table-td">{{ number_format($payment->amount, 2) }}</td>
                                        <td class="table-td">{{ \Carbon\Carbon::parse($payment->due_date)->format('d M Y') }}</td>
                                        <td class="table-td">
                                            <span class="badge bg-success-500 text-white">{{ ucfirst($payment->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="table-td text-center py-4">No extra payments found</td>
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
</div>
