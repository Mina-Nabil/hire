<div>
    {{-- In work, do what you enjoy. --}}
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Departments and Positions Management
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @can('create', App\Models\Hierarchy\Position::class)
                <button wire:click="openNewPositionSec"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    Create Position
                </button>
                <button wire:click="openNewDepartmentSec"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    Create Department
                </button>
            @endcan
        </div>
    </div>

    <!-- Search Bar -->
    <div class="flex flex-wrap sm:flex-nowrap justify-between space-x-3 rtl:space-x-reverse mb-6">
        <div class="flex-0 w-full sm:w-auto mb-3 sm:mb-0">
            <div class="relative">
                <input type="text" class="form-control pl-10" placeholder="Search..."
                    wire:model.live.debounce.300ms="search">
                <span class="absolute right-0 top-0 w-9 h-full flex items-center justify-center text-slate-400">
                    <iconify-icon icon="heroicons-solid:search"></iconify-icon>
                </span>
            </div>
        </div>
    </div>

    <!-- Grid Layout for Tables -->
    <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-5 mb-5 text-wrap">
        <!-- Departments Table -->
        <div class="card mb-6">
            <header class="card-header noborder">
                <h4 class="card-title">Departments</h4>
            </header>
            <div class="card-body px-6 pb-6">
                <div class="overflow-x-auto -mx-6">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead class="bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th">Name</th>
                                        <th scope="col" class="table-th">Prefix</th>
                                        <th scope="col" class="table-th">Positions</th>
                                        <th scope="col" class="table-th">Actions</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse($departments as $department)
                                        <tr>
                                            <td class="table-td">{{ $department->name }}</td>
                                            <td class="table-td">{{ $department->prefix_code }}</td>
                                            <td class="table-td">{{ $department->positions_count }}</td>
                                            <td class="table-td">
                                                <div class="flex space-x-3 rtl:space-x-reverse">
                                                    <button wire:click="openEditDepartmentSec({{ $department->id }})"
                                                        class="action-btn text-primary">
                                                        <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                    </button>
                                                    <button wire:click="confirmDeleteDepartment({{ $department->id }})"
                                                        class="action-btn text-danger">
                                                        <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="table-td text-center">No departments found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Positions Table -->
        <div class="card">
            <header class="card-header noborder">
                <h4 class="card-title">Positions</h4>
            </header>
            <div class="card-body px-6 pb-6">
                <div class="overflow-x-auto -mx-6">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead class="bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th">Name</th>
                                        <th scope="col" class="table-th">Department</th>
                                        <th scope="col" class="table-th">Parent</th>
                                        <th scope="col" class="table-th">Actions</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse($positions as $position)
                                        <tr>
                                            <td class="table-td">{{ $position->name }}</td>
                                            <td class="table-td">{{ $position->department->name }}</td>
                                            <td class="table-td">{{ $position->parent ? $position->parent->name : '-' }}
                                            </td>
                                            <td class="table-td">
                                                <div class="flex space-x-3 rtl:space-x-reverse">
                                                    <button wire:click="openEditPositionSec({{ $position->id }})"
                                                        class="action-btn text-primary">
                                                        <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                    </button>
                                                    <button wire:click="confirmDeletePosition({{ $position->id }})"
                                                        class="action-btn text-danger">
                                                        <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="table-td text-center">No positions found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    {{ $positions->links('vendor.livewire.simple-bootstrap') }}
                </div>
            </div>
        </div>
    </div>

    <!-- New Department Modal -->
    @if ($newDepartmentModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog top-1/2 !-translate-y-1/2 relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Create New Department
                            </h3>
                            <button wire:click="closeNewDepartmentSec" type="button"
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
                        <div class="p-6 space-y-4">
                            <div class="from-group">
                                <label for="departmentName" class="form-label">Department Name</label>
                                <input id="departmentName" type="text"
                                    class="form-control @error('departmentName') !border-danger-500 @enderror"
                                    wire:model="departmentName">
                                @error('departmentName')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="from-group">
                                <label for="departmentPrefixCode" class="form-label">Prefix Code</label>
                                <input id="departmentPrefixCode" type="text"
                                    class="form-control @error('departmentPrefixCode') !border-danger-500 @enderror"
                                    wire:model="departmentPrefixCode">
                                @error('departmentPrefixCode')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="from-group">
                                <label for="departmentDescription" class="form-label">Description</label>
                                <textarea id="departmentDescription" class="form-control @error('departmentDescription') !border-danger-500 @enderror"
                                    wire:model="departmentDescription" rows="3"></textarea>
                                @error('departmentDescription')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="addNewDepartment"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="addNewDepartment">Create</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="addNewDepartment"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Department Modal -->
    @if ($editDepartmentModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog top-1/2 !-translate-y-1/2 relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Edit Department
                            </h3>
                            <button wire:click="closeEditDepartmentSec" type="button"
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
                        <div class="p-6 space-y-4">
                            <div class="from-group">
                                <label for="departmentName" class="form-label">Department Name</label>
                                <input id="departmentName" type="text"
                                    class="form-control @error('departmentName') !border-danger-500 @enderror"
                                    wire:model="departmentName">
                                @error('departmentName')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="from-group">
                                <label for="departmentPrefixCode" class="form-label">Prefix Code</label>
                                <input id="departmentPrefixCode" type="text"
                                    class="form-control @error('departmentPrefixCode') !border-danger-500 @enderror"
                                    wire:model="departmentPrefixCode">
                                @error('departmentPrefixCode')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="from-group">
                                <label for="departmentDescription" class="form-label">Description</label>
                                <textarea id="departmentDescription" class="form-control @error('departmentDescription') !border-danger-500 @enderror"
                                    wire:model="departmentDescription" rows="3"></textarea>
                                @error('departmentDescription')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="updateDepartment"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="updateDepartment">Update</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="updateDepartment"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- New Position Modal -->
    @if ($newPositionModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog modal-lg top-1/2 !-translate-y-1/2 relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Create New Position
                            </h3>
                            <button wire:click="closeNewPositionSec" type="button"
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
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="from-group col-span-2">
                                    <label for="code" class="form-label">Code</label>
                                    <input id="code" type="text"
                                        class="form-control @error('code') !border-danger-500 @enderror"
                                        wire:model="code">
                                    @error('code')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="from-group">

                                    <label for="positionName" class="form-label">Position Name (English)</label>
                                    <input id="positionName" type="text"
                                        class="form-control @error('positionName') !border-danger-500 @enderror"
                                        wire:model="positionName">
                                    @error('positionName')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="from-group">
                                    <label for="positionArabicName" class="form-label">Position Name (Arabic)</label>
                                    <input id="positionArabicName" type="text"
                                        class="form-control @error('positionArabicName') !border-danger-500 @enderror"
                                        wire:model="positionArabicName">
                                    @error('positionArabicName')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="from-group">
                                    <label for="selectedDepartmentId" class="form-label">Department</label>
                                    <select id="selectedDepartmentId"
                                        class="form-control @error('selectedDepartmentId') !border-danger-500 @enderror"
                                        wire:model="selectedDepartmentId">
                                        <option value="">Select a department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedDepartmentId')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="from-group">
                                    <label for="parentId" class="form-label">Parent Position (Optional)</label>
                                    <select id="parentId" class="form-control" wire:model="parentId">
                                        <option value="">None</option>
                                        @foreach ($allPositions as $pos)
                                            <option value="{{ $pos->id }}">{{ $pos->code }} -
                                                {{ $pos->department->name }} - {{ $pos->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="from-group">
                                    <label for="jobDescription" class="form-label">Job Description (English)</label>
                                    <textarea id="jobDescription" class="form-control" wire:model="jobDescription" rows="3"></textarea>
                                </div>
                                <div class="from-group">
                                    <label for="arabicJobDescription" class="form-label">Job Description
                                        (Arabic)</label>
                                    <textarea id="arabicJobDescription" class="form-control" wire:model="arabicJobDescription" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="from-group">
                                    <label for="jobRequirements" class="form-label">Job Requirements (English)</label>
                                    <textarea id="jobRequirements" class="form-control" wire:model="jobRequirements" rows="3"></textarea>
                                </div>
                                <div class="from-group">
                                    <label for="arabicJobRequirements" class="form-label">Job Requirements
                                        (Arabic)</label>
                                    <textarea id="arabicJobRequirements" class="form-control" wire:model="arabicJobRequirements" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="from-group">
                                    <label for="jobQualifications" class="form-label">Job Qualifications
                                        (English)</label>
                                    <textarea id="jobQualifications" class="form-control" wire:model="jobQualifications" rows="3"></textarea>
                                </div>
                                <div class="from-group">
                                    <label for="arabicJobQualifications" class="form-label">Job Qualifications
                                        (Arabic)</label>
                                    <textarea id="arabicJobQualifications" class="form-control" wire:model="arabicJobQualifications" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="from-group">
                                    <label for="jobBenefits" class="form-label">Job Benefits (English)</label>
                                    <textarea id="jobBenefits" class="form-control" wire:model="jobBenefits" rows="3"></textarea>
                                </div>
                                <div class="from-group">
                                    <label for="arabicJobBenefits" class="form-label">Job Benefits (Arabic)</label>
                                    <textarea id="arabicJobBenefits" class="form-control" wire:model="arabicJobBenefits" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="addNewPosition"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="addNewPosition">Create</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="addNewPosition"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Position Modal -->
    @if ($editPositionModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog modal-lg top-1/2 !-translate-y-1/2 relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Edit Position
                            </h3>
                            <button wire:click="closeEditPositionSec" type="button"
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
                        <div class="p-6 space-y-4">
                            <div class="from-group col-span-2">
                                <label for="code" class="form-label">Code</label>
                                <input id="code" type="text"
                                    class="form-control @error('code') !border-danger-500 @enderror"
                                    wire:model="code">
                                @error('code')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="from-group">
                                    <label for="positionName" class="form-label">Position Name (English)</label>
                                    <input id="positionName" type="text"
                                        class="form-control @error('positionName') !border-danger-500 @enderror"
                                        wire:model="positionName">
                                    @error('positionName')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="from-group">
                                    <label for="positionArabicName" class="form-label">Position Name (Arabic)</label>
                                    <input id="positionArabicName" type="text"
                                        class="form-control @error('positionArabicName') !border-danger-500 @enderror"
                                        wire:model="positionArabicName">
                                    @error('positionArabicName')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="from-group">
                                    <label for="selectedDepartmentId" class="form-label">Department</label>
                                    <select id="selectedDepartmentId"
                                        class="form-control @error('selectedDepartmentId') !border-danger-500 @enderror"
                                        wire:model="selectedDepartmentId">
                                        <option value="">Select a department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedDepartmentId')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="from-group">
                                    <label for="parentId" class="form-label">Parent Position (Optional)</label>
                                    <select id="parentId" class="form-control" wire:model="parentId">
                                        <option value="">None</option>
                                        @foreach ($allPositions as $pos)
                                            @if ($pos->id != $positionId)
                                                <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="from-group">
                                    <label for="jobDescription" class="form-label">Job Description (English)</label>
                                    <textarea id="jobDescription" class="form-control" wire:model="jobDescription" rows="3"></textarea>
                                </div>
                                <div class="from-group">
                                    <label for="arabicJobDescription" class="form-label">Job Description
                                        (Arabic)</label>
                                    <textarea id="arabicJobDescription" class="form-control" wire:model="arabicJobDescription" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="from-group">
                                    <label for="jobRequirements" class="form-label">Job Requirements (English)</label>
                                    <textarea id="jobRequirements" class="form-control" wire:model="jobRequirements" rows="3"></textarea>
                                </div>
                                <div class="from-group">
                                    <label for="arabicJobRequirements" class="form-label">Job Requirements
                                        (Arabic)</label>
                                    <textarea id="arabicJobRequirements" class="form-control" wire:model="arabicJobRequirements" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="from-group">
                                    <label for="jobQualifications" class="form-label">Job Qualifications
                                        (English)</label>
                                    <textarea id="jobQualifications" class="form-control" wire:model="jobQualifications" rows="3"></textarea>
                                </div>
                                <div class="from-group">
                                    <label for="arabicJobQualifications" class="form-label">Job Qualifications
                                        (Arabic)</label>
                                    <textarea id="arabicJobQualifications" class="form-control" wire:model="arabicJobQualifications" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="from-group">
                                    <label for="jobBenefits" class="form-label">Job Benefits (English)</label>
                                    <textarea id="jobBenefits" class="form-control" wire:model="jobBenefits" rows="3"></textarea>
                                </div>
                                <div class="from-group">
                                    <label for="arabicJobBenefits" class="form-label">Job Benefits (Arabic)</label>
                                    <textarea id="arabicJobBenefits" class="form-control" wire:model="arabicJobBenefits" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="updatePosition"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="updatePosition">Update</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="updatePosition"
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
            <div class="modal-dialog top-1/2 !-translate-y-1/2 relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Confirm Delete
                            </h3>
                            <button wire:click="closeDeleteConfirmationModal" type="button"
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
                        <div class="p-6 space-y-4">
                            <p>Are you sure you want to delete this {{ $itemTypeToDelete }}? This action cannot be
                                undone.</p>

                            @if ($itemTypeToDelete === 'department')
                                <p class="text-danger-500">Warning: If this department has associated positions, it
                                    cannot be deleted.</p>
                            @endif

                            @if ($itemTypeToDelete === 'position')
                                <div class="text-danger-500">
                                    <p>Warning: A position cannot be deleted if:</p>
                                    <ul class="list-disc ml-5 mt-2">
                                        <li>It has child positions</li>
                                        <li>It has an employee assigned to it</li>
                                        <li>It has active vacancies</li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="closeDeleteConfirmationModal"
                                class="btn inline-flex justify-center text-white bg-slate-500">
                                Cancel
                            </button>
                            <button wire:click="confirmDelete"
                                class="btn inline-flex justify-center text-white bg-danger-500">
                                <span wire:loading.remove wire:target="confirmDelete">Delete</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="confirmDelete"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
