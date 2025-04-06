<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Applicant Channels
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            <button wire:click="openCreateModal" class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1 btn-sm">
                Add Channel
            </button>
        </div>
    </div>

    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg" icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search" wire:model.live.debounce.400ms="search">
        </header>

        <div class="card-body">
            <div class="table-responsive">
                @if (!$channels->isEmpty())
                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                    <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                        <tr>
                            <th scope="col" class="table-th">Name</th>
                            <th scope="col" class="table-th">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                        @foreach ($channels as $channel)
                            <tr class="even:bg-slate-100 dark:even:bg-slate-700">
                                <td class="table-td">
                                    <span class="hover-underline">
                                        <b>{{ $channel->name }}</b>
                                    </span>
                                </td>
                                <td class="table-td">
                                    <div class="flex space-x-3 rtl:space-x-reverse">
                                        <button wire:click="openEditModal({{ $channel }})" class="action-btn" type="button">
                                            <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                        </button>
                                        <button wire:click="openDeleteModal({{ $channel }})" class="action-btn" type="button">
                                            <iconify-icon icon="heroicons:trash"></iconify-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            @if ($channels->isEmpty())
                <div class="card m-5 p-5">
                    <div class="card-body rounded-md bg-white dark:bg-slate-800">
                        <div class="items-center text-center p-5">
                            <h2>
                                <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                            </h2>
                            <h2 class="card-title text-slate-900 dark:text-white mb-3">No channels with the applied filters</h2>
                            <p class="card-text">Try changing the filters or search terms for this view.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div style="position: sticky; bottom:0;width:100%; z-index:10;" class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            {{ $channels->links('vendor.livewire.simple-bootstrap') }}
        </div>
    </div>

    <!-- Create Modal -->
    @if ($showCreateModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show" tabindex="-1" aria-labelledby="createModal" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Create New Channel
                            </h3>
                            <button wire:click="closeCreateModal" type="button" class="text-white hover:text-gray-200">
                                <iconify-icon icon="material-symbols:close" width="24" height="24"></iconify-icon>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-6 space-y-4">
                            <div class="from-group">
                                <div class="input-area">
                                    <label for="name" class="form-label">Name</label>
                                    <input id="name" type="text" class="form-control @error('name') !border-danger-500 @enderror" wire:model="name">
                                </div>
                                @error('name')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="createChannel" class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="createChannel">Create</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]" wire:loading wire:target="createChannel" icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Modal -->
    @if ($showEditModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show" tabindex="-1" aria-labelledby="editModal" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Edit Channel
                            </h3>
                            <button wire:click="closeEditModal" type="button" class="text-white hover:text-gray-200">
                                <iconify-icon icon="material-symbols:close" width="24" height="24"></iconify-icon>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-6 space-y-4">
                            <div class="from-group">
                                <div class="input-area">
                                    <label for="editName" class="form-label">Name</label>
                                    <input id="editName" type="text" class="form-control @error('editName') !border-danger-500 @enderror" wire:model="editName">
                                </div>
                                @error('editName')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="updateChannel" class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="updateChannel">Update</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]" wire:loading wire:target="updateChannel" icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if ($showDeleteModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show" tabindex="-1" aria-labelledby="deleteModal" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Delete Channel
                            </h3>
                            <button wire:click="closeDeleteModal" type="button" class="text-white hover:text-gray-200">
                                <iconify-icon icon="material-symbols:close" width="24" height="24"></iconify-icon>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-6">
                            <p>Are you sure you want to delete this channel? This action cannot be undone.</p>
                            <p class="mt-2"><strong>Channel Name:</strong> {{ $selectedChannel->name }}</p>
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="deleteChannel" class="btn inline-flex justify-center text-white bg-danger-500">
                                <span wire:loading.remove wire:target="deleteChannel">Delete</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]" wire:loading wire:target="deleteChannel" icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div> 