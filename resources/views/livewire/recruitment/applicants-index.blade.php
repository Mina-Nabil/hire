<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Applicants
            </h4>
        </div>
    </div>

    <div class="card">
        <header class="card-header noborder">
            <div class="flex flex-wrap justify-between items-center w-full">
                <div class="flex items-center min-w-[310px]">
                    <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg mr-2"
                        icon="line-md:loading-twotone-loop"></iconify-icon>
                    <input type="text" class="form-control !pl-9 mr-1 basis-1/4 w-full"
                        placeholder="Search by name or position" wire:model.live.debounce.400ms="search">
                </div>

                <div>
                    <button type="button" class="btn inline-flex justify-center btn-outline-primary dropdown-toggle"
                        wire:click="toggleFilters" aria-expanded="false">
                        <span class="flex items-center">
                            <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2"
                                icon="heroicons-outline:filter"></iconify-icon>
                            <span>Filters</span>
                        </span>
                    </button>

                    @if ($showFilters)
                        <div class="p-4" style="min-width: 550px;">
                            <!-- Date Range -->
                            <div class="mb-4">
                                <label class="form-label">Created</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="form-label text-sm">From</label>
                                        <input type="date" wire:model.live="startDate" class="form-control">
                                    </div>
                                    <div>
                                        <label class="form-label text-sm">To</label>
                                        <input type="date" wire:model.live="endDate" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <!-- Military Status -->
                            <div class="mb-4">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="form-label">Military Status</label>
                                        <select wire:model.live="militaryStatus" class="form-control">
                                            <option value="">All</option>
                                            @foreach ($militaryStatusOptions as $status)
                                                <option value="{{ $status }}">{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label">Marital Status</label>
                                        <select wire:model.live="maritalStatus" class="form-control">
                                            <option value="">All</option>
                                            @foreach ($maritalStatusOptions as $status)
                                                <option value="{{ $status }}">{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- City -->
                            <div class="mb-4">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="form-label">City</label>
                                        <select wire:model.live="cityId" class="form-control">
                                            <option value="">All</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>


                                        <!-- Area -->
                                        <label class="form-label">Area</label>
                                        <select wire:model.live="areaId" class="form-control">
                                            <option value="">Please select a city first</option>
                                            @foreach ($areas as $area)
                                                <option value="{{ $area->id }}">{{ $area->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Age Range -->
                            <div class="mb-4">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="form-label text-sm">Min Age</label>
                                        <input type="number" wire:model.live="minAge" min="16" max="100"
                                            class="form-control" placeholder="Min">
                                    </div>
                                    <div>
                                        <label class="form-label text-sm">Max Age</label>
                                        <input type="number" wire:model.live="maxAge" min="16" max="100"
                                            class="form-control" placeholder="Max">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end mt-4">
                                <button type="button" wire:click="resetFilters" class="btn btn-sm btn-outline-danger">
                                    <span class="flex items-center">
                                        <iconify-icon class="text-lg ltr:mr-1 rtl:ml-1"
                                            icon="heroicons-outline:x"></iconify-icon>
                                        <span>Clear Filters</span>
                                    </span>
                                </button>
                            </div>
                    @endif
                </div>
            </div>
    </div>
    </header>

    <div class="card-body">
        <div class="overflow-x-auto">
            @if (count($applicants) > 0)
                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                    <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                        <tr>
                            <th scope="col" class="table-th">Full Name</th>
                            <th scope="col" class="table-th">Phone</th>
                            <th scope="col" class="table-th">Email</th>
                            <th scope="col" class="table-th">Area</th>
                            <th scope="col" class="table-th">Marital Status</th>
                            <th scope="col" class="table-th">Military Status</th>
                            <th scope="col" class="table-th">Applications</th>
                            <th scope="col" class="table-th">Interviews</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                        @foreach ($applicants as $applicant)
                            <tr class="even:bg-slate-100 dark:even:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer" wire:click="showApplicant({{ $applicant->id }})">
                                <td class="table-td">
                                    <div class="flex items-center">
                                        @if ($applicant->image_url)
                                            <div class="flex-none">
                                                <div class="h-10 w-10 rounded-full overflow-hidden mr-2">
                                                    <img src="{{ Storage::url($applicant->image_url) }}"
                                                        alt="{{ $applicant->full_name }}"
                                                        class="h-full w-full object-cover">
                                                </div>
                                            </div>
                                        @endif
                                        <span
                                            class="text-sm text-slate-600 dark:text-slate-300 capitalize">{{ $applicant->full_name }}</span>
                                    </div>
                                </td>
                                <td class="table-td">{{ $applicant->phone }}</td>
                                <td class="table-td">{{ $applicant->email }}</td>
                                <td class="table-td">
                                    {{ $applicant->area ? $applicant->area->city->name . ' - ' . $applicant->area->name : '-' }}
                                </td>
                                <td class="table-td">{{ $applicant->marital_status ?? '-' }}</td>
                                <td class="table-td">{{ $applicant->military_status ?? '-' }}</td>
                                <td class="table-td">
                                    <div class="flex space-x-3 rtl:space-x-reverse">
                                        <span class="text-success-500 text-xl leading-[0]">
                                            {{ $applicant->applications->count() }}
                                        </span>
                                    </div>
                                </td>
                                <td class="table-td">
                                    <div class="flex space-x-3 rtl:space-x-reverse">
                                        <span class="text-success-500 text-xl leading-[0]">
                                            {{ $applicant->interviews->count() }}
                                        </span>
                                    </div>
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
                            <h2 class="card-title text-slate-900 dark:text-white mb-3">No applicants with the applied
                                filters</h2>
                            <p class="card-text">Try changing the filters or search terms for this view.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div style="position: sticky; bottom:0;width:100%; z-index:10;"
        class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
        {{ $applicants->links('vendor.livewire.simple-bootstrap') }}
    </div>
</div>
</div>
