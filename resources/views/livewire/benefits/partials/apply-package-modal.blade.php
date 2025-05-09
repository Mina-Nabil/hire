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

                    <!-- Package Selection -->
                    <div class="mb-6">
                        <x-select title="Select Package" wire:model="selectedPackageId" wire:change="loadPackageDetails"
                            errorMessage="{{ $errors->first('selectedPackageId') }}">
                            <option value="">-- Select Package --</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}">{{ $package->name }}</option>
                            @endforeach
                        </x-select>
                    </div>

                    @if ($selectedPackage)
                        <!-- Package Details Section -->
                        <div class="card mb-6">
                            <div class="card-header">
                                <h3 class="card-title">Package Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="space-y-4">
                                    <div class="border rounded-lg p-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <x-text-input label="Start Date*" type="date"
                                                wire:model="packageStartDate" />
                                            <x-text-input label="End Date" type="date" wire:model="packageEndDate" />
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
                                                            {{ $detail['is_gross'] ? 'Gross' : '' }}
                                                        </span>
                                                        <span class="text-sm text-gray-500">
                                                            {{ $detail['is_grand_gross'] ? 'Grand Gross' : '' }}
                                                        </span>
                                                        <span class="text-sm text-gray-500">
                                                            {{ $detail['is_net'] ? 'Net' : '' }}
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

                        <!-- Vacation Benefits Section -->
                        <div class="card mb-6">
                            <div class="card-header">
                                <h3 class="card-title">Vacation Benefits</h3>
                            </div>
                            <div class="card-body">
                                <div class="space-y-4">
                                    @foreach ($vacationBenefits as $index => $benefit)
                                        <div class="border rounded-lg p-4">
                                            <div class="grid grid-cols-4">
                                                <div class="col-span-3">
                                                    <div class="flex justify-between items-center mb-2">
                                                        <p class="font-bold">{{ $benefit['name'] }}</p>
                                                    </div>
                                                    <div class="grid grid-cols-3 gap-4">
                                                        <x-text-input label="Increment Rate*" type="number"
                                                            wire:model="vacationBenefits.{{ $index }}.inc_rate"
                                                            errorMessage="{{ $errors->first('vacationBenefits.' . $index . '.inc_rate') }}"
                                                            min="{{ $benefit['inc_rate_min'] }}"
                                                            max="{{ $benefit['inc_rate_max'] }}" />
                                                        <x-text-input label="Max Balance*" type="number"
                                                            wire:model="vacationBenefits.{{ $index }}.max_balance"
                                                            errorMessage="{{ $errors->first('vacationBenefits.' . $index . '.max_balance') }}"
                                                            min="{{ $benefit['max_balance_min'] }}"
                                                            max="{{ $benefit['max_balance_max'] }}" />
                                                        <x-text-input label="Current Balance*" type="number"
                                                            wire:model="vacationBenefits.{{ $index }}.current_balance"
                                                            errorMessage="{{ $errors->first('vacationBenefits.' . $index . '.current_balance') }}" />
                                                        <x-text-input label="Hour Price*" type="number"
                                                            wire:model="vacationBenefits.{{ $index }}.hour_price"
                                                            errorMessage="{{ $errors->first('vacationBenefits.' . $index . '.hour_price') }}"
                                                            min="{{ $benefit['hour_price_min'] }}"
                                                            max="{{ $benefit['hour_price_max'] }}" />
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
                                </div>
                            </div>
                        </div>

                        <!-- Working Parameters Section -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Attendance Calculation</h3>
                            </div>
                            <div class="card-body">
                                @if ($attendanceCalculation)
                                    <span class="text-sm text-gray-500">
                                        @switch($attendanceCalculation)
                                            @case('fixed')
                                                Fixed starting and ending time per day
                                            @break

                                            @case('semi-flexible')
                                                Start and end can be a time range (8:00 - 9:00) -> (17:00 - 18:00)
                                            @break

                                            @case('flexible')
                                                No Start or End time required, any 8 hours per day
                                            @break
                                        @endswitch
                                    </span>
                                @endif
                                <div class="grid grid-cols-2 gap-4 mt-5">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Attendance
                                            Calculation</label>
                                        <x-select wire:model.live="attendanceCalculation"
                                            errorMessage="{{ $errors->first('attendanceCalculation') }}">
                                            @foreach ($attendanceCalculations as $type)
                                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                    <x-text-input label="Daily Working Hours" type="number"
                                        wire:model="dailyWorkingHours"
                                        errorMessage="{{ $errors->first('dailyWorkingHours') }}" min="1"
                                        max="24" />
                                    <x-text-input label="Working Day Start Min" type="time"
                                        wire:model="workingDayStartMin"
                                        errorMessage="{{ $errors->first('workingDayStartMin') }}" />
                                    <x-text-input label="Working Day Start Max" type="time"
                                        wire:model="workingDayStartMax"
                                        errorMessage="{{ $errors->first('workingDayStartMax') }}" />
                                    <x-text-input label="Working Day End Min" type="time"
                                        wire:model="workingDayEndMin"
                                        errorMessage="{{ $errors->first('workingDayEndMin') }}" />
                                    <x-text-input label="Working Day End Max" type="time"
                                        wire:model="workingDayEndMax"
                                        errorMessage="{{ $errors->first('workingDayEndMax') }}" />
                                    <x-text-input label="Overtime Rate" type="number" step="0.01"
                                        wire:model="overtimeRate" errorMessage="{{ $errors->first('overtimeRate') }}"
                                        min="1" />
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
