<div>
    <div class="flex justify-between items-center mb-6">
        <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block">HR Letter Requests</h4>
        
    </div>

    <div class="flex space-x-2 mb-2">
        <!-- Filters & Search -->
        <div class="flex items-center space-x-3">
            <select wire:model.live="statusFilter" class="form-control min-w-[160px]">
                <option value="">All Statuses</option>
                @foreach($statusList as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>
            
            <div class="relative" style="min-width: 300px;">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" style="padding-left: 30px;" placeholder="Search employee...">
                <span class="absolute left-2 top-2 text-lg">
                    <iconify-icon icon="heroicons-outline:search"></iconify-icon>
                </span>
            </div>

            <select wire:model.live="perPage" class="form-control min-w-[70px]">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>
    <!-- Request List -->
    <div class="card">
        <div class="card-body px-6 pb-6">
            <div class="overflow-x-auto -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class="bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class="table-th">Employee</th>
                                    <th scope="col" class="table-th">Directed To</th>
                                    <th scope="col" class="table-th">Requested By</th>
                                    <th scope="col" class="table-th">Requested Date</th>
                                    <th scope="col" class="table-th">Status</th>
                                    <th scope="col" class="table-th">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse($requests as $request)
                                    <tr class="hover:bg-slate-200 dark:hover:bg-slate-700">
                                        <td class="table-td">
                                            {{ $request->employee->name ?? 'N/A' }}
                                        </td>
                                        <td class="table-td">
                                            {{ $request->directed_to }}
                                        </td>
                                        <td class="table-td">
                                            {{ $request->requestedBy->name ?? 'N/A' }}
                                        </td>
                                        <td class="table-td">
                                            {{ $request->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="table-td">
                                            <span class="{{ $this->getStatusBadgeClasses($request->status) }}">
                                                {{ $request->status }}
                                            </span>
                                        </td>
                                        <td class="table-td">
                                            <div class="flex items-center space-x-3">
                                                <button wire:click="viewRequest({{ $request->id }})" class="action-btn" type="button">
                                                    <iconify-icon icon="heroicons:eye"></iconify-icon>
                                                </button>
                                                
                                                @if($request->status === \App\Models\Personel\Docs\EmployeeHrLetterRequest::STATUS_PENDING)
                                                    <button wire:click="openApprovalModal({{ $request->id }})" class="action-btn text-success-500" type="button">
                                                        <iconify-icon icon="heroicons:check"></iconify-icon>
                                                    </button>
                                                    
                                                    <button wire:click="openRejectionModal({{ $request->id }})" class="action-btn text-danger-500" type="button">
                                                        <iconify-icon icon="heroicons:x-mark"></iconify-icon>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="table-td text-center">No HR letter requests found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $requests->links() }}
            </div>
        </div>
    </div>

    <!-- Request Details Modal -->
    <div>
        <x-modal wire:model="showDetailsModal" maxWidth="4xl">
            <x-slot name="title">
                HR Letter Request Details
            </x-slot>

            <div class="py-4">
                @if($selectedRequest)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Employee Name</p>
                            <p class="font-medium">{{ $selectedRequest->employee->name ?? 'N/A' }}</p>
                        </div>
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Status</p>
                            <p class="font-medium">
                                <span class="{{ $this->getStatusBadgeClasses($selectedRequest->status) }}">
                                    {{ $selectedRequest->status }}
                                </span>
                            </p>
                        </div>
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Letter Directed To</p>
                            <p class="font-medium">{{ $selectedRequest->directed_to }}</p>
                        </div>
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Requested By</p>
                            <p class="font-medium">{{ $selectedRequest->requestedBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Request Date</p>
                            <p class="font-medium">{{ $selectedRequest->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @if($selectedRequest->approved_by)
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Approved/Rejected By</p>
                            <p class="font-medium">{{ $selectedRequest->approvedBy->name ?? 'N/A' }}</p>
                            <p class="text-sm text-slate-500 mt-1">Date</p>
                            <p class="font-medium">{{ $selectedRequest->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                    </div>

                    @if($selectedRequest->employee_note)
                    <div class="border rounded p-3 mb-4">
                        <p class="text-sm text-slate-500">Employee Note</p>
                        <p>{{ $selectedRequest->employee_note }}</p>
                    </div>
                    @endif

                    @if($selectedRequest->admin_note)
                    <div class="border rounded p-3 mb-4">
                        <p class="text-sm text-slate-500">Admin Note</p>
                        <p>{{ $selectedRequest->admin_note }}</p>
                    </div>
                    @endif

                    @if($selectedRequest->status === \App\Models\Personel\Docs\EmployeeHrLetterRequest::STATUS_COMPLETED)
                    <div class="mt-4 bg-green-50 p-4 rounded border border-green-200">
                        <div class="flex items-center">
                            <iconify-icon icon="heroicons:check-circle" class="text-2xl text-green-500 mr-2"></iconify-icon>
                            <div>
                                <h5 class="font-medium text-green-700">Letter Has Been Generated</h5>
                                <p class="text-sm text-green-600">The HR letter has been successfully generated and added to the employee's documents.</p>
                            </div>
                        </div>
                    </div>
                    @endif
                @endif
            </div>

            <x-slot name="footer">
                <div class="flex justify-end space-x-3">
                    <button type="button" class="btn btn-outline-dark" wire:click="closeModal">
                        Close
                    </button>
                    
                    @if($selectedRequest && $selectedRequest->status === \App\Models\Personel\Docs\EmployeeHrLetterRequest::STATUS_PENDING)
                        <button type="button" class="btn btn-success" wire:click="openApprovalModal({{ $selectedRequest->id }})">
                            Approve
                        </button>
                        
                        <button type="button" class="btn btn-danger" wire:click="openRejectionModal({{ $selectedRequest->id }})">
                            Reject
                        </button>
                    @endif
                </div>
            </x-slot>
        </x-modal>
    </div>

    <!-- Approval Modal -->
    <div>
        <x-modal wire:model="showApprovalModal">
            <x-slot name="title">
                Approve HR Letter Request
            </x-slot>

            <div class="py-4">
                @if($selectedRequest)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Employee</p>
                            <p class="font-medium">{{ $selectedRequest->employee->name ?? 'N/A' }}</p>
                        </div>
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Request Date</p>
                            <p class="font-medium">{{ $selectedRequest->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="border rounded p-3 md:col-span-2">
                            <p class="text-sm text-slate-500">Letter Directed To</p>
                            <p class="font-medium">{{ $selectedRequest->directed_to }}</p>
                        </div>
                    </div>

                    @if($selectedRequest->employee_note)
                    <div class="border rounded p-3 mb-4">
                        <p class="text-sm text-slate-500">Employee Note</p>
                        <p>{{ $selectedRequest->employee_note }}</p>
                    </div>
                    @endif

                    <div class="mb-5">
                        <label for="hrLetterFile" class="block text-sm font-medium text-slate-700 mb-2">Upload HR Letter Document</label>
                        <div class="border border-slate-200 rounded-md p-3 bg-slate-50 @error('hrLetterFile') !border-danger-500 @enderror">
                            <input type="file" wire:model="hrLetterFile" id="hrLetterFile" class="form-control">
                            <p class="text-xs text-slate-500 mt-1">Accepted formats: PDF, JPG, JPEG, PNG (max 2MB)</p>
                            @error('hrLetterFile') <span class="text-danger text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-5">
                        <label for="adminNote" class="block text-sm font-medium text-slate-700 mb-2">Admin Note (Optional)</label>
                        <textarea wire:model="adminNote" id="adminNote" class="form-control w-full" rows="3" placeholder="Add any additional notes or instructions for the employee"></textarea>
                        @error('adminNote') <span class="text-danger text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-blue-50 p-4 rounded border border-blue-200 mb-2">
                        <div class="flex items-start">
                            <iconify-icon icon="heroicons:information-circle" class="text-2xl text-blue-500 mr-2 mt-0.5"></iconify-icon>
                            <div>
                                <h5 class="font-medium text-blue-700">Important Information</h5>
                                <p class="text-sm text-blue-600">Approving this request will automatically generate an HR letter document for the employee and mark the request as completed.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <x-slot name="footer">
                <div class="flex justify-end space-x-3">
                    <button type="button" class="btn btn-outline-dark" wire:click="closeModal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="approveRequest">
                        Approve & Upload
                    </button>
                </div>
            </x-slot>
        </x-modal>
    </div>

    <!-- Rejection Modal -->
    <div>
        <x-modal wire:model="showRejectionModal">
            <x-slot name="title">
                Reject HR Letter Request
            </x-slot>

            <div class="py-4">
                @if($selectedRequest)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Employee</p>
                            <p class="font-medium">{{ $selectedRequest->employee->name ?? 'N/A' }}</p>
                        </div>
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Request Date</p>
                            <p class="font-medium">{{ $selectedRequest->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="border rounded p-3 md:col-span-2">
                            <p class="text-sm text-slate-500">Letter Directed To</p>
                            <p class="font-medium">{{ $selectedRequest->directed_to }}</p>
                        </div>
                    </div>

                    @if($selectedRequest->employee_note)
                    <div class="border rounded p-3 mb-4">
                        <p class="text-sm text-slate-500">Employee Note</p>
                        <p>{{ $selectedRequest->employee_note }}</p>
                    </div>
                    @endif

                    <div class="mb-5">
                        <label for="adminNote" class="block text-sm font-medium text-slate-700 mb-2">Rejection Reason (Required)</label>
                        <textarea wire:model="adminNote" id="adminNote" class="form-control w-full" rows="3" placeholder="Please provide a detailed reason for rejecting this request"></textarea>
                        @error('adminNote') <span class="text-danger text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-red-50 p-4 rounded border border-red-200 mb-2">
                        <div class="flex items-start">
                            <iconify-icon icon="heroicons:exclamation-triangle" class="text-2xl text-red-500 mr-2 mt-0.5"></iconify-icon>
                            <div>
                                <h5 class="font-medium text-red-700">Rejection Notice</h5>
                                <p class="text-sm text-red-600">Rejecting this request will notify the employee. Please provide a clear explanation for the rejection.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <x-slot name="footer">
                <div class="flex justify-end space-x-3">
                    <button type="button" class="btn btn-outline-dark" wire:click="closeModal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="rejectRequest">
                        Reject Request
                    </button>
                </div>
            </x-slot>
        </x-modal>
    </div>
</div> 