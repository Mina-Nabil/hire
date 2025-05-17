<div>
    @if ($showApplyAttendanceModal)
        <!-- Apply Attendance Configuration Modal -->
        <x-modal wire:model="showApplyAttendanceModal">
            <x-slot name="title">Apply Attendance Configuration</x-slot>

            <div class="space-y-6">
                @if (isset($selectedEmployee))
                    <div class="alert alert-info mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            <div>
                                <p class="font-medium">Applying attendance configuration for:</p>
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
                    <!-- Working Days Section -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Working Days</h3>
                        </div>
                        <div class="card-body">
                            <div class="flex space-x-4 mb-4 mt-5">
                                <button type="button"
                                    wire:click="$set('workingDays', ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'])"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    Sun - Thu
                                </button>
                                <button type="button"
                                    wire:click="$set('workingDays', ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'])"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    All days
                                </button>

                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-5">
                                @foreach ($AllworkingDays as $day)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" wire:model="workingDays" value="{{ $day }}"
                                            class="form-checkbox h-5 w-5 text-primary-600">
                                        <span class="text-sm font-medium text-gray-700">{{ ucfirst($day) }}</span>
                                    </label>
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

                            <div class="grid grid-cols-2 gap-4 mt-5">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Attendance
                                        Calculation</label>


                                    <div class="col-span-2 mt-5 mb-5">
                                        <button type="button" wire:click="setFixedCalculation"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                            Fixed Calculation
                                        </button>
                                    </div>

                                    <x-select wire:model.live="attendanceCalculation"
                                        errorMessage="{{ $errors->first('attendanceCalculation') }}">
                                        @foreach ($attendanceCalculations as $type)
                                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                        @endforeach
                                    </x-select>
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
                                </div>
                                <x-text-input label="Daily Working Hours" type="number" wire:model="dailyWorkingHours"
                                    errorMessage="{{ $errors->first('dailyWorkingHours') }}" min="1"
                                    max="24" />
                                <x-text-input label="Working Day Start Min" type="time"
                                    wire:model="workingDayStartMin"
                                    errorMessage="{{ $errors->first('workingDayStartMin') }}" />
                                <x-text-input label="Working Day Start Max" type="time"
                                    wire:model="workingDayStartMax"
                                    errorMessage="{{ $errors->first('workingDayStartMax') }}" />
                                <x-text-input label="Working Day End Min" type="time" wire:model="workingDayEndMin"
                                    errorMessage="{{ $errors->first('workingDayEndMin') }}" />
                                <x-text-input label="Working Day End Max" type="time" wire:model="workingDayEndMax"
                                    errorMessage="{{ $errors->first('workingDayEndMax') }}" />
                                <x-text-input label="Overtime Rate" type="number" step="0.01"
                                    wire:model="overtimeRate" errorMessage="{{ $errors->first('overtimeRate') }}"
                                    min="1" />

                                <label class="flex items-center mt-2 col-span-2">
                                    <input type="checkbox" wire:model="isAutomaticOvertime" class="form-checkbox">
                                    <span class="ml-2">Enable Automatic Overtime from Attendance Sheet</span>
                                </label>

                                <label class="flex items-center mt-2 col-span-2">
                                    <input type="checkbox" wire:model="isRequireAttendanceApproval" class="form-checkbox">
                                    <span class="ml-2">Require Attendance Approval</span>
                                </label>

                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        No employee selected for attendance configuration.
                    </div>
                @endif
            </div>

            <x-slot name="footer">
                <div class="mt-4 flex justify-end gap-3">
                    <x-secondary-button wire:click="closeApplyAttendanceModal">Cancel</x-secondary-button>
                    <x-primary-button wire:click.prevent="applyAttendance" loadingFunction="applyAttendance">
                        Apply Configuration
                    </x-primary-button>
                </div>
            </x-slot>
        </x-modal>
    @endif
</div>
