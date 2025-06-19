<div class="space-y-5 profile-page mx-auto">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-medium">Grading Systems</h2>
        <button type="button" class="btn btn-primary" wire:click="showCreateModal">
            <i class="fas fa-plus mr-1"></i> Create & Manage Salary Tiers
        </button>
    </div>

    <div class="card">
        <div class="card-body px-6 pb-6">
            <div class="-mx-6">
                <div class="inline-block min-w-full align-middle">
                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                        <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th class="table-th">Name</th>
                                <th class="table-th">Gross Min</th>
                                <th class="table-th">Gross Max</th>
                                <th class="table-th">Compensation & Benefit Details</th>
                                <th class="table-th">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            @forelse ($packages as $package)
                                <tr>
                                    <td class="table-td">{{ $package->name }}</td>
                                    <td class="table-td">{{ $package->gross_min }}</td>
                                    <td class="table-td">{{ $package->gross_max }}</td>
                                    <td class="table-td">{{ $package->packageDetails->count() }}</td>
                                    <td class="table-td">
                                        <div class="flex space-x-3 rtl:space-x-reverse">
                                            <button class="action-btn btn-edit"
                                                wire:click="showEditModal({{ $package->id }})">
                                                <iconify-icon icon="heroicons-outline:pencil-alt"></iconify-icon>
                                            </button>
                                            <button class="action-btn btn-delete"
                                                wire:click="deletePackage({{ $package->id }})">
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
                <div class="space-y-2">
                    <x-text-input label="Salary Grade Name" type="text" class="w-full" wire:model.defer="name" />
                    <x-textarea label="Description" class="w-full" wire:model.defer="desc"></x-textarea>
                    <x-text-input label="Gross Min" type="number" step="0.01" class="w-full"
                        wire:model.defer="grossMin" />
                    <x-text-input label="Gross Max" type="number" step="0.01" class="w-full"
                        wire:model.defer="grossMax" />
                </div>


                <!-- Package Details Section -->
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex flex-col">
                            <span class="font-semibold">Compensation & Benefit Details</span>
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Included payments & allowances for the employee, paid directly to the employee or other
                                parties.
                            </span>
                        </div>
                        <button type="button" class="btn btn-xs btn-outline-primary" wire:click="addPackageDetail">
                            <i class="fas fa-plus mr-1"></i> Add Detail
                        </button>
                    </div>
                    @foreach ($packageDetails as $i => $detail)
                        <hr class="w-full mt-5">
                        <div class="flex flex-wrap justify-between gap-2 mb-1 w-full mt-2">
                            <div class="flex flex-col">
                                <span class="font-semibold mb-2">Compensation / Benefit Detail
                                    #{{ $i + 1 }}</span>
                                <div class="flex gap-5">
                                    <x-select class="w-20" label="Paid to"
                                        wire:model.defer="packageDetails.{{ $i }}.receiver">
                                        <option value="" disabled selected>Receiver</option>
                                        @foreach ($packageDetailReceivers as $receiver)
                                            <option value="{{ $receiver }}">{{ ucfirst($receiver) }}</option>
                                        @endforeach
                                    </x-select>
                                    <x-text-input type="text" class="w-24" label="Benefit Name"
                                        wire:model.defer="packageDetails.{{ $i }}.name" />
                                    <x-select class="w-20" label="Type"
                                        wire:model.defer="packageDetails.{{ $i }}.type">
                                        <option value="" disabled selected>Type</option>
                                        @foreach ($packageDetailTypes as $type)
                                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                        @endforeach
                                    </x-select>
                                    <x-text-input type="number" step="0.01" class="w-20" label="Payment Min"
                                        wire:model.defer="packageDetails.{{ $i }}.amount_min" />
                                    <x-text-input type="number" step="0.01" class="w-20" label="Payment Max"
                                        wire:model.defer="packageDetails.{{ $i }}.amount_max" />
                                </div>
                                <div class="flex gap-5 mt-2">
                                    <label class="flex items-center space-x-1 text-md">
                                        <input type="checkbox"
                                            wire:model="packageDetails.{{ $i }}.is_hidden"
                                            @checked($detail['is_hidden'])>
                                        <span>Hidden</span>
                                    </label>
                                </div>
                            </div>
                            <div class="flex flex-col items-center">
                                <button type="button" class="btn btn-xs btn-danger"
                                    wire:click="removePackageDetail({{ $i }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
            <x-slot name="footer">
                <div class="flex justify-end gap-3">
                    <x-secondary-button wire:click="$set('showAddModal', false)">Cancel</x-secondary-button>
                    <x-primary-button wire:click.prevent="savePackage" loadingFunction="savePackage">
                        {{ $isEditing ? 'Update Package' : 'Save Package' }}
                    </x-primary-button>
                </div>
            </x-slot>
        </x-modal>
    @endif
</div>
