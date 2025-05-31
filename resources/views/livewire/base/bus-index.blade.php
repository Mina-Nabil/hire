<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                {{ __('Buses Management') }}
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @can('create', App\Models\Attendance\Bus::class)
                <button wire:click="openNewBusSec"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    {{ __('Create Bus') }}
                </button>
            @endcan
        </div>
    </div>
    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="{{ __('Search buses') }}"
                wire:model.live.debounce.500ms="search">
        </header>

        <div class="card-body px-6 pb-6">
            <div class=" -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden ">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead
                                class=" border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class=" table-th ">
                                        {{ __('Name') }}
                                    </th>
                                    <th scope="col" class=" table-th ">
                                        {{ __('Total Arrivals') }}
                                    </th>
                                    <th scope="col" class=" table-th ">
                                        {{ __('Created At') }}
                                    </th>
                                    <th scope="col" class=" table-th ">
                                        {{ __('Action') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @foreach ($buses as $bus)
                                    <tr>
                                        <td class="table-td">
                                            <div class="flex items-center">
                                                <div class="flex-none">
                                                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mr-2">
                                                        <iconify-icon icon="mdi:bus" class="text-slate-600 dark:text-slate-300 text-lg"></iconify-icon>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="text-sm text-slate-600 dark:text-slate-300 capitalize font-medium">
                                                        {{ $bus->name }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="table-td">
                                            <span class="badge badge-light">{{ $bus->arrivals_count ?? $bus->arrivals()->count() }}</span>
                                        </td>
                                        <td class="table-td">
                                            {{ $bus->created_at->format('Y-m-d') }}
                                        </td>
                                        <td>
                                            <div class="dropstart relative z-[9999]">
                                                <button class="inline-flex justify-center items-center" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2"
                                                        icon="heroicons-outline:dots-vertical"></iconify-icon>
                                                </button>
                                                <ul
                                                    class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">

                                                    @can('update', $bus)
                                                        <li wire:click="updateThisBus({{ $bus->id }})">
                                                            <span
                                                                class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                                <iconify-icon icon="lucide:edit"></iconify-icon>
                                                                <span>{{ __('Edit') }}</span></span>
                                                        </li>
                                                    @endcan

                                                    @can('delete', $bus)
                                                        <li wire:click="$dispatch('showConfirmation',{message:'Are you sure you want to delete this bus?',color:'danger',callback:'deleteBus',params:{{ $bus->id }}})">
                                                            <span
                                                                class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                                <iconify-icon icon="lucide:trash-2"></iconify-icon>
                                                                <span>{{ __('Delete') }}</span></span>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if ($buses->isEmpty())
                            {{-- START: empty filter result --}}
                            <div class="card m-5 p-5">
                                <div class="card-body rounded-md bg-white dark:bg-slate-800">
                                    <div class="items-center text-center p-5">
                                        <h2><iconify-icon icon="icon-park-outline:search"></iconify-icon></h2>
                                        <h2 class="card-title text-slate-900 dark:text-white mb-3">{{ __('No buses found') }}</h2>
                                        <p class="card-text">{{ __('Try changing your search criteria') }}
                                        </p>
                                        <a href="{{ url('/buses') }}"
                                            class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">{{ __('View all buses') }}</a>
                                    </div>
                                </div>
                            </div>
                            {{-- END: empty filter result --}}
                        @endif
                    </div>

                </div>
                <div class="mt-6">
                    {{ $buses->links('vendor.livewire.simple-bootstrap') }}
                </div>
            </div>
        </div>
    </div>

    @can('create', App\Models\Attendance\Bus::class)
        @if ($setBusSec)
            <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
                tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    {{ $setBusSec === true ? __('Create New Bus') : __('Edit Bus') }}
                                </h3>
                                <button wire:click="closeSetBusSec" type="button"
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
                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="name" class="form-label">{{ __('Bus Name') }}</label>
                                        <input id="name" type="text"
                                            class="form-control @error('name') !border-danger-500 @enderror"
                                            wire:model="name" autocomplete="off" placeholder="Enter bus name">
                                    </div>
                                    @error('name')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeSetBusSec" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    {{ __('Close') }}
                                </button>
                                <button
                                    @if ($setBusSec === true) wire:click="addNewBus" @else wire:click="editBus" @endif
                                    data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="addNewBus,editBus">{{ __('Submit') }}</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="addNewBus,editBus"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan
</div>
