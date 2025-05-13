<div>
    @if ($showApplyVacationsModal)
        <!-- Apply Vacation Package Modal -->
        <x-modal wire:model="showApplyVacationsModal">
            <x-slot name="title">Apply Vacation Package</x-slot>

            <div class="space-y-6">
                @if (isset($selectedEmployee))
                    <div class="alert alert-info mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            <div>
                                <p class="font-medium">Applying vacation package for:</p>
                                <p>{{ $selectedEmployee->name }}</p>
                                <p class="text-sm mt-1">{{ $selectedEmployee->email }}</p>
                            </div>
                        </div>
                    </div>

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

                    <!-- Configuration Options -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h3 class="card-title">Configuration Options</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="deleteOldConf" class="form-checkbox">
                                    <span class="ml-2">Delete old configuration and create new one</span>
                                </label>
                                <p class="text-sm text-gray-500 mt-1">
                                    If unchecked, the old configuration will be ended and a new one will be created
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Vacation Package Selection -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Select Vacation Package</h3>
                        </div>
                        <div class="card-body">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Vacation Package</label>
                                    <x-select wire:model="selectedVacationPackage"
                                        errorMessage="{{ $errors->first('selectedVacationPackage') }}">
                                        <option value="">Select a package</option>
                                        @foreach ($vacationPackages as $package)
                                            <option value="{{ $package->id }}">{{ $package->name }}</option>
                                        @endforeach
                                    </x-select>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        No employee selected for vacation package configuration.
                    </div>
                @endif
            </div>

            <x-slot name="footer">
                <div class="mt-4 flex justify-end gap-3">
                    <x-secondary-button wire:click="closeApplyVacationsModal">Cancel</x-secondary-button>
                    <x-primary-button wire:click.prevent="applyVacation" loadingFunction="applyVacation">
                        Apply Package
                    </x-primary-button>
                </div>
            </x-slot>
        </x-modal>
    @endif
</div>
