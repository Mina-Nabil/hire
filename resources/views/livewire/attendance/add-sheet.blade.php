<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Attendance Sheet Management
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
        @if (count($uploadedAttendance) > 0)
            <header class="card-header noborder">
                <div class="flex justify-between items-center w-full">
                    <div class="flex-col space-y-1">
                        <h4 class="card-title">Uploaded Attendance Data</h4>
                        <span class="text-sm text-slate-500 mt-1">({{ count($uploadedAttendance) }} rows)</span>
                    </div>
                    <div class="flex space-x-2">
                        <x-secondary-button wire:click="clearUploadedAttendance">Clear</x-secondary-button>
                        <x-primary-button wire:click.prevent="saveAttendance">Save Valid Attendance</x-primary-button>
                    </div>
                </div>
            </header>
            <div class="card-body">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                        <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col" class="table-th">Employee</th>
                                <th scope="col" class="table-th">Date</th>
                                <th scope="col" class="table-th">Start Time</th>
                                <th scope="col" class="table-th">End Time</th>
                                <th scope="col" class="table-th">Hours</th>
                                <th scope="col" class="table-th">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            @foreach ($uploadedAttendance as $attendance)
                                <tr
                                    class="even:bg-slate-100 dark:even:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700">
                                    <td class="table-td">
                                        <div class="flex items-center">
                                            @if ($attendance['employee'] && $attendance['employee']->full_image_url)
                                                <div class="flex-none">
                                                    <div class="h-10 w-10 rounded-full overflow-hidden mr-2">
                                                        <img src="{{ $attendance['employee']->full_image_url }}"
                                                            alt="{{ $attendance['employee']->name }}"
                                                            class="h-full w-full object-cover">
                                                    </div>
                                                </div>
                                            @endif
                                            <div>
                                                <span class="text-sm text-slate-600 dark:text-slate-300 capitalize">
                                                    {{ $attendance['employee'] ? $attendance['employee']->name : 'Not Found' }}
                                                </span>
                                                @if ($attendance['employee'])
                                                    <span class="block text-xs text-slate-500">
                                                        {{ $attendance['employee']->email }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-td">
                                        {{ $attendance['date'] }}
                                    </td>
                                    <td class="table-td">
                                        {{ $attendance['start_time'] }}
                                    </td>
                                    <td class="table-td">
                                        {{ $attendance['end_time'] }}
                                    </td>
                                    <td class="table-td">
                                        {{ $attendance['hours'] }}
                                    </td>
                                    <td class="table-td">
                                        @if ($attendance['error'])
                                            <span class="badge badge-danger">Employee {{ $attendance['uploaded_name'] }}
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

        @if (!empty($zktecoResults))
            <header class="card-header noborder">
                <div class="flex justify-between items-center w-full">
                    <div class="flex-col space-y-1">
                        <h4 class="card-title">ZKTeco Device File Processing Results</h4>
                        <span class="text-sm text-slate-500 mt-1">
                            {{ $zktecoResults['punches_created'] }} punches created for {{ count($zktecoResults['dates_processed']) }} dates
                        </span>
                    </div>
                    <div class="flex space-x-2">
                        <x-secondary-button wire:click="clearUploadedAttendance">Clear</x-secondary-button>
                    </div>
                </div>
            </header>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-success-50 dark:bg-success-500/10 p-4 rounded-lg">
                        <div class="flex items-center">
                            <iconify-icon icon="ph:check-circle-bold" class="text-success-500 text-2xl mr-3"></iconify-icon>
                            <div>
                                <h5 class="text-success-700 dark:text-success-400 font-medium">Punches Created</h5>
                                <p class="text-success-600 dark:text-success-300 text-2xl font-bold">{{ $zktecoResults['punches_created'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-info-50 dark:bg-info-500/10 p-4 rounded-lg">
                        <div class="flex items-center">
                            <iconify-icon icon="ph:calendar-bold" class="text-info-500 text-2xl mr-3"></iconify-icon>
                            <div>
                                <h5 class="text-info-700 dark:text-info-400 font-medium">Dates Processed</h5>
                                <p class="text-info-600 dark:text-info-300 text-2xl font-bold">{{ count($zktecoResults['dates_processed']) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-warning-50 dark:bg-warning-500/10 p-4 rounded-lg">
                        <div class="flex items-center">
                            <iconify-icon icon="ph:warning-bold" class="text-warning-500 text-2xl mr-3"></iconify-icon>
                            <div>
                                <h5 class="text-warning-700 dark:text-warning-400 font-medium">Errors</h5>
                                <p class="text-warning-600 dark:text-warning-300 text-2xl font-bold">{{ count($zktecoResults['errors']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if (!empty($zktecoResults['dates_processed']))
                    <div class="mb-6">
                        <h6 class="text-slate-700 dark:text-slate-300 font-medium mb-2">Processed Dates:</h6>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($zktecoResults['dates_processed'] as $date)
                                <span class="badge badge-success">{{ $date }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (!empty($zktecoResults['errors']))
                    <div>
                        <h6 class="text-slate-700 dark:text-slate-300 font-medium mb-2">Errors Encountered:</h6>
                        <div class="bg-danger-50 dark:bg-danger-500/10 p-4 rounded-lg">
                            <ul class="text-danger-700 dark:text-danger-400 text-sm space-y-1">
                                @foreach ($zktecoResults['errors'] as $error)
                                    <li class="flex items-start">
                                        <iconify-icon icon="ph:x-circle" class="text-danger-500 mr-2 mt-0.5"></iconify-icon>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    @if ($showUploadModal)
        <x-modal wire:model="showUploadModal">
            <x-slot name="title">Upload Attendance Sheet</x-slot>
            <!-- Modal body -->
            <div class="p-6 space-y-4">
                <div class="form-group">
                    <div class="input-area">
                        <label for="fileType" class="form-label">File Type</label>
                        <select wire:model.live="fileType" class="form-control">
                            <option value="standard">Standard Attendance Sheet</option>
                            <option value="zkteco">ZKTeco Device File</option>
                        </select>
                        <p class="text-sm text-slate-500 mt-1">
                            <span x-show="$wire.fileType === 'standard'">Standard Excel format with employee names and times</span>
                            <span x-show="$wire.fileType === 'zkteco'">ZKTeco device export with device_id and movement data</span>
                        </p>
                    </div>
                </div>

                <div class="form-group">
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

                @if($fileType === 'zkteco')
                    <div class="bg-info-50 dark:bg-info-500/10 p-4 rounded-lg">
                        <div class="flex items-start">
                            <iconify-icon icon="ph:info-bold" class="text-info-500 text-xl mr-3 mt-0.5"></iconify-icon>
                            <div class="text-info-700 dark:text-info-400 text-sm">
                                <h6 class="font-medium mb-1">ZKTeco Device File Format:</h6>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Column C should contain device_id (employee identifier)</li>
                                    <li>Column D should contain timestamp and movement type</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
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
