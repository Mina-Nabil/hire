<div class="space-y-5 profile-page mx-auto">

    <div class="flex justify-between">
        <div class="flex gap-5">
            <h4>
                <b>{{ $vacancy->position->name }} Vacancy</b>
            </h4>
            <div class="dropdown relative">
                <button class="btn inline-flex justify-center btn-dark items-center btn-sm" type="button"
                    id="darkDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Manage Vacancy
                    <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2" icon="ic:round-keyboard-arrow-down"></iconify-icon>
                </button>
                <ul
                    class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                                z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">


                    <li wire:click="openEditVacancySec()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                        dark:hover:text-white cursor-pointer">
                        Edit Vacancy
                    </li>

                    <li wire:click='$dispatch("showConfirmation", {
                        title: "Delete Applicant",
                        message: "Are you sure you want to delete this applicant?",
                        color: "danger",
                        action: "deleteApplicant",
                    })'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                        dark:hover:text-white cursor-pointer">
                        Delete Vacancy
                    </li>

                </ul>
            </div>
        </div>

    </div>



    <div class="card-body flex flex-col col-span-2" >
        <div class="card-text h-full">

            <div class="flex" wire:ignore>
                <ul class="nav nav-tabs flex flex-col md:flex-row flex-wrap list-none border-b-0 pl-0" id="tabs-tab"
                    role="tablist">
                    <li class="nav-item" role="presentation" wire:click="changeSection('info')">
                        <a href="#tabs-profile-withIcon"
                            class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($section === 'info') active @endif dark:text-slate-300"
                            id="tabs-profile-withIcon-tab" data-bs-toggle="pill" data-bs-target="#tabs-profile-withIcon"
                            role="tab" aria-controls="tabs-profile-withIcon" aria-selected="false">
                            <iconify-icon class="mr-1" icon="hugeicons:new-job"></iconify-icon>
                            Vacancy Information</a>
                    </li>
                    <li class="nav-item" role="presentation" wire:click="changeSection('applications')">
                        <a href="#tabs-messages-withIcon"
                            class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($section === 'applications') active @endif dark:text-slate-300"
                            id="tabs-messages-withIcon-tab" data-bs-toggle="pill"
                            data-bs-target="#tabs-messages-withIcon" role="tab"
                            aria-controls="tabs-messages-withIcon" aria-selected="false">
                            <iconify-icon class="mr-1" icon="healthicons:city-worker"></iconify-icon>
                            Applicants & Interviews </a>
                    </li>

                </ul>
                <div>
                    <h4>
                        <iconify-icon class="ml-3" style="position: absolute" wire:loading wire:target="changeSection"
                            icon="svg-spinners:180-ring"></iconify-icon>
                    </h4>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="tabs-tabContent">

                @if ($section === 'info')
                    <div class="tab-pane fade show active" id="tabs-profile-withIcon" role="tabpanel"
                        aria-labelledby="tabs-profile-withIcon-tab">
                        @include('livewire.recruitment.partials.vacancy-information')
                    </div>
                @endif

                @if ($section === 'applications')
                    <div class="tab-pane fade show active" id="tabs-messages-withIcon" role="tabpanel"
                        aria-labelledby="tabs-messages-withIcon-tab">
                        @include('livewire.recruitment.partials.vacancy-applicants')
                    </div>
                @endif

            </div>
        </div>
    </div>



    <!-- Edit Vacancy Modal -->
    <x-modal wire:model="editVacancyModal">
        <x-slot name="title">Edit Vacancy</x-slot>
        <div class="space-y-4">
            <!-- Position Selection -->
            <x-select title="Position" wire:model="positionId" errorMessage="{{ $errors->first('positionId') }}">
                <option value="">-- Select Position --</option>
                @foreach ($positions as $position)
                    <option value="{{ $position->id }}">{{ $position->name }} - {{ $position->department->name }}
                    </option>
                @endforeach
            </x-select>

            <!-- Assigned To -->
            <x-select title="Assigned To" wire:model="assignedTo" errorMessage="{{ $errors->first('assignedTo') }}">
                <option value="">-- Select User --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </x-select>

            <!-- Hiring Manager -->
            <x-select title="Hiring Manager" wire:model="hiringManagerId"
                errorMessage="{{ $errors->first('hiringManagerId') }}">
                <option value="">-- Select Hiring Manager --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </x-select>

            <!-- HR Manager -->
            <x-select title="HR Manager" wire:model="hrManagerId" errorMessage="{{ $errors->first('hrManagerId') }}">
                <option value="">-- Select HR Manager --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </x-select>

            <!-- Vacancy Type & Status -->
            <div class="grid grid-cols-2 gap-4">
                <x-select title="Type" wire:model="vacancyType" errorMessage="{{ $errors->first('vacancyType') }}">
                    <option value="">-- Select Type --</option>
                    @foreach ($vacancyTypes as $key => $type)
                        <option value="{{ $key }}">{{ $type }}</option>
                    @endforeach
                </x-select>

                <x-select title="Status" wire:model="vacancyStatus"
                    errorMessage="{{ $errors->first('vacancyStatus') }}">
                    <option value="">-- Select Status --</option>
                    @foreach ($vacancyStatuses as $key => $status)
                        <option value="{{ $key }}">{{ $status }}</option>
                    @endforeach
                </x-select>
            </div>

            <!-- Job Details -->
            <x-textarea title="Job Responsibilities" wire:model="jobResponsibilities"
                errorMessage="{{ $errors->first('jobResponsibilities') }}" rows="4" />

            <x-textarea title="Arabic Job Responsibilities" wire:model="arabicJobResponsibilities"
                errorMessage="{{ $errors->first('arabicJobResponsibilities') }}" rows="4" />

            <x-textarea title="Job Qualifications" wire:model="jobQualifications"
                errorMessage="{{ $errors->first('jobQualifications') }}" rows="4" />

            <x-textarea title="Arabic Job Qualifications" wire:model="arabicJobQualifications"
                errorMessage="{{ $errors->first('arabicJobQualifications') }}" rows="4" />

            <x-textarea title="Job Benefits" wire:model="jobBenefits"
                errorMessage="{{ $errors->first('jobBenefits') }}" rows="4" />

            <x-textarea title="Arabic Job Benefits" wire:model="arabicJobBenefits"
                errorMessage="{{ $errors->first('arabicJobBenefits') }}" rows="4" />

            <x-text-input title="Salary Information" wire:model="jobSalary"
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
                            <x-text-input title="Question" wire:model="questions.{{ $index }}.question"
                                errorMessage="{{ $errors->first('questions.' . $index . '.question') }}" />

                            <x-text-input title="Arabic Question"
                                wire:model="questions.{{ $index }}.arabic_question"
                                errorMessage="{{ $errors->first('questions.' . $index . '.arabic_question') }}" />

                            <x-select title="Type" wire:model="questions.{{ $index }}.type"
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

                            @if (in_array($question['type'], ['select', 'checkbox', 'radio']))
                                <x-text-input title="Options (comma separated)"
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
                            <x-text-input title="Date" type="date" wire:model="slots.{{ $index }}.date"
                                errorMessage="{{ $errors->first('slots.' . $index . '.date') }}" />

                            <x-text-input title="Start Time" type="time"
                                wire:model="slots.{{ $index }}.start_time"
                                errorMessage="{{ $errors->first('slots.' . $index . '.start_time') }}" />

                            <x-text-input title="End Time" type="time"
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

        <x-slot name="footer">
            <div class="flex justify-end gap-3">
                <x-secondary-button wire:click="closeEditVacancySec">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="updateVacancy" loadingFunction="updateVacancy">
                    Update Vacancy
                </x-primary-button>
            </div>
        </x-slot>
    </x-modal>

    <!-- Include all the interview management modals -->
    @include('livewire.recruitment.partials.interview-modals')
</div>
