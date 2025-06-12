<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 no-wrap">
        <thead>
            <tr>
                <th class="table-th">Employee</th>
                <th class="table-th">Date</th>
                <th class="table-th">Start Time</th>
                <th class="table-th">End Time</th>
                <th class="table-th">Hours</th>
                <th class="table-th">Status</th>
                <th class="table-th">Admin Note</th>
                <th class="table-th">Approved At</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            @forelse($overtimes as $overtime)
                <tr wire:key="overtime-{{ $overtime->id }}">
                    <td class="table-td">
                        <div class="flex items-center">
                            <div class="flex-none">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                    <span class="text-slate-600 dark:text-slate-300 text-sm font-medium">
                                        {{ substr($overtime->employee->name ?? 'N/A', 0, 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h6 class="text-sm font-medium text-slate-900 dark:text-white">
                                    {{ $overtime->employee->name ?? 'N/A' }}
                                </h6>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $overtime->employee->position->name ?? 'No Position' }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="table-td">
                        <div class="text-sm font-medium text-slate-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($overtime->date)->format('d M Y') }}
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            {{ \Carbon\Carbon::parse($overtime->date)->format('l') }}
                        </div>
                    </td>
                    <td class="table-td">
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            {{ $overtime->start_time ? \Carbon\Carbon::parse($overtime->start_time)->format('H:i A') : 'N/A' }}
                        </div>
                    </td>
                    <td class="table-td">
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            {{ $overtime->end_time ? \Carbon\Carbon::parse($overtime->end_time)->format('H:i A') : 'N/A' }}
                        </div>
                    </td>
                    <td class="table-td">
                        <div class="text-sm font-semibold text-success-500">
                            {{ number_format($overtime->hours, 2) }} hours
                        </div>
                    </td>
                    <td class="table-td">
                        @if ($overtime->status === 'pending')
                            <span class="badge bg-warning-500 text-white">Pending</span>
                        @elseif($overtime->status === 'approved')
                            <span class="badge bg-success-500 text-white">Approved</span>
                        @elseif($overtime->status === 'rejected')
                            <span class="badge bg-danger-500 text-white">Rejected</span>
                        @else
                            <span class="badge bg-slate-500 text-white">{{ ucfirst($overtime->status) }}</span>
                        @endif
                    </td>
                    <td class="table-td">
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            {{ $overtime->admin_note ?? '-' }}
                        </div>
                    </td>
                    <td class="table-td">
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            {{ $overtime->approved_at ? \Carbon\Carbon::parse($overtime->approved_at)->format('d M Y H:i') : '-' }}
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="table-td text-center py-8">
                        <div class="flex flex-col items-center">
                            <iconify-icon icon="heroicons-outline:clock" class="text-5xl text-slate-400 mb-2"></iconify-icon>
                            <h5 class="text-xl font-medium text-slate-400">No overtime records found</h5>
                            <p class="text-sm text-slate-400 mt-1">This payroll doesn't have any overtime records</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(isset($overtimes) && $overtimes->count() > 0)
            <tfoot class="bg-slate-100 dark:bg-slate-700">
                <tr class="font-semibold">
                    <td class="table-td border-t-2 border-slate-200 dark:border-slate-600" colspan="4">
                        <div class="text-sm font-bold">
                            Total Overtime ({{ $overtimes->total() }} records)
                        </div>
                    </td>
                    <td class="table-td border-t-2 border-slate-200 dark:border-slate-600">
                        <div class="text-sm font-bold text-success-600 dark:text-success-400">
                            {{ number_format($overtimes->sum('hours'), 2) }} hours
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

@if(isset($overtimes))
    <div class="mt-6">
        {{ $overtimes->links('vendor.livewire.simple-bootstrap') }}
    </div>
@endif 