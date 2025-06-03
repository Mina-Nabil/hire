<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
        <thead>
            <tr>
                <th class="table-th">Employee</th>
                <th class="table-th">Benefit Name</th>
                <th class="table-th">Benefit Type</th>
                <th class="table-th">Amount</th>
                <th class="table-th">Status</th>
                <th class="table-th">Description</th>
                <th class="table-th">Created At</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            @forelse($benefitPayments as $benefit)
                <tr wire:key="benefit-{{ $benefit->id }}">
                    <td class="table-td">
                        <div class="flex items-center">
                            <div class="flex-none">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                    <span class="text-slate-600 dark:text-slate-300 text-sm font-medium">
                                        {{ substr($benefit->employee->name ?? 'N/A', 0, 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h6 class="text-sm font-medium text-slate-900 dark:text-white">
                                    {{ $benefit->employee->name ?? 'N/A' }}
                                </h6>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $benefit->employee->position->name ?? 'No Position' }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="table-td">
                        <div class="text-sm font-medium text-slate-900 dark:text-white">
                            {{ $benefit->baseBenefit->name ?? 'N/A' }}
                        </div>
                    </td>
                    <td class="table-td">
                        <span class="badge 
                            @if(($benefit->baseBenefit->type ?? '') === 'employee_base') bg-primary-500 
                            @elseif(($benefit->baseBenefit->type ?? '') === 'other_base') bg-info-500 
                            @elseif(($benefit->baseBenefit->type ?? '') === 'medical') bg-success-500 
                            @else bg-slate-500 @endif text-white">
                            {{ ucfirst(str_replace('_', ' ', $benefit->baseBenefit->type ?? 'Unknown')) }}
                        </span>
                    </td>
                    <td class="table-td">
                        <div class="text-sm font-semibold">
                            {{ number_format($benefit->amount, 2) }} EGP
                        </div>
                    </td>
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
                            <span class="badge bg-slate-500 text-white">{{ ucfirst($benefit->status) }}</span>
                        @endif
                    </td>
                    <td class="table-td">
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            {{ $benefit->desc ?? '-' }}
                        </div>
                    </td>
                    <td class="table-td">
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            {{ $benefit->created_at->format('d M Y H:i') }}
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="table-td text-center py-8">
                        <div class="flex flex-col items-center">
                            <iconify-icon icon="heroicons-outline:gift" class="text-5xl text-slate-400 mb-2"></iconify-icon>
                            <h5 class="text-xl font-medium text-slate-400">No benefit payments found</h5>
                            <p class="text-sm text-slate-400 mt-1">This payroll doesn't have any benefit payment records</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(isset($benefitPayments) && $benefitPayments->count() > 0)
            <tfoot class="bg-slate-100 dark:bg-slate-700">
                <tr class="font-semibold">
                    <td class="table-td border-t-2 border-slate-200 dark:border-slate-600" colspan="3">
                        <div class="text-sm font-bold">
                            Total Benefits ({{ $benefitPayments->total() }} records)
                        </div>
                    </td>
                    <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                        <div class="text-sm font-bold text-primary-600 dark:text-primary-400">
                            {{ number_format($benefitPayments->sum('amount'), 2) }} EGP
                        </div>
                    </td>
                    <td class="table-td border-t-2 border-slate-200 dark:border-slate-600" colspan="3">
                        <div class="text-sm">-</div>
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>

@if(isset($benefitPayments))
    <div class="mt-6">
        {{ $benefitPayments->links('vendor.livewire.simple-bootstrap') }}
    </div>
@endif 