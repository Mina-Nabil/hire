<div>
    <div class="flex justify-between items-center mb-6">
        <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block">My Overtime Requests</h4>
        <button type="button" wire:click="openRequestModal" class="btn btn-primary">Request Overtime</button>
    </div>

    <div class="flex space-x-2 mb-2">
        <!-- Filters & Search -->
        <div class="flex items-center space-x-3">
            <select wire:model.live="statusFilter" class="form-control min-w-[160px]">
                <option value="">All Statuses</option>
                @foreach ($statusList as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>

            <div class="relative" style="min-width: 300px;">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                    style="padding-left: 30px;" placeholder="Search date, hours...">
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

    <!-- Overtime List -->
    <div class="card">
        <div class="card-body px-6 pb-6">
            <div class="overflow-x-auto -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class="bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class="table-th">Date</th>
                                    <th scope="col" class="table-th">Time</th>
                                    <th scope="col" class="table-th">Hours</th>
                                    <th scope="col" class="table-th">Status</th>
                                    <th scope="col" class="table-th">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse($overtimes as $overtime)
                                    <tr class="hover:bg-slate-200 dark:hover:bg-slate-700">
                                        <td class="table-td">
                                            {{ \Carbon\Carbon::parse($overtime->date)->format('M d, Y') }}
                                        </td>
                                        <td class="table-td">
                                            {{ $overtime->start_time }} - {{ $overtime->end_time }}
                                        </td>
                                        <td class="table-td">
                                            {{ $overtime->hours }}
                                        </td>
                                        <td class="table-td">
                                            <span class="{{ $this->getStatusBadgeClasses($overtime->status) }}">
                                                {{ $overtime->status }}
                                            </span>
                                        </td>
                                        <td class="table-td">
                                            <div class="flex items-center space-x-3">
                                                <button wire:click="viewDetails({{ $overtime->id }})" class="action-btn"
                                                    type="button">
                                                    <iconify-icon icon="heroicons:eye"></iconify-icon>
                                                </button>

                                                @if ($overtime->status === \App\Models\Attendance\Overtime::STATUS_PENDING)
                                                    <button wire:click="cancelRequest({{ $overtime->id }})"
                                                        class="action-btn text-danger-500" type="button"
                                                        onclick="return confirm('Are you sure you want to cancel this overtime request?')">
                                                        <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="table-td text-center">No overtime requests found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $overtimes->links('vendor.livewire.simple-bootstrap') }}
            </div>
        </div>
    </div>

    <!-- Request Modal -->
    <div>
        <x-modal wire:model="showRequestModal">
            <x-slot name="title">
                Request Overtime
            </x-slot>

            <div class="py-4">
                <div class="mb-5">
                    <label for="startDate" class="form-label">Date</label>
                    <input type="date" id="startDate" wire:model="startDate" class="form-control"
                        min="{{ now()->format('Y-m-d') }}">
                    @error('startDate')
                        <span class="text-danger text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label for="startTime" class="form-label">Start Time</label>
                        <input type="time" id="startTime" wire:model="startTime" class="form-control">
                        @error('startTime')
                            <span class="text-danger text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="endTime" class="form-label">End Time</label>
                        <input type="time" id="endTime" wire:model="endTime" class="form-control">
                        @error('endTime')
                            <span class="text-danger text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-5">
                    <label for="reason" class="form-label">Reason (Optional)</label>
                    <textarea id="reason" wire:model="reason" class="form-control" rows="3"
                        placeholder="Explain why overtime was needed"></textarea>
                    @error('reason')
                        <span class="text-danger text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="bg-blue-50 p-4 rounded border border-blue-200">
                    <div class="flex items-start">
                        <iconify-icon icon="heroicons:information-circle"
                            class="text-2xl text-blue-500 mr-2 mt-0.5"></iconify-icon>
                        <div>
                            <h5 class="font-medium text-blue-700">Important Information</h5>
                            <p class="text-sm text-blue-600">
                                Overtime requests need to be approved by management. You will be notified once your
                                request has been reviewed.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end space-x-3">
                    <button type="button" class="btn btn-outline-dark" wire:click="closeRequestModal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="submitOvertimeRequest">
                        Submit Request
                    </button>
                </div>
            </x-slot>
        </x-modal>
    </div>

    <!-- Details Modal -->
    <div>
        <x-modal wire:model="showDetailsModal">
            <x-slot name="title">
                Overtime Request Details
            </x-slot>

            <div class="py-4">
                @if ($selectedOvertime)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Date</p>
                            <p class="font-medium">
                                {{ \Carbon\Carbon::parse($selectedOvertime->date)->format('M d, Y') }}</p>
                        </div>
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Status</p>
                            <p class="font-medium">
                                <span class="{{ $this->getStatusBadgeClasses($selectedOvertime->status) }}">
                                    {{ $selectedOvertime->status }}
                                </span>
                            </p>
                        </div>
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Time</p>
                            <p class="font-medium">{{ $selectedOvertime->start_time }} -
                                {{ $selectedOvertime->end_time }}</p>
                        </div>
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Hours</p>
                            <p class="font-medium">{{ $selectedOvertime->hours }}</p>
                        </div>
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Created Date</p>
                            <p class="font-medium">{{ $selectedOvertime->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @if ($selectedOvertime->approved_at)
                            <div class="border rounded p-3">
                                <p class="text-sm text-slate-500">Reviewed Date</p>
                                <p class="font-medium">
                                    {{ \Carbon\Carbon::parse($selectedOvertime->approved_at)->format('M d, Y h:i A') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    @if (isset($selectedOvertime->admin_note) && $selectedOvertime->admin_note)
                        <div class="border rounded p-3 mb-4">
                            <p class="text-sm text-slate-500">Admin Note</p>
                            <p>{{ $selectedOvertime->admin_note }}</p>
                        </div>
                    @endif

                    @if ($selectedOvertime->status === \App\Models\Attendance\Overtime::STATUS_APPROVED)
                        <div class="mt-4 bg-green-50 p-4 rounded border border-green-200">
                            <div class="flex items-center">
                                <iconify-icon icon="heroicons:check-circle"
                                    class="text-2xl text-green-500 mr-2"></iconify-icon>
                                <div>
                                    <h5 class="font-medium text-green-700">Overtime Request Approved</h5>
                                    <p class="text-sm text-green-600">Your overtime request has been approved and will
                                        be considered for compensation.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($selectedOvertime->status === \App\Models\Attendance\Overtime::STATUS_REJECTED)
                        <div class="mt-4 bg-red-50 p-4 rounded border border-red-200">
                            <div class="flex items-center">
                                <iconify-icon icon="heroicons:x-circle"
                                    class="text-2xl text-red-500 mr-2"></iconify-icon>
                                <div>
                                    <h5 class="font-medium text-red-700">Overtime Request Rejected</h5>
                                    <p class="text-sm text-red-600">Your overtime request has been rejected. Please
                                        check the admin note for details.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            <x-slot name="footer">
                <button type="button" class="btn btn-outline-dark" wire:click="closeDetailsModal">
                    Close
                </button>
            </x-slot>
        </x-modal>
    </div>
</div>
