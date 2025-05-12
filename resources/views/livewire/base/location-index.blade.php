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
</div> 