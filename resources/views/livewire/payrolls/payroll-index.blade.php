<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Payrolls</h4>
        </div>
        <div class="card-body p-6">
            <div class="flex justify-between items-center mb-6">
                <div class="flex-1 mr-4">
                    <div class="relative">
                        <input type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Search payrolls..." 
                            class="form-control py-2 pl-10">
                        <span class="absolute left-2 top-2 text-lg">
                            <iconify-icon icon="heroicons-outline:search"></iconify-icon>
                        </span>
                    </div>
                </div>
                
                <div>
                    <a href="{{ route('payrolls.create') }}" class="btn btn-primary">
                        <span class="flex items-center">
                            <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="heroicons-outline:plus"></iconify-icon>
                            Create Payroll
                        </span>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('id')" class="table-th">
                                <div class="flex items-center">
                                    ID
                                    @if($sortField === 'id')
                                        <span class="ml-1">
                                            @if($sortDirection === 'asc')
                                                <iconify-icon icon="heroicons-outline:chevron-up"></iconify-icon>
                                            @else
                                                <iconify-icon icon="heroicons-outline:chevron-down"></iconify-icon>
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('start_date')" class="table-th">
                                <div class="flex items-center">
                                    Period
                                    @if($sortField === 'start_date')
                                        <span class="ml-1">
                                            @if($sortDirection === 'asc')
                                                <iconify-icon icon="heroicons-outline:chevron-up"></iconify-icon>
                                            @else
                                                <iconify-icon icon="heroicons-outline:chevron-down"></iconify-icon>
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('total_employees')" class="table-th">
                                <div class="flex items-center">
                                    Employees
                                    @if($sortField === 'total_employees')
                                        <span class="ml-1">
                                            @if($sortDirection === 'asc')
                                                <iconify-icon icon="heroicons-outline:chevron-up"></iconify-icon>
                                            @else
                                                <iconify-icon icon="heroicons-outline:chevron-down"></iconify-icon>
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('total_paid')" class="table-th">
                                <div class="flex items-center">
                                    Total Paid
                                    @if($sortField === 'total_paid')
                                        <span class="ml-1">
                                            @if($sortDirection === 'asc')
                                                <iconify-icon icon="heroicons-outline:chevron-up"></iconify-icon>
                                            @else
                                                <iconify-icon icon="heroicons-outline:chevron-down"></iconify-icon>
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('status')" class="table-th">
                                <div class="flex items-center">
                                    Status
                                    @if($sortField === 'status')
                                        <span class="ml-1">
                                            @if($sortDirection === 'asc')
                                                <iconify-icon icon="heroicons-outline:chevron-up"></iconify-icon>
                                            @else
                                                <iconify-icon icon="heroicons-outline:chevron-down"></iconify-icon>
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('created_at')" class="table-th">
                                <div class="flex items-center">
                                    Created At
                                    @if($sortField === 'created_at')
                                        <span class="ml-1">
                                            @if($sortDirection === 'asc')
                                                <iconify-icon icon="heroicons-outline:chevron-up"></iconify-icon>
                                            @else
                                                <iconify-icon icon="heroicons-outline:chevron-down"></iconify-icon>
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                        @forelse($payrolls as $payroll)
                            <tr wire:key="payroll-{{ $payroll->id }}" 
                                class="hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer"
                                onclick="window.location='{{ route('payrolls.show', $payroll->id) }}'">
                                <td class="table-td">{{ $payroll->id }}</td>
                                <td class="table-td">
                                    <div class="flex flex-col">
                                        <span>{{ \Carbon\Carbon::parse($payroll->start_date)->format('M d, Y') }}
                                        <span class="text-xs text-slate-500">-></span>
                                        {{ \Carbon\Carbon::parse($payroll->end_date)->format('M d, Y') }}</span>
                                    </div>
                                </td>
                                <td class="table-td">{{ $payroll->total_employees }}</td>
                                <td class="table-td">{{ number_format($payroll->total_paid, 2) }}</td>
                                <td class="table-td">
                                    @if($payroll->status === \App\Models\Benefits\Payrolls\Payroll::STATUS_APPROVED)
                                        <span class="badge bg-success-500 text-white">Approved</span>
                                    @else
                                        <span class="badge bg-warning-500 text-white">Pending</span>
                                    @endif
                                </td>
                                <td class="table-td">{{ $payroll->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="table-td text-center py-8">
                                    <div class="flex flex-col items-center">
                                        <iconify-icon icon="heroicons-outline:document-text" class="text-5xl text-slate-400 mb-2"></iconify-icon>
                                        <h5 class="text-xl font-medium text-slate-400">No payrolls found</h5>
                                        <p class="text-sm text-slate-400 mt-1">Create a new payroll to get started</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $payrolls->links() }}
            </div>
        </div>
    </div>
</div> 