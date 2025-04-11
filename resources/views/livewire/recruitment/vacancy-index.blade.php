<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Vacancies Management
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @can('create', App\Models\Recruitment\Vacancies\Vacancy::class)
                <button wire:click="openNewVacancySec"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    Create Vacancy
                </button>
            @endcan
        </div>
    </div>

    <!-- Search Bar -->
    <div class="flex flex-wrap sm:flex-nowrap justify-between space-x-3 rtl:space-x-reverse mb-6">
        <div class="flex-0 w-full sm:w-auto mb-3 sm:mb-0">
            <div class="relative">
                <input type="text" class="form-control" placeholder="Search..."
                    wire:model.live.debounce.300ms="search">
                <span class="absolute right-0 top-0 w-9 h-full flex items-center justify-center text-slate-400">
                    <iconify-icon icon="heroicons-solid:search"></iconify-icon>
                </span>
            </div>
        </div>
    </div>

    <!-- Vacancies Table -->
    <div class="card">
        <header class="card-header noborder">
            <h4 class="card-title">Vacancies</h4>
        </header>
        <div class="card-body px-6 pb-6">
            <div class="overflow-x-auto -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class="bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class="table-th">Position</th>
                                    <th scope="col" class="table-th">Type</th>
                                    <th scope="col" class="table-th">Status</th>
                                    <th scope="col" class="table-th">Closing Date</th>
                                    <th scope="col" class="table-th">Questions</th>
                                    <th scope="col" class="table-th">Slots</th>
                                    <th scope="col" class="table-th">Applications</th>
                                    <th scope="col" class="table-th">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse($vacancies as $vacancy)
                                    <tr class="even:bg-slate-100 dark:even:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer" wire:click="showVacancy({{ $vacancy->id }})">
                                        <td class="table-td">{{ $vacancy->position->name }}</td>
                                        <td class="table-td">{{ ucfirst($vacancy->type) }}</td>
                                        <td class="table-td">
                                            <span
                                                class="badge {{ $vacancy->status === 'open' ? 'badge-success' : 'badge-danger' }}">
                                                {{ ucfirst($vacancy->status) }}
                                            </span>
                                        </td>
                                        <td class="table-td">
                                            {{ $vacancy->closing_date ? $vacancy->closing_date->format('Y-m-d') : 'N/A' }}
                                        </td>
                                        <td class="table-td">{{ $vacancy->vacancy_questions_count }}</td>
                                        <td class="table-td">{{ $vacancy->vacancy_slots_count }}</td>
                                        <td class="table-td">{{ $vacancy->applications_count }}</td>
                                        <td class="table-td">
                                            <div class="flex space-x-3 rtl:space-x-reverse">
                                                <button wire:click="viewVacancy({{ $vacancy->id }})"
                                                    class="action-btn text-success">
                                                    <iconify-icon icon="heroicons:eye"></iconify-icon>
                                                </button>
                                                @can('update', $vacancy)
                                                    <button wire:click="openEditVacancySec({{ $vacancy->id }})"
                                                        class="action-btn text-primary">
                                                        <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                    </button>
                                                @endcan
                                                @can('delete', $vacancy)
                                                    <button wire:click="confirmDeleteVacancy({{ $vacancy->id }})"
                                                        class="action-btn text-danger">
                                                        <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="table-td text-center">No vacancies found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                {{ $vacancies->links('vendor.livewire.simple-bootstrap') }}
            </div>
        </div>
    </div>

    <!-- New Vacancy Modal -->
    @if ($newVacancyModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog modal-xl relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Create New Vacancy
                            </h3>
                            <button wire:click="closeNewVacancySec" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Left Column -->
                                <div class="space-y-4">
                                    <!-- Position -->
                                    <div class="from-group">
                                        <label for="positionId" class="form-label">Position</label>
                                        <select id="positionId"
                                            class="form-control @error('positionId') !border-danger-500 @enderror"
                                            wire:model="positionId">
                                            <option value="">Select a position</option>
                                            @foreach ($positions as $position)
                                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('positionId')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Assigned To -->
                                    <div class="from-group">
                                        <label for="assignedTo" class="form-label">Assigned To</label>
                                        <select id="assignedTo"
                                            class="form-control @error('assignedTo') !border-danger-500 @enderror"
                                            wire:model="assignedTo">
                                            <option value="">Select a user</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('assignedTo')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <!-- Hiring Manager -->
                                    <div class="from-group">
                                        <label for="hiringManagerId" class="form-label">Hiring Manager</label>
                                        <select id="hiringManagerId"
                                            class="form-control @error('hiringManagerId') !border-danger-500 @enderror"
                                            wire:model="hiringManagerId">
                                            <option value="">Select a user</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('hiringManagerId')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <!-- HR Manager -->
                                    <div class="from-group">
                                        <label for="hrManagerId" class="form-label">HR Manager</label>
                                        <select id="hrManagerId"
                                            class="form-control @error('hrManagerId') !border-danger-500 @enderror"
                                            wire:model="hrManagerId">
                                            <option value="">Select a user</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('hrManagerId')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>

                                <div class="space-y-4">

                                    <!-- Type -->
                                    <div class="from-group">
                                        <label for="vacancyType" class="form-label">Type</label>
                                        <select id="vacancyType"
                                            class="form-control @error('vacancyType') !border-danger-500 @enderror"
                                            wire:model="vacancyType">
                                            @foreach ($vacancyTypes as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('vacancyType')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Salary -->
                                    <div class="from-group">
                                        <label for="jobSalary" class="form-label">Salary</label>
                                        <input id="jobSalary" type="text"
                                            class="form-control @error('jobSalary') !border-danger-500 @enderror"
                                            wire:model="jobSalary">
                                        @error('jobSalary')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <!-- Left Column -->
                                <div class="space-y-4">
                                    <!-- Responsibilities -->
                                    <div class="from-group">
                                        <label for="jobResponsibilities" class="form-label">Job
                                            Responsibilities</label>
                                        <textarea id="jobResponsibilities" rows="3"
                                            class="form-control @error('jobResponsibilities') !border-danger-500 @enderror" wire:model="jobResponsibilities"></textarea>
                                        @error('jobResponsibilities')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Qualifications -->
                                    <div class="from-group">
                                        <label for="jobQualifications" class="form-label">Job Qualifications</label>
                                        <textarea id="jobQualifications" rows="3"
                                            class="form-control @error('jobQualifications') !border-danger-500 @enderror" wire:model="jobQualifications"></textarea>
                                        @error('jobQualifications')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Job Benefits -->
                                    <div class="from-group">
                                        <label for="jobBenefits" class="form-label">Job Benefits</label>
                                        <textarea id="jobBenefits" rows="3" class="form-control @error('jobBenefits') !border-danger-500 @enderror"
                                            wire:model="jobBenefits"></textarea>
                                        @error('jobBenefits')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>


                                </div>

                                <!-- Right Column -->
                                <div class="space-y-4">
                                    <!-- Arabic Responsibilities -->
                                    <div class="from-group">
                                        <label for="arabicJobResponsibilities" class="form-label">Arabic Job
                                            Responsibilities</label>
                                        <textarea id="arabicJobResponsibilities" rows="3"
                                            class="form-control @error('arabicJobResponsibilities') !border-danger-500 @enderror"
                                            wire:model="arabicJobResponsibilities"></textarea>
                                        @error('arabicJobResponsibilities')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Arabic Qualifications -->
                                    <div class="from-group">
                                        <label for="arabicJobQualifications" class="form-label">Arabic Job
                                            Qualifications</label>
                                        <textarea id="arabicJobQualifications" rows="3"
                                            class="form-control @error('arabicJobQualifications') !border-danger-500 @enderror"
                                            wire:model="arabicJobQualifications"></textarea>
                                        @error('arabicJobQualifications')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Arabic Job Benefits -->
                                    <div class="from-group">
                                        <label for="arabicJobBenefits" class="form-label">Arabic Job Benefits</label>
                                        <textarea id="arabicJobBenefits" rows="3"
                                            class="form-control @error('arabicJobBenefits') !border-danger-500 @enderror" wire:model="arabicJobBenefits"></textarea>
                                        @error('arabicJobBenefits')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>

                            </div>


                            <!-- Vacancy Questions Section -->
                            <div class="mt-6">
                                <h4 class="text-lg font-medium mb-3">Vacancy Questions</h4>
                                @foreach ($questions as $index => $question)
                                    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded mb-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <h5 class="text-base font-medium">Question {{ $index + 1 }}</h5>
                                            <button type="button" wire:click="removeQuestion({{ $index }})"
                                                class="text-danger-500">
                                                <iconify-icon icon="heroicons:trash"></iconify-icon>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="from-group">
                                                <label class="form-label">Question</label>
                                                <input type="text" class="form-control"
                                                    wire:model="questions.{{ $index }}.question">
                                            </div>
                                            <div class="from-group">
                                                <label class="form-label">Arabic Question</label>
                                                <input type="text" class="form-control"
                                                    wire:model="questions.{{ $index }}.arabic_question">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                                            <div class="from-group">
                                                <label class="form-label">Type</label>
                                                <select class="form-control"
                                                    wire:model.live="questions.{{ $index }}.type">
                                                    @foreach ($questionTypes as $type)
                                                        <option value="{{ $type }}">{{ ucfirst($type) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="from-group">
                                                <label class="form-label">Options (comma separated)</label>
                                                <input type="text" class="form-control"
                                                    wire:model="questions.{{ $index }}.options"
                                                    @disabled(!in_array($questions[$index]['type'], ['select', 'checkbox', 'radio']))>
                                            </div>
                                            <div class="from-group flex items-center">
                                                <label class="inline-flex items-center mt-6">
                                                    <input type="checkbox" class="form-checkbox"
                                                        wire:model="questions.{{ $index }}.required">
                                                    <span class="ml-2">Required</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <button type="button" wire:click="addQuestion" class="btn btn-outline-primary">
                                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2"
                                        icon="ph:plus-bold"></iconify-icon>
                                    Add Question
                                </button>
                            </div>

                            <!-- Vacancy Slots Section -->
                            <div class="mt-6">
                                <h4 class="text-lg font-medium mb-3">Interview Slots</h4>
                                @foreach ($slots as $index => $slot)
                                    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded mb-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <h5 class="text-base font-medium">Slot {{ $index + 1 }}</h5>
                                            <button type="button" wire:click="removeSlot({{ $index }})"
                                                class="text-danger-500">
                                                <iconify-icon icon="heroicons:trash"></iconify-icon>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div class="from-group">
                                                <label class="form-label">Date</label>
                                                <input type="date" class="form-control"
                                                    wire:model="slots.{{ $index }}.date">
                                            </div>
                                            <div class="from-group">
                                                <label class="form-label">Start Time</label>
                                                <input type="time" class="form-control"
                                                    wire:model="slots.{{ $index }}.start_time">
                                            </div>
                                            <div class="from-group">
                                                <label class="form-label">End Time</label>
                                                <input type="time" class="form-control"
                                                    wire:model="slots.{{ $index }}.end_time">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <button type="button" wire:click="addSlot" class="btn btn-outline-primary">
                                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2"
                                        icon="ph:plus-bold"></iconify-icon>
                                    Add Slot
                                </button>
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="addNewVacancy"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="addNewVacancy">Create</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="addNewVacancy"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Vacancy Modal -->
    @if ($editVacancyModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog modal-xl relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Edit Vacancy
                            </h3>
                            <button wire:click="closeEditVacancySec" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Left Column -->
                                <div class="space-y-4">
                                    <!-- Position -->
                                    <div class="from-group">
                                        <label for="positionId" class="form-label">Position</label>
                                        <select id="positionId"
                                            class="form-control @error('positionId') !border-danger-500 @enderror"
                                            wire:model="positionId">
                                            <option value="">Select a position</option>
                                            @foreach ($positions as $position)
                                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('positionId')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Assigned To -->
                                    <div class="from-group">
                                        <label for="assignedTo" class="form-label">Assigned To</label>
                                        <select id="assignedTo"
                                            class="form-control @error('assignedTo') !border-danger-500 @enderror"
                                            wire:model="assignedTo">
                                            <option value="">Select a user</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('assignedTo')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Type -->
                                    <div class="from-group">
                                        <label for="vacancyType" class="form-label">Type</label>
                                        <select id="vacancyType"
                                            class="form-control @error('vacancyType') !border-danger-500 @enderror"
                                            wire:model="vacancyType">
                                            @foreach ($vacancyTypes as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('vacancyType')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Status -->
                                    <div class="from-group">
                                        <label for="vacancyStatus" class="form-label">Status</label>
                                        <select id="vacancyStatus"
                                            class="form-control @error('vacancyStatus') !border-danger-500 @enderror"
                                            wire:model="vacancyStatus">
                                            @foreach ($vacancyStatuses as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('vacancyStatus')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Closing Date -->
                                    <div class="from-group">
                                        <label for="closingDate" class="form-label">Closing Date</label>
                                        <input id="closingDate" type="date"
                                            class="form-control @error('closingDate') !border-danger-500 @enderror"
                                            wire:model="closingDate" readonly>
                                        @error('closingDate')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Salary -->
                                    <div class="from-group">
                                        <label for="jobSalary" class="form-label">Salary</label>
                                        <input id="jobSalary" type="text"
                                            class="form-control @error('jobSalary') !border-danger-500 @enderror"
                                            wire:model="jobSalary">
                                        @error('jobSalary')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-4">
                                    <!-- Responsibilities -->
                                    <div class="from-group">
                                        <label for="jobResponsibilities" class="form-label">Job
                                            Responsibilities</label>
                                        <textarea id="jobResponsibilities" rows="3"
                                            class="form-control @error('jobResponsibilities') !border-danger-500 @enderror" wire:model="jobResponsibilities"></textarea>
                                        @error('jobResponsibilities')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Arabic Responsibilities -->
                                    <div class="from-group">
                                        <label for="arabicJobResponsibilities" class="form-label">Arabic Job
                                            Responsibilities</label>
                                        <textarea id="arabicJobResponsibilities" rows="3"
                                            class="form-control @error('arabicJobResponsibilities') !border-danger-500 @enderror"
                                            wire:model="arabicJobResponsibilities"></textarea>
                                        @error('arabicJobResponsibilities')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Qualifications -->
                                    <div class="from-group">
                                        <label for="jobQualifications" class="form-label">Job Qualifications</label>
                                        <textarea id="jobQualifications" rows="3"
                                            class="form-control @error('jobQualifications') !border-danger-500 @enderror" wire:model="jobQualifications"></textarea>
                                        @error('jobQualifications')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Arabic Qualifications -->
                                    <div class="from-group">
                                        <label for="arabicJobQualifications" class="form-label">Arabic Job
                                            Qualifications</label>
                                        <textarea id="arabicJobQualifications" rows="3"
                                            class="form-control @error('arabicJobQualifications') !border-danger-500 @enderror"
                                            wire:model="arabicJobQualifications"></textarea>
                                        @error('arabicJobQualifications')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Job Benefits -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="from-group">
                                    <label for="jobBenefits" class="form-label">Job Benefits</label>
                                    <textarea id="jobBenefits" rows="3" class="form-control @error('jobBenefits') !border-danger-500 @enderror"
                                        wire:model="jobBenefits"></textarea>
                                    @error('jobBenefits')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="from-group">
                                    <label for="arabicJobBenefits" class="form-label">Arabic Job Benefits</label>
                                    <textarea id="arabicJobBenefits" rows="3"
                                        class="form-control @error('arabicJobBenefits') !border-danger-500 @enderror" wire:model="arabicJobBenefits"></textarea>
                                    @error('arabicJobBenefits')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Vacancy Questions Section -->
                            <div class="mt-6">
                                <h4 class="text-lg font-medium mb-3">Vacancy Questions</h4>
                                @foreach ($questions as $index => $question)
                                    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded mb-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <h5 class="text-base font-medium">Question {{ $index + 1 }}</h5>
                                            <button type="button" wire:click="removeQuestion({{ $index }})"
                                                class="text-danger-500">
                                                <iconify-icon icon="heroicons:trash"></iconify-icon>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="from-group">
                                                <label class="form-label">Question</label>
                                                <input type="text" class="form-control"
                                                    wire:model="questions.{{ $index }}.question">
                                            </div>
                                            <div class="from-group">
                                                <label class="form-label">Arabic Question</label>
                                                <input type="text" class="form-control"
                                                    wire:model="questions.{{ $index }}.arabic_question">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                                            <div class="from-group">
                                                <label class="form-label">Type</label>
                                                <select class="form-control"
                                                    wire:model.live="questions.{{ $index }}.type">
                                                    @foreach ($questionTypes as $type)
                                                        <option value="{{ $type }}">{{ ucfirst($type) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="from-group">
                                                <label class="form-label">Options (comma separated)</label>
                                                <input type="text" class="form-control"
                                                    wire:model="questions.{{ $index }}.options"
                                                    {{ !in_array($questions[$index]['type'], ['select', 'checkbox', 'radio']) ? 'disabled' : '' }}>
                                            </div>
                                            <div class="from-group flex items-center">
                                                <label class="inline-flex items-center mt-6">
                                                    <input type="checkbox" class="form-checkbox"
                                                        wire:model="questions.{{ $index }}.required">
                                                    <span class="ml-2">Required</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <button type="button" wire:click="addQuestion" class="btn btn-outline-primary">
                                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2"
                                        icon="ph:plus-bold"></iconify-icon>
                                    Add Question
                                </button>
                            </div>

                            <!-- Vacancy Slots Section -->
                            <div class="mt-6">
                                <h4 class="text-lg font-medium mb-3">Interview Slots</h4>
                                @foreach ($slots as $index => $slot)
                                    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded mb-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <h5 class="text-base font-medium">Slot {{ $index + 1 }}</h5>
                                            <button type="button" wire:click="removeSlot({{ $index }})"
                                                class="text-danger-500">
                                                <iconify-icon icon="heroicons:trash"></iconify-icon>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div class="from-group">
                                                <label class="form-label">Date</label>
                                                <input type="date" class="form-control"
                                                    wire:model="slots.{{ $index }}.date">
                                            </div>
                                            <div class="from-group">
                                                <label class="form-label">Start Time</label>
                                                <input type="time" class="form-control"
                                                    wire:model="slots.{{ $index }}.start_time">
                                            </div>
                                            <div class="from-group">
                                                <label class="form-label">End Time</label>
                                                <input type="time" class="form-control"
                                                    wire:model="slots.{{ $index }}.end_time">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <button type="button" wire:click="addSlot" class="btn btn-outline-primary">
                                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2"
                                        icon="ph:plus-bold"></iconify-icon>
                                    Add Slot
                                </button>
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="updateVacancy"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="updateVacancy">Update</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="updateVacancy"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- View Vacancy Modal -->
    @if ($viewVacancyModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog modal-xl relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                View Vacancy Details
                            </h3>
                            <button wire:click="closeViewVacancySec" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Left Column - Basic Info -->
                                <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded">
                                    <h4 class="text-lg font-medium mb-4">Basic Information</h4>

                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-sm text-slate-500">Position</p>
                                            <p class="font-medium">
                                                {{ $positions->firstWhere('id', $positionId)->name ?? 'N/A' }}</p>
                                        </div>

                                        <div>
                                            <p class="text-sm text-slate-500">Assigned To</p>
                                            <p class="font-medium">
                                                {{ $users->firstWhere('id', $assignedTo)->name ?? 'N/A' }}</p>
                                        </div>

                                        <div>
                                            <p class="text-sm text-slate-500">Type</p>
                                            <p class="font-medium">{{ ucfirst($vacancyType) }}</p>
                                        </div>

                                        <div>
                                            <p class="text-sm text-slate-500">Status</p>
                                            <p class="font-medium">
                                                <span
                                                    class="badge {{ $vacancyStatus === 'open' ? 'badge-success' : 'badge-danger' }}">
                                                    {{ ucfirst($vacancyStatus) }}
                                                </span>
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-sm text-slate-500">Closing Date</p>
                                            <p class="font-medium">{{ $closingDate ?? 'N/A' }}</p>
                                        </div>

                                        <div>
                                            <p class="text-sm text-slate-500">Salary</p>
                                            <p class="font-medium">{{ $jobSalary ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column - Job Details -->
                                <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded">
                                    <h4 class="text-lg font-medium mb-4">Job Details</h4>

                                    @if ($jobResponsibilities)
                                        <div class="mb-4">
                                            <p class="text-sm text-slate-500">Job Responsibilities</p>
                                            <div class="mt-1 whitespace-pre-line">{{ $jobResponsibilities }}</div>
                                        </div>
                                    @endif

                                    @if ($arabicJobResponsibilities)
                                        <div class="mb-4">
                                            <p class="text-sm text-slate-500">Arabic Job Responsibilities</p>
                                            <div class="mt-1 whitespace-pre-line">{{ $arabicJobResponsibilities }}
                                            </div>
                                        </div>
                                    @endif

                                    @if ($jobQualifications)
                                        <div class="mb-4">
                                            <p class="text-sm text-slate-500">Job Qualifications</p>
                                            <div class="mt-1 whitespace-pre-line">{{ $jobQualifications }}</div>
                                        </div>
                                    @endif

                                    @if ($arabicJobQualifications)
                                        <div class="mb-4">
                                            <p class="text-sm text-slate-500">Arabic Job Qualifications</p>
                                            <div class="mt-1 whitespace-pre-line">{{ $arabicJobQualifications }}</div>
                                        </div>
                                    @endif

                                    @if ($jobBenefits)
                                        <div class="mb-4">
                                            <p class="text-sm text-slate-500">Job Benefits</p>
                                            <div class="mt-1 whitespace-pre-line">{{ $jobBenefits }}</div>
                                        </div>
                                    @endif

                                    @if ($arabicJobBenefits)
                                        <div class="mb-4">
                                            <p class="text-sm text-slate-500">Arabic Job Benefits</p>
                                            <div class="mt-1 whitespace-pre-line">{{ $arabicJobBenefits }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Vacancy Questions Section -->
                            @if (count($questions) > 0)
                                <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded">
                                    <h4 class="text-lg font-medium mb-4">Vacancy Questions</h4>

                                    <div class="space-y-4">
                                        @foreach ($questions as $index => $question)
                                            <div class="border-b pb-3 dark:border-slate-700">
                                                <div class="flex justify-between">
                                                    <div>
                                                        <p class="font-medium">{{ $index + 1 }}.
                                                            {{ $question['question'] }}</p>
                                                        @if (!empty($question['arabic_question']))
                                                            <p class="text-sm text-slate-500 mt-1">
                                                                {{ $question['arabic_question'] }}</p>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="badge badge-primary">{{ ucfirst($question['type']) }}</span>
                                                        @if (isset($question['required']) && $question['required'])
                                                            <span class="badge badge-danger ml-1">Required</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if (!empty($question['options']))
                                                    <div class="mt-2">
                                                        <p class="text-sm text-slate-500">Options:</p>
                                                        <p>{{ $question['options'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Vacancy Slots Section -->
                            @if (count($slots) > 0)
                                <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded">
                                    <h4 class="text-lg font-medium mb-4">Interview Slots</h4>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach ($slots as $index => $slot)
                                            <div class="border p-3 rounded-md dark:border-slate-600">
                                                <p class="font-medium">Slot {{ $index + 1 }}</p>
                                                <div class="mt-2 space-y-1">
                                                    <p class="text-sm"><span class="text-slate-500">Date:</span>
                                                        {{ $slot['date'] }}</p>
                                                    <p class="text-sm"><span class="text-slate-500">Time:</span>
                                                        {{ $slot['start_time'] }} - {{ $slot['end_time'] }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($deleteConfirmationModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none top-1/2 !-translate-y-1/2">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-danger-500">
                            <h3 class="text-xl font-medium text-white">
                                Confirm Delete
                            </h3>
                            <button wire:click="closeDeleteConfirmationModal" type="button"
                                class="text-white bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-6 text-center">
                            <svg class="mx-auto mb-4 text-danger-500 w-12 h-12 dark:text-danger-500"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <h3 class="mb-5 text-lg font-normal text-slate-500 dark:text-slate-400">
                                Are you sure you want to delete this vacancy?
                                <br>This action cannot be undone.
                            </h3>
                            <div class="flex gap-2 justify-center">
                                <button wire:click="confirmDelete" type="button"
                                    class="btn inline-flex justify-center text-white bg-danger-500">
                                    <span wire:loading.remove wire:target="confirmDelete">Yes, delete it</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="confirmDelete"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                                <button wire:click="closeDeleteConfirmationModal" type="button"
                                    class="btn inline-flex justify-center text-white bg-slate-800">
                                    No, cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
