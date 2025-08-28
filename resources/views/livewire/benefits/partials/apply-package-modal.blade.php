<div>
    @if ($showApplyPackageModal)
        <!-- Apply Benefits Package Modal -->
        <x-modal wire:model="showApplyPackageModal">
            <x-slot name="title">Apply Benefits Package</x-slot>

            <div class="space-y-6">
                @if (isset($selectedEmployee))
                    <div class="alert alert-info mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            <div>
                                <p class="font-medium">Applying benefits package for:</p>
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

                    <!-- Package Selection -->
                    <div class="mb-6">
                        <x-select title="Select Package" wire:model="selectedPackageId" wire:change="loadPackageDetails"
                            errorMessage="{{ $errors->first('selectedPackageId') }}">
                            <option value="">-- Package is selected from position automatically, make sure
                                position has a salary grade and the employee is selected --</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}">{{ $package->name }}</option>
                            @endforeach
                        </x-select>
                    </div>

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
                        </div>

                        <!-- Package Details Section -->
                        <div class="card mb-6">
                            <div class="card-header">
                                <h3 class="card-title">Compensation & Benefits</h3>
                            </div>
                            <div class="card-body">
                                <div class="space-y-4">
                                    <div class="border rounded-lg p-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <x-text-input label="Start Date*" type="date"
                                                wire:model="packageStartDate"
                                                errorMessage="{{ $errors->first('packageStartDate') }}" />
                                            <x-text-input label="End Date" type="date" wire:model="packageEndDate"
                                                errorMessage="{{ $errors->first('packageEndDate') }}" />

                                            <x-select label="Manager" wire:model="managerId"
                                                errorMessage="{{ $errors->first('managerId') }}">
                                                <option value="">Select a manager</option>
                                                @foreach ($managersList as $manager)
                                                    <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                                @endforeach
                                            </x-select>
                                            <div>

                                                <x-text-input label="Gross Salary*" type="number"
                                                    wire:model="grossSalary"
                                                    errorMessage="{{ $errors->first('grossSalary') }}"
                                                    min="{{ $selectedPackage->gross_min }}"
                                                    max="{{ $selectedPackage->gross_max }}" />
                                                <span class="text-sm text-gray-500">
                                                    Amount: {{ $selectedPackage->gross_min }} ->
                                                    {{ $selectedPackage->gross_max }}
                                                </span>
                                            </div>

                                            <div>
                                                <x-text-input label="Social Insurance Salary*" type="number"
                                                    wire:model="insuranceAmount"
                                                    errorMessage="{{ $errors->first('insuranceAmount') }}" />
                                            </div>

                                            <div class="col-span-2">
                                                <label class="flex items-center">
                                                    <input type="checkbox" wire:model="isTaxable" class="form-checkbox">
                                                    <span class="ml-2">Is Taxable</span>
                                                </label>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    Check if this benefit configuration should be subject to taxation
                                                </p>
                                            </div>

                                        </div>
                                    </div>
                                    @foreach ($packageDetails as $index => $detail)
                                        <div class="border rounded-lg p-4">
                                            <div class="grid grid-cols-4">
                                                <div class="col-span-3">
                                                    <div class="flex justify-between items-center mb-2">
                                                        <p class="font-bold">{{ $detail['name'] }}</p>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <x-text-input label="Amount*" type="number"
                                                            wire:model="packageDetails.{{ $index }}.amount"
                                                            errorMessage="{{ $errors->first('packageDetails.' . $index . '.amount') }}"
                                                            min="{{ $detail['amount_min'] }}"
                                                            max="{{ $detail['amount_max'] }}" />
                                                    </div>
                                                </div>
                                                <div class="col-span-1">
                                                    <div class="flex items-end flex-col ml-2">
                                                        <span class="text-sm text-gray-500">
                                                            {{ ucfirst($detail['type']) }}
                                                        </span>
                                                        <span class="text-sm text-gray-500">
                                                            {{ $detail['amount_min'] }} ->
                                                            {{ $detail['amount_max'] }}
                                                        </span>
                                                        <span class="text-sm text-gray-500">
                                                            Paid to {{ $detail['receiver'] }}
                                                        </span>
                                                        <span class="text-sm text-gray-500">
                                                            {{ $detail['is_hidden'] ? 'Hidden' : '' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>


                    @endif
                @else
                    <div class="alert alert-warning">
                        No employee selected for package application.
                    </div>
                @endif
            </div>

            <x-slot name="footer">
                <div class="mt-4 flex justify-end gap-3">
                    <x-secondary-button wire:click="closeApplyPackageModal">Cancel</x-secondary-button>
                    <x-primary-button wire:click.prevent="applyPackage" loadingFunction="applyPackage">
                        Apply Package
                    </x-primary-button>
                </div>
            </x-slot>
        </x-modal>
    @endif
</div>
