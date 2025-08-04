<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Payrolls</h4>
            <a href="{{ route('payrolls.create') }}" class="btn btn-primary btn-sm">
                <span class="flex items-center">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="heroicons-outline:plus"></iconify-icon>
                    Create Payroll
                </span>
            </a>
        </div>
        <div class="card-body p-6">
            <div class="flex justify-between items-center mb-6">
                <div class="flex-1 mr-4">
                    <div class="relative">
                        <input type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Search payrolls..." 
                            class="form-control py-2 !pl-12">
                        <span class="absolute left-2 top-2 text-lg">
                            <iconify-icon icon="heroicons-outline:search"></iconify-icon>
                        </span>
                    </div>
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
                                <td class="table-td">
                                    <button 
                                        wire:click.stop="showEmployees({{ $payroll->id }})"
                                        class="text-blue-600 hover:text-blue-800 font-medium underline">
                                        {{ $payroll->total_employees }}
                                    </button>
                                </td>
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
                {{ $payrolls->links('vendor.livewire.simple-bootstrap') }}
            </div>
        </div>
    </div>

    <!-- Employee Modal -->
    @if($showEmployeeModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     wire:click="closeEmployeeModal"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <iconify-icon icon="heroicons-outline:users" class="text-blue-600"></iconify-icon>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Employees in Payroll
                                </h3>
                                @if($selectedPayroll)
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Period: {{ \Carbon\Carbon::parse($selectedPayroll->start_date)->format('M d, Y') }} - 
                                            {{ \Carbon\Carbon::parse($selectedPayroll->end_date)->format('M d, Y') }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-4 max-h-96 overflow-y-auto">
                            @if(count($payrollEmployees) > 0)
                                <div class="space-y-2">
                                    @foreach($payrollEmployees as $payrollEmployee)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <span class="text-blue-600 font-medium text-sm">
                                                        {{ strtoupper(substr($payrollEmployee->employee->name ?? 'N/A', 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">
                                                        {{ $payrollEmployee->employee->name ?? 'N/A' }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $payrollEmployee->position ?? 'N/A' }} - {{ $payrollEmployee->department ?? 'N/A' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-medium text-gray-900">
                                                    {{ number_format($payrollEmployee->after_tax_salary ?? 0, 2) }}
                                                </p>
                                                <p class="text-xs text-gray-500">Net Salary</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <iconify-icon icon="heroicons-outline:user-group" class="text-4xl text-gray-400 mx-auto mb-2"></iconify-icon>
                                    <p class="text-gray-500">No employees found in this payroll.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" 
                                wire:click="closeEmployeeModal"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div> 