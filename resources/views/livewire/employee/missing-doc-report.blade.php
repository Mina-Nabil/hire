<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Employees with Missing or Expired Documents
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @if ($anyFiltersActive)
                <button wire:click="clearAllFilters" class="btn inline-flex justify-center btn-outline-dark btn-sm">
                    Clear Filters
                </button>
            @endif
            <button wire:click="showMissingFilters" class="btn inline-flex justify-center btn-dark btn-sm">
                Filter Missing
            </button>
            <button wire:click="showExpiredFilters" class="btn inline-flex justify-center btn-dark btn-sm">
                Filter Expired
            </button>
        </div>
    </div>

    <div class="card mb-6">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="searchTerm" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search by name or email"
                wire:model.live.debounce.500ms="searchTerm">

        </header>
        @if ($anyFiltersActive)
            <header class="card-header cust-card-header noborder">
                <div class="text-sm text-warning-500 mt-1">
                    @if ($missingFilterActive)
                        @php
                            $activeFilters = array_keys(array_filter($missingDocFilters));
                            $filterText = implode(', ', $activeFilters);
                        @endphp
                        <span class="badge bg-slate-900 text-white mr-1" title="{{ $filterText }}">
                            Missing: ({{ $filterText }})
                        </span>
                    @endif
                    @if ($expiredFilterActive)
                        @php
                            $activeFilters = array_keys(array_filter($expiredDocFilters));
                            $filterText = implode(', ', $activeFilters);
                        @endphp
                        <span class="badge bg-slate-900 text-white" title="{{ $filterText }}">
                            Expired: ({{ $filterText }})
                        </span>
                    @endif
                </div>
            </header>
        @endif
        {{-- <header class="card-header noborder">
            
        </header> --}}
        <div class="card-body px-6 pb-6">
            <div class="overflow-x-auto -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class="bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class="table-th">ID</th>
                                    <th scope="col" class="table-th">Name</th>
                                    <th scope="col" class="table-th">Email</th>
                                    <th scope="col" class="table-th">Phone</th>
                                    <th scope="col" class="table-th">Missing Documents</th>
                                    <th scope="col" class="table-th">Expired Documents</th>
                                    <th scope="col" class="table-th">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse($employees as $employee)
                                    @if (!$employee->hidden)
                                        <tr>
                                            <td class="table-td">{{ $employee->id }}</td>
                                            <td class="table-td">{{ $employee->name }}</td>
                                            <td class="table-td">{{ $employee->email }}</td>
                                            <td class="table-td">{{ $employee->phone }}</td>
                                            <td class="table-td">
                                                @if ($employee->missing_docs_count > 0)
                                                    <button wire:click="showMissingDocuments({{ $employee->id }})"
                                                        class="btn btn-sm inline-flex justify-center btn-outline-danger btn-sm">
                                                        {{ $employee->missing_docs_count }} Missing
                                                    </button>
                                                @else
                                                    <span class="badge bg-success-500 text-white">None</span>
                                                @endif
                                            </td>
                                            <td class="table-td">
                                                @if ($employee->expired_docs_count > 0)
                                                    <button wire:click="showExpiredDocuments({{ $employee->id }})"
                                                        class="btn btn-sm inline-flex justify-center btn-outline-warning btn-sm">
                                                        {{ $employee->expired_docs_count }} Expired
                                                    </button>
                                                @else
                                                    <span class="badge bg-success-500 text-white">None</span>
                                                @endif
                                            </td>
                                            <td class="table-td">
                                                <a href="{{ route('employees.show', $employee->id) }}"
                                                    class="btn btn-sm inline-flex justify-center btn-dark btn-sm">
                                                    <iconify-icon icon="heroicons:eye"></iconify-icon>
                                                    <span class="ml-2">View</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="7" class="table-td text-center">No employees found with missing
                                            or expired documents</td>
                                    </tr>
                                @endforelse

                                @if ($employees->count() > 0 && $employees->where('hidden', false)->count() === 0)
                                    <tr>
                                        <td colspan="7" class="table-td text-center">No employees match the current
                                            filters</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                {{ $employees->links('vendor.livewire.simple-bootstrap') }}
            </div>
        </div>
    </div>

    <!-- Missing Documents Modal -->
    @if ($showMissingDocModal && $selectedEmployee)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-warning-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Missing Documents for {{ $selectedEmployee->name }}
                            </h3>
                            <button wire:click="closeModal" type="button"
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
                            @if (count($missingDocuments) > 0)
                                <ul class="list-group">
                                    @foreach ($missingDocuments as $document)
                                        <li class="list-group-item border border-slate-200 p-3 rounded mb-2">
                                            {{ $document }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p>No missing documents found.</p>
                            @endif
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <a href="{{ route('employees.show', $selectedEmployee->id) }}"
                                class="btn inline-flex justify-center text-white bg-primary-500">
                                <iconify-icon icon="heroicons:user"></iconify-icon>
                                <span class="ml-2">Go to Employee</span>
                            </a>
                            <button type="button" class="btn inline-flex justify-center text-white bg-slate-500"
                                wire:click="closeModal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Expired Documents Modal -->
    @if ($showExpiredDocModal && $selectedEmployee)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-danger-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Expired Documents for {{ $selectedEmployee->name }}
                            </h3>
                            <button wire:click="closeModal" type="button"
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
                            @if (count($expiredDocuments) > 0)
                                <ul class="list-group">
                                    @foreach ($expiredDocuments as $document)
                                        <li class="list-group-item border border-slate-200 p-3 rounded mb-2">
                                            {{ $document }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p>No expired documents found.</p>
                            @endif
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <a href="{{ route('employees.show', $selectedEmployee->id) }}"
                                class="btn inline-flex justify-center text-white bg-primary-500">
                                <iconify-icon icon="heroicons:user"></iconify-icon>
                                <span class="ml-2">Go to Employee</span>
                            </a>
                            <button type="button" class="btn inline-flex justify-center text-white bg-slate-500"
                                wire:click="closeModal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filter Missing Documents Modal -->
    @if ($showFilterMissingModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-slate-900">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Filter by Missing Documents
                            </h3>
                            <button wire:click="closeModal" type="button"
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
                            <p class="text-slate-700 dark:text-slate-300 mb-4">Select document types to filter:</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($documentTypes as $docType => $value)
                                    <div class="checkbox-area">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="hidden"
                                                wire:model.live="missingDocFilters.{{ $docType }}">
                                            <span
                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                <img src="{{ asset('images/icon/ck-white.svg') }}" alt=""
                                                    class="h-[10px] w-[10px] block m-auto opacity-0" />
                                            </span>
                                            <span
                                                class="text-slate-600 dark:text-slate-300 text-sm leading-6">{{ $docType }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button type="button" class="btn inline-flex justify-center text-white bg-slate-900"
                                wire:click="applyMissingFilters">Apply Filters</button>
                            <button type="button" class="btn inline-flex justify-center text-white bg-slate-500"
                                wire:click="closeModal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filter Expired Documents Modal -->
    @if ($showFilterExpiredModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-slate-900">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Filter by Expired Documents
                            </h3>
                            <button wire:click="closeModal" type="button"
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
                            <p class="text-slate-700 dark:text-slate-300 mb-4">Select document types to filter:</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($documentTypes as $docType => $value)
                                    <div class="checkbox-area">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="hidden"
                                                wire:model.live="expiredDocFilters.{{ $docType }}">
                                            <span
                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                <img src="{{ asset('images/icon/ck-white.svg') }}" alt=""
                                                    class="h-[10px] w-[10px] block m-auto opacity-0" />
                                            </span>
                                            <span
                                                class="text-slate-600 dark:text-slate-300 text-sm leading-6">{{ $docType }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button type="button" class="btn inline-flex justify-center text-white bg-slate-900"
                                wire:click="applyExpiredFilters">Apply Filters</button>
                            <button type="button" class="btn inline-flex justify-center text-white bg-slate-500"
                                wire:click="closeModal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
