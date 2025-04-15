<div class="card">
    <header class="card-header noborder">
        <div class="flex flex-wrap justify-between items-center w-full">
            <div class="flex-col items-center min-w-[310px]">
                <h4 class="card-title">Edit Vacancy</h4>
                <h5 class="card-subtitle">Edit the vacancy details like questions, time slots, etc.</h5>
            </div>
            <div>
                <x-primary-button class=btn-sm wire:click.prevent="updateVacancy" loadingFunction="updateVacancy">
                    <i class="fas fa-save"></i> Save
                </x-primary-button>
            </div>
        </div>
    </header>

    <div class="card-body p-6">

        <div class="space-y-4">
            <!-- Position Selection -->
            <x-select label="Position" wire:model="positionId" errorMessage="{{ $errors->first('positionId') }}">
                <option value="">-- Select Position --</option>
                @foreach ($positions as $position)
                    <option value="{{ $position->id }}">{{ $position->name }} - {{ $position->department->name }}
                    </option>
                @endforeach
            </x-select>

            <!-- Assigned To -->
            <x-select label="Assigned To" wire:model="assignedTo" errorMessage="{{ $errors->first('assignedTo') }}">
                <option value="">-- Select User --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </x-select>

            <!-- Hiring Manager -->
            <x-select label="Hiring Manager" wire:model="hiringManagerId"
                errorMessage="{{ $errors->first('hiringManagerId') }}">
                <option value="">-- Select Hiring Manager --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </x-select>

            <!-- HR Manager -->
            <x-select label="HR Manager" wire:model="hrManagerId" errorMessage="{{ $errors->first('hrManagerId') }}">
                <option value="">-- Select HR Manager --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </x-select>

            <!-- Vacancy Type & Status -->
            <div class="grid grid-cols-2 gap-4">
                <x-select label="Type" wire:model="vacancyType" errorMessage="{{ $errors->first('vacancyType') }}">
                    <option value="">-- Select Type --</option>
                    @foreach ($vacancyTypes as $key => $type)
                        <option value="{{ $key }}">{{ $type }}</option>
                    @endforeach
                </x-select>

                <x-select label="Status" wire:model="vacancyStatus"
                    errorMessage="{{ $errors->first('vacancyStatus') }}">
                    <option value="">-- Select Status --</option>
                    @foreach ($vacancyStatuses as $key => $status)
                        <option value="{{ $key }}">{{ $status }}</option>
                    @endforeach
                </x-select>
            </div>

            <!-- Job Details -->
            <x-textarea label="Job Responsibilities" wire:model="jobResponsibilities"
                errorMessage="{{ $errors->first('jobResponsibilities') }}" rows="4" />

            <x-textarea label="Arabic Job Responsibilities" wire:model="arabicJobResponsibilities"
                errorMessage="{{ $errors->first('arabicJobResponsibilities') }}" rows="4" />

            <x-textarea label="Job Qualifications" wire:model="jobQualifications"
                errorMessage="{{ $errors->first('jobQualifications') }}" rows="4" />

            <x-textarea label="Arabic Job Qualifications" wire:model="arabicJobQualifications"
                errorMessage="{{ $errors->first('arabicJobQualifications') }}" rows="4" />

            <x-textarea label="Job Benefits" wire:model="jobBenefits"
                errorMessage="{{ $errors->first('jobBenefits') }}" rows="4" />

            <x-textarea label="Arabic Job Benefits" wire:model="arabicJobBenefits"
                errorMessage="{{ $errors->first('arabicJobBenefits') }}" rows="4" />

            <x-text-input label="Salary Information" wire:model="jobSalary"
                errorMessage="{{ $errors->first('jobSalary') }}" />

            <!-- Questions Section -->
            <div class="mt-8">
                <h4 class="text-lg font-medium mb-3">Questions</h4>
                @foreach ($questions as $index => $question)
                    <div class="border rounded-md p-4 mb-4">
                        <div class="flex justify-between mb-2">
                            <h5 class="font-medium">Question {{ $index + 1 }}</h5>
                            <button type="button" wire:click="removeQuestion({{ $index }})"
                                class="text-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="space-y-3">
                            <x-text-input label="Question" wire:model="questions.{{ $index }}.question"
                                errorMessage="{{ $errors->first('questions.' . $index . '.question') }}" />

                            <x-text-input label="Arabic Question"
                                wire:model="questions.{{ $index }}.arabic_question"
                                errorMessage="{{ $errors->first('questions.' . $index . '.arabic_question') }}" />

                            <x-select label="Type" wire:model.live="questions.{{ $index }}.type"
                                errorMessage="{{ $errors->first('questions.' . $index . '.type') }}">
                                @foreach ($questionTypes as $type)
                                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                @endforeach
                            </x-select>

                            <div class="flex items-center">
                                <input type="checkbox" id="required-{{ $index }}"
                                    wire:model="questions.{{ $index }}.required"
                                    class="form-checkbox h-5 w-5 text-blue-600">
                                <label for="required-{{ $index }}" class="ml-2 text-gray-700">Required</label>
                            </div>

                            @if (in_array($question['type'], ['select', 'radio']))
                                <x-text-input label="Options (comma separated)"
                                    wire:model="questions.{{ $index }}.options"
                                    errorMessage="{{ $errors->first('questions.' . $index . '.options') }}" />
                            @endif
                        </div>
                    </div>
                @endforeach

                <button type="button" wire:click="addQuestion" class="btn btn-secondary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Question
                </button>
            </div>

            <!-- Time Slots Section -->
            <div class="mt-8">
                <h4 class="text-lg font-medium mb-3">Time Slots</h4>
                @foreach ($slots as $index => $slot)
                    <div class="border rounded-md p-4 mb-4">
                        <div class="flex justify-between mb-2">
                            <h5 class="font-medium">Slot {{ $index + 1 }}</h5>
                            <button type="button" wire:click="removeSlot({{ $index }})" class="text-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <x-text-input label="Date" type="date" wire:model="slots.{{ $index }}.date"
                                errorMessage="{{ $errors->first('slots.' . $index . '.date') }}" />

                            <x-text-input label="Start Time" type="time"
                                wire:model="slots.{{ $index }}.start_time"
                                errorMessage="{{ $errors->first('slots.' . $index . '.start_time') }}" />

                            <x-text-input label="End Time" type="time"
                                wire:model="slots.{{ $index }}.end_time"
                                errorMessage="{{ $errors->first('slots.' . $index . '.end_time') }}" />
                        </div>
                    </div>
                @endforeach

                <button type="button" wire:click="addSlot" class="btn btn-secondary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Time Slot
                </button>
            </div>
        </div>

    </div>
</div>
