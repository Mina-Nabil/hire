<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 no-wrap">
        <thead>
            <tr>
                <th class="table-th border sticky-colomn border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                    Employee</th>
                <th class="table-th">Position</th>
                <th class="table-th">Gross Salary</th>
                <th class="table-th">Social Insurance Salary</th>
                <th class="table-th">Other Amount</th>
                <th class="table-th">Absence</th>
                <th class="table-th">Penalty</th>
                <th class="table-th">Extra Payments</th>
                <th class="table-th">Overtime</th>
                <th class="table-th">Adjustment</th>
                <th class="table-th">Employee Insurance</th>
                <th class="table-th">Before Tax</th>
                <th class="table-th">Tax Amount</th>
                <th class="table-th">Net After Tax Amount</th>
                <th class="table-th">Employer Insurance</th>
                <th class="table-th">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            @forelse($payrollEmployees as $payrollEmployee)
                <tr wire:key="employee-{{ $payrollEmployee->id }}">
                    <td class="table-td sticky-colomn border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
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
                    <td class="table-td">{{ number_format($payrollEmployee->employee_insurance, 2) }}</td>
                    <td class="table-td">{{ number_format($payrollEmployee->other_amount, 2) }}</td>
                    <td class="table-td">
                        <div class="flex flex-col space-y-1">
                            @if (($payrollEmployee->total_penalty_hours ?? 0) > 0)
                                <!-- Total Penalty Hours -->
                                <div class="text-xs text-slate-600 flex item-center gap-2">
                                    Total: {{ number_format($payrollEmployee->total_penalty_hours ?? 0, 1) }}h
                                </div>

                                <!-- Vacation Offset -->
                                @if (($payrollEmployee->vacation_offset_hours ?? 0) > 0)
                                    <div class="text-xs text-info-500 flex item-center gap-2">
                                        <iconify-icon icon="heroicons-outline:calendar"
                                            class="inline w-3 h-3"></iconify-icon>
                                        Vacation: {{ number_format($payrollEmployee->vacation_offset_hours, 1) }}h
                                    </div>
                                @endif

                                <!-- New Vacation Applied -->
                                @if (($payrollEmployee->new_vacation_hours ?? 0) > 0)
                                    <div class="text-xs text-success-500 flex item-center gap-2">
                                        <iconify-icon icon="heroicons-outline:plus-circle"
                                            class="inline w-3 h-3"></iconify-icon>
                                        New Vacation: {{ number_format($payrollEmployee->new_vacation_hours, 1) }}h
                                    </div>
                                @endif

                                <!-- Direct Deduction -->
                                @if (($payrollEmployee->direct_deduction_hours ?? 0) > 0)
                                    <div class="text-xs text-danger-500 flex item-center gap-2">
                                        <iconify-icon icon="heroicons-outline:minus-circle"
                                            class="inline w-3 h-3"></iconify-icon>
                                        Deduction: {{ number_format($payrollEmployee->direct_deduction_hours, 1) }}h
                                        (-{{ number_format($payrollEmployee->direct_deduction_amount ?? 0, 2) }} EGP)
                                    </div>
                                @endif

                                <!-- Legacy penalty display for backward compatibility -->
                                @if (($payrollEmployee->penalties_days ?? 0) > 0 && ($payrollEmployee->total_penalty_hours ?? 0) == 0)
                                    <span>{{ number_format($payrollEmployee->penalties_days, 2) }} days</span>
                                    <span
                                        class="text-xs text-danger-500">-{{ number_format($payrollEmployee->penalties_amount, 2) }}
                                        EGP</span>
                                @endif
                            @else
                                <span class="text-xs text-success-500">No penalties</span>
                            @endif
                        </div>
                    </td>
                    <td class="table-td">
                        <div class="flex flex-col">
                            <span class="text-xs text-danger-500">{{ number_format($payrollEmployee->penalties_amount ?? 0, 1) }} EGP</span>
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
                    <td class="table-td">
                        <div class="flex flex-col">
                            <span>{{ number_format($payrollEmployee->adj_amount, 2) }} EGP</span>
                            @if (!empty($payrollEmployee->adj_desc))
                                <span
                                    class="text-xs text-slate-500">{{ Str::limit($payrollEmployee->adj_desc, 30) }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="table-td font-semibold">
                        {{ number_format($payrollEmployee->net_after_deductions, 2) }}</td>
                    <td class="table-td">{{ number_format($payrollEmployee->tax_amount, 2) }}</td>
                    <td class="table-td">{{ number_format($payrollEmployee->after_tax_salary, 2) }}</td>
                    <td class="table-td">{{ number_format($payrollEmployee->employer_insurance, 2) }}</td>
                    <td class="table-td">
                        <div class="flex space-x-2">
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                wire:click="showEmployeeDetails({{ $payrollEmployee->id }})">
                                <span class="flex items-center">
                                    <iconify-icon icon="heroicons-outline:eye"
                                        class="text-base ltr:mr-1 rtl:ml-1"></iconify-icon>
                                    Details
                                </span>
                            </button>

                            @if (($payrollEmployee->total_penalty_hours ?? 0) > 0)
                                <button type="button" class="btn btn-sm btn-outline-info"
                                    wire:click="showPenaltyBreakdown({{ $payrollEmployee->id }})">
                                    <span class="flex items-center">
                                        <iconify-icon icon="heroicons-outline:chart-bar"
                                            class="text-base ltr:mr-1 rtl:ml-1"></iconify-icon>
                                        Penalties
                                    </span>
                                </button>
                            @endif

                            @if ($payroll->status === \App\Models\Benefits\Payrolls\Payroll::STATUS_PENDING)
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    wire:click="openAdjustmentModal({{ $payrollEmployee->id }})">
                                    <span class="flex items-center">
                                        <iconify-icon icon="heroicons-outline:pencil"
                                            class="text-base ltr:mr-1 rtl:ml-1"></iconify-icon>
                                        Adjust
                                    </span>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="table-td text-center py-8">
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
        <tfoot class="bg-slate-100 dark:bg-slate-700">
            <tr class="font-semibold">
                <td class="table-td sticky-colomn border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                    <div class="text-sm font-bold">
                        Total ({{ $totals['total_employees'] }} employees)
                    </div>
                </td>
                <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                    <div class="text-sm">-</div>
                </td>
                <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                    <div class="text-sm font-bold">
                        {{ number_format($totals['gross_salary'], 2) }}
                    </div>
                </td>
                <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                    <div class="text-sm font-bold">
                        {{ number_format($totals['insurance_amount'], 2) }}
                    </div>
                </td>
                <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                    <div class="text-sm font-bold">
                        {{ number_format($totals['other_amount'], 2) }}
                    </div>
                </td>
                <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                    <div class="text-sm">-</div>
                </td>
                <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                    <div class="flex flex-col">
                        <span class="text-sm font-bold">{{ number_format($totals['total_penalty_hours'], 2) }} hours</span>
                        <span
                            class="text-xs text-danger-500 font-bold">-{{ number_format($totals['penalties_amount'], 2) }}
                            EGP</span>
                    </div>
                </td>
                <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                    <div class="text-sm font-bold">
                        {{ number_format($totals['extra_payments'], 2) }}
                    </div>
                </td>
                <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                    <div class="flex flex-col">
                        <span class="text-sm font-bold">{{ number_format($totals['overtime_hours'], 2) }} hours</span>
                        <span
                            class="text-xs text-success-500 font-bold">+{{ number_format($totals['overtime_amount'], 2) }}
                            EGP</span>
                    </div>
                </td>
                <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                    <div class="flex flex-col">
                        <span class="text-sm font-bold">
                            @if ($totals['adj_amount'] >= 0)
                                <span class="text-success-500">+{{ number_format($totals['adj_amount'], 2) }}
                                    EGP</span>
                            @else
                                <span class="text-danger-500">{{ number_format($totals['adj_amount'], 2) }} EGP</span>
                            @endif
                        </span>
                    </div>
                </td>
                <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                    <div class="text-sm font-bold text-primary-600 dark:text-primary-400">
                        {{ number_format($totals['net_after_deductions'], 2) }}
                    </div>
                </td>
                <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                    <div class="text-sm font-bold">
                        {{ number_format($totals['tax_amount'], 2) }}
                    </div>
                </td>
                <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                    <div class="text-sm font-bold">
                        {{ number_format($totals['after_tax_salary'], 2) }}
                    </div>
                </td>
              
                <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                    <div class="text-sm font-bold">
                        {{ number_format($totals['employer_insurance'], 2) }}
                    </div>
                </td>
                <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                </td>
            </tr>
        </tfoot>
    </table>
</div>

@if (isset($payrollEmployees))
    <div class="mt-6">
        {{ $payrollEmployees->links('vendor.livewire.simple-bootstrap') }}
    </div>
@endif
