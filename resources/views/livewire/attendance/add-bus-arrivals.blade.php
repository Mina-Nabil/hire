<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Bus Arrival Sheet Management
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            <button wire:click="openFileUpload"
                class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:upload-bold"></iconify-icon>
                Upload Sheet
            </button>
            <button wire:click="downloadTemplate"
                class="btn inline-flex justify-center btn-light dark:bg-slate-700 dark:text-slate-300 m-1">
                <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:download-bold"></iconify-icon>
                Download Template
            </button>
        </div>
    </div>

    <div class="card">
        @if (count($uploadedBusArrivals) > 0)
            <header class="card-header noborder">
                <div class="flex justify-between items-center w-full">
                    <div class="flex-col space-y-1">
                        <h4 class="card-title">Uploaded Bus Arrival Data</h4>
                        <span class="text-sm text-slate-500 mt-1">({{ count($uploadedBusArrivals) }} rows)</span>
                    </div>
                    <div class="flex space-x-2">
                        <x-secondary-button wire:click="clearUploadedBusArrivals">Clear</x-secondary-button>
                        <x-primary-button wire:click.prevent="saveBusArrivals">Save Valid Bus Arrivals</x-primary-button>
                    </div>
                </div>
            </header>
            <div class="card-body">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                        <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col" class="table-th">Bus</th>
                                <th scope="col" class="table-th">Date</th>
                                <th scope="col" class="table-th">Arrival Time</th>
                                <th scope="col" class="table-th">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            @foreach ($uploadedBusArrivals as $busArrival)
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
                                                <span class="text-sm text-slate-600 dark:text-slate-300 capitalize">
                                                    {{ $busArrival['bus'] ? $busArrival['bus']->name : 'Not Found' }}
                                                </span>
                                                @if ($busArrival['bus'])
                                                    <span class="block text-xs text-slate-500">
                                                        Bus ID: {{ $busArrival['bus']->id }}
                                                    </span>
                                                @else
                                                    <span class="block text-xs text-slate-500">
                                                        Uploaded: {{ $busArrival['uploaded_name'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-td">
                                        {{ $busArrival['date'] }}
                                    </td>
                                    <td class="table-td">
                                        {{ $busArrival['time'] }}
                                    </td>
                                    <td class="table-td">
                                        @if ($busArrival['bus_id'] === "Not Found")
                                            <span class="badge badge-danger">Bus {{ $busArrival['uploaded_name'] }}
                                                Not Found</span>
                                        @else
                                            <span class="badge badge-success">Valid</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    @if ($showUploadModal)
        <x-modal wire:model="showUploadModal">
            <x-slot name="title">Upload Bus Arrival Sheet</x-slot>
            <!-- Modal body -->
            <div class="p-6 space-y-4">
                <div class="from-group">
                    <div class="input-area">
                        <label for="file" class="form-label">Select File</label>
                        <input type="file" wire:model.live="file"
                            class="form-control @error('file') !border-danger-500 @enderror" accept=".xlsx,.xls">
                        <p class="text-sm text-slate-500 mt-1">Supported formats: XLSX, XLS (Max size: 20MB)</p>
                    </div>
                    @error('file')
                        <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <!-- Modal footer -->
            <x-slot name="footer">
                <x-secondary-button wire:click="closeUploadModal">Close</x-secondary-button>
                <x-primary-button wire:click.prevent="uploadSheet"
                    loadingFunction="uploadSheet">Upload</x-primary-button>
            </x-slot>
        </x-modal>
    @endif

</div>
