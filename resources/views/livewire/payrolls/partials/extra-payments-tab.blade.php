<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
        <thead>
            <tr>
                <th class="table-th">Employee</th>
                <th class="table-th">Payment Name</th>
                <th class="table-th">Amount</th>
                <th class="table-th">Due Date</th>
                <th class="table-th">Status</th>
                <th class="table-th">Description</th>
                <th class="table-th">Created At</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            @forelse($extraPayments as $payment)
                <tr wire:key="payment-{{ $payment->id }}">
                    <td class="table-td">
                        <div class="flex items-center">
                            <div class="flex-none">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                    <span class="text-slate-600 dark:text-slate-300 text-sm font-medium">
                                        {{ substr($payment->employee->name ?? 'N/A', 0, 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h6 class="text-sm font-medium text-slate-900 dark:text-white">
                                    {{ $payment->employee->name ?? 'N/A' }}
                                </h6>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $payment->employee->position->name ?? 'No Position' }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="table-td">
                        <div class="text-sm font-medium text-slate-900 dark:text-white">
                            {{ $payment->name }}
                        </div>
                    </td>
                    <td class="table-td">
                        <div class="text-sm font-semibold 
                            @if($payment->amount >= 0) text-success-500 @else text-danger-500 @endif">
                            @if($payment->amount >= 0) + @endif{{ number_format($payment->amount, 2) }} EGP
                        </div>
                    </td>
                    <td class="table-td">
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            {{ \Carbon\Carbon::parse($payment->due_date)->format('d M Y') }}
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            {{ \Carbon\Carbon::parse($payment->due_date)->format('l') }}
                        </div>
                    </td>
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
                            <span class="badge bg-slate-500 text-white">{{ ucfirst($payment->status) }}</span>
                        @endif
                    </td>
                    <td class="table-td">
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            {{ $payment->desc ?? '-' }}
                        </div>
                    </td>
                    <td class="table-td">
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            {{ $payment->created_at->format('d M Y H:i') }}
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="table-td text-center py-8">
                        <div class="flex flex-col items-center">
                            <iconify-icon icon="heroicons-outline:currency-dollar" class="text-5xl text-slate-400 mb-2"></iconify-icon>
                            <h5 class="text-xl font-medium text-slate-400">No extra payments found</h5>
                            <p class="text-sm text-slate-400 mt-1">This payroll doesn't have any extra payment records</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(isset($extraPayments) && $extraPayments->count() > 0)
            <tfoot class="bg-slate-100 dark:bg-slate-700">
                <tr class="font-semibold">
                    <td class="table-td border-t-2 border-slate-200 dark:border-slate-600" colspan="2">
                        <div class="text-sm font-bold">
                            Total Extra Payments ({{ $extraPayments->total() }} records)
                        </div>
                    </td>
                    <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                        <div class="text-sm font-bold 
                            @if($extraPayments->sum('amount') >= 0) text-success-600 dark:text-success-400 @else text-danger-600 dark:text-danger-400 @endif">
                            @if($extraPayments->sum('amount') >= 0) + @endif{{ number_format($extraPayments->sum('amount'), 2) }} EGP
                        </div>
                    </td>
                    <td class="table-td border-t-2 border-slate-200 dark:border-slate-600" colspan="4">
                        <div class="text-sm">-</div>
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>

@if(isset($extraPayments))
    <div class="mt-6">
        {{ $extraPayments->links('vendor.livewire.simple-bootstrap') }}
    </div>
@endif 