<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                {{ __('Document Manager') }}
            </h4>
        </div>
    </div>
    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="{{ __('Search document types...') }}"
                wire:model.live.debounce.500ms="search">
        </header>

        <div class="card-body px-6 pb-6">
            <div class="overflow-x-auto -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden ">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class=" border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class="table-th">{{ __('Name') }}</th>
                                    <th scope="col" class="table-th">{{ __('Description') }}</th>
                                    <th scope="col" class="table-th">{{ __('Required') }}</th>
                                    <th scope="col" class="table-th">{{ __('Status') }}</th>
                                    <th scope="col" class="table-th">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse($docManagers as $docManager)
                                    <tr>
                                        <td class="table-td">
                                            <div class="flex items-center space-x-3">
                                                <div class="avatar">
                                                    <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center">
                                                        <iconify-icon icon="heroicons:document-text" class="text-slate-600"></iconify-icon>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="font-semibold">{{ $docManager->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="table-td">
                                            <span class="text-sm text-slate-600">
                                                {{ $docManager->description ?: 'No description' }}
                                            </span>
                                        </td>
                                        <td class="table-td">
                                            @if($docManager->is_required)
                                                <span class="badge bg-success-500 text-success-500 bg-opacity-30">Required</span>
                                            @else
                                                <span class="badge bg-slate-500 text-slate-500 bg-opacity-30">Optional</span>
                                            @endif
                                        </td>
                                        <td class="table-td">
                                            @if($docManager->is_active)
                                                <span class="badge bg-success-500 text-success-500 bg-opacity-30">Active</span>
                                            @else
                                                <span class="badge bg-danger-500 text-danger-500 bg-opacity-30">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="table-td">
                                            <button wire:click="openEditDocManagerSec({{ $docManager->id }})"
                                                class="btn btn-sm btn-outline-primary">
                                                <iconify-icon icon="lucide:edit"></iconify-icon>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-8">
                                            <div class="flex flex-col items-center">
                                                <iconify-icon icon="heroicons:document-search" class="text-4xl text-slate-400 mb-2"></iconify-icon>
                                                <p class="text-slate-500">{{ __('No document types found') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
             <!-- Pagination -->
             <div class="mt-6">
                {{ $docManagers->links('vendor.livewire.simple-bootstrap') }}
            </div>
        </div>
    </div>

    @if ($setDocManagerSec)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                {{ __('Edit Document Type') }}
                            </h3>
                            <button wire:click="closeSetDocManagerSec" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">{{ __('Close') }}</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-6 space-y-4">
                            <!-- Name -->
                            <div>
                                <label for="name" class="form-label">{{ __('Name') }}</label>
                                <input type="text" wire:model="name" id="name" class="form-control">
                                @error('name') <span class="text-danger-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="form-label">{{ __('Description') }}</label>
                                <textarea wire:model="description" id="description" rows="3"
                                    class="form-control"
                                    placeholder="Optional description for this document type"></textarea>
                                @error('description') <span class="text-danger-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Required -->
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="is_required" id="is_required" class="form-checkbox">
                                <label for="is_required" class="ml-2 form-label">{{ __('Required for employees') }}</label>
                            </div>

                            <!-- Active -->
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="is_active" id="is_active" class="form-checkbox">
                                <label for="is_active" class="ml-2 form-label">{{ __('Active') }}</label>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="closeSetDocManagerSec" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                {{ __('Cancel') }}
                            </button>
                            <button wire:click="editDocManager" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="editDocManager">{{ __('Update') }}</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="editDocManager"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
