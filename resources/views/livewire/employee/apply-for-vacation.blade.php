<div class="space-y-5 profile-page mx-auto" style="max-width: 1000px;">
    <div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Apply for Vacation For ({{ $employee->name }})</h4>
            </div>
            <div class="card-body px-6 pb-6">
                @if (!$employee)
                    <div class="alert alert-warning">
                        Your employee record was not found. Please contact HR.
                    </div>
                @else
                    <form wire:submit.prevent="openConfirmModal">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5 mt-5">
                            <!-- Vacation Type Selection -->
                            <x-select wire:model.live="selectedEmployee" label="Select Another Employee"
                                errorMessage="{{ $errors->first('selectedEmployee') }}"
                                class="w-full {{ $errors->has('selectedEmployee') ? '!border-danger-500' : '' }}">
                                <option value="">-- You are applying for yourself --</option>
                                @foreach ($childrenEmployees as $e)
                                    <option value="{{ $e->id }}">
                                        {{ $e->name }}
                                    </option>
                                @endforeach
                            </x-select>

                            <!-- Vacation Type Selection -->
                            <x-select wire:model.live="selectedBenefitId" label="Vacation Type*"
                                errorMessage="{{ $errors->first('selectedBenefitId') }}"
                                class="w-full {{ $errors->has('selectedBenefitId') ? '!border-danger-500' : '' }}">
                                <option value="">-- Select Vacation Type --</option>
                                @foreach ($vacationBenefits as $benefit)
                                    <option value="{{ $benefit->id }}">
                                        {{ $benefit->name }} (Available: {{ $benefit->current_balance }} hours)
                                    </option>
                                @endforeach
                            </x-select>


                            <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Date Range -->
                                <div>
                                    <x-text-input wire:model.live="fromDate" label="From Date*" type="date"
                                        errorMessage="{{ $errors->first('fromDate') }}" />
                                </div>
                                <div>
                                    <x-text-input wire:model.live="toDate" label="To Date*" type="date"
                                        errorMessage="{{ $errors->first('toDate') }}" />
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-span-1 md:col-span-2">
                                <x-textarea wire:model="description" label="Reason for Vacation (Optional)"
                                    errorMessage="{{ $errors->first('description') }}" />
                            </div>
                        </div>

                        <!-- Selected Days Table -->
                        @if (count($days) > 0)
                            <div class="border rounded-lg p-4 mb-5">
                                <h5 class="font-medium mb-3">Selected Days (Total Hours: {{ $totalHours }})</h5>
                                <div class="overflow-x-auto">
                                    <table
                                        class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                        <thead class="bg-slate-200 dark:bg-slate-700">
                                            <tr>
                                                <th scope="col" class="table-th">Date</th>
                                                <th scope="col" class="table-th">Hours</th>
                                                <th scope="col" class="table-th">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                            @foreach ($days as $index => $day)
                                                <tr>
                                                    <td class="table-td">
                                                        {{ \Carbon\Carbon::parse($day['vacation_date'])->format('d/m/Y (D)') }}
                                                    </td>
                                                    <td class="table-td">
                                                        <input type="number" class="form-control py-2 w-20"
                                                            min="1" max="24"
                                                            wire:model.live="days.{{ $index }}.hours">
                                                        @error('days.' . $index . '.hours')
                                                            <span class="text-danger text-sm">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td class="table-td">
                                                        <button wire:click="removeDay({{ $index }})"
                                                            class="action-btn" type="button">
                                                            <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        @if (count($vacationBenefits) === 0)
                            <div class="alert alert-info">
                                Employee don't have any available vacation benefits or have no balance.
                            </div>
                        @else
                            <!-- Submit Button -->
                            <div class="flex justify-end md:w-full">
                                <x-primary-button type="submit" class="w-auto sm:w-full"
                                    loadingFunction="openConfirmModal">
                                    Review Application
                                </x-primary-button>
                            </div>
                        @endif
                    </form>
                @endif
            </div>
        </div>

        <!-- Confirmation Modal -->
        <x-modal wire:model="showConfirmModal" maxWidth="4xl">
            <x-slot name="title">
                Confirm Vacation Application
            </x-slot>

            <div class="py-4">
                <div class="mb-5">
                    <h5 class="font-medium text-lg mb-3">Application Summary</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Vacation Type</p>
                            <p class="font-medium">{{ $selectedBenefit->name ?? '' }}</p>
                        </div>
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Total Hours</p>
                            <p class="font-medium">{{ $totalHours }} hours</p>
                        </div>
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">Current Balance</p>
                            <p class="font-medium">{{ $selectedBenefit->current_balance ?? 0 }} hours</p>
                        </div>
                        <div class="border rounded p-3">
                            <p class="text-sm text-slate-500">New Balance After Approval</p>
                            <p class="font-medium">{{ ($selectedBenefit->current_balance ?? 0) - $totalHours }} hours
                            </p>
                        </div>
                        @if ($description)
                            <div class="border rounded p-3 md:col-span-2">
                                <p class="text-sm text-slate-500">Reason</p>
                                <p>{{ $description }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mb-5">
                    <h5 class="font-medium text-lg mb-3">Selected Days</h5>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class="bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class="table-th">Date</th>
                                    <th scope="col" class="table-th">Day</th>
                                    <th scope="col" class="table-th">Hours</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @foreach ($days as $day)
                                    <tr>
                                        <td class="table-td">
                                            {{ \Carbon\Carbon::parse($day['vacation_date'])->format('d/m/Y') }}</td>
                                        <td class="table-td">
                                            {{ \Carbon\Carbon::parse($day['vacation_date'])->format('l') }}</td>
                                        <td class="table-td">{{ $day['hours'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 dark:bg-slate-700">
                                <tr>
                                    <td colspan="2" class="table-td text-right font-medium">Total:</td>
                                    <td class="table-td font-medium">{{ $totalHours }} hours</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="mt-5 bg-yellow-50 p-3 rounded border border-yellow-200">
                    <p class="text-sm text-yellow-800">
                        <iconify-icon icon="mdi:information-outline" class="text-lg mr-1"></iconify-icon>
                        Please confirm vacation application. Once submitted, it will be pending approval from HR.
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

        <!-- Vacation Benefits Info -->
        <div class="card mt-5">
            <div class="card-header mb-5">
                <h4 class="card-title">Vacation Benefits</h4>
            </div>
            <div class="card-body px-6 pb-6">
                @if (count($vacationBenefits) === 0)
                    <div class="alert alert-info">
                        Employee don't have any vacation benefits.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-5">
                        @foreach ($vacationBenefits as $benefit)
                            <div class="border rounded-lg p-4 hover:bg-slate-50">
                                <div class="flex justify-between items-center">
                                    <h6 class="font-medium text-slate-900">{{ $benefit->name }}</h6>
                                    <span
                                        class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Active</span>
                                </div>
                                <div class="grid grid-cols-2 gap-1 mt-2 text-sm">
                                    <div class="flex items-center">
                                        <span class="text-slate-500 mr-2">Balance:</span>
                                        <span
                                            class="font-medium">{{ $benefit->current_balance }}/{{ $benefit->max_balance }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-slate-500 mr-2">Hourly Rate:</span>
                                        <span>{{ $benefit->hour_price }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-slate-500 mr-2">Increment:</span>
                                        <span>{{ $benefit->inc_rate }} per {{ $benefit->type }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
