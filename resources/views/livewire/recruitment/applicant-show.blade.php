<div class="space-y-5 profile-page mx-auto">
    <div class="flex justify-between">
        <div class="flex gap-5">
            <h4>
                <b>{{ $applicant->full_name }}</b>
            </h4>
            <div class="dropdown relative">
                <button class="btn inline-flex justify-center btn-dark items-center btn-sm" type="button"
                    id="darkDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Edit Applicant Information
                    <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2" icon="ic:round-keyboard-arrow-down"></iconify-icon>
                </button>
                <ul
                    class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                                z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">


                    <li wire:click="toggleMainInfo()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                        dark:hover:text-white cursor-pointer">
                        Main Information
                    </li>
                    <li wire:click="toggleEducation()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                        dark:hover:text-white cursor-pointer">
                        Education
                    </li>
                    <li wire:click="toggleTraining()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                        dark:hover:text-white cursor-pointer">
                        Training
                    </li>
                    <li wire:click="toggleExperience()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                        dark:hover:text-white cursor-pointer">
                        Experience
                    </li>
                    <li wire:click="toggleSkills()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                        dark:hover:text-white cursor-pointer">
                        Skills
                    </li>
                    <li wire:click="toggleLanguages()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                        dark:hover:text-white cursor-pointer">
                        Languages
                    </li>
                    <li wire:click="toggleReferences()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                        dark:hover:text-white cursor-pointer">
                        References
                    </li>
                    <li wire:click="toggleHealth()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                        dark:hover:text-white cursor-pointer">
                        Health
                    </li>

                </ul>
            </div>
        </div>
        <div class="float-right grid-col-2">

            <a wire:click='$dispatch("showConfirmation", {
                title: "Delete Applicant",
                message: "Are you sure you want to delete this applicant?",
                color: "danger",
                action: "deleteApplicant",
            })'
                class="btn btn-danger inline-flex justify-center btn-sm">
                Delete Applicant
            </a>


        </div>
    </div>

    <div class="card-body flex flex-col col-span-2" wire:ignore>
        <div class="card-text h-full">
            <div class="flex">
                <ul class="nav nav-tabs flex flex-col md:flex-row flex-wrap list-none border-b-0 pl-0" id="tabs-tab"
                    role="tablist">
                    <li class="nav-item" role="presentation" wire:click="changeSection('info')">
                        <a href="#tabs-profile-withIcon"
                            class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($section === 'info') active @endif dark:text-slate-300"
                            id="tabs-profile-withIcon-tab" data-bs-toggle="pill" data-bs-target="#tabs-profile-withIcon"
                            role="tab" aria-controls="tabs-profile-withIcon" aria-selected="false">
                            <iconify-icon class="mr-1" icon="hugeicons:new-job"></iconify-icon>
                            Applicant Information</a>
                    </li>
                    <li class="nav-item" role="presentation" wire:click="changeSection('applications')">
                        <a href="#tabs-messages-withIcon"
                            class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($section === 'applications') active @endif dark:text-slate-300"
                            id="tabs-messages-withIcon-tab" data-bs-toggle="pill"
                            data-bs-target="#tabs-messages-withIcon" role="tab"
                            aria-controls="tabs-messages-withIcon" aria-selected="false">
                            <iconify-icon class="mr-1" icon="healthicons:city-worker"></iconify-icon>
                            Applications </a>
                    </li>
                    <li class="nav-item" role="presentation" wire:click="changeSection('documents')">
                        <a href="#tabs-messages-withIcon"
                            class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($section === 'documents') active @endif dark:text-slate-300"
                            id="tabs-messages-withIcon-tab" data-bs-toggle="pill"
                            data-bs-target="#tabs-messages-withIcon" role="tab"
                            aria-controls="tabs-messages-withIcon" aria-selected="false">
                            <iconify-icon class="mr-1" icon="mdi:file-document-outline"></iconify-icon>
                            Documents </a>
                    </li>
                </ul>
                <div>
                    <h4>
                        <iconify-icon class="ml-3" style="position: absolute" wire:loading wire:target="changeSection"
                            icon="svg-spinners:180-ring"></iconify-icon>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="tabs-tabContent">
        <!-- Applicant Information Tab -->
        @if ($section === 'info')
            <div class="tab-pane fade show active" id="tabs-profile-withIcon" role="tabpanel"
                aria-labelledby="tabs-profile-withIcon-tab">
                @include('livewire.recruitment.partials.applicant-profile')
            </div>
        @endif
        <!-- Applications Tab -->
        @if ($section === 'applications')
            <div class="tab-pane fade show active" id="tabs-messages-withIcon" role="tabpanel"
                aria-labelledby="tabs-messages-withIcon-tab">
                <div class="space-y-6">
                    @include('livewire.recruitment.partials.applicant-applications')

                    <!-- Display interviews if available -->
                    @if (isset($interviews) && count($interviews) > 0)
                        <div class="mt-8">
                            @include('livewire.recruitment.partials.applicant-interviews')
                        </div>
                    @endif

                    <!-- Display offers -->
                    <div class="mt-8">
                        @include('livewire.recruitment.partials.applicant-offers')
                    </div>
                </div>
            </div>
        @endif
        <!-- Documents Tab -->
        @if ($section === 'documents')
            <div class="tab-pane fade show active" id="tabs-settings-withIcon" role="tabpanel"
                aria-labelledby="tabs-settings-withIcon-tab">
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h5 class="card-title">Documents</h5>
                        <button type="button" class="btn btn-primary btn-sm" wire:click="openDocumentUploadModal">
                            <i class="fas fa-upload mr-1"></i> Upload Document
                        </button>
                    </div>
                    <div class="card-body">
                        @if ($applicant->cv_url || ($applicant->documents && $applicant->documents->count() > 0))
                            <div class="space-y-4">
                                @if ($applicant->cv_url)
                                    <div class="flex justify-between items-center p-3 border rounded-md">
                                        <div class="flex items-center">
                                            <div class="text-primary mr-3">
                                                <i class="fas fa-file-pdf text-xl"></i>
                                            </div>
                                            <div>
                                                <h6 class="font-medium">Resume/CV</h6>
                                                <p class="text-sm text-slate-500">Uploaded:
                                                    {{ $applicant->created_at->format('d M Y') }}</p>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="{{ $applicant->full_cv_url }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download mr-1"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                    <div class="flex items-center h-[720px]">
                                        <iframe src="{{ $applicant->full_cv_url }}" width="100%"
                                            height="100%"></iframe>
                                    </div>
                                @endif

                                @foreach ($applicant->documents as $document)
                                    <div class="flex justify-between items-center p-3 border rounded-md">
                                        <div class="flex items-center">
                                            <div class="text-primary mr-3">
                                                <i class="fas fa-file-alt text-xl"></i>
                                            </div>
                                            <div>
                                                <h6 class="font-medium">{{ $document->name }}</h6>
                                                <p class="text-sm text-slate-500">Uploaded:
                                                    {{ $document->created_at->format('d M Y') }}</p>
                                                @if ($document->notes)
                                                    <p class="text-xs text-slate-500 mt-1">{{ $document->notes }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ Storage::url($document->file_path) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download mr-1 text-blue-500"></i> Download
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                wire:click="confirmDeleteDocument({{ $document->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <div class="text-slate-400 mb-3">
                                    <i class="fas fa-file-upload text-4xl"></i>
                                </div>
                                <h5 class="font-medium text-lg mb-1">No Documents Available</h5>
                                <p class="text-slate-500 mb-4">Upload documents for this applicant to view them here.
                                </p>
                                <button type="button" class="btn btn-primary" wire:click="openDocumentUploadModal">
                                    <i class="fas fa-upload mr-1"></i> Upload Document
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>



    <!-- Main Information Modal -->
    <x-modal wire:model="showMainInfoModal">
        <x-slot name="title">Edit Personal Information</x-slot>

        <div class="grid grid-cols-2 gap-4">

            <div class="col-span-2">
                <div>
                    <x-input-label>Update CV/Resume</x-input-label>
                    <input type="file" wire:model="cvResume" accept="application/pdf" class="form-control" />
                    @error('cvResume')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <x-text-input wire:model="editMainInfo.first_name"
                errorMessage="{{ $errors->first('editMainInfo.first_name') }}" label="First Name" />

            <x-text-input wire:model="editMainInfo.middle_name"
                errorMessage="{{ $errors->first('editMainInfo.middle_name') }}" label="Middle Name" />


            <x-text-input wire:model="editMainInfo.last_name"
                errorMessage="{{ $errors->first('editMainInfo.last_name') }}" label="Last Name" />

            <x-text-input type="email" wire:model="editMainInfo.email"
                errorMessage="{{ $errors->first('editMainInfo.email') }}" label="Email" />

            <x-text-input wire:model="editMainInfo.phone" errorMessage="{{ $errors->first('editMainInfo.phone') }}"
                label="Phone" />

            <x-select wire:model="editMainInfo.channel_id"
                errorMessage="{{ $errors->first('editMainInfo.channel_id') }}" label="Channel">
                <option value="">Select Channel</option>
                @foreach ($channels as $channel)
                    <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                @endforeach
            </x-select>

            <x-select wire:model="editMainInfo.area_id" errorMessage="{{ $errors->first('editMainInfo.area_id') }}"
                label="Area">
                <option value="">Select Area</option>
                @foreach ($areas as $area)
                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                @endforeach
            </x-select>

            <x-text-input wire:model="editMainInfo.home_phone"
                errorMessage="{{ $errors->first('editMainInfo.home_phone') }}" label="Home Phone" />

            <x-text-input wire:model="editMainInfo.nationality"
                errorMessage="{{ $errors->first('editMainInfo.nationality') }}" label="Nationality" />


            <x-text-input wire:model="editMainInfo.social_number"
                errorMessage="{{ $errors->first('editMainInfo.social_number') }}" label="Social Number" />


            <x-text-input type="date" wire:model="editMainInfo.birth_date"
                errorMessage="{{ $errors->first('editMainInfo.birth_date') }}" label="Birth Date" />

            <x-select wire:model="editMainInfo.gender" errorMessage="{{ $errors->first('editMainInfo.gender') }}"
                label="Gender">
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </x-select>

            <x-select wire:model="editMainInfo.marital_status"
                errorMessage="{{ $errors->first('editMainInfo.marital_status') }}" label="Marital Status">
                <option value="">Select Status</option>
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Divorced">Divorced</option>
                <option value="Widowed">Widowed</option>
            </x-select>

            <x-select wire:model="editMainInfo.military_status"
                errorMessage="{{ $errors->first('editMainInfo.military_status') }}" label="Military Status">
                <option value="">Select Status</option>
                <option value="Exempted">Exempted</option>
                <option value="Drafted">Drafted</option>
                <option value="Completed">Completed</option>
            </x-select>


            <div class="col-span-2">
                <x-textarea wire:model="editMainInfo.address"
                    errorMessage="{{ $errors->first('editMainInfo.address') }}" label="Address"
                    rows="3"></x-textarea>
            </div>
        </div>

        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="toggleMainInfo">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="saveMainInfo" loadingFunction="saveMainInfo">
                    Save Changes
                </x-primary-button>
            </div>
        </x-slot>
    </x-modal>

    <!-- Education Modal -->
    <x-modal wire:model="showEducationModal">
        <x-slot name="title">Edit Education</x-slot>

        <div class="space-y-4">
            @foreach ($educations as $index => $education)
                <div class="border p-4 rounded-lg">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <x-text-input wire:model="educations.{{ $index }}.school_name" label="School Name"
                                errorMessage="{{ $errors->first('educations.' . $index . '.school_name') }}" />
                        </div>

                        <x-text-input wire:model="educations.{{ $index }}.degree" label="Degree"
                            errorMessage="{{ $errors->first('educations.' . $index . '.degree') }}" />

                        <x-text-input wire:model="educations.{{ $index }}.field_of_study"
                            label="Field of Study"
                            errorMessage="{{ $errors->first('educations.' . $index . '.field_of_study') }}" />

                        <x-text-input type="date" wire:model="educations.{{ $index }}.start_date"
                            errorMessage="{{ $errors->first('educations.' . $index . '.start_date') }}"
                            label="Start Date" />

                        <x-text-input type="date" wire:model="educations.{{ $index }}.end_date"
                            errorMessage="{{ $errors->first('educations.' . $index . '.end_date') }}"
                            label="End Date" />


                    </div>
                    <button type="button" class="btn btn-danger btn-sm mt-2"
                        wire:click="removeEducation({{ $index }})">
                        Remove
                    </button>
                </div>
            @endforeach
            <button type="button" class="btn btn-secondary" wire:click="addEducation">
                Add Education
            </button>
        </div>

        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="toggleEducation">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="saveEducations" loadingFunction="saveEducations">Save
                    Changes</x-primary-button>
            </div>
        </x-slot>
    </x-modal>

    <!-- Training Modal -->
    <x-modal wire:model="showTrainingModal">
        <x-slot name="title">Edit Training</x-slot>

        <div class="space-y-4">
            @foreach ($trainings as $index => $training)
                <div class="border p-4 rounded-lg">
                    <div class="grid grid-cols-2 gap-4">

                        <x-text-input label="Training Name" wire:model="trainings.{{ $index }}.name"
                            errorMessage="{{ $errors->first('trainings.' . $index . '.name') }}" />


                        <x-text-input label="Organization" wire:model="trainings.{{ $index }}.sponsor"
                            errorMessage="{{ $errors->first('trainings.' . $index . '.sponsor') }}" />

                        <x-text-input label="Start Date" type="date"
                            wire:model="trainings.{{ $index }}.start_date"
                            errorMessage="{{ $errors->first('trainings.' . $index . '.start_date') }}" />

                        <x-text-input label="Duration" wire:model="trainings.{{ $index }}.duration"
                            errorMessage="{{ $errors->first('trainings.' . $index . '.duration') }}" />



                    </div>
                    <button type="button" class="btn btn-danger btn-sm mt-2"
                        wire:click="removeTraining({{ $index }})">
                        Remove
                    </button>
                </div>
            @endforeach
            <button type="button" class="btn btn-secondary" wire:click="addTraining">
                Add Training
            </button>
        </div>
        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="toggleTraining">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="saveTrainings" loadingFunction="saveTrainings">Save
                    Changes</x-primary-button>
            </div>
        </x-slot>

    </x-modal>

    <!-- Experience Modal -->
    <x-modal wire:model="showExperienceModal">
        <x-slot name="title">Edit Experience</x-slot>

        <div class="space-y-4">
            @foreach ($experiences as $index => $experience)
                <div class="border p-4 rounded-lg">
                    <div class="grid grid-cols-2 gap-4">
                        <x-text-input wire:model="experiences.{{ $index }}.company_name" label="Company Name"
                            errorMessage="{{ $errors->first('experiences.' . $index . '.company_name') }}" />

                        <x-text-input wire:model="experiences.{{ $index }}.position" label="Position"
                            errorMessage="{{ $errors->first('experiences.' . $index . '.position') }}" />

                        <x-text-input type="date" wire:model="experiences.{{ $index }}.start_date"
                            label="Start Date"
                            errorMessage="{{ $errors->first('experiences.' . $index . '.start_date') }}" />

                        <x-text-input type="date" wire:model="experiences.{{ $index }}.end_date"
                            label="End Date"
                            errorMessage="{{ $errors->first('experiences.' . $index . '.end_date') }}" />

                        <x-text-input type="number" wire:model="experiences.{{ $index }}.salary"
                            label="Salary" type="number"
                            errorMessage="{{ $errors->first('experiences.' . $index . '.salary') }}" />

                        <x-textarea wire:model="experiences.{{ $index }}.reason_for_leaving"
                            label="Reason for Leaving"
                            errorMessage="{{ $errors->first('experiences.' . $index . '.reason_for_leaving') }}"
                            rows="2"></x-textarea>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm mt-2"
                        wire:click="removeExperience({{ $index }})">
                        Remove
                    </button>
                </div>
            @endforeach
            <button type="button" class="btn btn-secondary" wire:click="addExperience">
                Add Experience
            </button>
        </div>

        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="toggleExperience">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="saveExperiences" loadingFunction="saveExperiences">
                    Save Changes
                </x-primary-button>
            </div>
        </x-slot>
    </x-modal>

    <!-- Language Modal -->
    <x-modal wire:model="showLanguageModal">
        <x-slot name="title">Edit Languages</x-slot>

        <div class="space-y-4">
            @foreach ($languages as $index => $language)
                <div class="border p-4 rounded-lg">
                    <div class="grid grid-cols-2 gap-4">


                        <x-text-input wire:model="languages.{{ $index }}.language" label="Language"
                            errorMessage="{{ $errors->first('languages.' . $index . '.language') }}" />


                        <x-select label="Speaking Level"
                            errorMessage="{{ $errors->first('languages.' . $index . '.speaking_level') }}"
                            wire:model="languages.{{ $index }}.speaking_level">
                            <option value="">Select Speaking Level</option>
                            @foreach ($languageLevels as $level)
                                <option value="{{ $level }}">{{ $level }}</option>
                            @endforeach
                        </x-select>

                        <x-select label="Writing Level"
                            errorMessage="{{ $errors->first('languages.' . $index . '.writing_level') }}"
                            wire:model="languages.{{ $index }}.writing_level">
                            <option value="">Select Writing Level</option>
                            @foreach ($languageLevels as $level)
                                <option value="{{ $level }}">{{ $level }}</option>
                            @endforeach
                        </x-select>

                        <x-select label="Reading Level"
                            errorMessage="{{ $errors->first('languages.' . $index . '.reading_level') }}"
                            wire:model="languages.{{ $index }}.reading_level">
                            <option value="">Select Reading Level</option>
                            @foreach ($languageLevels as $level)
                                <option value="{{ $level }}">{{ $level }}</option>
                            @endforeach
                        </x-select>

                    </div>

                    <button type="button" class="btn btn-danger btn-sm mt-2"
                        wire:click="removeLanguage({{ $index }})">
                        Remove
                    </button>
                </div>
            @endforeach
            <button type="button" class="btn btn-secondary" wire:click="addLanguage">
                Add Language
            </button>
        </div>
        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="toggleLanguages">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="saveLanguages" loadingFunction="saveLanguages">Save
                    Changes</x-primary-button>
            </div>
        </x-slot>

    </x-modal>

    <!-- References Modal -->
    <x-modal wire:model="showReferenceModal">
        <x-slot name="title">Edit References</x-slot>

        <div class="space-y-4">
            @foreach ($references as $index => $reference)
                <div class="border p-4 rounded-lg">
                    <div class="grid grid-cols-2 gap-4">

                        <x-text-input label="Name" wire:model="references.{{ $index }}.name"
                            errorMessage="{{ $errors->first('references.' . $index . '.name') }}" />

                        <x-text-input label="Phone" wire:model="references.{{ $index }}.phone"
                            errorMessage="{{ $errors->first('references.' . $index . '.phone') }}" />

                        <x-text-input label="Relationship"
                            errorMessage="{{ $errors->first('references.' . $index . '.relationship') }}"
                            wire:model="references.{{ $index }}.relationship" />

                        <x-text-input label="Email" type="email"
                            wire:model="references.{{ $index }}.email"
                            errorMessage="{{ $errors->first('references.' . $index . '.email') }}" />

                        <div class="col-span-2">
                            <x-textarea label="Address"
                                errorMessage="{{ $errors->first('references.' . $index . '.address') }}"
                                wire:model="references.{{ $index }}.address" />
                        </div>




                    </div>
                    <button type="button" class="btn btn-danger btn-sm mt-2"
                        wire:click="removeReference({{ $index }})">
                        Remove
                    </button>
                </div>
            @endforeach
            <button type="button" class="btn btn-secondary" wire:click="addReference">
                Add Reference
            </button>
        </div>
        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="toggleReferences">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="saveReferences" loadingFunction="saveReferences">Save
                    Changes</x-primary-button>
            </div>
        </x-slot>
    </x-modal>

    <!-- Skills Modal -->
    <x-modal wire:model="showSkillModal">
        <x-slot name="title">Edit Skills</x-slot>

        <div class="space-y-4">
            @foreach ($skills as $index => $skill)
                <div class="border p-4 rounded-lg">
                    <div class="grid grid-cols-2 gap-4">

                        <x-text-input label="Skill"
                            errorMessage="{{ $errors->first('skills.' . $index . '.skill') }}"
                            listOptions="{{ $allSkillsString }}" wire:model="skills.{{ $index }}.skill" />

                        <x-select label="Level" errorMessage="{{ $errors->first('skills.' . $index . '.level') }}"
                            wire:model="skills.{{ $index }}.level">
                            <option value="">Select Level</option>
                            @foreach ($skillsLevels as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </x-select>

                    </div>
                    <button type="button" class="btn btn-danger btn-sm mt-2"
                        wire:click="removeSkill({{ $index }})">
                        Remove
                    </button>
                </div>
            @endforeach
            <button type="button" class="btn btn-secondary" wire:click="addSkill">
                Add Skill
            </button>
        </div>
        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="toggleSkills">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="saveSkills" loadingFunction="saveSkills">Save
                    Changes</x-primary-button>
            </div>
        </x-slot>

    </x-modal>

    <!-- Health Modal -->
    <x-modal wire:model="showHealthModal">
        <x-slot name="title">Edit Health Information</x-slot>

        <div class="space-y-4">
            <div>
                <x-input-label>Has Health Issues?</x-input-label>
                <x-select wire:model.live="hasHealthIssues">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </x-select>
                @error('hasHealthIssues')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            @if ($hasHealthIssues)
                <div>
                    <x-input-label>Health Issues Details</x-input-label>
                    <x-textarea wire:model="healthIssues" rows="3"></x-textarea>
                    @error('healthIssues')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="toggleHealth">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="saveHealth" loadingFunction="saveHealth">Save
                    Changes</x-primary-button>
            </div>
        </x-slot>
    </x-modal>

    <!-- Image Upload Modal -->
    <x-modal wire:model="showImageUploadModal">
        <x-slot name="title">Update Profile Image</x-slot>

        <div class="space-y-4">
            <div>
                <x-input-label>Profile Image</x-input-label>
                <input type="file" wire:model="newImage" accept="image/*" class="form-control" />
                @error('newImage')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            @if ($newImage)
                <div>
                    <img src="{{ $newImage->temporaryUrl() }}" class="max-w-xs mx-auto rounded-lg" alt="Preview">
                </div>
            @endif
        </div>
        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="$set('showImageUploadModal', false)">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="uploadImage" loadingFunction="uploadImage">Upload
                    Image</x-primary-button>
            </div>
        </x-slot>
    </x-modal>

</div>
