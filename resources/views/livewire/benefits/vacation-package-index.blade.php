<div class="space-y-5 profile-page mx-auto">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-medium">Vacation Packages</h2>
        <button type="button" class="btn btn-primary" wire:click="showCreateModal">
            <i class="fas fa-plus mr-1"></i> Create & Manage Vacation Packages
        </button>
    </div>

    <div class="card">
        <div class="card-body px-6 pb-6">
            <div class="-mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead
                                class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th class="table-th">Name</th>
                                    <th class="table-th">Vacation & Leaves</th>
                                    <th class="table-th">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse ($packages as $package)
                                    <tr>
                                        <td class="table-td">{{ $package->name }}</td>
                                        <td class="table-td">{{ $package->vacationDetails->count() }}</td>
                                        <td class="table-td">
                                            <div class="flex space-x-3 rtl:space-x-reverse">
                                                <button class="action-btn btn-edit"
                                                    wire:click="showEditModal({{ $package->id }})"
                                                    title="Edit Package">
                                                    <iconify-icon icon="heroicons-outline:pencil-alt"></iconify-icon>
                                                </button>
                                                <button class="action-btn btn-primary"
                                                    wire:click="$dispatch('showConfirmation', { 
                                                        message: 'This will apply the vacation package &quot;{{ $package->name }}&quot; to all active employees. Active employees are those with status active and no termination_date, release_date, or absent_date set in the current year. Each employee will receive vacation benefits with maximum balance values from the package. Existing vacation benefits will be preserved (not deleted). Continue?', 
                                                         color: 'primary', 
                                                        callback: 'applyPackageToAllActiveEmployees', 
                                                        params: {{ $package->id }}
                                                    })"
                                                    title="Apply to All Active Employees">
                                                    <iconify-icon icon="heroicons-outline:user-group"></iconify-icon>
                                                </button>
                                                <button class="action-btn btn-delete"
                                                    wire:click="deletePackage({{ $package->id }})"
                                                    title="Delete Package">
                                                    <iconify-icon icon="heroicons-outline:trash"></iconify-icon>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="table-td text-center">No packages found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($showAddModal)
        <x-modal wire:model="showAddModal">
            @if ($errors->any())
                <div class="alert alert-warning mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <div>
                            <p class="font-medium">Please fix the following errors:</p>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <x-slot name="title">{{ $isEditing ? 'Edit Salary Grade' : 'Create Salary Grade' }}</x-slot>
            <div class="modal-body">
                <!-- Main Info -->

                <div class="space-y-4">
                    <x-text-input label="Vacation Package Name" type="text" class="w-full" wire:model.defer="name" />
                    <x-textarea label="Description" class="w-full" wire:model.defer="desc"></x-textarea>
                </div>


                <!-- Vacation Details Section -->
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex flex-col">
                            <span class="font-semibold">Vacation & Leaves</span>
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Vacations/Leaves allowed for the employee. Other than public holidays.
                            </span>
                        </div>
                        <button type="button" class="btn btn-xs btn-outline-primary" wire:click="addVacationDetail">
                            <i class="fas fa-plus mr-1"></i> Add Vacation
                        </button>
                    </div>
                    @foreach ($vacationDetails as $i => $detail)
                        <hr class="w-full mt-5">
                        <div class="flex flex-wrap justify-between gap-2 mb-1 w-full mt-2">
                            <div class="flex flex-col gap-2">
                                <div class="flex gap-5">
                                    <x-text-input type="text" class="w-24" label="Name"
                                        wire:model.defer="vacationDetails.{{ $i }}.name" />
                                    <x-select class="w-20" label="Type"
                                        wire:model.defer="vacationDetails.{{ $i }}.type">
                                        <option value="" disabled selected>Type</option>
                                        @foreach ($vacationDetailTypes as $type)
                                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                        @endforeach
                                    </x-select>

                                    <x-text-input type="number" step="0.01" class="w-20"
                                        label="Increase Rate Minimum"
                                        wire:model.defer="vacationDetails.{{ $i }}.inc_rate_min" />
                                    <x-text-input type="number" step="0.01" class="w-20"
                                        label="Increase Rate Maximum"
                                        wire:model.defer="vacationDetails.{{ $i }}.inc_rate_max" />
                                </div>
                                <div class="flex gap-5">
                                    <x-text-input type="number" step="0.01" class="w-20"
                                        label="Max Balance Minimum"
                                        wire:model.defer="vacationDetails.{{ $i }}.max_balance_min" />
                                    <x-text-input type="number" step="0.01" class="w-20"
                                        label="Max Balance Maximum"
                                        wire:model.defer="vacationDetails.{{ $i }}.max_balance_max" />
                                    <x-text-input type="number" step="0.01" class="w-20"
                                        label="Hour Price Minimum"
                                        wire:model.defer="vacationDetails.{{ $i }}.hour_price_min" />
                                    <x-text-input type="number" step="0.01" class="w-20"
                                        label="Hour Price Maximum"
                                        wire:model.defer="vacationDetails.{{ $i }}.hour_price_max" />
                                    <x-text-input type="number" class="w-20"
                                        label="Apply Deadline (days after leave)"
                                        wire:model.defer="vacationDetails.{{ $i }}.apply_deadline"
                                        />
                                </div>
                            </div>
                            <div class="flex flex-col items-center">
                                <button type="button" class="btn btn-xs btn-danger"
                                    wire:click="removeVacationDetail({{ $i }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
            <x-slot name="footer">
                <div class="flex justify-end gap-3">
                    <x-secondary-button wire:click="closeAddModal">Cancel</x-secondary-button>
                    <x-primary-button wire:click.prevent="savePackage" loadingFunction="savePackage">
                        {{ $isEditing ? 'Update Package' : 'Save Package' }}
                    </x-primary-button>
                </div>
            </x-slot>
        </x-modal>
    @endif
</div>
