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
                                    <x-select wire:model.live="selectedPackageId"
                                        errorMessage="{{ $errors->first('selectedPackageId') }}">
                                        <option value="">Select a package</option>
                                        @foreach ($packages as $package)
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

            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="card-title">Vacation Benefits</h3>
                </div>
                <div class="card-body">
                    <div class="space-y-4">
                        @if ($selectedPackage)
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
                                <x-text-input label="Start Date*" type="date" wire:model="packageStartDate"
                                    errorMessage="{{ $errors->first('packageStartDate') }}" />
                                <x-text-input label="End Date*" type="date" wire:model="packageEndDate"
                                    errorMessage="{{ $errors->first('packageEndDate') }}" />
                            </div>

                            @foreach ($vacationBenefits as $index => $benefit)
                                <div class="border rounded-lg p-4">
                                    <div class="grid grid-cols-4">
                                        <div class="col-span-3">
                                            <div class="flex justify-between items-center mb-2">
                                                <p class="font-bold">{{ $benefit['name'] }}</p>
                                            </div>
                                            <div class="grid grid-cols-3 gap-4">
                                                @if (!$benefit['is_disabled'])
                                                    <x-text-input label="Increment Rate* (in hours)" type="number"
                                                        wire:change="updateCurrentBalance({{ $index }})"
                                                        wire:model="vacationBenefits.{{ $index }}.inc_rate"
                                                        errorMessage="{{ $errors->first('vacationBenefits.' . $index . '.inc_rate') }}"
                                                        min="{{ $benefit['inc_rate_min'] }}"
                                                        max="{{ $benefit['inc_rate_max'] }}" />
                                                @endif
                                                @if (!$benefit['is_disabled'])
                                                    <x-text-input label="Max Balance* (in hours)" type="number"
                                                        wire:model="vacationBenefits.{{ $index }}.max_balance"
                                                        errorMessage="{{ $errors->first('vacationBenefits.' . $index . '.max_balance') }}"
                                                        min="{{ $benefit['max_balance_min'] }}"
                                                        max="{{ $benefit['max_balance_max'] }}"
                                                        />
                                                @endif

                                                <x-text-input label="Current Balance* (in hours)" type="number"
                                                    wire:model="vacationBenefits.{{ $index }}.current_balance"
                                                    errorMessage="{{ $errors->first('vacationBenefits.' . $index . '.current_balance') }}"
                                                    disabled />

                                                @if (!$benefit['is_disabled'])
                                                    <x-text-input label="Hour Price*" type="number"
                                                        wire:model="vacationBenefits.{{ $index }}.hour_price"
                                                        errorMessage="{{ $errors->first('vacationBenefits.' . $index . '.hour_price') }}"
                                                        min="{{ $benefit['hour_price_min'] }}"
                                                        max="{{ $benefit['hour_price_max'] }}" />
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-span-1">
                                            <div class="flex items-end flex-col ml-2">
                                                <span class="text-sm text-gray-500">
                                                    {{ ucfirst($benefit['type']) }}
                                                </span>
                                                <span class="text-sm text-gray-500">
                                                    Increment rate: {{ $benefit['inc_rate_min'] }} ->
                                                    {{ $benefit['inc_rate_max'] }}
                                                </span>
                                                <span class="text-sm text-gray-500">
                                                    Max balance: {{ $benefit['max_balance_min'] }} ->
                                                    {{ $benefit['max_balance_max'] }}
                                                </span>
                                                <span class="text-sm text-gray-500">
                                                    Hour price: {{ $benefit['hour_price_min'] }} ->
                                                    {{ $benefit['hour_price_max'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>




            <x-slot name="footer">
                <div class="mt-4 flex justify-end gap-3">
                    <x-secondary-button wire:click="closeApplyVacationsModal">Cancel</x-secondary-button>
                    <x-primary-button wire:click.prevent="applyVacationPackage" loadingFunction="applyVacationPackage">
                        Apply Vacation Package
                    </x-primary-button>
                </div>
            </x-slot>
        </x-modal>
    @endif
</div>
