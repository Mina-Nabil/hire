<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Bus Arrival Records
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @if ($isAdmin || $isHr)
                <button wire:click="openCreateModal"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    Add Bus Arrival
                </button>
            @endif
        </div>
    </div>

    <div class="card">
        <header class="card-header noborder">
            <div class="flex flex-wrap justify-between items-center w-full">
                <div class="flex items-center min-w-[310px]">
                    <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg mr-2"
                        icon="line-md:loading-twotone-loop"></iconify-icon>
                    <input type="text" class="form-control !pl-9 mr-1 basis-1/4 w-full"
                        placeholder="Search by bus name" wire:model.live.debounce.400ms="search">
                </div>

                <div class="flex items-center sm:mt-2">
                    @if ($showFilters)
                        <button type="button" wire:click="resetFilters"
                            class="btn btn-sm btn-outline-danger btn-sm mr-2">
                            <span class="flex items-center">
                                <iconify-icon class="text-lg ltr:mr-1 rtl:ml-1 text-sm"
                                    icon="heroicons-outline:x"></iconify-icon>
                                <span>Clear Filters</span>
                            </span>
                        </button>
                    @endif
                    <button type="button"
                        class="btn inline-flex justify-center btn-outline-primary dropdown-toggle btn-sm"
                        wire:click="toggleFilters" aria-expanded="false">
                        <span class="flex items-center">
                            <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2 text-sm"
                                icon="heroicons-outline:filter"></iconify-icon>
                            <span>Filters</span>
                        </span>
                    </button>
                </div>
            </div>
        </header>

        @if ($showFilters)
            <div class="p-4">
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="form-label text-sm">Date From</label>
                        <input type="date" wire:model.live="startDate" class="form-control">
                    </div>
                    <div>
                        <label class="form-label text-sm">Date To</label>
                        <input type="date" wire:model.live="endDate" class="form-control">
                    </div>
                    <div>
                        <label class="form-label text-sm">Bus</label>
                        <select wire:model.live="busFilter" class="form-control">
                            <option value="">All Buses</option>
                            @foreach ($buses as $bus)
                                <option value="{{ $bus->id }}">{{ $bus->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif

        <div class="card-body">
            <div class="overflow-x-auto">
                @if (count($busArrivals) > 0)
                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                        <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col" class="table-th">Bus</th>
                                <th scope="col" class="table-th">Date</th>
                                <th scope="col" class="table-th">Arrival Time</th>
                                <th scope="col" class="table-th">Created At</th>
                                @if ($isAdmin || $isHr)
                                    <th scope="col" class="table-th">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            @foreach ($busArrivals as $arrival)
                                <tr
                                    class="even:bg-slate-100 dark:even:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700">
                                    <td class="table-td">
                                        <div class="flex items-center">
                                            <div class="flex-none">
                                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mr-2">
                                                    <iconify-icon icon="mdi:bus" class="text-slate-600 dark:text-slate-300 text-lg"></iconify-icon>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="text-sm text-slate-600 dark:text-slate-300 capitalize font-medium">
                                                    {{ $arrival->bus ? $arrival->bus->name : 'N/A' }}
                                                </span>
                                                @if ($arrival->bus)
                                                    <span class="block text-xs text-slate-500">
                                                        Bus ID: {{ $arrival->bus->id }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-td">
                                        <span class="text-sm text-slate-600 dark:text-slate-300">
                                            {{ \Carbon\Carbon::parse($arrival->date)->format('M d, Y') }}
                                        </span>
                                        <span class="block text-xs text-slate-500">
                                            {{ \Carbon\Carbon::parse($arrival->date)->format('l') }}
                                        </span>
                                    </td>
                                    <td class="table-td">
                                        <span class="text-sm text-slate-600 dark:text-slate-300 font-medium">
                                            {{ \Carbon\Carbon::parse($arrival->time)->format('h:i A') }}
                                        </span>
                                    </td>
                                    <td class="table-td">
                                        <span class="text-sm text-slate-600 dark:text-slate-300">
                                            {{ $arrival->created_at->format('M d, Y') }}
                                        </span>
                                        <span class="block text-xs text-slate-500">
                                            {{ $arrival->created_at->format('h:i A') }}
                                        </span>
                                    </td>
                                    @if ($isAdmin || $isHr)
                                        <td class="table-td">
                                            <div class="flex space-x-3 rtl:space-x-reverse">
                                                <button 
                                                    wire:click="$dispatch('showConfirmation',{message:'Are you sure you want to delete this bus arrival?',color:'danger',callback:'deleteBusArrival',params:{{ $arrival->id }}})"
                                                    class="action-btn" 
                                                    type="button"
                                                    title="Delete Bus Arrival">
                                                    <iconify-icon icon="heroicons:trash" class="text-danger-500"></iconify-icon>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No bus arrival records found
                                    with the applied filters</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div style="position: sticky; bottom:0;width:100%; z-index:10;"
            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            {{ $busArrivals->links('vendor.livewire.simple-bootstrap') }}
        </div>
    </div>

    <!-- Create Bus Arrival Modal -->
    <x-modal wire:model="showCreateModal">
        <x-slot name="title">Add New Bus Arrival</x-slot>
        
        <!-- Modal body -->
        <div class="p-6 space-y-4">
            <div class="from-group">
                <x-select label="Bus" wire:model="selectedBusId" errorMessage="{{ $errors->first('selectedBusId') }}">
                    <option value="">Select a bus</option>
                    @foreach ($buses as $bus)
                        <option value="{{ $bus->id }}">{{ $bus->name }}</option>
                    @endforeach
                </x-select>
            </div>
            
            <div class="from-group">
                <div class="input-area">
                    <label for="arrivalDateTime" class="form-label">Arrival Date & Time <span class="text-danger-500">*</span></label>
                    <input 
                        type="datetime-local" 
                        id="arrivalDateTime"
                        wire:model="arrivalDateTime"
                        class="form-control @error('arrivalDateTime') !border-danger-500 @enderror">
                    @error('arrivalDateTime')
                        <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="bg-slate-50 dark:bg-slate-700 p-3 rounded-md">
                <div class="flex items-start">
                    <iconify-icon class="text-lg text-info-500 mr-2 mt-0.5" 
                        icon="heroicons-outline:information-circle"></iconify-icon>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        This will create a new bus arrival record for the selected bus at the specified date and time.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Modal footer -->
        <x-slot name="footer">
            <x-secondary-button wire:click="closeCreateModal">Cancel</x-secondary-button>
            <x-primary-button wire:click.prevent="createBusArrival" 
                loadingFunction="createBusArrival">Create Bus Arrival</x-primary-button>
        </x-slot>
    </x-modal>
</div>
