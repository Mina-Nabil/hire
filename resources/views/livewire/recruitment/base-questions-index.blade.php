<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Base Questions Management
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @can('create', App\Models\Recruitment\Vacancies\BaseQuestion::class)
                <button wire:click="openNewQuestionSec"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    Create Question
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

    <!-- Base Questions Table -->
    <div class="card">
        <header class="card-header noborder">
            <h4 class="card-title">Base Questions</h4>
        </header>
        <div class="card-body px-6 pb-6">
            <div class="overflow-x-auto -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class="bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class="table-th">Question</th>
                                    <th scope="col" class="table-th">Type</th>
                                    <th scope="col" class="table-th">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse($baseQuestions as $baseQuestion)
                                    <tr>
                                        <td class="table-td">{{ $baseQuestion->question }}</td>
                                        <td class="table-td">{{ ucfirst($baseQuestion->type) }}</td>
                                        <td class="table-td">
                                            <div class="flex space-x-3 rtl:space-x-reverse">
                                                @can('update', $baseQuestion)
                                                    <button wire:click="openEditQuestionSec({{ $baseQuestion->id }})"
                                                        class="action-btn text-primary">
                                                        <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                    </button>
                                                @endcan
                                                @can('delete', $baseQuestion)
                                                    <button wire:click="confirmDeleteQuestion({{ $baseQuestion->id }})"
                                                        class="action-btn text-danger">
                                                        <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="table-td text-center">No base questions found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                {{ $baseQuestions->links('vendor.livewire.simple-bootstrap') }}
            </div>
        </div>
    </div>

    <!-- New Question Modal -->
    @if ($newQuestionModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog modal-lg relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Create New Base Question
                            </h3>
                            <button wire:click="closeNewQuestionSec" type="button"
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
                            <div class="space-y-4">
                                <!-- Question -->
                                <div class="from-group">
                                    <label for="question" class="form-label">Question</label>
                                    <input id="question" type="text"
                                        class="form-control @error('question') !border-danger-500 @enderror"
                                        wire:model="question">
                                    @error('question')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Type -->
                                <div class="from-group">
                                    <label for="type" class="form-label">Type</label>
                                    <select id="type"
                                        class="form-control @error('type') !border-danger-500 @enderror"
                                        wire:model.live="type">
                                        @foreach ($questionTypes as $questionType)
                                            <option value="{{ $questionType }}">{{ ucfirst($questionType) }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Options -->
                                <div class="from-group">
                                    <label for="options" class="form-label">Options (comma separated)</label>
                                    <input id="options" type="text"
                                        class="form-control @error('options') !border-danger-500 @enderror"
                                        wire:model="options"
                                        @disabled(!in_array($type, ['select', 'checkbox', 'radio']))>
                                    @error('options')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="addNewQuestion"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="addNewQuestion">Create</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="addNewQuestion"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Question Modal -->
    @if ($editQuestionModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog modal-lg relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Edit Base Question
                            </h3>
                            <button wire:click="closeEditQuestionSec" type="button"
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
                            <div class="space-y-4">
                                <!-- Question -->
                                <div class="from-group">
                                    <label for="edit-question" class="form-label">Question</label>
                                    <input id="edit-question" type="text"
                                        class="form-control @error('question') !border-danger-500 @enderror"
                                        wire:model="question">
                                    @error('question')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Type -->
                                <div class="from-group">
                                    <label for="edit-type" class="form-label">Type</label>
                                    <select id="edit-type"
                                        class="form-control @error('type') !border-danger-500 @enderror"
                                        wire:model.live="type">
                                        @foreach ($questionTypes as $questionType)
                                            <option value="{{ $questionType }}">{{ ucfirst($questionType) }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Options -->
                                <div class="from-group">
                                    <label for="edit-options" class="form-label">Options (comma separated)</label>
                                    <input id="edit-options" type="text"
                                        class="form-control @error('options') !border-danger-500 @enderror"
                                        wire:model="options"
                                        @disabled(!in_array($type, ['select', 'checkbox', 'radio']))>
                                    @error('options')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="updateQuestion"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="updateQuestion">Update</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="updateQuestion"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
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
                                Are you sure you want to delete this question?
                                <br>This action cannot be undone.
                            </h3>
                            <div class="flex gap-2 justify-center">
                                <button wire:click="deleteQuestion" type="button"
                                    class="btn inline-flex justify-center text-white bg-danger-500">
                                    <span wire:loading.remove wire:target="deleteQuestion">Yes, delete it</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="deleteQuestion"
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