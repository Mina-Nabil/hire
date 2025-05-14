<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Locations
            </h4>
        </div>
        <div class="flex space-x-3 items-center rtl:space-x-reverse">
            @can('create', App\Models\Hierarchy\Location::class)
                <button class="btn inline-flex justify-center btn-primary" wire:click="openModal">
                    <span class="flex items-center">
                        <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="heroicons-outline:plus"></iconify-icon>
                        <span>Add Location</span>
                    </span>
                </button>
            @endcan
        </div>
    </div>

    <div class="card">
        <header class="card-header noborder">
            <div class="w-full">
                <div class="relative">
                    <input type="text" class="form-control !pl-9 w-full" placeholder="Search..."
                        wire:model.live.debounce.300ms="search">
                    <iconify-icon class="absolute left-2 top-1/2 -translate-y-1/2 text-base text-slate-500" style="transform: translate(5px, -13px);"
                        icon="heroicons-solid:search"></iconify-icon>
                </div>
            </div>
        </header>
        <div class="card-body px-6 pb-6">
            <div class="overflow-x-auto -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden ">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class="bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class="table-th">
                                        Name
                                    </th>
                                    <th scope="col" class="table-th">
                                        Positions Count
                                    </th>
                                    <th scope="col" class="table-th">
                                        HR Users
                                    </th>
                                    <th scope="col" class="table-th w-20">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @foreach ($locations as $location)
                                    <tr class="even:bg-slate-50 dark:even:bg-slate-700">
                                        <td class="table-td">{{ $location->name }}</td>
                                        <td class="table-td">{{ $location->positions()->count() }}</td>
                                        <td class="table-td">
                                            <span class="badge bg-primary-500 text-primary-500 bg-opacity-30 capitalize rounded-3xl cursor-pointer" wire:click="openHrUsersModal({{ $location->id }})">
                                                HR Users ({{ $location->assignedHrUsers()->count() }})
                                            </span>
                                        </td>
                                        <td class="table-td">
                                            <div class="flex space-x-3 rtl:space-x-reverse">
                                                @can('update', $location)
                                                    <button class="action-btn" wire:click="edit({{ $location->id }})">
                                                        <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                    </button>
                                                @endcan
                                                @can('delete', $location)
                                                    <button class="action-btn" wire:confirm="Are you sure you want to delete this location?"
                                                        wire:click="deleteLocation({{ $location->id }})">
                                                        <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                    </button>
                                                @endcan
                                                @can('update', $location)
                                                    <button class="action-btn" wire:click="openHrUsersModal({{ $location->id }})">
                                                        <iconify-icon icon="heroicons:user-group"></iconify-icon>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if ($locations->isEmpty())
                            <div class="text-center py-4">
                                <span class="text-slate-500 dark:text-slate-400">No locations found</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex justify-between items-center mt-4">
                <div>{{ $locations->links() }}</div>
            </div>
        </div>
    </div>

    <x-modal wire:model.live="showModal">
        <x-slot name="title">
            {{ $locationId ? 'Edit Location' : 'Add Location' }}
        </x-slot>

        <div class="space-y-4">
            <div>
                <x-text-input class="" wire:model.live="name" label="Name" placeholder="Enter location name" />
                @error('name')
                    <span class="font-Inter text-sm text-danger-500 inline-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-end space-x-2">
                <button class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                <button class="btn btn-primary" wire:click="save">Save</button>
            </div>
        </x-slot>
    </x-modal>

    <!-- HR Users Assignment Modal -->
    @if($hrUsersModal)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="hr_users_modal" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Assign HR Users to {{ $selectedLocation ? $selectedLocation->name : '' }}
                            </h3>
                            <button wire:click="closeHrUsersModal" type="button"
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
                                <div class="grid grid-cols-1 gap-4">
                                    @forelse($availableHrUsers as $user)
                                        <div class="flex items-center">
                                            <div class="checkbox-area">
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input id="user-{{ $user->id }}" value="{{ $user->id }}" type="checkbox" class="hidden"
                                                        wire:model.live="selectedUsers">
                                                    <span
                                                        class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                        <img src="{{ asset('images/icon/ck-white.svg') }}"
                                                            alt=""
                                                            class="h-[10px] w-[10px] block m-auto opacity-0" />
                                                    </span>
                                                    <span
                                                        class="text-slate-600 dark:text-slate-300 text-sm leading-6">{{ $user->name }} ({{ $user->username }})</span>
                                                </label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center p-4">
                                            <p>No HR users available</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="closeHrUsersModal" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                Cancel
                            </button>
                            <button wire:click="saveHrUserAssignments" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="saveHrUserAssignments">Save</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="saveHrUserAssignments"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div> 