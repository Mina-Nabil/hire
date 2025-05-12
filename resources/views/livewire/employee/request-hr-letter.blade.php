<div class="space-y-5 profile-page mx-auto" style="max-width: 1000px;">
    <div class="card-body flex flex-col" wire:ignore>
        <div class="card-text h-full">
            <div class="flex">
                <ul class="nav nav-tabs flex flex-col md:flex-row flex-wrap list-none border-b-0 pl-0" id="tabs-tab"
                    role="tablist">
                    <li class="nav-item" role="presentation" wire:click="setActiveTab('request')">
                        <a href="#tabs-request"
                            class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($activeTab === 'request') active @endif dark:text-slate-300"
                            id="tabs-request-tab" data-bs-toggle="pill" data-bs-target="#tabs-request" role="tab"
                            aria-controls="tabs-request" aria-selected="true">
                            <iconify-icon class="mr-1" icon="mdi:file-document-edit-outline"></iconify-icon>
                            Request HR Letter</a>
                    </li>
                    <li class="nav-item" role="presentation" wire:click="setActiveTab('history')">
                        <a href="#tabs-history"
                            class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($activeTab === 'history') active @endif dark:text-slate-300"
                            id="tabs-history-tab" data-bs-toggle="pill" data-bs-target="#tabs-history" role="tab"
                            aria-controls="tabs-history" aria-selected="false">
                            <iconify-icon class="mr-1" icon="mdi:history"></iconify-icon>
                            Request History</a>
                    </li>
                </ul>
                <div>
                    <h4>
                        <iconify-icon class="ml-3" style="position: absolute" wire:loading wire:target="setActiveTab"
                            icon="svg-spinners:180-ring"></iconify-icon>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="tabs-tabContent">
        <!-- Request Form Tab -->
        <div class="tab-pane fade @if ($activeTab === 'request') show active @endif" id="tabs-request" role="tabpanel"
            aria-labelledby="tabs-request-tab">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Request HR Letter</h4>
                </div>
                <div class="card-body px-6 pb-6">
                    @if (!$employee)
                        <div class="alert alert-warning">
                            Your employee record was not found. Please contact HR.
                        </div>
                    @else
                        <form wire:submit.prevent="openConfirmModal">
                            <div class="grid grid-cols-1 gap-5 mb-5 mt-5">
                                <!-- Directed To -->
                                <div>
                                    <x-text-input wire:model="directed_to" label="Letter Directed To*"
                                        placeholder="Enter the recipient or purpose of the letter"
                                        errorMessage="{{ $errors->first('directed_to') }}" />
                                </div>

                                <!-- Note / Reason -->
                                <div>
                                    <x-textarea wire:model="employee_note" label="Additional Notes or Reasons (Optional)"
                                        placeholder="Explain why you need this HR letter or provide any additional details"
                                        errorMessage="{{ $errors->first('employee_note') }}" rows="5" />
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <x-primary-button type="submit" class="w-auto sm:w-full" loadingFunction="openConfirmModal">
                                    Review Request
                                </x-primary-button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <!-- HR Letter Request Info -->
            <div class="card mt-5">
                <div class="card-header mb-5">
                    <h4 class="card-title">About HR Letters</h4>
                </div>
                <div class="card-body px-6 pb-6">
                    <div class="border-l-4 border-blue-500 pl-4 py-2 mb-4">
                        <p class="text-slate-600">
                            HR letters are official documents issued by the Human Resources department that you may need for various purposes such as:
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div class="border rounded-lg p-4 hover:bg-slate-50">
                            <h6 class="font-medium text-slate-900 mb-2">Employment Verification</h6>
                            <p class="text-sm text-slate-600">Letters confirming your employment status, position, and tenure with the company.</p>
                        </div>
                        <div class="border rounded-lg p-4 hover:bg-slate-50">
                            <h6 class="font-medium text-slate-900 mb-2">Salary Verification</h6>
                            <p class="text-sm text-slate-600">Documents certifying your current salary, for loan applications or other financial matters.</p>
                        </div>
                        <div class="border rounded-lg p-4 hover:bg-slate-50">
                            <h6 class="font-medium text-slate-900 mb-2">Visa/Immigration</h6>
                            <p class="text-sm text-slate-600">Support letters for visa applications or immigration processes.</p>
                        </div>
                        <div class="border rounded-lg p-4 hover:bg-slate-50">
                            <h6 class="font-medium text-slate-900 mb-2">Other Purposes</h6>
                            <p class="text-sm text-slate-600">Letters for housing applications, government agencies, or other institutions requiring formal documentation.</p>
                        </div>
                    </div>
                    <div class="bg-slate-50 p-4 rounded">
                        <p class="text-sm text-slate-600">
                            <iconify-icon icon="mdi:clock-time-four-outline" class="text-lg mr-1"></iconify-icon>
                            HR letter requests are typically processed within 2-3 business days, depending on request volume and complexity.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request History Tab -->
        <div class="tab-pane fade @if ($activeTab === 'history') show active @endif" id="tabs-history" role="tabpanel"
            aria-labelledby="tabs-history-tab">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">HR Letter Request History</h4>
                </div>
                <div class="card-body px-6 pb-6">
                    @if (count($hrLetterRequests) > 0)
                        <div class="overflow-x-auto -mx-6">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden">
                                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                        <thead class="bg-slate-200 dark:bg-slate-700">
                                            <tr>
                                                <th scope="col" class="table-th">Date</th>
                                                <th scope="col" class="table-th">Directed To</th>
                                                <th scope="col" class="table-th">Status</th>
                                                <th scope="col" class="table-th">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                            @foreach ($hrLetterRequests as $request)
                                                <tr>
                                                    <td class="table-td">{{ \Carbon\Carbon::parse($request['created_at'])->format('d/m/Y') }}</td>
                                                    <td class="table-td">{{ $request['directed_to'] }}</td>
                                                    <td class="table-td">
                                                        <span class="badge {{ $this->getStatusBadgeClass($request['status']) }} capitalize rounded-3xl">
                                                            {{ $this->getStatusLabel($request['status']) }}
                                                        </span>
                                                    </td>
                                                    <td class="table-td">
                                                        <button wire:click="viewRequestDetails({{ $request['id'] }})" class="btn btn-sm btn-outline-primary">
                                                            <iconify-icon icon="heroicons:eye"></iconify-icon>
                                                            View
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            You have not made any HR letter requests yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <x-modal wire:model="showConfirmModal" maxWidth="2xl">
        <x-slot name="title">
            Confirm HR Letter Request
        </x-slot>

        <div class="py-4">
            <div class="mb-5">
                <h5 class="font-medium text-lg mb-3">Request Summary</h5>
                <div class="grid grid-cols-1 gap-3">
                    <div class="border rounded p-3">
                        <p class="text-sm text-slate-500">Letter Directed To</p>
                        <p class="font-medium">{{ $directed_to }}</p>
                    </div>
                    @if ($employee_note)
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Additional Notes</p>
                            <p>{{ $employee_note }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-5 bg-yellow-50 p-3 rounded border border-yellow-200">
                <p class="text-sm text-yellow-800">
                    <iconify-icon icon="mdi:information-outline" class="text-lg mr-1"></iconify-icon>
                    Please confirm your HR letter request. Once submitted, it will be pending approval from the HR
                    department.
                </p>
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-end space-x-3">
                <button type="button" class="btn btn-secondary" wire:click="closeConfirmModal">
                    Cancel
                </button>
                <x-primary-button wire:click="submit" loadingFunction="submit">
                    Confirm & Submit
                </x-primary-button>
            </div>
        </x-slot>
    </x-modal>

    <!-- Details Modal -->
    <x-modal wire:model="showDetailsModal" maxWidth="3xl">
        <x-slot name="title">
            HR Letter Request Details
        </x-slot>

        <div class="py-4">
            @if ($selectedRequest)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="border rounded p-3">
                        <p class="text-sm text-slate-500">Request Date</p>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($selectedRequest['created_at'])->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="border rounded p-3">
                        <p class="text-sm text-slate-500">Status</p>
                        <p class="font-medium">
                            <span class="badge {{ $this->getStatusBadgeClass($selectedRequest['status']) }} capitalize rounded-3xl">
                                {{ $this->getStatusLabel($selectedRequest['status']) }}
                            </span>
                        </p>
                    </div>
                    <div class="border rounded p-3">
                        <p class="text-sm text-slate-500">Letter Directed To</p>
                        <p class="font-medium">{{ $selectedRequest['directed_to'] }}</p>
                    </div>
                    <div class="border rounded p-3">
                        <p class="text-sm text-slate-500">Requested By</p>
                        <p class="font-medium">
                            {{ $selectedRequest['requested_by']['name'] ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                @if(!empty($selectedRequest['employee_note']))
                    <div class="border rounded p-3 mb-4">
                        <p class="text-sm text-slate-500">Employee Note</p>
                        <p>{{ $selectedRequest['employee_note'] }}</p>
                    </div>
                @endif

                @if(!empty($selectedRequest['admin_note']))
                    <div class="border rounded p-3 mb-4">
                        <p class="text-sm text-slate-500">Admin Note</p>
                        <p>{{ $selectedRequest['admin_note'] }}</p>
                    </div>
                @endif

                @if(!empty($selectedRequest['approved_by']))
                    <div class="border rounded p-3 mb-4">
                        <p class="text-sm text-slate-500">Approved/Processed By</p>
                        <p class="font-medium">
                            {{ $selectedRequest['approved_by']['name'] ?? 'N/A' }}
                        </p>
                    </div>
                @endif

                @if($selectedRequest['status'] === 'pending')
                    <div class="bg-yellow-50 p-3 rounded border border-yellow-200 mt-4">
                        <p class="text-sm text-yellow-800">
                            <iconify-icon icon="mdi:information-outline" class="text-lg mr-1"></iconify-icon>
                            Your request is currently being reviewed by the HR department.
                        </p>
                    </div>
                @elseif($selectedRequest['status'] === 'approved')
                    <div class="bg-blue-50 p-3 rounded border border-blue-200 mt-4">
                        <p class="text-sm text-blue-800">
                            <iconify-icon icon="mdi:information-outline" class="text-lg mr-1"></iconify-icon>
                            Your request has been approved and is being processed.
                        </p>
                    </div>
                @elseif($selectedRequest['status'] === 'completed')
                    <div class="bg-green-50 p-3 rounded border border-green-200 mt-4">
                        <p class="text-sm text-green-800">
                            <iconify-icon icon="mdi:check-circle-outline" class="text-lg mr-1"></iconify-icon>
                            Your HR letter is ready. Please contact HR to receive your document.
                        </p>
                    </div>
                @elseif($selectedRequest['status'] === 'rejected')
                    <div class="bg-red-50 p-3 rounded border border-red-200 mt-4">
                        <p class="text-sm text-red-800">
                            <iconify-icon icon="mdi:alert-circle-outline" class="text-lg mr-1"></iconify-icon>
                            Your request has been rejected. Please check the admin note for details or contact HR.
                        </p>
                    </div>
                @endif
            @else
                <div class="alert alert-warning">
                    Request details not found.
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="flex justify-end">
                <button type="button" class="btn btn-secondary" wire:click="closeDetailsModal">
                    Close
                </button>
            </div>
        </x-slot>
    </x-modal>
</div> 